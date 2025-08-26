<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyUnitOfMeasureModel;
use App\Models\Pharmacy\PharmacyMedicineModel;

class UnitsOfMeasure extends BaseController
{
    protected $unitModel;

    public function __construct()
    {
        $this->unitModel = new PharmacyUnitOfMeasureModel();
    }

    /**
     * Lists all units of measure.
     */
    public function index()
    {
        $data = [
            'title' => 'Manage Units of Measure',
            'units' => $this->unitModel->findAll(),
        ];
        return view('pharmacy/units_of_measure/index', $data);
    }

    /**
     * Displays the form to create a new unit of measure.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Unit of Measure',
            'validation' => service('validation'),
        ];
        return view('pharmacy/units_of_measure/create', $data);
    }

    /**
     * Handles the submission of a new unit of measure form.
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[1]|max_length[50]|is_unique[pharmacy_units_of_measure.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->unitModel->save([
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->to(site_url('pharmacy/units_of_measure'))->with('success', 'Unit of measure added successfully.');
    }

    /**
     * Displays the form to edit an existing unit of measure.
     */
    public function edit($id = null)
    {
        $unit = $this->unitModel->find($id);

        if (empty($unit)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the unit of measure item: ' . $id);
        }

        $data = [
            'title' => 'Edit Unit of Measure',
            'unit' => $unit,
            'validation' => service('validation'),
        ];
        return view('pharmacy/units_of_measure/edit', $data);
    }

    /**
     * Handles the update of an existing unit of measure.
     */
    public function update($id = null)
    {
        $rules = [
            'name' => 'required|min_length[1]|max_length[50]|is_unique[pharmacy_units_of_measure.name,id,' . $id . ']',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->unitModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->to(site_url('pharmacy/units_of_measure'))->with('success', 'Unit of measure updated successfully.');
    }

    /**
     * Deletes an existing unit of measure.
     */
    public function delete($id = null)
    {
        $unit = $this->unitModel->find($id);

        if (empty($unit)) {
            return redirect()->back()->with('error', 'Unit of measure not found.');
        }

        // Before deleting, check if this unit is used by any medicine.
        $medicineModel = new PharmacyMedicineModel();
        // The column in the pharmacy_medicines table is 'unit_of_measure' and stores the name, not the ID.
       $isUsed = $medicineModel->where('unit_of_measure_id', $unit['id'])->first();
        if ($isUsed) {
            return redirect()->back()->with('error', 'Cannot delete unit. It is currently in use by one or more medicines.');
        }

        $this->unitModel->delete($id);

        return redirect()->to(site_url('pharmacy/units_of_measure'))->with('success', 'Unit of measure deleted successfully.');
    }
}
