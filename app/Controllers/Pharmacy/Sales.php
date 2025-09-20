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
use App\Models\Pharmacy\PharmacyReturnModel;
use App\Models\Pharmacy\PharmacyBrandModel;
use App\Models\Pharmacy\PharmacyGenericModel;

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
    protected $returnModel;
    protected $brandModel;
    protected $genericModel;


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
        $this->returnModel = new PharmacyReturnModel();
        $this->brandModel        = new PharmacyBrandModel();
        $this->genericModel      = new PharmacyGenericModel();
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
        // 1. Validation rules (same as your code)
        $rules = [
            'prescription_type' => 'required|in_list[in_hospital,outside_sale]',
            'items.*.medicine_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_selling_price' => 'required|decimal|greater_than[0]',
            'payment_method' => $this->request->getPost('prescription_type') === 'outside_sale' ? 'required|max_length[50]' : 'permit_empty|max_length[50]',
        ];
        $prescriptionType = $this->request->getPost('prescription_type');
        if ($prescriptionType === 'in_hospital') {
            $rules['patient_id_code'] = 'required|max_length[50]';
            $rules['doctor_id'] = 'permit_empty|integer';
        } else {
            $rules['outside_patient_name'] = 'required|min_length[3]|max_length[255]';
            $rules['outside_patient_phone'] = 'permit_empty|max_length[20]';
            $rules['outside_patient_address'] = 'permit_empty|max_length[255]';
        }
        if (!$this->validate($rules)) {
            die(print_r($this->validator->getErrors(), true));
        }

        $saleData = $this->request->getPost();
        $saleItems = $this->request->getPost('items');
        $this->db->transStart();
        $recordId = null;
        $invoiceNumber = '';

        try {
            // 2. Totals calculation block - this is where accuracy matters!
            $totalSubtotal = 0;    // Subtotal after discounts (before GST)
            $totalDiscount = 0;    // Total discount value summed from each item
            $totalGST = 0;        // Total GST for OP bills

            foreach ($saleItems as $item) {
                $qty = $item['quantity'];
                $unit = $item['unit_selling_price'];
                $disc = $item['discount_per_item'] ?? 0;
                $gstRate = $item['gst_rate'] ?? 0;

                $itemDiscount = $qty * $disc;
                $itemSubTotal = ($qty * $unit) - $itemDiscount;
                $totalSubtotal += $itemSubTotal;    // running subtotal (after discount)
                $totalDiscount += $itemDiscount;

                // For OP only: calculate GST on discounted subtotal
                if ($prescriptionType === 'outside_sale') {
                    $itemGST = $itemSubTotal * ($gstRate / 100);
                    $totalGST += $itemGST;
                }
            }
            $grandTotal = $totalSubtotal + $totalGST;

            // 3. Payment logic
            $paymentMethod = $saleData['payment_method'] ?? '';
            if ($paymentMethod === 'Credit') {
                $paidAmount = 0.00;
                $dueAmount = $grandTotal;
            } else {
                $paidAmount = $grandTotal;
                $dueAmount = 0.00;
            }

            $invoiceNumber = $this->generateInvoiceNumber($prescriptionType);

            // ---- OP (outside_sale) Bill Save ----
            if ($prescriptionType === 'outside_sale') {
                $mainSaleData = [
                    'invoice_number' => $invoiceNumber,
                    'sale_date' => date('Y-m-d H:i:s'),
                    'sales_person_id' => session()->get('user_id'),
                    'prescription_type' => $prescriptionType,
                    'outside_patient_name' => $saleData['outside_patient_name'],
                    'outside_patient_phone' => $saleData['outside_patient_phone'] ?? null,
                    'outside_patient_address' => $saleData['outside_patient_address'] ?? null,
                    // THE IMPORTANT FIELDS! Always set as below:
                    'net_amount' => $totalSubtotal, // After discounts/before GST
                    'discount_amount' => $totalDiscount, // Sum of line discounts
                    'total_amount' => $grandTotal, // Discounted subtotal + GST
                    'payment_method' => $paymentMethod,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'notes' => $saleData['notes'] ?? null,
                ];
                $recordId = $this->salesModel->insert($mainSaleData);
                if (!$recordId) {
                    throw new \Exception('Failed to create sale record.');
                }
            }
            // ---- IP (in_hospital) Bill Save ----
            elseif ($prescriptionType === 'in_hospital') {
                $patient = $this->patientModel->where('ipd_id_code', $saleData['patient_id_code'])->first();
                if (empty($patient)) {
                    throw new \Exception('Invalid In-Hospital Patient ID Code.');
                }
                $billingData = [
                    'bill_id' => $invoiceNumber,
                    'patient_id' => $patient['id'],
                    'sales_person_id' => session()->get('user_id'), // ADDED THIS LINE
                    'bill_date' => date('Y-m-d H:i:s'),
                    'total_amount' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                ];
                $recordId = $this->pharmacyBillingModel->insert($billingData);
                if (!$recordId) {
                    throw new \Exception('Failed to create billing record.');
                }
                // Save payment entry
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

            // 4. Sale items save (unchanged)
            foreach ($saleItems as $item) {
                $batch = $this->batchModel->find($item['batch_id']);
                if (empty($batch) || $batch['current_stock'] < $item['quantity']) {
                    throw new \Exception('Insufficient stock for medicine in batch ' . $item['batch_id'] . '.');
                }
                $qty = $item['quantity'];
                $unit = $item['unit_selling_price'];
                $disc = $item['discount_per_item'] ?? 0;
                $itemSubTotal = ($qty * $unit) - ($qty * $disc);

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
                    log_message('error', '[PharmacySaleItemModel validation errors] ' . json_encode($this->saleItemModel->errors()));
                    log_message('error', '[Sale item data failed to insert] ' . json_encode($saleItemData));
                    $errors = $this->saleItemModel->errors();
                    die('Failed to add sale item. Errors: ' . print_r($errors, true) . '<br>Data: ' . print_r($saleItemData, true));
                }
                $this->batchModel->update($item['batch_id'], ['current_stock' => new \CodeIgniter\Database\RawSql('current_stock - ' . $item['quantity'])]);
            }

            // 5. Finalize transaction/redirect
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




    public function getMedicinesByCategory($categoryId = null)
    {
        if (!is_numeric($categoryId) || $categoryId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid category ID.']);
        }

        try {
            $query = $this->medicineModel
                ->select('pharmacy_medicines.id, pharmacy_medicines.strength, pharmacy_medicines.gst_rate, pharmacy_medicines.hsn_code,
                      pb.brand_name, pg.generic_name, uom.name AS unit_of_measure_name,
                      pharmacy_batches.selling_price')
                ->join('pharmacy_brands pb', 'pb.id = pharmacy_medicines.brand_id', 'left')
                ->join('pharmacy_generics pg', 'pg.id = pharmacy_medicines.generic_id', 'left')
                ->join('pharmacy_units_of_measure uom', 'uom.id = pharmacy_medicines.unit_of_measure_id', 'left')
                ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
                ->where('pharmacy_medicines.category_id', $categoryId)
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



    public function invoice(string $invoiceNumber)
    {
        // Handle error invoice
        if ($invoiceNumber === 'ERROR-GEN') {
            return view('sales/error_general', [
                'title' => 'Invoice Generation Failed',
                'message' => 'The system failed to generate a valid invoice number. Please check logs and try again.'
            ]);
        }

        // Fetch the sale record (outside or in-hospital)
        $saleRecord = $this->salesModel->where('invoice_number', $invoiceNumber)->first();
        $isOutsideSale = !empty($saleRecord);
        $isInHospital = false;

        if (!$isOutsideSale) {
            $saleRecord = $this->pharmacyBillingModel->where('bill_id', $invoiceNumber)->first();
            $isInHospital = !empty($saleRecord);
        }

        if (empty($saleRecord)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Invoice not found: ' . $invoiceNumber);
        }

        // Set prescription type for view logic
        if ($isOutsideSale) {
            $saleRecord['prescription_type'] = 'outside_sale';
        } elseif ($isInHospital) {
            $saleRecord['prescription_type'] = 'in_hospital';
        }

        // Get field for sale items
        $idFieldName = $isOutsideSale ? 'sale_id' : 'billing_id';

        // Fetch sale items with all details
        $sql = "
        SELECT
            psi.*,
            pg.generic_name,
            pb.brand_name,
            pm.strength,
            pm.hsn_code,
            pm.gst_rate,
            b.batch_number,
            b.expiry_date,
            um.name AS unit_name
        FROM pharmacy_sale_items psi
        JOIN pharmacy_medicines pm ON pm.id = psi.medicine_id
        JOIN pharmacy_generics pg ON pg.id = pm.generic_id
        JOIN pharmacy_brands pb ON pb.id = pm.brand_id
        JOIN pharmacy_batches b ON b.id = psi.batch_id
        LEFT JOIN pharmacy_units_of_measure um ON um.id = pm.unit_of_measure_id
        WHERE psi.{$idFieldName} = ?
    ";
        $query = $this->db->query($sql, [$saleRecord['id']]);
        $saleItems = $query->getResultArray();

        // Fetch salesperson for OP
        $salesPerson = $isOutsideSale ? $this->userModel->find($saleRecord['sales_person_id']) : null;

        // Fetch patient and doctor for IP
        $patientDetails = null;
        $doctorDetails = null;
        if ($isInHospital) {
            $patientDetails = $this->patientModel->find($saleRecord['patient_id']);
            if ($patientDetails) {
                $patientDetails['name'] = trim(($patientDetails['first_name'] ?? '') . ' ' . ($patientDetails['last_name'] ?? ''));
            }
            if (!empty($patientDetails['referred_to_doctor_id'])) {
                $doctorDetails = $this->doctorModel->find($patientDetails['referred_to_doctor_id']);
                if ($doctorDetails) {
                    $doctorDetails['name'] = trim(($doctorDetails['first_name'] ?? '') . ' ' . ($doctorDetails['last_name'] ?? ''));
                }
            }
        }

        // Fetch payments and total paid (all installments for IP)
        $payments = [];
        $totalPaid = 0;
        if ($isInHospital) {
            $payments = $this->pharmacyBillingPaymentModel
                ->where('bill_id', $saleRecord['bill_id'])
                ->orderBy('payment_date', 'ASC')
                ->findAll();
            foreach ($payments as $payment) {
                $totalPaid += $payment['payment_amount'];
            }
        }

        // Calculate main sale totals
        $totalGST = 0;
        $totalSubtotal = 0;
        $totalQuantity = 0;
        $totalDiscount = 0;
        foreach ($saleItems as &$item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_selling_price'] ?? 0);
            $discountPerItem = floatval($item['discount_per_item'] ?? 0);

            $grossAmount = $quantity * $unitPrice;
            $itemDiscount = $quantity * $discountPerItem;
            $itemSubTotal = $grossAmount - $itemDiscount;
            $item['item_sub_total'] = $itemSubTotal;

            $gstRate = floatval($item['gst_rate'] ?? 0);
            $itemGSTAmount = ($isOutsideSale) ? ($itemSubTotal * $gstRate / 100) : 0;
            $item['gst_amount'] = $itemGSTAmount;

            $totalSubtotal += $itemSubTotal;
            $totalGST += $itemGSTAmount;
            $totalQuantity += $quantity;
            $totalDiscount += $itemDiscount;
        }

        // Grand total before returns
        $grandTotal = $totalSubtotal + $totalGST;
        $grandTotalWords = $this->numberToCurrencyWords($grandTotal);

        // Fetch returns with joins (include GST rate for OP)
        $this->returnModel->resetQuery();
        if ($isInHospital) {
            $returns = $this->returnModel
                ->select('pharmacy_returns.*, pg.generic_name as medicine_name, pharmacy_sale_items.unit_selling_price, pharmacy_sale_items.discount_per_item')
                ->join('pharmacy_medicines m', 'm.id = pharmacy_returns.medicine_id')
                ->join('pharmacy_generics pg', 'pg.id = m.generic_id')
                ->join('pharmacy_sale_items', 'pharmacy_sale_items.id = pharmacy_returns.sale_item_id')
                ->where('pharmacy_returns.billing_id', $saleRecord['id'])
                ->where('pharmacy_returns.approval_status', 'approved')
                ->findAll();
        } else {
            $returns = $this->returnModel
                ->select('pharmacy_returns.*, pg.generic_name as medicine_name, pharmacy_sale_items.unit_selling_price, pharmacy_sale_items.discount_per_item, pm.gst_rate')
                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id')
                ->join('pharmacy_generics pg', 'pg.id = pm.generic_id')
                ->join('pharmacy_sale_items', 'pharmacy_sale_items.id = pharmacy_returns.sale_item_id')
                ->where('pharmacy_returns.sale_id', $saleRecord['id'])
                ->where('pharmacy_returns.approval_status', 'approved')
                ->findAll();
        }



        // Calculate total amount returned (after discounts), and for OP calculate GST on returns if needed
        $totalReturnAmount = 0;
        $totalReturnGST = 0;
        foreach ($returns as $return) {
            $qty = $return['quantity_returned'] ?? 0;
            $unitPrice = $return['unit_selling_price'] ?? 0;
            $discountPerItem = $return['discount_per_item'] ?? 0;
            $amt = ($unitPrice - $discountPerItem) * $qty;
            $totalReturnAmount += $amt;

            // For OP only, refund GST as well (if required)
            if ($isOutsideSale && isset($return['gst_rate'])) {
                $totalReturnGST += $amt * ($return['gst_rate'] / 100);
            }
        }


        // For summary display, work with paid amount
        $totalPaidAmount = $totalPaid ?? 0;
        if ($isOutsideSale && !isset($totalPaid)) {
            $totalPaidAmount = 0;
        }

        // Calculate accurate due and excess using original grand total (add back returns)
        $originalGrandTotal = $grandTotal + $totalReturnAmount;

        $combinedPaidAndReturned = $totalPaidAmount + $totalReturnAmount;

        if ($combinedPaidAndReturned < $originalGrandTotal) {
            $dueAmount = $originalGrandTotal - $combinedPaidAndReturned;
            $excessPaidAmount = 0;
        } else {
            $dueAmount = 0;
            $excessPaidAmount = $combinedPaidAndReturned - $originalGrandTotal;
        }

        $extraPaidBeforeReturn = max(0, $totalPaidAmount - $originalGrandTotal);
        $finalRefundAmount = $totalReturnAmount + $excessPaidAmount;
        $data['finalRefundAmount'] = $finalRefundAmount;

        // Pass data to view
        $data = [
            'title' => 'Invoice',
            'sale' => $saleRecord,
            'salesPerson' => $salesPerson,
            'patientDetails' => $patientDetails,
            'doctorDetails' => $doctorDetails,
            'saleItems' => $saleItems,
            'gstAmount' => $totalGST,
            'grandTotal' => $grandTotal,
            'grandTotalWords' => $grandTotalWords,
            'subTotal' => $totalSubtotal,
            'totalQuantity' => $totalQuantity,
            'totalItems' => count($saleItems),
            'payments' => $payments,
            'paidAmount' => $totalPaidAmount,
            'returns' => $returns,
            'totalDiscount' => $totalDiscount,
            'totalReturnAmount' => $totalReturnAmount,
            'totalReturnGST' => $totalReturnGST,
            'extraPaidBeforeReturn' => $extraPaidBeforeReturn,
            'excessPaidAmount' => $excessPaidAmount,
            'dueAmount' => $dueAmount,
            'finalRefundAmount' => $finalRefundAmount,
        ];

        return view('pharmacy/sales/invoice', $data);
    }

    public function printInvoice(string $invoiceNumber)
    {
        // Fetch sale record: outside sale first then in-hospital billing
        $saleRecord = $this->salesModel->where('invoice_number', $invoiceNumber)->first();
        $isOutsideSale = !empty($saleRecord);

        if (!$isOutsideSale) {
            $saleRecord = $this->pharmacyBillingModel->where('bill_id', $invoiceNumber)->first();
            $isInHospital = !empty($saleRecord);
        } else {
            $isInHospital = false;
        }

        if (empty($saleRecord)) {
            throw new PageNotFoundException('Sale invoice not found for printing: ' . $invoiceNumber);
        }

        // Set prescription type for view logic
        if ($isOutsideSale) {
            $saleRecord['prescription_type'] = 'outside_sale';
        } elseif ($isInHospital) {
            $saleRecord['prescription_type'] = 'in_hospital';
        }

        // Determine the ID field for sale items
        $idFieldName = $isOutsideSale ? 'sale_id' : 'billing_id';

        // Fetch sale items with details including GST rate
        $sql = "
        SELECT
            psi.*,
            pg.generic_name,
            pb.brand_name,
            pm.strength,
            pm.hsn_code,
            pm.gst_rate,
            b.batch_number,
            b.expiry_date,
            um.name AS unit_name
            FROM pharmacy_sale_items psi
            JOIN pharmacy_medicines pm ON pm.id = psi.medicine_id
            JOIN pharmacy_generics pg ON pg.id = pm.generic_id
            JOIN pharmacy_brands pb ON pb.id = pm.brand_id
            JOIN pharmacy_batches b ON b.id = psi.batch_id
            LEFT JOIN pharmacy_units_of_measure um ON um.id = pm.unit_of_measure_id
            WHERE psi.{$idFieldName} = ?
        ";
        $query = $this->db->query($sql, [$saleRecord['id']]);
        $saleItems = $query->getResultArray();

        // Fetch salesperson for OP
        $salesPerson = $isOutsideSale ? $this->userModel->find($saleRecord['sales_person_id']) : null;

        // Fetch patient and doctor for IP
        $patientDetails = null;
        $doctorDetails = null;
        if ($isInHospital) {
            $patientDetails = $this->patientModel->find($saleRecord['patient_id']);
            if ($patientDetails) {
                $patientDetails['name'] = trim(($patientDetails['first_name'] ?? '') . ' ' . ($patientDetails['last_name'] ?? ''));
            }
            if (!empty($patientDetails['referred_to_doctor_id'])) {
                $doctorDetails = $this->doctorModel->find($patientDetails['referred_to_doctor_id']);
                if ($doctorDetails) {
                    $doctorDetails['name'] = trim(($doctorDetails['first_name'] ?? '') . ' ' . ($doctorDetails['last_name'] ?? ''));
                }
            }
        }

        // Fetch payments and total paid (all installments for IP)
        $payments = [];
        $totalPaid = 0;
        if ($isInHospital) {
            $payments = $this->pharmacyBillingPaymentModel
                ->where('bill_id', $saleRecord['bill_id'])
                ->orderBy('payment_date', 'ASC')
                ->findAll();
            foreach ($payments as $payment) {
                $totalPaid += $payment['payment_amount'];
            }
        }

        // Calculate main sale totals
        $totalGST = 0;
        $totalSubtotal = 0;
        $totalQuantity = 0;
        $totalDiscount = 0;
        foreach ($saleItems as &$item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_selling_price'] ?? 0);
            $discountPerItem = floatval($item['discount_per_item'] ?? 0);

            $grossAmount = $quantity * $unitPrice;
            $itemDiscount = $quantity * $discountPerItem;
            $itemSubTotal = $grossAmount - $itemDiscount;
            $item['item_sub_total'] = $itemSubTotal;

            $gstRate = floatval($item['gst_rate'] ?? 0);
            $itemGSTAmount = ($isOutsideSale) ? ($itemSubTotal * $gstRate / 100) : 0;
            $item['gst_amount'] = $itemGSTAmount;

            $totalSubtotal += $itemSubTotal;
            $totalGST += $itemGSTAmount;
            $totalQuantity += $quantity;
            $totalDiscount += $itemDiscount;
        }

        // Grand total before returns
        $grandTotal = $totalSubtotal + $totalGST;
        $grandTotalWords = $this->numberToCurrencyWords($grandTotal);

        // Fetch returns with joins (include GST rate for OP)
        $this->returnModel->resetQuery();
        if ($isInHospital) {
            $returns = $this->returnModel
                ->select('pharmacy_returns.*, pg.generic_name as medicine_name, pharmacy_sale_items.unit_selling_price, pharmacy_sale_items.discount_per_item')
                ->join('pharmacy_medicines m', 'm.id = pharmacy_returns.medicine_id')
                ->join('pharmacy_generics pg', 'pg.id = m.generic_id')
                ->join('pharmacy_sale_items', 'pharmacy_sale_items.id = pharmacy_returns.sale_item_id')
                ->where('pharmacy_returns.billing_id', $saleRecord['id'])
                ->where('pharmacy_returns.approval_status', 'approved')
                ->findAll();
        } else {
            $returns = $this->returnModel
                ->select('pharmacy_returns.*, pg.generic_name as medicine_name, pharmacy_sale_items.unit_selling_price, pharmacy_sale_items.discount_per_item, pm.gst_rate')
                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id')
                ->join('pharmacy_generics pg', 'pg.id = pm.generic_id')
                ->join('pharmacy_sale_items', 'pharmacy_sale_items.id = pharmacy_returns.sale_item_id')
                ->where('pharmacy_returns.sale_id', $saleRecord['id'])
                ->where('pharmacy_returns.approval_status', 'approved')
                ->findAll();
        }
        // Calculate total amount returned (after discounts), and for OP calculate GST on returns if needed
        $totalReturnAmount = 0;
        $totalReturnGST = 0;
        foreach ($returns as $return) {
            $qty = $return['quantity_returned'] ?? 0;
            $unitPrice = $return['unit_selling_price'] ?? 0;
            $discountPerItem = $return['discount_per_item'] ?? 0;
            $amt = ($unitPrice - $discountPerItem) * $qty;
            $totalReturnAmount += $amt;

            // For OP only, refund GST as well (if required)
            if ($isOutsideSale && isset($return['gst_rate'])) {
                $totalReturnGST += $amt * ($return['gst_rate'] / 100);
            }
        }


        // For summary display, work with paid amount
        $totalPaidAmount = $totalPaid ?? 0;
        if ($isOutsideSale && !isset($totalPaid)) {
            $totalPaidAmount = 0;
        }

        // Calculate accurate due and excess using original grand total (add back returns)
        $originalGrandTotal = $grandTotal + $totalReturnAmount;

        $combinedPaidAndReturned = $totalPaidAmount + $totalReturnAmount;

        if ($combinedPaidAndReturned < $originalGrandTotal) {
            $dueAmount = $originalGrandTotal - $combinedPaidAndReturned;
            $excessPaidAmount = 0;
        } else {
            $dueAmount = 0;
            $excessPaidAmount = $combinedPaidAndReturned - $originalGrandTotal;
        }

        $extraPaidBeforeReturn = max(0, $totalPaidAmount - $originalGrandTotal);
        $finalRefundAmount = $totalReturnAmount + $excessPaidAmount;
        $data['finalRefundAmount'] = $finalRefundAmount;

        // Pass data to view
        $data = [
            'title' => 'Invoice',
            'sale' => $saleRecord,
            'salesPerson' => $salesPerson,
            'patientDetails' => $patientDetails,
            'doctorDetails' => $doctorDetails,
            'saleItems' => $saleItems,
            'gstAmount' => $totalGST,
            'grandTotal' => $grandTotal,
            'grandTotalWords' => $grandTotalWords,
            'subTotal' => $totalSubtotal,
            'totalQuantity' => $totalQuantity,
            'totalItems' => count($saleItems),
            'payments' => $payments,
            'paidAmount' => $totalPaidAmount,
            'returns' => $returns,
            'totalDiscount' => $totalDiscount,
            'totalReturnAmount' => $totalReturnAmount,
            'totalReturnGST' => $totalReturnGST,
            'extraPaidBeforeReturn' => $extraPaidBeforeReturn,
            'excessPaidAmount' => $excessPaidAmount,
            'dueAmount' => $dueAmount,
            'finalRefundAmount' => $finalRefundAmount,
        ];

        return view('pharmacy/sales/print_invoice', $data);
    }



  public function listBills($type = 'all')
    {
        // Check if the user is authenticated.
        if (!session()->get('user_id')) {
            return redirect()->to(site_url('unauthorized'))->with('error', 'You are not authorized to view this page.');
        }

        $bills = [];
        $title = 'Sales Bills';

        // Fetch data based on the type of sales
        if ($type === 'in_hospital') {
            $bills = $this->db->table('pharmacy_billings pb')
                ->select('pb.*, p.first_name, p.last_name, p.phone_number, p.ipd_id_code')
                ->join('patients p', 'p.id = pb.patient_id')
                ->orderBy('pb.bill_date', 'DESC')
                ->get()
                ->getResultArray();
            $title = 'In-Patients Bills';
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

            $paymentModel = new PharmacyBillingPaymentModel();

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
            $title = 'Patients List';
        } elseif ($type === 'outside_sale') {
            $bills = $this->db->table('pharmacy_sales')
                ->where('prescription_type', 'outside_sale')
                ->orderBy('sale_date', 'DESC')
                ->get()
                ->getResultArray();
            $title = 'Out-Patients Bills';
        } else {
            // This is the updated logic for the 'all' bills view
            // 1. Fetch Out-Patient bills
            $outsideBills = $this->db->table('pharmacy_sales')
                ->orderBy('sale_date', 'DESC')
                ->get()
                ->getResultArray();
            
            // 2. Fetch In-Patient bills with patient details
            $inHospitalBills = $this->db->table('pharmacy_billings pb')
                ->select('pb.*, p.first_name, p.last_name, p.phone_number')
                ->join('patients p', 'p.id = pb.patient_id')
                ->orderBy('pb.bill_date', 'DESC')
                ->get()
                ->getResultArray();

            // 3. Add a type key to each bill to unify the data
            foreach ($outsideBills as &$bill) {
                $bill['bill_id'] = $bill['invoice_number']; // Unify the ID key
                $bill['sale_type'] = 'Out-Patient';
                $bill['patient_name'] = $bill['outside_patient_name'];
                $bill['phone_number'] = $bill['outside_patient_phone'];
            }
            foreach ($inHospitalBills as &$bill) {
                $bill['bill_id'] = $bill['bill_id'];
                $bill['sale_type'] = 'In-Patient';
                $bill['patient_name'] = $bill['first_name'] . ' ' . $bill['last_name'];
                $bill['phone_number'] = $bill['phone_number'];
            }

            // 4. Merge the two arrays into one
            $bills = array_merge($outsideBills, $inHospitalBills);

            // 5. Sort the merged array by date
            usort($bills, function ($a, $b) {
                $dateA = strtotime($a['sale_date'] ?? $a['bill_date']);
                $dateB = strtotime($b['sale_date'] ?? $b['bill_date']);
                return $dateB <=> $dateA;
            });
        }

        $data = [
            'title'       => $title,
            'bills'       => $bills,
            'currentType' => $type
        ];

        return view('pharmacy/sales/list', $data);
    }




    public function listToday()
    {
        // Check if the user is authenticated.
        if (!session()->get('user_id')) {
            return redirect()->to(site_url('unauthorized'))->with('error', 'You are not authorized to view this page.');
        }

        $today = date('Y-m-d');

        // Fetch all outside sales for today
        $outsideSales = $this->db->table('pharmacy_sales ps')
            ->select('ps.invoice_number AS bill_id, ps.sale_date AS date, ps.outside_patient_name AS patient_name, ps.outside_patient_phone AS phone_number, ps.total_amount, u.first_name AS user_name, "Out-Patient" as type')->join('users u', 'u.id = ps.sales_person_id')
            ->where('DATE(ps.sale_date)', $today)
            ->get()
            ->getResultArray();

        // Fetch all in-hospital bills for today
        $inHospitalBills = $this->db->table('pharmacy_billings pb')
            ->select('pb.bill_id, pb.bill_date AS date, CONCAT(p.first_name, " ", p.last_name) AS patient_name, p.phone_number, pb.total_amount, u.first_name AS user_name, "In-Patient" as type')
            ->join('patients p', 'p.id = pb.patient_id')
            ->join('users u', 'u.id = pb.sales_person_id')
            ->where('DATE(pb.bill_date)', $today)
            ->get()
            ->getResultArray();

        // Combine and sort the results
        $bills = array_merge($outsideSales, $inHospitalBills);

        usort($bills, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $data = [
            'title' => 'Today\'s Sales',
            'bills' => $bills,
            'currentType' => 'today'
        ];

        return view('pharmacy/sales/todays_sales', $data);
    }
}
