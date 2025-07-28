<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetModel;
use CodeIgniter\API\ResponseTrait;

class AssetController extends BaseController
{
    use ResponseTrait;

    protected $assetModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all assets.
     */
    public function index()
    {
        $data['title'] = 'Assets & Equipment Management';
        $data['assets'] = $this->assetModel->orderBy('name', 'ASC')->findAll();
        return view('hospital_resources/assets_equipment/index', $data);
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $data['title'] = 'Add New Asset';
        $data['validation'] = \Config\Services::validation();
        $data['asset'] = [];
        return view('hospital_resources/assets_equipment/form', $data);
    }

    /**
     * Store a newly created asset in the database.
     */
    public function store()
    {
        $session = session();

        if (!$this->validate($this->assetModel->validationRules)) {
            $session->setFlashdata('error', 'Please correct the errors in the form.');
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'                 => $this->request->getPost('name'),
            'asset_tag'            => $this->request->getPost('asset_tag') ?: null, // Allow null if empty
            'category'             => $this->request->getPost('category'),
            'description'          => $this->request->getPost('description'),
            'purchase_date'        => $this->request->getPost('purchase_date') ?: null,
            'warranty_expiry_date' => $this->request->getPost('warranty_expiry_date') ?: null,
            'location'             => $this->request->getPost('location'),
            'status'               => $this->request->getPost('status'),
            'notes'                => $this->request->getPost('notes'),
        ];

        if ($this->assetModel->save($data)) {
            $session->setFlashdata('success', 'Asset added successfully!');
            return redirect()->to('/assets');
        } else {
            $session->setFlashdata('error', 'Failed to add asset. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified asset.
     * @param int $id The ID of the asset to edit.
     */
    public function edit($id = null)
    {
        $data['title'] = 'Edit Asset';
        $data['validation'] = \Config\Services::validation();

        $asset = $this->assetModel->find($id);

        if (!$asset) {
            session()->setFlashdata('error', 'Asset not found.');
            return redirect()->to('/assets');
        }

        $data['asset'] = $asset;
        return view('hospital_resources/assets_equipment/form', $data);
    }

    /**
     * Update the specified asset in the database.
     * @param int $id The ID of the asset to update.
     */
    public function update($id)
    {
        $session = session();

        $currentAsset = $this->assetModel->find($id);
        if (!$currentAsset) {
            $session->setFlashdata('error', 'Asset not found for update.');
            return redirect()->to('/assets');
        }

        // Adjust validation rules for update (specifically for unique asset_tag)
        $rules = $this->assetModel->validationRules;
        $rules['asset_tag'] = "permit_empty|max_length[100]|is_unique[assets_equipment.asset_tag,id,{$id}]";

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', 'Please correct the errors in the form.');
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'                 => $this->request->getPost('name'),
            'asset_tag'            => $this->request->getPost('asset_tag') ?: null,
            'category'             => $this->request->getPost('category'),
            'description'          => $this->request->getPost('description'),
            'purchase_date'        => $this->request->getPost('purchase_date') ?: null,
            'warranty_expiry_date' => $this->request->getPost('warranty_expiry_date') ?: null,
            'location'             => $this->request->getPost('location'),
            'status'               => $this->request->getPost('status'),
            'notes'                => $this->request->getPost('notes'),
        ];

        if ($this->assetModel->update($id, $data)) {
            $session->setFlashdata('success', 'Asset updated successfully!');
            return redirect()->to('/assets');
        } else {
            $session->setFlashdata('error', 'Failed to update asset. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete the specified asset from the database (soft delete).
     * @param int $id The ID of the asset to delete.
     */
    public function delete($id = null)
    {
        $session = session();
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            $session->setFlashdata('error', 'Asset not found.');
            return redirect()->to('/assets');
        }

        if ($this->assetModel->delete($id)) {
            $session->setFlashdata('success', 'Asset deleted successfully!');
        } else {
            $session->setFlashdata('error', 'Failed to delete asset. Please try again.');
        }
        return redirect()->to('/assets');
    }
}
