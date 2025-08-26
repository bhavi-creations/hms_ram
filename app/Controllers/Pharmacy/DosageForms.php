<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyDosageFormModel;

class DosageForms extends BaseController
{
    // Declare the model property
    protected $dosageFormModel;

    /**
     * Constructor to load the necessary models.
     */
    public function __construct()
    {
        $this->dosageFormModel = new PharmacyDosageFormModel();
    }

    /**
     * Displays a list of all dosage forms.
     */
    public function index()
    {
        $data = [
            'title' => 'Dosage Forms',
            'dosageForms' => $this->dosageFormModel->findAll()
        ];
        return view('pharmacy/dosage_forms/index', $data);
    }

    /**
     * Displays the form to create a new dosage form.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Dosage Form'
        ];
        return view('pharmacy/dosage_forms/create', $data);
    }

    /**
     * Handles the form submission for creating a new dosage form.
     */
    public function store()
    {
        // Define validation rules
        $rules = [
            'name' => 'required|is_unique[pharmacy_dosage_forms.name]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
        ];

        if ($this->dosageFormModel->insert($data)) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('success', 'Dosage form added successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to add dosage form.');
        }
    }

    /**
     * Displays the form to edit an existing dosage form.
     *
     * @param int $id The ID of the dosage form to edit.
     */
    public function edit($id = null)
    {
        $dosageForm = $this->dosageFormModel->find($id);

        if (!$dosageForm) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('error', 'Dosage form not found.');
        }

        $data = [
            'title' => 'Edit Dosage Form',
            'dosageForm' => $dosageForm
        ];
        return view('pharmacy/dosage_forms/edit', $data);
    }

    /**
     * Handles the form submission for updating an existing dosage form.
     *
     * @param int $id The ID of the dosage form to update.
     */
    public function update($id = null)
    {
        $dosageForm = $this->dosageFormModel->find($id);

        if (!$dosageForm) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('error', 'Dosage form not found.');
        }

        // Define validation rules for update
        $rules = [
            'name' => 'required|is_unique[pharmacy_dosage_forms.name,id,' . $id . ']'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
        ];

        if ($this->dosageFormModel->update($id, $data)) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('success', 'Dosage form updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update dosage form.');
        }
    }

    /**
     * Deletes a dosage form.
     *
     * @param int $id The ID of the dosage form to delete.
     */
    public function delete($id = null)
    {
        $dosageForm = $this->dosageFormModel->find($id);

        if (!$dosageForm) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('error', 'Dosage form not found.');
        }

        // Add a dependency check here later if needed, e.g., if a medicine is using this dosage form.
        // For now, it will delete directly.

        if ($this->dosageFormModel->delete($id)) {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('success', 'Dosage form deleted successfully.');
        } else {
            return redirect()->to(site_url('pharmacy/dosage_forms'))->with('error', 'Failed to delete dosage form.');
        }
    }
}
    