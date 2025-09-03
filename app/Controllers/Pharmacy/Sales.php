<?php

namespace App\Controllers\Pharmacy;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql;

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacySaleItemModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyUnitOfMeasureModel;
use App\Models\Pharmacy\PharmacyCategoryModel;
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacyInvoiceSequenceModel;
use App\Models\Pharmacy\PharmacyBillingPaymentModel;


// Import HMS Core Models
use App\Models\PatientModel;
use App\Models\DoctorModel;

use App\Models\UserModel;

class Sales extends BaseController
{
    protected $salesModel;
    protected $saleItemModel;
    protected $batchModel;
    protected $medicineModel;
    protected $unitOfMeasureModel;
    protected $patientModel;
    protected $doctorModel;
    protected $userModel;
    protected $categoryModel;
    protected $pharmacyBillingModel;
    protected $pharmacyInvoiceSequenceModel;
    protected $pharmacyBillingPaymentModel;


    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();

        $this->salesModel = new PharmacySalesModel();
        $this->saleItemModel = new PharmacySaleItemModel();
        $this->batchModel = new PharmacyBatchModel();
        $this->medicineModel = new PharmacyMedicineModel();
        $this->unitOfMeasureModel = new PharmacyUnitOfMeasureModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new PharmacyCategoryModel();
        $this->pharmacyBillingModel = new PharmacyBillingModel();
        $this->pharmacyInvoiceSequenceModel = new pharmacyInvoiceSequenceModel();
        $this->pharmacyBillingPaymentModel = new PharmacyBillingPaymentModel();
    }





    protected function generateInvoiceNumber(string $prescriptionType): string
    {
        $prefix = ($prescriptionType === 'outside_sale') ? 'PHM-OP' : 'PHM-IP';
        $date = date('Ymd');

        try {
            $nextNumber = $this->pharmacyInvoiceSequenceModel->getAndIncrementSequence($prefix);
            return $prefix . '-' . $date . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            log_message('error', 'Error generating invoice number: ' . $e->getMessage());
            return 'ERROR-GEN';
        }
    }

   public function processSale()
{
    // 1. Define and perform validation based on user input
    $rules = [
        'prescription_type' => 'required|in_list[in_hospital,outside_sale]',
        'items.*.medicine_id' => 'required|integer',
        'items.*.batch_id' => 'required|integer',
        'items.*.quantity' => 'required|integer|greater_than[0]',
        'items.*.unit_selling_price' => 'required|decimal|greater_than[0]',
        // Make payment_method required only for outside_sale
        'payment_method' => $this->request->getPost('prescription_type') === 'outside_sale' ? 'required|max_length[50]' : 'permit_empty|max_length[50]',
    ];

    // Add conditional validation rules for patient/doctor details
    $prescriptionType = $this->request->getPost('prescription_type');
    if ($prescriptionType === 'in_hospital') {
        $rules['patient_id_code'] = 'required|max_length[50]';
        $rules['doctor_id'] = 'permit_empty|integer';
    } else { // 'outside_sale'
        $rules['outside_patient_name'] = 'required|min_length[3]|max_length[255]';
        $rules['outside_patient_phone'] = 'permit_empty|max_length[20]';
        $rules['outside_patient_address'] = 'permit_empty|max_length[255]';
    }

    if (!$this->validate($rules)) {
        // This will now halt the script and show the exact validation errors.
        die(print_r($this->validator->getErrors(), true));
    }

    $saleData = $this->request->getPost();
    $saleItems = $this->request->getPost('items');

    // Start a database transaction
    $this->db->transStart();
    $recordId = null; // Variable to hold the final ID for redirect
    $invoiceNumber = ''; // Variable to hold the final invoice number

    try {
        // Calculate totals before inserting anything
        $totalAmount = 0;
        $netAmount = 0;
        foreach ($saleItems as $item) {
            $itemTotal = ($item['quantity'] * $item['unit_selling_price']);
            $itemSubTotal = $itemTotal - ($item['discount_per_item'] ?? 0);
            $totalAmount += $itemTotal;
            $netAmount += $itemSubTotal;
        }
        $finalNetAmount = $netAmount - ($saleData['total_discount'] ?? 0);

        $paymentMethod = $saleData['payment_method'] ?? '';

        // Determine paid and due amounts based on payment method
        if ($paymentMethod === 'Credit') {
            $paidAmount = 0.00;
            $dueAmount = $finalNetAmount;
        } else {
            $paidAmount = $finalNetAmount;
            $dueAmount = 0.00;
        }

        // Generate the invoice number first
        $invoiceNumber = $this->generateInvoiceNumber($prescriptionType);

        // Conditional logic to save to the correct table
        if ($prescriptionType === 'outside_sale') {
            // Prepare data for the pharmacy_sales table
            $mainSaleData = [
                'invoice_number' => $invoiceNumber,
                'sale_date' => date('Y-m-d H:i:s'),
                'sales_person_id' => session()->get('user_id'),
                'prescription_type' => $prescriptionType,
                'outside_patient_name' => $saleData['outside_patient_name'],
                'outside_patient_phone' => $saleData['outside_patient_phone'] ?? null,
                'outside_patient_address' => $saleData['outside_patient_address'] ?? null,
                'total_amount' => $totalAmount,
                'discount_amount' => $saleData['total_discount'] ?? 0,
                'net_amount' => $finalNetAmount,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'notes' => $saleData['notes'] ?? null,
            ];

            // Insert the main sale record
            $recordId = $this->salesModel->insert($mainSaleData);
            if (!$recordId) {
                throw new \Exception('Failed to create sale record.');
            }
        } elseif ($prescriptionType === 'in_hospital') {
            // Prepare data for the pharmacy_billings table
            $patient = $this->patientModel->where('ipd_id_code', $saleData['patient_id_code'])->first();
            if (empty($patient)) {
                throw new \Exception('Invalid In-Hospital Patient ID Code.');
            }

            $billingData = [
                'bill_id' => $invoiceNumber,
                'patient_id' => $patient['id'],
                'bill_date' => date('Y-m-d H:i:s'),
                'total_amount' => $finalNetAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
            ];

            // Insert the main billing record
            $recordId = $this->pharmacyBillingModel->insert($billingData);
            if (!$recordId) {
                throw new \Exception('Failed to create billing record.');
            }

            // Insert initial payment record with the paid amount (zero if Credit)
            $paymentData = [
                'bill_id' => $invoiceNumber,
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_amount' => $paidAmount,
                'payment_method' => $paymentMethod ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->pharmacyBillingPaymentModel->insert($paymentData)) {
                throw new \Exception('Failed to create billing payment record.');
            }
        }

        // Loop through each sale item, add to the correct table, and deduct stock
        foreach ($saleItems as $item) {
            $batch = $this->batchModel->find($item['batch_id']);
            if (empty($batch) || $batch['current_stock'] < $item['quantity']) {
                throw new \Exception('Insufficient stock for medicine in batch ' . $item['batch_id'] . '.');
            }

            $itemSubTotal = ($item['quantity'] * $item['unit_selling_price']) - ($item['discount_per_item'] ?? 0);

            // The saleItemModel needs to be able to handle both a sale_id and a billing_id
            $saleItemData = [
                'sale_id' => ($prescriptionType === 'outside_sale') ? $recordId : null,
                'billing_id' => ($prescriptionType === 'in_hospital') ? $recordId : null,
                'medicine_id' => $item['medicine_id'],
                'batch_id' => $item['batch_id'],
                'quantity' => $item['quantity'],
                'unit_selling_price' => $item['unit_selling_price'],
                'discount_per_item' => $item['discount_per_item'] ?? 0,
                'sub_total' => $itemSubTotal,
            ];

            if (!$this->saleItemModel->insert($saleItemData)) {
                // Log the model errors for investigation
                log_message('error', '[PharmacySaleItemModel validation errors] ' . json_encode($this->saleItemModel->errors()));
                log_message('error', '[Sale item data failed to insert] ' . json_encode($saleItemData));
                // Also show the error details to the user (for debugging)
                $errors = $this->saleItemModel->errors();
                die('Failed to add sale item. Errors: ' . print_r($errors, true) . '<br>Data: ' . print_r($saleItemData, true));
            }

            $this->batchModel->update($item['batch_id'], ['current_stock' => new \CodeIgniter\Database\RawSql('current_stock - ' . $item['quantity'])]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Transaction failed. Database status check returned false.');
        }

        return redirect()->to(site_url('pharmacy/sales/invoice/' . $invoiceNumber))->with('success', 'Sale processed successfully!');
    } catch (\Exception $e) {
        $this->db->transRollback();
        die('Sale failed: ' . $e->getMessage());
    }
}


    public function invoice(string $invoiceNumber)
    {
        // If the invoice number is the special ERROR-GEN string, handle it gracefully.
        if ($invoiceNumber === 'ERROR-GEN') {
            return view('pharmacy/sales/error_general', [
                'title' => 'Invoice Generation Failed',
                'message' => 'The system failed to generate an invoice number. Please check the application logs for a more detailed error message and try again.'
            ]);
        }

        // First, assume it's an outside sale and try to find it
        $saleRecord = $this->salesModel->where('invoice_number', $invoiceNumber)->first();
        $isOutsideSale = !empty($saleRecord);

        // If not found in sales, assume it's an in-hospital billing and try to find it
        if (!$isOutsideSale) {
            $saleRecord = $this->pharmacyBillingModel->where('bill_id', $invoiceNumber)->first();
            $isInHospital = !empty($saleRecord);
        } else {
            $isInHospital = false;
        }

        log_message('debug', 'Sale record patient_id: ' . ($saleRecord['patient_id'] ?? 'not present'));

        if (empty($saleRecord)) {
            // If still not found, throw a PageNotFound exception
            throw new PageNotFoundException('Sale invoice not found: ' . $invoiceNumber);
        }

        // Explicitly set prescription_type in sale record for the view logic
        if ($isOutsideSale) {
            $saleRecord['prescription_type'] = 'outside_sale';
        } elseif ($isInHospital) {
            $saleRecord['prescription_type'] = 'in_hospital';
        }

        // Fetch the corresponding sale items using the correct ID field
        $idFieldName = $isOutsideSale ? 'sale_id' : 'billing_id';
        $items = $this->saleItemModel->where($idFieldName, $saleRecord['id'])->findAll();

        // Fetch related user/patient/doctor details
        $salesPerson = $isOutsideSale ? $this->userModel->find($saleRecord['sales_person_id']) : null;

        $patientDetails = null;
        $doctorDetails = null;

        if ($isInHospital) {
            $patientDetails = $this->patientModel->find($saleRecord['patient_id']);
            if ($patientDetails) {
                $patientDetails['name'] = trim(($patientDetails['first_name'] ?? '') . ' ' . ($patientDetails['last_name'] ?? ''));
                log_message('debug', 'Patient found: ' . $patientDetails['name']);
            } else {
                log_message('error', 'No patient details found for patient_id: ' . ($saleRecord['patient_id'] ?? ''));
            }

            if (!empty($patientDetails['referred_to_doctor_id'])) {
                $doctorModel = new \App\Models\DoctorModel();
                $doctorDetails = $doctorModel->find($patientDetails['referred_to_doctor_id']);
                if (!empty($doctorDetails)) {
                    $doctorDetails['name'] = trim(($doctorDetails['first_name'] ?? '') . ' ' . ($doctorDetails['last_name'] ?? ''));
                }
            }

            // Load pharmacy billing payments model to get installment payments
            $billingPaymentModel = new PharmacyBillingPaymentModel();
            $payments = $billingPaymentModel->where('bill_id', $saleRecord['bill_id'])->orderBy('payment_date', 'ASC')->findAll();

            // Sum amounts of installments paid
            $totalPaid = 0;
            foreach ($payments as $payment) {
                $totalPaid += $payment['payment_amount'];
            }
        }

        // Raw SQL Query to get all required data, including HSN code and expiry date.
        $sql = "
        SELECT
            psi.*,
            pm.generic_name,
            pm.brand_name,
            pm.strength,
            pm.hsn_code,
            pm.gst_rate,
            pb.batch_number,
            pb.expiry_date,
            um.name AS unit_of_measure
        FROM
            pharmacy_sale_items psi
        JOIN
            pharmacy_medicines pm ON pm.id = psi.medicine_id
        JOIN
            pharmacy_batches pb ON pb.id = psi.batch_id
        LEFT JOIN
            pharmacy_units_of_measure um ON um.id = pm.unit_of_measure_id
        WHERE
            psi.{$idFieldName} = ?
        ";

        $query = $this->db->query($sql, [$saleRecord['id']]);
        $saleItems = $query->getResultArray();

        // GST and total calculation logic.
        $totalGstAmount = 0;
        $totalSubTotal = 0;
        $totalQuantity = 0;

        foreach ($saleItems as &$item) {
            $itemGross = $item['quantity'] * $item['unit_selling_price'];
            // The subtotal is the amount after per-item discounts but before GST
            $itemSubTotal = $itemGross - $item['discount_per_item'];
            $item['item_sub_total'] = $itemSubTotal;
            $totalSubTotal += $itemSubTotal;

            // Calculate GST amount only if prescription type is outside sale
            $prescriptionType = $isOutsideSale ? 'outside_sale' : 'in_hospital';
            $itemGSTAmount = ($prescriptionType === 'outside_sale') ? $itemSubTotal * ($item['gst_rate'] / 100) : 0;
            $totalGstAmount += $itemGSTAmount;
            $item['gst_amount'] = $itemGSTAmount;

            // Sum up total quantity
            $totalQuantity += $item['quantity'];
        }

        // Grand total is the sum of subtotal, minus the total sale discount, plus GST
        $grandTotal = $totalSubTotal - ($saleRecord['discount_amount'] ?? 0) + $totalGstAmount;

        $grandTotalInWords = $this->numberToCurrencyWords($grandTotal);

        $data = [
            'title' => 'Sales Invoice',
            'sale' => $saleRecord,
            'salesPerson' => $salesPerson,
            'patientDetails' => $patientDetails,
            'doctorDetails' => $doctorDetails,
            'saleItems' => $saleItems,
            'gstAmount' => $totalGstAmount,
            'grandTotal' => $grandTotal,
            'subTotal' => $totalSubTotal,
            'totalItems' => count($saleItems),
            'totalQuantity' => $totalQuantity,
            'grandTotalInWords' => $grandTotalInWords,
        ];

        // Attach billing payments info for in-hospital bills
        if ($isInHospital) {
            $data['payments'] = $payments ?? [];
            $data['paidAmount'] = $totalPaid ?? 0;
            $data['dueAmount'] = $saleRecord['total_amount'] - ($totalPaid ?? 0);
        }

        return view('pharmacy/sales/invoice', $data);
    }






    private function numberToCurrencyWords($number)
    {
        $whole = floor($number);
        $fraction = round(($number - $whole) * 100);

        $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $wholePart = $formatter->format($whole);
        $fractionPart = $fraction > 0 ? " and " . $formatter->format($fraction) . " paisa" : "";

        return ucfirst($wholePart) . " rupees" . $fractionPart . " only";
    }



    public function index()
    {
        $medicinesWithPrice = $this->medicineModel
            ->select('pharmacy_medicines.*, pharmacy_batches.selling_price')
            ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
            ->where('pharmacy_batches.current_stock >', 0)
            ->where('pharmacy_batches.expiry_date >', date('Y-m-d'))
            ->groupBy('pharmacy_medicines.id')
            ->findAll();

        $categories = $this->categoryModel->findAll();

        $data = [
            'title' => 'Pharmacy Sales POS',
            'medicines' => $medicinesWithPrice,
            'categories' => $categories,
            'validation' => service('validation')
        ];
        return view('pharmacy/sales/pos', $data);
    }



    public function listSales()
    {
        $sales = $this->salesModel->findAll();

        $data = [
            'title' => 'Sales History',
            'sales' => $sales
        ];
        return view('pharmacy/sales/list', $data);
    }
    public function listBills($type = 'all')
    {
        if (!session()->get('user_id')) {
            return redirect()->to(site_url('unauthorized'))->with('error', 'You are not authorized to view this page.');
        }

        $bills = [];

        if ($type === 'in_hospital') {
            $bills = $this->db->table('pharmacy_billings')
                ->select('pharmacy_billings.*, patients.*')
                ->join('patients', 'patients.id = pharmacy_billings.patient_id')
                ->orderBy('bill_date', 'DESC')
                ->get()
                ->getResultArray();
        } elseif ($type === 'patients') {
            // Fetch patients billing summary with total billed amount and latest bill date
            $bills = $this->db->table('pharmacy_billings pb')
                ->select('
                p.id,
                p.first_name,
                p.last_name,
                p.ipd_id_code,
                p.phone_number,
                MAX(pb.bill_date) AS latest_bill_date,
                SUM(pb.total_amount) AS total_amount
            ')
                ->join('patients p', 'p.id = pb.patient_id')
                ->groupBy('pb.patient_id')
                ->orderBy('latest_bill_date', 'DESC')
                ->get()
                ->getResultArray();

            $paymentModel = new \App\Models\Pharmacy\PharmacyBillingPaymentModel();

            // Calculate total paid and due for each patient
            foreach ($bills as &$bill) {
                $payments = $paymentModel->selectSum('payment_amount')
                    ->whereIn('bill_id', function ($builder) use ($bill) {
                        $builder->select('bill_id')
                            ->from('pharmacy_billings')
                            ->where('patient_id', $bill['id']);
                    })->first();

                $totalPaid = $payments['payment_amount'] ?? 0;
                $bill['total_paid_amount'] = $totalPaid;
                $bill['due_amount'] = $bill['total_amount'] - $totalPaid;
            }
        } else {
            $query = $this->db->table('pharmacy_sales');
            if ($type === 'outside_sale') {
                $query->where('prescription_type', 'outside_sale');
            }
            $bills = $query->orderBy('sale_date', 'DESC')->get()->getResultArray();
        }

        $data = [
            'title' => 'Sales Bills',
            'bills' => $bills,
            'currentType' => $type
        ];

        return view('pharmacy/sales/list', $data);
    }






    // Add this new method to your Sales.php controller
    public function getMedicinesByCategory($categoryId = null)
    {
        // A simple validation to ensure categoryId is a valid integer
        if (!is_numeric($categoryId) || $categoryId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid category ID.']);
        }

        try {
            $query = $this->medicineModel
                ->select('pharmacy_medicines.*, pharmacy_batches.selling_price')
                ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
                ->where('pharmacy_medicines.category_id', $categoryId) // Filter by category ID
                ->where('pharmacy_batches.current_stock >', 0)
                ->where('pharmacy_batches.expiry_date >', date('Y-m-d'))
                ->groupBy('pharmacy_medicines.id')
                ->findAll();

            return $this->response->setJSON(['status' => 'success', 'medicines' => $query]);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching medicines by category: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'An error occurred while fetching medicines.']);
        }
    }


    public function billsByPatient(int $patientId)
    {
        $patient = $this->patientModel->find($patientId);
        if (!$patient) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Patient not found');
        }
        $bills = $this->pharmacyBillingModel->where('patient_id', $patientId)->findAll();

        // Optionally calculate payments and due amounts here for each bill

        return view('pharmacy/sales/bills_by_patient', [
            'title' => 'Bills for ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient' => $patient,
            'bills' => $bills,
        ]);
    }


    public function printInvoice(string $invoiceNumber)
    {
        // Try to fetch outside sale first
        $saleRecord = $this->salesModel->where('invoice_number', $invoiceNumber)->first();
        $isOutsideSale = !empty($saleRecord);

        // If not found, try as in-hospital billing
        if (!$isOutsideSale) {
            $saleRecord = $this->pharmacyBillingModel->where('bill_id', $invoiceNumber)->first();
            $isInHospital = !empty($saleRecord);
        } else {
            $isInHospital = false;
        }

        if (empty($saleRecord)) {
            throw new PageNotFoundException('Sale invoice not found for printing: ' . $invoiceNumber);
        }

        // Set the type for logic below
        if ($isOutsideSale) {
            $saleRecord['prescription_type'] = 'outside_sale';
        } elseif ($isInHospital) {
            $saleRecord['prescription_type'] = 'in_hospital';
        }

        // Fetch sale items for the sale
        $idFieldName = $isOutsideSale ? 'sale_id' : 'billing_id';
        $items = $this->saleItemModel->where($idFieldName, $saleRecord['id'])->findAll();

        // Get all patient/doctor details
        $salesPerson = $isOutsideSale ? $this->userModel->find($saleRecord['sales_person_id']) : null;
        $patientDetails = null;
        $doctorDetails = null;

        if ($isInHospital) {
            $patientDetails = $this->patientModel->find($saleRecord['patient_id']);
            if ($patientDetails) {
                $patientDetails['name'] = trim(($patientDetails['first_name'] ?? '') . ' ' . ($patientDetails['last_name'] ?? ''));
            }

            if (!empty($patientDetails['referred_to_doctor_id'])) {
                $doctorDetails = $this->doctorModel->find($patientDetails['referred_to_doctor_id']);
                if (!empty($doctorDetails)) {
                    $doctorDetails['name'] = trim(($doctorDetails['first_name'] ?? '') . ' ' . ($doctorDetails['last_name'] ?? ''));
                }
            }

            $payments = $this->pharmacyBillingPaymentModel
                ->where('bill_id', $saleRecord['bill_id'])->orderBy('payment_date', 'ASC')->findAll();
            $totalPaid = array_sum(array_column($payments, 'payment_amount'));
        }

        // Same SQL and calculations as in invoice() for detailed items
        $sql = "
        SELECT
            psi.*,
            pm.generic_name,
            pm.brand_name,
            pm.strength,
            pm.hsn_code,
            pm.gst_rate,
            pb.batch_number,
            pb.expiry_date,
            um.name AS unit_of_measure
        FROM
            pharmacy_sale_items psi
        JOIN
            pharmacy_medicines pm ON pm.id = psi.medicine_id
        JOIN
            pharmacy_batches pb ON pb.id = psi.batch_id
        LEFT JOIN
            pharmacy_units_of_measure um ON um.id = pm.unit_of_measure_id
        WHERE
            psi.{$idFieldName} = ?
    ";
        $query = $this->db->query($sql, [$saleRecord['id']]);
        $saleItems = $query->getResultArray();

        // Calculate GST, subtotal, and quantities
        $totalGstAmount = 0;
        $totalSubTotal = 0;
        $totalQuantity = 0;

        foreach ($saleItems as &$item) {
            $itemGross = $item['quantity'] * $item['unit_selling_price'];
            $itemSubTotal = $itemGross - $item['discount_per_item'];
            $item['item_sub_total'] = $itemSubTotal;
            $totalSubTotal += $itemSubTotal;

            $prescriptionType = $isOutsideSale ? 'outside_sale' : 'in_hospital';
            $itemGSTAmount = ($prescriptionType === 'outside_sale') ? $itemSubTotal * ($item['gst_rate'] / 100) : 0;
            $totalGstAmount += $itemGSTAmount;
            $item['gst_amount'] = $itemGSTAmount;

            $totalQuantity += $item['quantity'];
        }

        $grandTotal = $totalSubTotal - ($saleRecord['discount_amount'] ?? 0) + $totalGstAmount;
        $grandTotalInWords = $this->numberToCurrencyWords($grandTotal);

        $data = [
            'title' => 'Print Sales Invoice',
            'sale' => $saleRecord,
            'salesPerson' => $salesPerson,
            'patientDetails' => $patientDetails,
            'doctorDetails' => $doctorDetails,
            'saleItems' => $saleItems,
            'gstAmount' => $totalGstAmount,
            'grandTotal' => $grandTotal,
            'subTotal' => $totalSubTotal,
            'totalItems' => count($saleItems),
            'totalQuantity' => $totalQuantity,
            'grandTotalInWords' => $grandTotalInWords,
        ];

        if ($isInHospital) {
            $data['payments'] = $payments ?? [];
            $data['paidAmount'] = $totalPaid ?? 0;
            $data['dueAmount'] = $saleRecord['total_amount'] - ($totalPaid ?? 0);
        }

        // Use the minimal print view (no sidebars/navs)
        return view('pharmacy/sales/print_invoice', $data);
    }
}
