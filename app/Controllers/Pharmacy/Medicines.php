<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql; // Import RawSql if you plan to use it for summing, or just use GROUP BY and SUM directly in builder

// Import all necessary Pharmacy Models
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyManufacturerModel;
use App\Models\Pharmacy\PharmacyCategoryModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyStockAdjustmentModel; // For stock adjustments
use App\Models\Pharmacy\PharmacySupplierModel;

class Medicines extends BaseController
{
    protected $medicineModel;
    protected $manufacturerModel;
    protected $categoryModel;
    protected $batchModel;
    protected $stockAdjustmentModel;
    protected $supplierModel;


    public function __construct()
    {
        // Ensure parent constructor runs for session, etc.
        // parent::__construct();

        $this->medicineModel        = new PharmacyMedicineModel();
        $this->manufacturerModel    = new PharmacyManufacturerModel();
        $this->categoryModel        = new PharmacyCategoryModel();
        $this->batchModel           = new PharmacyBatchModel();
        $this->stockAdjustmentModel = new PharmacyStockAdjustmentModel();
        $this->supplierModel        = new PharmacySupplierModel();
    }

    /**
     * Lists all medicines with their total stock.
     */
    public function index()
    {
        $medicines = $this->medicineModel
            ->select('pharmacy_medicines.*, pm.name as manufacturer_name, pc.name as category_name, SUM(pb.current_stock) as total_stock')
            ->join('pharmacy_manufacturers pm', 'pm.id = pharmacy_medicines.manufacturer_id')
            ->join('pharmacy_categories pc', 'pc.id = pharmacy_medicines.category_id')
            ->join('pharmacy_batches pb', 'pb.medicine_id = pharmacy_medicines.id', 'left') // LEFT JOIN to include medicines with no batches yet
            ->groupBy('pharmacy_medicines.id') // Group by medicine to sum batches
            ->findAll();

        // Ensure total_stock is an integer, default to 0 if null (no batches)
        foreach ($medicines as &$medicine) {
            $medicine['total_stock'] = (int) ($medicine['total_stock'] ?? 0);
        }
        unset($medicine); // Unset the reference

        $data = [
            'title'     => 'Manage Medicines',
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
            'title'         => 'Add New Medicine',
            'manufacturers' => $this->manufacturerModel->findAll(),
            'categories'    => $this->categoryModel->findAll(),
            'validation'    => service('validation')
        ];
        return view('pharmacy/medicines/create', $data);
    }
    // ... rest of your controller methods


    /**
     * Handles the submission of a new medicine form.
     */
    public function store()
    {
        $rules = [
            'generic_name'    => 'required|min_length[3]|max_length[255]',
            'dosage_form'     => 'required|max_length[100]',
            'strength'        => 'required|max_length[100]',
            'unit_of_measure' => 'required|max_length[50]',
            'manufacturer_id' => 'required|integer',
            'category_id'     => 'required|integer',
            'reorder_level'   => 'required|integer|greater_than_equal_to[0]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->medicineModel->save([
            'generic_name'       => $this->request->getPost('generic_name'),
            'brand_name'         => $this->request->getPost('brand_name'),
            'dosage_form'        => $this->request->getPost('dosage_form'),
            'strength'           => $this->request->getPost('strength'),
            'unit_of_measure'    => $this->request->getPost('unit_of_measure'),
            'manufacturer_id'    => $this->request->getPost('manufacturer_id'),
            'category_id'        => $this->request->getPost('category_id'),
            'reorder_level'      => $this->request->getPost('reorder_level'),
            'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
            'description'        => $this->request->getPost('description'),
            'created_by_user_id' => session()->get('user_id') // Assuming user_id is in session
        ]);

        return redirect()->to(site_url('pharmacy/medicines'))->with('success', 'Medicine added successfully.');
    }

    /**
     * Displays a form to edit an existing medicine.
     */
    public function edit($id = null)
    {
        $medicine = $this->medicineModel->find($id);

        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the medicine item: ' . $id);
        }

        $data = [
            'title'         => 'Edit Medicine',
            'medicine'      => $medicine,
            'manufacturers' => $this->manufacturerModel->findAll(),
            'categories'    => $this->categoryModel->findAll(),
            'validation'    => service('validation')
        ];
        return view('pharmacy/medicines/edit', $data);
    }

    /**
     * Handles the update of an existing medicine.
     */
    public function update($id = null)
    {
        $rules = [
            'generic_name'    => 'required|min_length[3]|max_length[255]',
            // ... (rest of your validation rules for update)
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->medicineModel->update($id, [
            'generic_name'       => $this->request->getPost('generic_name'),
            'brand_name'         => $this->request->getPost('brand_name'),
            'dosage_form'        => $this->request->getPost('dosage_form'),
            'strength'           => $this->request->getPost('strength'),
            'unit_of_measure'    => $this->request->getPost('unit_of_measure'),
            'manufacturer_id'    => $this->request->getPost('manufacturer_id'),
            'category_id'        => $this->request->getPost('category_id'),
            'reorder_level'      => $this->request->getPost('reorder_level'),
            'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
            'description'        => $this->request->getPost('description'),
            // created_by_user_id is set only on creation, not update.
        ]);

        return redirect()->to(site_url('pharmacy/medicines'))->with('success', 'Medicine updated successfully.');
    }

    /**
     * Displays batches for a specific medicine.
     */
    public function batches($medicineId = null)
    {
        $medicine = $this->medicineModel->find($medicineId);

        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Medicine not found for batches: ' . $medicineId);
        }

        // Fetch batches for this medicine, joining with supplier name
        $batches = $this->batchModel
            ->select('pharmacy_batches.*, ps.name as supplier_name') // Added select for supplier_name
            ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_batches.supplier_id', 'left') // Added join
            ->where('medicine_id', $medicineId)
            ->orderBy('expiry_date', 'ASC') // Added ordering by expiry date
            ->findAll();

        $data = [
            'title'    => 'Manage Batches', // You can also make this 'Batches for ' . $medicine['brand_name']
            'medicine' => $medicine,
            'batches'  => $batches
        ];
        return view('pharmacy/medicines/batches', $data);
    }

    public function deleteBatch($batchId = null)
    {
        // Ensure this is a POST request to prevent accidental deletion via GET
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back()->with('error', 'Invalid request method.');
        }

        $batch = $this->batchModel->find($batchId);
        if (empty($batch)) {
            return redirect()->back()->with('error', 'Batch not found.');
        }

        // You might want to add a check if the batch has been used in sales
        // If it has been used, you might prevent deletion or only allow if current_stock is 0.
        // For now, it will simply delete if found.

        $this->batchModel->db->transStart(); // Start database transaction
        try {
            if (!$this->batchModel->delete($batchId)) {
                // If delete method returns false (e.g., due to database error)
                throw new \Exception('Failed to delete batch.');
            }

            $this->batchModel->db->transComplete(); // Complete transaction

            if ($this->batchModel->db->transStatus() === false) {
                // Check transaction status for errors
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/medicines/batches/' . $batch['medicine_id']))
                ->with('success', 'Batch deleted successfully.');
        } catch (\Exception $e) {
            $this->batchModel->db->transRollback(); // Rollback on error
            return redirect()->back()->with('error', 'Error deleting batch: ' . $e->getMessage());
        }
    }

    public function addBatch($medicineId = null)
    {
        $medicine = $this->medicineModel->find($medicineId);

        if (empty($medicine)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Medicine not found for adding batch: ' . $medicineId);
        }

        $supplierModel = new \App\Models\Pharmacy\PharmacySupplierModel(); // Assuming you have this model

        $data = [
            'title'      => 'Add New Batch',
            'medicine'   => $medicine,
            'suppliers'  => $supplierModel->findAll(), // Pass all suppliers for the dropdown
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/add_batch', $data);
    }

    // You will also need a storeBatch method to handle the form submission
    public function storeBatch()
    {
        $rules = [
            'medicine_id'           => 'required|integer|is_not_unique[pharmacy_medicines.id]',
            'batch_number'          => 'required|max_length[100]|is_unique[pharmacy_batches.batch_number,id,{id}]', // Unique per medicine or globally? Assume globally for now.
            'supplier_id'           => 'required|integer|is_not_unique[pharmacy_suppliers.id]',
            'initial_stock'         => 'required|integer|greater_than[0]',
            'cost_price_per_unit'   => 'required|numeric|greater_than_equal_to[0]',
            'selling_price_per_unit' => 'required|numeric|greater_than_equal_to[0]|greater_than_equal_to[cost_price_per_unit]',
            'manufacture_date'      => 'required|valid_date',
            'expiry_date'           => 'required|valid_date|after_current_date', // Ensure expiry is in the future
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $medicineId = $this->request->getPost('medicine_id');
        $initialStock = $this->request->getPost('initial_stock');

        $data = [
            'medicine_id'            => $medicineId,
            'batch_number'           => $this->request->getPost('batch_number'),
            'supplier_id'            => $this->request->getPost('supplier_id'),
            'initial_stock'          => $initialStock,
            'current_stock'          => $initialStock, // Current stock is initially the same as initial stock
            'cost_price_per_unit'    => $this->request->getPost('cost_price_per_unit'),
            'selling_price_per_unit' => $this->request->getPost('selling_price_per_unit'),
            'manufacture_date'       => $this->request->getPost('manufacture_date'),
            'expiry_date'            => $this->request->getPost('expiry_date'),
        ];

        $this->batchModel->db->transStart();
        try {
            if (!$this->batchModel->save($data)) {
                throw new \Exception('Failed to save new batch.');
            }

            // Optionally, update total stock of medicine if you store it in medicine table
            // $this->medicineModel->update($medicineId, ['total_stock' => new RawSql('total_stock + ' . $initialStock)]);

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

        $supplierModel = new \App\Models\Pharmacy\PharmacySupplierModel();

        $data = [
            'title'      => 'Edit Batch',
            'batch'      => $batch,
            'medicine'   => $medicine, // Pass medicine details for breadcrumbs and context
            'suppliers'  => $supplierModel->findAll(),
            'validation' => service('validation')
        ];
        return view('pharmacy/medicines/edit_batch', $data);
    }

    /**
     * Handles the update of an existing batch.
     */
    public function updateBatch($batchId = null)
    {
        // Ensure this is a POST request
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back()->with('error', 'Invalid request method.');
        }

        $batch = $this->batchModel->find($batchId);
        if (empty($batch)) {
            return redirect()->back()->with('error', 'Batch not found.');
        }

        $rules = [
            // 'medicine_id' is hidden and should not change
            'batch_number'          => 'required|max_length[100]|is_unique[pharmacy_batches.batch_number,id,' . $batchId . ']', // Unique per medicine or globally? Adjusted for update
            'supplier_id'           => 'required|integer|is_not_unique[pharmacy_suppliers.id]',
            // Initial stock and current stock are typically not edited directly here
            'cost_price_per_unit'   => 'required|numeric|greater_than_equal_to[0]',
            'selling_price_per_unit' => 'required|numeric|greater_than_equal_to[0]|greater_than_equal_to[cost_price_per_unit]',
            'manufacture_date'      => 'required|valid_date',
            'expiry_date'           => 'required|valid_date|after_current_date[if_not_past, ' . $batch['expiry_date'] . ']', // Custom rule if editing to past is allowed
        ];

        // Custom rule for expiry_date to allow keeping past date if already past
        // Or just check if new date is in future
        $this->validator->setRule('expiry_date', 'Expiry Date', 'required|valid_date');
        if ($this->request->getPost('expiry_date') < date('Y-m-d') && $this->request->getPost('expiry_date') != $batch['expiry_date']) {
            // If user tries to change to a *new* past date (and it's not the original past date)
            $rules['expiry_date'] = 'required|valid_date|after_current_date';
        }


        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $medicineId = $batch['medicine_id']; // Get medicine ID from the fetched batch

        $data = [
            'batch_number'           => $this->request->getPost('batch_number'),
            'supplier_id'            => $this->request->getPost('supplier_id'),
            'cost_price_per_unit'    => $this->request->getPost('cost_price_per_unit'),
            'selling_price_per_unit' => $this->request->getPost('selling_price_per_unit'),
            'manufacture_date'       => $this->request->getPost('manufacture_date'),
            'expiry_date'            => $this->request->getPost('expiry_date'),
        ];

        $this->batchModel->db->transStart();
        try {
            if (!$this->batchModel->update($batchId, $data)) {
                throw new \Exception('Failed to update batch.');
            }

            $this->batchModel->db->transComplete();

            if ($this->batchModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/medicines/batches/' . $medicineId))
                ->with('success', 'Batch updated successfully!');
        } catch (\Exception $e) {
            $this->batchModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error updating batch: ' . $e->getMessage());
        }
    }






    public function adjustStock()
    {
        $data = [
            'title'                 => 'Adjust Medicine Stock',
            'medicines'             => $this->medicineModel->findAll(), // Get all medicines for selection
            // If there's old input from a failed validation, fetch batches for that medicine
            'batches_for_old_medicine' => old('medicine_id') ? $this->batchModel->where('medicine_id', old('medicine_id'))->findAll() : [],
            'validation'            => service('validation')
        ];
        return view('pharmacy/medicines/adjust_stock', $data);
    }

    /**
     * AJAX endpoint to get batches for a specific medicine.
     */
    public function getBatchesByMedicine($medicineId = null)
    {
        if ($this->request->isAJAX()) {
            $batches = $this->batchModel
                ->select('id, batch_number, current_stock, expiry_date')
                ->where('medicine_id', $medicineId)
                ->where('current_stock >', 0) // Only show batches with stock
                ->orderBy('expiry_date', 'ASC')
                ->findAll();
            return $this->response->setJSON(['batches' => $batches]);
        }
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    /**
     * Handles the submission of stock adjustment.
     */
    public function storeAdjustment()
    {
        $rules = [
            'medicine_id'       => 'required|integer|is_not_unique[pharmacy_medicines.id]',
            'batch_id'          => 'required|integer|is_not_unique[pharmacy_batches.id]',
            'adjustment_type'   => 'required|in_list[increase,decrease]',
            'quantity'          => 'required|integer|greater_than[0]',
            'reason'            => 'required|min_length[5]|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $batchId         = $this->request->getPost('batch_id');
        $adjustmentType  = $this->request->getPost('adjustment_type');
        $quantity        = (int) $this->request->getPost('quantity');
        $reason          = $this->request->getPost('reason');

        $batch = $this->batchModel->find($batchId);
        if (empty($batch)) {
            return redirect()->back()->withInput()->with('error', 'Selected batch not found.');
        }

        $newStock = $batch['current_stock'];
        if ($adjustmentType === 'increase') {
            $newStock += $quantity;
        } else { // decrease
            if ($newStock < $quantity) {
                return redirect()->back()->withInput()->with('error', 'Cannot decrease stock by more than available quantity. Available: ' . $newStock);
            }
            $newStock -= $quantity;
        }

        $this->batchModel->db->transStart();
        try {
            // 1. Update batch current stock
            if (!$this->batchModel->update($batchId, ['current_stock' => $newStock])) {
                throw new \Exception('Failed to update batch stock.');
            }

            // 2. Record the adjustment in pharmacy_stock_adjustments table
            $adjustmentData = [
                'medicine_id'       => $batch['medicine_id'],
                'batch_id'          => $batchId,
                'adjustment_type'   => $adjustmentType,
                'quantity'          => $quantity,
                'current_stock_before' => $batch['current_stock'],
                'current_stock_after' => $newStock,
                'reason'            => $reason,
                'adjusted_by_user_id' => session()->get('id') ?? null, // Assuming you store user ID in session
            ];

            if (!$this->stockAdjustmentModel->save($adjustmentData)) {
                throw new \Exception('Failed to record stock adjustment.');
            }

            // Optionally, if you maintain total_stock in the medicine table, update it here too.
            // Example for increasing: $this->medicineModel->update($batch['medicine_id'], ['total_stock' => new RawSql('total_stock + ' . $quantity)]);
            // Example for decreasing: $this->medicineModel->update($batch['medicine_id'], ['total_stock' => new RawSql('total_stock - ' . $quantity)]);


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
}
