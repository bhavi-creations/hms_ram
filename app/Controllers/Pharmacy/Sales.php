<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql; // For direct SQL in update

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacySaleItemModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyMedicineModel; // To fetch medicine details for sales panel

// Import HMS Core Models (assuming they exist in App\Models\)
use App\Models\PatientModel; // Adjust namespace if different, e.g., App\Models\HMS\PatientModel
use App\Models\DoctorModel;  // Adjust namespace if different

class Sales extends BaseController
{
    protected $salesModel;
    protected $saleItemModel;
    protected $batchModel;
    protected $medicineModel;
    protected $patientModel; // Existing HMS model
    protected $doctorModel;  // Existing HMS model

    public function __construct()
    {
        $this->salesModel    = new PharmacySalesModel();
        $this->saleItemModel = new PharmacySaleItemModel();
        $this->batchModel    = new PharmacyBatchModel();
        $this->medicineModel = new PharmacyMedicineModel();
        $this->patientModel  = new PatientModel(); // Instantiate your existing Patient Model
        $this->doctorModel   = new DoctorModel();  // Instantiate your existing Doctor Model
    }

    /**
     * Displays the sales Point of Sale (POS) panel.
     */
    public function index()
    {
        $data = [
            'title'         => 'Pharmacy Sales POS',
            'medicines'     => $this->medicineModel->findAll(), // Or a more detailed query for stock
            'validation'    => service('validation')
        ];
        return view('pharmacy/sales/pos', $data);
    }

    /**
     * Handles the submission of a new sales transaction.
     */
    public function processSale()
    {
        $rules = [
            'prescription_type'   => 'required|in_list[in_hospital,outside_sale]',
            'items.*.medicine_id' => 'required|integer',
            'items.*.batch_id'    => 'required|integer',
            'items.*.quantity'    => 'required|integer|greater_than[0]',
            'items.*.unit_selling_price' => 'required|decimal|greater_than[0]',
            'payment_method'      => 'required|max_length[50]',
        ];

        // Conditional validation for patient/doctor details
        if ($this->request->getPost('prescription_type') === 'in_hospital') {
            $rules['patient_id_code'] = 'required|max_length[50]'; // User inputs patient_id_code
            $rules['doctor_id'] = 'permit_empty|integer'; // Doctor might be optional for in-hospital
        } else { // outside_sale
            $rules['outside_patient_name'] = 'required|min_length[3]|max_length[255]';
            $rules['outside_patient_phone'] = 'permit_empty|max_length[20]'; // Optional
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $saleData = $this->request->getPost();
        $saleItems = $this->request->getPost('items');

        // Prepare main sale data
        $mainSaleData = [
            // 'invoice_number'  => 'INV-' . time() . rand(100, 999), 
            'sale_date'       => date('Y-m-d H:i:s'),
            'sales_person_id' => session()->get('user_id'), // Assuming user_id is in session
            'prescription_type' => $saleData['prescription_type'],
            'payment_method'  => $saleData['payment_method'],
            'notes'           => $saleData['notes'] ?? null,
            'total_amount'    => 0, // Will calculate below
            'discount_amount' => $saleData['total_discount'] ?? 0,
            'net_amount'      => 0, // Will calculate below
        ];

        // Handle patient details based on prescription type
        if ($saleData['prescription_type'] === 'in_hospital') {
            // Fetch patient ID from patient_id_code
            $patient = $this->patientModel->where('patient_id_code', $saleData['patient_id_code'])->first();
            if (empty($patient)) {
                return redirect()->back()->withInput()->with('error', 'Invalid In-Hospital Patient ID Code.');
            }
            $mainSaleData['patient_id'] = $patient['id'];
            $mainSaleData['doctor_id']  = $saleData['doctor_id'] ?? null;
        } else {
            $mainSaleData['outside_patient_name']  = $saleData['outside_patient_name'];
            $mainSaleData['outside_patient_phone'] = $saleData['outside_patient_phone'] ?? null;
            $mainSaleData['outside_patient_address'] = $saleData['outside_patient_address'] ?? null;
        }

        $totalAmount = 0;
        $netAmount = 0;

        // Start database transaction
        $this->salesModel->db->transStart();

        try {
            // 1. Insert into pharmacy_sales
            $saleId = $this->salesModel->insert($mainSaleData);
            if (!$saleId) {
                throw new \Exception('Failed to create sale record.');
            }

            // 2. Process each sale item
            foreach ($saleItems as $item) {
                $batch = $this->batchModel->find($item['batch_id']);
                if (empty($batch) || $batch['current_stock'] < $item['quantity']) {
                    throw new \Exception('Insufficient stock for medicine ' . $item['medicine_id'] . ' in batch ' . $item['batch_id']);
                }

                $itemSubTotal = ($item['quantity'] * $item['unit_selling_price']) - ($item['discount_per_item'] ?? 0);
                $totalAmount += ($item['quantity'] * $item['unit_selling_price']); // Sum of unit_selling_price * quantity
                $netAmount += $itemSubTotal; // Sum of final item price after discount

                $saleItemData = [
                    'sale_id'            => $saleId,
                    'medicine_id'        => $item['medicine_id'],
                    'batch_id'           => $item['batch_id'],
                    'quantity'           => $item['quantity'],
                    'unit_selling_price' => $item['unit_selling_price'],
                    'discount_per_item'  => $item['discount_per_item'] ?? 0,
                    'sub_total'          => $itemSubTotal,
                ];

                if (!$this->saleItemModel->insert($saleItemData)) {
                    throw new \Exception('Failed to add sale item.');
                }

                // Deduct stock from the batch
                $this->batchModel->update($item['batch_id'], ['current_stock' => new RawSql('current_stock - ' . $item['quantity'])]);
            }

            // Update total_amount and net_amount in the sales record
            $this->salesModel->update($saleId, [
                'total_amount' => $totalAmount,
                'net_amount'   => $netAmount - ($mainSaleData['discount_amount'] ?? 0) // Apply total discount to net amount
            ]);

            $this->salesModel->db->transComplete();

            if ($this->salesModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/sales/invoice/' . $saleId))->with('success', 'Sale processed successfully!');

        } catch (\Exception $e) {
            $this->salesModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Sale failed: ' . $e->getMessage());
        }
    }

    /**
     * Displays a specific sales invoice.
     */
    public function invoice($saleId = null)
    {
        $sale = $this->salesModel->find($saleId);
        if (empty($sale)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Sale invoice not found: ' . $saleId);
        }

        // Fetch sales person (user) details
        $userModel = new \App\Models\UserModel(); // Assuming your main UserModel is in App\Models
        $salesPerson = $userModel->find($sale['sales_person_id']);

        // Fetch patient/doctor details based on prescription type
        $patientDetails = null;
        $doctorDetails = null;
        if ($sale['prescription_type'] === 'in_hospital') {
            if ($sale['patient_id']) {
                $patientDetails = $this->patientModel->find($sale['patient_id']);
            }
            if ($sale['doctor_id']) {
                $doctorDetails = $this->doctorModel->find($sale['doctor_id']);
            }
        }

        // Fetch sale items with medicine details
        $saleItems = $this->saleItemModel
                            ->select('pharmacy_sale_items.*, pm.generic_name, pm.brand_name, pm.strength, pm.unit_of_measure, pb.batch_number')
                            ->join('pharmacy_medicines pm', 'pm.id = pharmacy_sale_items.medicine_id')
                            ->join('pharmacy_batches pb', 'pb.id = pharmacy_sale_items.batch_id')
                            ->where('sale_id', $saleId)
                            ->findAll();

        $data = [
            'title'        => 'Sales Invoice',
            'sale'         => $sale,
            'salesPerson'  => $salesPerson,
            'patientDetails' => $patientDetails,
            'doctorDetails'  => $doctorDetails,
            'saleItems'    => $saleItems
        ];

        return view('pharmacy/sales/invoice', $data);
    }

    /**
     * Lists all sales transactions (for reporting/history).
     */
    public function listSales()
    {
        $sales = $this->salesModel->findAll(); 

        $data = [
            'title' => 'Sales History',
            'sales' => $sales
        ];
        return view('pharmacy/sales/list', $data);
    }
}