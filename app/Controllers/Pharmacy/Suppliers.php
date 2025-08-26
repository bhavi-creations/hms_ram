<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacySupplierModel;
use App\Models\Pharmacy\PharmacyBatchModel;

class Suppliers extends BaseController
{
    protected $supplierModel;
    protected $batchModel;


    public function __construct()
    {
        // Ensure parent constructor runs for session, etc.
        // parent::__construct();

        $this->supplierModel = new PharmacySupplierModel();
         $this->batchModel = new PharmacyBatchModel(); 
    }

    /**
     * Displays a list of all suppliers.
     */
    public function index()
    {
        $data = [
            'title'     => 'Manage Suppliers',
            'suppliers' => $this->supplierModel->findAll()
        ];
        return view('pharmacy/suppliers/index', $data);
    }

    /**
     * Shows the form to add a new supplier.
     */
    public function create()
    {
        $data = [
            'title'      => 'Add New Supplier',
            'validation' => service('validation')
        ];
        return view('pharmacy/suppliers/create', $data);
    }

    /**
     * Handles the submission of a new supplier form.
     */
    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]|max_length[255]|is_unique[pharmacy_suppliers.name]',
            'contact_person' => 'permit_empty|max_length[100]',
            'phone'         => 'permit_empty|max_length[20]',
            'email'         => 'permit_empty|valid_email|max_length[255]',
            'address'       => 'permit_empty|max_length[500]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->supplierModel->save([
            'name'           => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address')
        ]);

        return redirect()->to(site_url('pharmacy/suppliers'))->with('success', 'Supplier added successfully.');
    }

    /**
     * Displays a form to edit an existing supplier.
     */
    public function edit($id = null)
    {
        $supplier = $this->supplierModel->find($id);

        if (empty($supplier)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the supplier: ' . $id);
        }

        $data = [
            'title'      => 'Edit Supplier',
            'supplier'   => $supplier,
            'validation' => service('validation')
        ];
        return view('pharmacy/suppliers/edit', $data);
    }

    /**
     * Handles the update of an existing supplier.
     */
    public function update($id = null)
    {
        $currentSupplier = $this->supplierModel->find($id);
        if (empty($currentSupplier)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the supplier to update: ' . $id);
        }

        $rules = [
            'name'          => 'required|min_length[3]|max_length[255]',
            'contact_person' => 'permit_empty|max_length[100]',
            'phone'         => 'permit_empty|max_length[20]',
            'email'         => 'permit_empty|valid_email|max_length[255]',
            'address'       => 'permit_empty|max_length[500]'
        ];

        // Only add unique rule if name is changed
        if ($this->request->getPost('name') !== $currentSupplier['name']) {
            $rules['name'] .= '|is_unique[pharmacy_suppliers.name]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->supplierModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address')
        ]);

        return redirect()->to(site_url('pharmacy/suppliers'))->with('success', 'Supplier updated successfully.');
    }

    /**
     * Deletes a supplier. Consider restrictions if associated with purchases or batches.
     */
    public function delete($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (empty($supplier)) {
            return redirect()->to(site_url('pharmacy/suppliers'))->with('error', 'Supplier not found.');
        }

        // Add logic to check for existing dependencies before deleting
        // E.g., check if supplier has associated purchases or medicine batches
        $associatedBatches = $this->batchModel->where('supplier_id', $id)->first();
        if ($associatedBatches) {
            return redirect()->to(site_url('pharmacy/suppliers'))->with('error', 'Cannot delete supplier with associated medicine batches. Consider deactivating instead.');
        }
        // Similar check for pharmacy_purchases

        if ($this->supplierModel->delete($id)) {
            return redirect()->to(site_url('pharmacy/suppliers'))->with('success', 'Supplier deleted successfully.');
        } else {
            return redirect()->to(site_url('pharmacy/suppliers'))->with('error', 'Failed to delete supplier.');
        }
    }
}
