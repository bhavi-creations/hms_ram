<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql;
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyManufacturerModel;
use App\Models\Pharmacy\PharmacyCategoryModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyStockAdjustmentModel;
use App\Models\Pharmacy\PharmacySupplierModel;
use App\Models\Pharmacy\PharmacyDosageFormModel;
use App\Models\Pharmacy\PharmacyUnitOfMeasureModel;

class Medicines extends BaseController
{
    protected $medicineModel;
    protected $manufacturerModel;
    protected $categoryModel;
    protected $batchModel;
    protected $stockAdjustmentModel;
    protected $supplierModel;
    protected $dosageFormModel;
    protected $unitOfMeasureModel;

    public function __construct()
    {
        $this->medicineModel = new PharmacyMedicineModel();
        $this->manufacturerModel = new PharmacyManufacturerModel();
        $this->categoryModel = new PharmacyCategoryModel();
        $this->batchModel = new PharmacyBatchModel();
        $this->stockAdjustmentModel = new PharmacyStockAdjustmentModel();
        $this->supplierModel = new PharmacySupplierModel();
        $this->dosageFormModel = new PharmacyDosageFormModel();
        $this->unitOfMeasureModel = new PharmacyUnitOfMeasureModel();
    }

    /**
     * Lists all medicines with their total stock.
     */
    public function index()
    {
        $medicines = $this->medicineModel
            ->select('pharmacy_medicines.*, pm.name as manufacturer_name, pc.name as category_name, SUM(pb.current_stock) as total_stock, p_df.name as dosage_form_name, p_uom.name as unit_of_measure_name')
            ->join('pharmacy_manufacturers pm', 'pm.id = pharmacy_medicines.manufacturer_id')
            ->join('pharmacy_categories pc', 'pc.id = pharmacy_medicines.category_id')
            ->join('pharmacy_dosage_forms p_df', 'p_df.id = pharmacy_medicines.dosage_form_id')
            ->join('pharmacy_units_of_measure p_uom', 'p_uom.id = pharmacy_medicines.unit_of_measure_id')
            ->join('pharmacy_batches pb', 'pb.medicine_id = pharmacy_medicines.id', 'left')
            ->groupBy('pharmacy_medicines.id')
            ->findAll();

        foreach ($medicines as &$medicine) {
            $medicine['total_stock'] = (int) ($medicine['total_stock'] ?? 0);
        }
        unset($medicine);

        $data = [
            'title' => 'Manage Medicines',
            'medicines' => $medicines
        ];
        return view('pharmacy/medicines/index', $data);
    }

    /**
     * Shows the form to add a new medicine.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Medicine',
            'manufacturers' => $this->manufacturerModel->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'dosageForms' => $this->dosageFormModel->findAll(),
            'units' => $this->unitOfMeasureModel->findAll(),
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/create', $data);
    }

    /**
     * Handles the submission of a new medicine form.
     */
      public function store()
    {
        // Define validation rules, including the new gst_rate and hsn_code fields.
        $rules = [
            'generic_name' => 'required|min_length[3]|max_length[255]',
            'brand_name' => 'permit_empty|max_length[255]',
            'dosage_form_id' => 'required|is_natural_no_zero',
            'strength' => 'required|max_length[100]',
            'unit_of_measure_id' => 'required|is_natural_no_zero',
            'manufacturer_id' => 'required|is_natural_no_zero',
            'category_id' => 'required|is_natural_no_zero',
            'reorder_level' => 'required|integer|greater_than_equal_to[0]',
            'description' => 'permit_empty',
            'is_active' => 'permit_empty|integer',
            'gst_rate' => 'required|decimal|greater_than_equal_to[0]', // Added validation for GST rate.
            'hsn_code' => 'required|string|max_length[255]', // Added validation for HSN code.
        ];

        // Run the validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get the user ID from the session. If not found, redirect to an error page or login.
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return redirect()->to(site_url('login'))->with('error', 'You must be logged in to add a medicine.');
        }

        // Get all post data and prepare for insertion
        // We explicitly cast integer and float values to ensure they are handled correctly.
        $data = [
            'generic_name' => $this->request->getPost('generic_name'),
            'brand_name' => $this->request->getPost('brand_name'),
            'dosage_form_id' => (int) $this->request->getPost('dosage_form_id'),
            'strength' => $this->request->getPost('strength'),
            'unit_of_measure_id' => (int) $this->request->getPost('unit_of_measure_id'),
            'manufacturer_id' => (int) $this->request->getPost('manufacturer_id'),
            'category_id' => (int) $this->request->getPost('category_id'),
            'reorder_level' => (int) $this->request->getPost('reorder_level'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'gst_rate' => (float) $this->request->getPost('gst_rate'), // Added to data array.
            'hsn_code' => $this->request->getPost('hsn_code'),       // Added to data array.
            'created_by_user_id' => $userId,
        ];

        // Insert the new medicine record into the database
        if ($this->medicineModel->insert($data)) {
            return redirect()->to(site_url('pharmacy/medicines'))->with('success', 'Medicine added successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add medicine.');
        }
    }



    public function edit($id)
    {
        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            return redirect()->to(site_url('pharmacy/medicines'))->with('error', 'Medicine not found.');
        }

        $data = [
            'title' => 'Edit Medicine',
            'medicine' => $medicine,
            'manufacturers' => $this->manufacturerModel->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'dosageForms' => $this->dosageFormModel->findAll(),
            'units' => $this->unitOfMeasureModel->findAll(),
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/edit', $data);
    }


       public function update($id = null)
    {
        // Get the ID from the URL segment.
        $medicine = $this->medicineModel->find($id);
        if (!$medicine) {
            return redirect()->back()->with('error', 'Medicine not found.');
        }

        // Define validation rules for the update process, including new fields.
        $rules = [
            'generic_name' => 'required|min_length[3]|max_length[255]',
            'brand_name' => 'permit_empty|min_length[3]|max_length[255]',
            'dosage_form_id' => 'required|integer',
            'strength' => 'required|max_length[100]',
            'unit_of_measure_id' => 'required|integer',
            'manufacturer_id' => 'required|integer',
            'category_id' => 'required|integer',
            'reorder_level' => 'required|integer|greater_than_equal_to[0]',
            'is_active' => 'permit_empty|integer',
            'description' => 'permit_empty|max_length[1000]',
            'gst_rate' => 'required|decimal|greater_than_equal_to[0]', // Added validation.
            'hsn_code' => 'required|string|max_length[255]', // Added validation.
        ];

        // Validate the incoming data from the form.
        if (!$this->validate($rules)) {
            // Redirect back with the input data and validation errors.
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare the data array for updating the record.
        $data = [
            'generic_name' => $this->request->getPost('generic_name'),
            'brand_name' => $this->request->getPost('brand_name'),
            'dosage_form_id' => (int) $this->request->getPost('dosage_form_id'),
            'strength' => $this->request->getPost('strength'),
            'unit_of_measure_id' => (int) $this->request->getPost('unit_of_measure_id'),
            'manufacturer_id' => (int) $this->request->getPost('manufacturer_id'),
            'category_id' => (int) $this->request->getPost('category_id'),
            'reorder_level' => (int) $this->request->getPost('reorder_level'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'description' => $this->request->getPost('description'),
            'gst_rate' => (float) $this->request->getPost('gst_rate'), // Added to data array.
            'hsn_code' => $this->request->getPost('hsn_code'),       // Added to data array.
            'updated_by_user_id' => session()->get('user_id'), // Set the user who updated the record.
        ];

        // Perform the update using the model.
        if ($this->medicineModel->update($id, $data)) {
            // Redirect on success with a flash message.
            return redirect()->to(site_url('pharmacy/medicines'))->with('success', 'Medicine updated successfully!');
        } else {
            // Redirect on failure with a flash message.
            return redirect()->back()->withInput()->with('error', 'Failed to update medicine.');
        }
    }
  


    

    public function delete($id = null)
    {
        $medicine = $this->medicineModel->find($id);

        if (empty($medicine)) {
            return redirect()->back()->with('error', 'Medicine not found.');
        }

        $this->medicineModel->delete($id);

        return redirect()->to(site_url('pharmacy/medicines'))->with('success', 'Medicine deleted successfully.');
    }





    public function batches($medicineId = null)
    {
        $medicine = $this->medicineModel->find($medicineId);

        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Medicine not found for batches: ' . $medicineId);
        }

        // Fetch batches and join with suppliers to get the supplier name
        $batches = $this->batchModel
            ->select('pharmacy_batches.*, ps.name as supplier_name')
            ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_batches.supplier_id', 'left')
            ->where('medicine_id', $medicineId)
            ->orderBy('expiry_date', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Manage Batches',
            'medicine' => $medicine,
            'batches' => $batches
        ];
        return view('pharmacy/medicines/batches', $data);
    }



    public function addBatch($medicineId = null)
    {
        $medicine = $this->medicineModel->find($medicineId);

        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Medicine not found for adding batch: ' . $medicineId);
        }

        // Use the protected model property instead of creating a new instance
        $suppliers = $this->supplierModel->findAll();

        $data = [
            'title' => 'Add New Batch',
            'medicine' => $medicine,
            'suppliers' => $suppliers,
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/add_batch', $data);
    }

    public function storeBatch()
    {
        // Define validation rules for the form submission
        $rules = [
            'medicine_id'        => 'required|integer|is_not_unique[pharmacy_medicines.id]',
            'batch_number'       => 'required|max_length[100]',
            'supplier_id'        => 'required|integer|is_not_unique[pharmacy_suppliers.id]',
            'initial_quantity'   => 'required|integer|greater_than_equal_to[1]',
            'purchase_price'     => 'required|numeric|greater_than_equal_to[0]',
            'selling_price'      => 'required|numeric|greater_than_equal_to[0]',
            'manufacturing_date' => 'required|valid_date',
            'expiry_date'        => 'required|valid_date',
            'packaging_unit_name.*' => 'required|max_length[50]',
            'packaging_unit_quantity.*' => 'required|integer|greater_than_equal_to[1]',
        ];

        // Manual check for selling price vs purchase price and expiry date vs manufacturing date
        $postData = $this->request->getPost();
        $validationErrors = [];

        if ($postData['selling_price'] < $postData['purchase_price']) {
            $validationErrors['selling_price'] = 'Selling price must be greater than or equal to the purchase price.';
        }

        if (strtotime($postData['expiry_date']) < strtotime($postData['manufacturing_date'])) {
            $validationErrors['expiry_date'] = 'Expiry date must be after the manufacture date.';
        }

        if (empty($postData['packaging_unit_name'])) {
            $validationErrors['packaging_levels'] = 'At least one packaging level is required.';
        }

        // Validate the incoming data from the form
        if (!$this->validate($rules) || !empty($validationErrors)) {
            // Merge validation errors from both the validator and manual checks
            $validator = service('validation');
            $errors = array_merge($validator->getErrors(), $validationErrors);
            return redirect()->back()->withInput()->with('validation', $validator)->with('errors', $errors);
        }

        $medicineId = $postData['medicine_id'];
        $initialQuantity = (int)$postData['initial_quantity'];

        // Build the structured array for packaging levels
        $packagingNames = $postData['packaging_unit_name'] ?? [];
        $packagingQuantities = $postData['packaging_unit_quantity'] ?? [];
        $packagingLevels = [];
        for ($i = 0; $i < count($packagingNames); $i++) {
            $packagingLevels[] = [
                'unit' => $packagingNames[$i],
                'quantity' => (int) $packagingQuantities[$i],
            ];
        }

        // Prepare the data array for the database insertion
        $data = [
            'medicine_id'        => $medicineId,
            'batch_number'       => $postData['batch_number'],
            'supplier_id'        => $postData['supplier_id'],
            'initial_quantity'   => $initialQuantity,
            'current_stock'      => $initialQuantity, // Initial stock is the same as initial quantity
            'purchase_price'     => $postData['purchase_price'],
            'selling_price'      => $postData['selling_price'],
            'manufacturing_date' => date('Y-m-d', strtotime($postData['manufacturing_date'])),
            'expiry_date'        => date('Y-m-d', strtotime($postData['expiry_date'])),
            'packaging_levels'   => json_encode($packagingLevels), // Store the packaging levels as a JSON string
        ];

        $this->batchModel->db->transStart();
        try {
            if (!$this->batchModel->save($data)) {
                throw new \Exception('Failed to save new batch.');
            }

            $this->batchModel->db->transComplete();

            if ($this->batchModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/medicines/batches/' . $medicineId))
                ->with('success', 'New batch added successfully!');
        } catch (\Exception $e) {
            $this->batchModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error adding batch: ' . $e->getMessage());
        }
    }


    public function editBatch($batchId = null)
    {
        $batch = $this->batchModel->find($batchId);

        if (empty($batch)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Batch not found: ' . $batchId);
        }

        $medicine = $this->medicineModel->find($batch['medicine_id']);
        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Associated medicine not found for batch: ' . $batch['medicine_id']);
        }

        // Use the protected model property instead of creating a new instance
        $suppliers = $this->supplierModel->findAll();

        $data = [
            'title' => 'Edit Batch',
            'batch' => $batch,
            'medicine' => $medicine,
            'suppliers' => $suppliers,
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/edit_batch', $data);
    }






    public function storeAdjustment()
    {
        $rules = [
            'medicine_id' => 'required|integer|is_not_unique[pharmacy_medicines.id]',
            'batch_id' => 'required|integer|is_not_unique[pharmacy_batches.id]',
            'adjustment_type' => 'required|in_list[increase,decrease]',
            'quantity' => 'required|integer|greater_than[0]',
            'reason' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $batchId = $this->request->getPost('batch_id');
        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = (int) $this->request->getPost('quantity');
        $reason = $this->request->getPost('reason');

        $batch = $this->batchModel->find($batchId);
        if (empty($batch)) {
            return redirect()->back()->withInput()->with('error', 'Selected batch not found.');
        }

        $newStock = $batch['current_stock'];
        if ($adjustmentType === 'increase') {
            $newStock += $quantity;
        } else {
            $newStock -= $quantity;
            // Check for insufficient stock on decrease
            if ($newStock < 0) {
                session()->setFlashdata('error', 'Insufficient stock to perform this adjustment.');
                return redirect()->back()->withInput();
            }
        }

        $this->batchModel->db->transStart();
        try {
            // 1. Update batch current stock
            if (!$this->batchModel->update($batchId, ['current_stock' => $newStock])) {
                throw new \Exception('Failed to update batch stock.');
            }

            // 2. Record the adjustment in pharmacy_stock_adjustments table
            $adjustmentData = [
                'medicine_id' => $batch['medicine_id'],
                'batch_id' => $batchId,
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'current_stock_before' => $batch['current_stock'],
                'current_stock_after' => $newStock,
                'reason' => $reason,
                'adjusted_by_user_id' => session()->get('id') ?? null,
            ];

            if (!$this->stockAdjustmentModel->save($adjustmentData)) {
                throw new \Exception('Failed to record stock adjustment.');
            }

            $this->batchModel->db->transComplete();

            if ($this->batchModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/medicines'))
                ->with('success', 'Stock adjusted successfully for batch ' . $batch['batch_number'] . '.');
        } catch (\Exception $e) {
            $this->batchModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error adjusting stock: ' . $e->getMessage());
        }
    }

    public function updateBatch($batchId = null)
    {
        // Define validation rules for the form submission.
        $rules = [
            'batch_number' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Batch number is required.',
                ],
            ],
            'supplier_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => [
                    'required' => 'Supplier is required.',
                ],
            ],
            'purchase_price' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Purchase price is required.',
                    'numeric' => 'Purchase price must be a number.',
                ],
            ],
            'selling_price' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Selling price is required.',
                    'numeric' => 'Selling price must be a number.',
                ],
            ],
            'manufacturing_date' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Manufacture date is required.',
                    'valid_date' => 'Please provide a valid manufacture date.',
                ],
            ],
            'expiry_date' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Expiry date is required.',
                    'valid_date' => 'Please provide a valid expiry date.',
                ],
            ],
            'initial_quantity' => [
                'rules' => 'required|integer|greater_than_equal_to[1]',
                'errors' => [
                    'required' => 'Initial quantity is required.',
                    'integer' => 'Initial quantity must be a whole number.',
                    'greater_than_equal_to' => 'Initial quantity must be at least 1.',
                ],
            ],
            'packaging_unit_name.*' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'All packaging unit names are required.',
                ],
            ],
            'packaging_unit_quantity.*' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'All packaging unit quantities are required.',
                    'greater_than' => 'All packaging quantities must be greater than 0.',
                ],
            ],
        ];

        // --- NEW: Custom check for empty packaging levels ---
        // This is a manual check to provide a more specific error message.
        $packagingNames = $this->request->getPost('packaging_unit_name');
        if (empty($packagingNames) || !is_array($packagingNames) || count($packagingNames) === 0) {
            session()->setFlashdata('error', 'Please enter at least one packaging level to update the batch.');
            return redirect()->back()->withInput();
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $postData = $this->request->getPost();

        // --- MANUAL VALIDATION CHECKS ---
        if ($postData['selling_price'] < $postData['purchase_price']) {
            session()->setFlashdata('error', 'Selling price must be greater than or equal to the purchase price.');
            return redirect()->back()->withInput();
        }

        if ($postData['expiry_date'] < $postData['manufacturing_date']) {
            session()->setFlashdata('error', 'Expiry date must be after the manufacture date.');
            return redirect()->back()->withInput();
        }
        // --- END MANUAL VALIDATION CHECKS ---

        // NEW LOGIC: Fetch the existing batch data before updating
        $existingBatch = $this->batchModel->find($batchId);
        if (!$existingBatch) {
            session()->setFlashdata('error', 'Batch not found.');
            return redirect()->back();
        }

        $oldInitialQuantity = $existingBatch['initial_quantity'];
        $oldCurrentStock = $existingBatch['current_stock'];
        $newInitialQuantity = (int)$postData['initial_quantity'];

        // Calculate the change in quantity and update the current stock
        $quantityDifference = $newInitialQuantity - $oldInitialQuantity;
        $newCurrentStock = $oldCurrentStock + $quantityDifference;

        // Check for negative stock and handle it
        if ($newCurrentStock < 0) {
            session()->setFlashdata('error', 'The updated quantity would result in a negative stock count. Please adjust your initial quantity.');
            return redirect()->back()->withInput();
        }

        // Build the structured array for packaging levels
        $packagingQuantities = $postData['packaging_unit_quantity'] ?? [];
        $packagingLevels = [];
        for ($i = 0; $i < count($packagingNames); $i++) {
            $packagingLevels[] = [
                'unit' => $packagingNames[$i],
                'quantity' => (int) $packagingQuantities[$i],
            ];
        }

        // Prepare the data array for the database update
        $data = [
            'batch_number' => $postData['batch_number'],
            'supplier_id' => $postData['supplier_id'],
            'purchase_price' => $postData['purchase_price'],
            'selling_price' => $postData['selling_price'],
            'manufacturing_date' => $postData['manufacturing_date'],
            'expiry_date' => $postData['expiry_date'],
            'initial_quantity' => $newInitialQuantity,
            'current_stock' => $newCurrentStock,
            'packaging_levels' => json_encode($packagingLevels),
        ];

        // Update the batch record in the database
        $success = $this->batchModel->update($batchId, $data);

        if ($success) {
            session()->setFlashdata('success', 'Batch updated successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to update batch. Please try again.');
        }

        // Redirect back to the batches list for this medicine
        return redirect()->to(site_url('pharmacy/medicines/batches/' . esc($postData['medicine_id'])));
    }

    /**
     * Deletes a batch via an AJAX request.
     * @param int|null $id The ID of the batch to delete.
     * @return mixed JSON response indicating success or failure.
     */
    public function deleteBatch($id = null)
    {
        // Use the model property instead of creating a new instance
        $batch = $this->batchModel->find($id);

        if (!$batch) {
            $response = [
                'success' => false,
                'message' => 'Batch not found.'
            ];
            // Return a 404 Not Found status code
            return $this->response->setJSON($response)->setStatusCode(404);
        }

        try {
            // Attempt to delete the batch from the database
            if ($this->batchModel->delete($id)) {
                $response = [
                    'success' => true,
                    'message' => 'Batch successfully deleted.'
                ];
                return $this->response->setJSON($response);
            } else {
                // This case handles a silent failure from the model's delete() method
                $response = [
                    'success' => false,
                    'message' => 'Failed to delete the batch.'
                ];
                return $this->response->setJSON($response)->setStatusCode(500);
            }
        } catch (\Exception $e) {
            // Catch any exceptions (e.g., database connection issues)
            $response = [
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ];
            return $this->response->setJSON($response)->setStatusCode(500);
        }
    }


    public function adjustStock($batchId = null)
    {
        // Handle POST request for form submission
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'batch_id' => 'required|integer',
                'current_stock' => 'required|numeric',
                'notes' => 'permit_empty|string'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            $data = $this->validator->getValidated();
            $batch = $this->batchModel->find($data['batch_id']);

            if (!$batch) {
                session()->setFlashdata('error', 'Batch not found.');
                return redirect()->to(site_url('pharmacy/medicines'));
            }

            $oldStock = (int)$batch['current_stock'];
            $newStock = (int)$data['current_stock'];
            $adjustmentQuantity = $newStock - $oldStock;

            // Update the batch stock
            if (!$this->batchModel->update($batch['id'], ['current_stock' => $newStock])) {
                session()->setFlashdata('error', 'Failed to update batch stock.');
                return redirect()->back()->withInput();
            }

            // Record the stock adjustment in the log
            $adjustmentData = [
                'batch_id' => $batch['id'],
                'medicine_id' => $batch['medicine_id'],
                'quantity' => abs($adjustmentQuantity),
                'adjustment_type' => ($adjustmentQuantity >= 0) ? 'addition' : 'subtraction',
                'notes' => $data['notes'] ?? 'Stock adjusted via edit page.',
                'created_by' => 'User',
            ];
            $this->stockAdjustmentModel->insert($adjustmentData);

            session()->setFlashdata('success', 'Stock successfully adjusted!');
            return redirect()->to(site_url('pharmacy/medicines'));
        }

        // Handle GET request to load the page
        $data = [
            'title' => 'Adjust Medicine Stock',
            'batch' => null,
            'medicine' => null,
            'validation' => service('validation')
        ];

        if ($batchId) {
            $batch = $this->batchModel->find($batchId);
            if ($batch) {
                $medicine = $this->medicineModel->find($batch['medicine_id']);
                if ($medicine) {
                    $data['batch'] = $batch;
                    $data['medicine'] = $medicine;
                } else {
                    session()->setFlashdata('error', 'Associated medicine not found.');
                    return redirect()->to(site_url('pharmacy/medicines'));
                }
            } else {
                session()->setFlashdata('error', 'Batch not found.');
                return redirect()->to(site_url('pharmacy/medicines'));
            }
        }

        return view('pharmacy/medicines/adjust_stock', $data);
    }

 


       public function getBatchesByMedicine($medicineId = null)
    {
        if ($this->request->isAJAX()) {
            // FIX: Added 'selling_price' to the select statement so it's
            // available in the JSON response for the JavaScript to use.
            $batches = $this->batchModel
                ->select('id, batch_number, current_stock, expiry_date, selling_price')
                ->where('medicine_id', $medicineId)
                ->where('current_stock >', 0)
                ->orderBy('expiry_date', 'ASC')
                ->findAll();
            return $this->response->setJSON(['batches' => $batches]);
        }

        // Return an error for non-AJAX requests
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }
}
