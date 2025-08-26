<?php

namespace App\Controllers\Pharmacy;

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
        $this->patientModel  = new PatientModel();  
        $this->doctorModel   = new DoctorModel();   
    }

    /**
     * Displays the sales Point of Sale (POS) panel.
     */
    // ...
    public function index()
    {
        $medicinesWithPrice = $this->medicineModel
            ->select('pharmacy_medicines.*, pharmacy_batches.selling_price')
            ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
            ->where('pharmacy_batches.current_stock >', 0)
            ->where('pharmacy_batches.expiry_date >', date('Y-m-d'))
            ->groupBy('pharmacy_medicines.id')
            ->findAll();

        $data = [
            'title'      => 'Pharmacy Sales POS',
            'medicines'  => $medicinesWithPrice,
            'validation' => service('validation')
        ];
        return view('pharmacy/sales/pos', $data);
    }


    /**
     * Handles the submission of a new sales transaction.
     */
      public function processSale()
    {
        // 1. Define and perform validation based on user input
        $rules = [
            'prescription_type' => 'required|in_list[in_hospital,outside_sale]',
            'items.*.medicine_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_selling_price' => 'required|decimal|greater_than[0]',
            'payment_method' => 'required|max_length[50]',
        ];

        // Add conditional validation rules for patient/doctor details
        $prescriptionType = $this->request->getPost('prescription_type');
        if ($prescriptionType === 'in_hospital') {
            $rules['patient_id_code'] = 'required|max_length[50]';
            // doctor_id is now optional for in-hospital sales
            $rules['doctor_id'] = 'permit_empty|integer'; 
        } else { // 'outside_sale'
            $rules['outside_patient_name'] = 'required|min_length[3]|max_length[255]';
            // Phone and address are optional for outside sales
            $rules['outside_patient_phone'] = 'permit_empty|max_length[20]'; 
            $rules['outside_patient_address'] = 'permit_empty|max_length[255]';
        }

        if (!$this->validate($rules)) {
            // If validation fails, redirect back with the errors and old input
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $saleData = $this->request->getPost();
        $saleItems = $this->request->getPost('items');

        // Prepare the main sale data array
        $mainSaleData = [
            // 'invoice_number' => 'INV-' . time() . rand(100, 999), // Uncomment if you want to generate an invoice number
            'sale_date' => date('Y-m-d H:i:s'),
            'sales_person_id' => session()->get('user_id'), // Assuming user_id is stored in the session
            'prescription_type' => $saleData['prescription_type'],
            'payment_method' => $saleData['payment_method'],
            'notes' => $saleData['notes'] ?? null,
            'discount_amount' => $saleData['total_discount'] ?? 0,
            'total_amount' => 0, // Will be calculated dynamically
            'net_amount' => 0, // Will be calculated dynamically
        ];

        // Handle patient details based on prescription type
        if ($prescriptionType === 'in_hospital') {
            // Fetch patient ID from the unique patient_id_code
            $patient = $this->patientModel->where('patient_id_code', $saleData['patient_id_code'])->first();
            if (empty($patient)) {
                return redirect()->back()->withInput()->with('error', 'Invalid In-Hospital Patient ID Code.');
            }
            $mainSaleData['patient_id'] = $patient['id'];
            $mainSaleData['doctor_id'] = $saleData['doctor_id'] ?? null;
        } else { // outside_sale
            $mainSaleData['outside_patient_name'] = $saleData['outside_patient_name'];
            $mainSaleData['outside_patient_phone'] = $saleData['outside_patient_phone'] ?? null;
            $mainSaleData['outside_patient_address'] = $saleData['outside_patient_address'] ?? null;
        }

        $totalAmount = 0;
        $netAmount = 0;

        // Start a database transaction to ensure all operations succeed or fail together
        $this->salesModel->db->transStart();

        try {
            // 2. Insert the main sale record and get the new sale ID
            $saleId = $this->salesModel->insert($mainSaleData);
            if (!$saleId) {
                // If the insert fails, throw an exception to trigger the rollback
                throw new \Exception('Failed to create sale record.');
            }

            // 3. Loop through each sale item, add to the sale, and deduct stock
            foreach ($saleItems as $item) {
                $batch = $this->batchModel->find($item['batch_id']);
                
                // Check for insufficient stock before proceeding with the item
                if (empty($batch) || $batch['current_stock'] < $item['quantity']) {
                    throw new \Exception('Insufficient stock for medicine in batch ' . $item['batch_id'] . '.');
                }

                $itemSubTotal = ($item['quantity'] * $item['unit_selling_price']) - ($item['discount_per_item'] ?? 0);
                
                // Accumulate totals for the main sale record
                $totalAmount += ($item['quantity'] * $item['unit_selling_price']);
                $netAmount += $itemSubTotal;

                $saleItemData = [
                    'sale_id' => $saleId,
                    'medicine_id' => $item['medicine_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_selling_price' => $item['unit_selling_price'],
                    'discount_per_item' => $item['discount_per_item'] ?? 0,
                    'sub_total' => $itemSubTotal,
                ];

                // Insert the sale item record
                if (!$this->saleItemModel->insert($saleItemData)) {
                    throw new \Exception('Failed to add sale item.');
                }

                // Deduct stock from the batch. Using RawSql is a good practice for atomic updates.
                $this->batchModel->update($item['batch_id'], ['current_stock' => new RawSql('current_stock - ' . $item['quantity'])]);
            }

            // 4. Update the main sales record with the final calculated amounts
            $finalNetAmount = $netAmount - ($mainSaleData['discount_amount'] ?? 0);
            $this->salesModel->update($saleId, [
                'total_amount' => $totalAmount,
                'net_amount' => $finalNetAmount
            ]);

            // 5. Complete the transaction. This commits all changes if no errors occurred.
            $this->salesModel->db->transComplete();

            if ($this->salesModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed. Database status check returned false.');
            }

            // Redirect on success with a success message
            return redirect()->to(site_url('pharmacy/sales/invoice/' . $saleId))->with('success', 'Sale processed successfully!');
        } catch (\Exception $e) {
            // On any exception, roll back the transaction to undo all changes
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
