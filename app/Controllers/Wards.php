<?php

namespace App\Controllers;

use App\Models\WardModel;
use App\Models\BedModel; // Ensure BedModel is used if its methods are called within Wards controller
use CodeIgniter\Controller;

class Wards extends BaseController
{
    protected $wardModel;
    protected $bedModel; // Keep this as it's used in your provided code (e.g., in delete, addBedsToWard)

    public function __construct()
    {
        // Initialize the WardModel and BedModel
        $this->wardModel = new WardModel();
        $this->bedModel  = new BedModel();
        // Helper for form validation
        helper(['form', 'url']);
    }

    /**
     * Displays a list of all wards.
     * This is the main view for ward management.
     */
    public function index()
    {
        $data = [
            'title' => 'Wards Management',
            'wards' => $this->wardModel->findAll() // Fetch all wards
        ];
        return view('hospital_resources/wards/index', $data);
    }

    /**
     * Displays the form to create a new ward.
     */
    public function create()
    {
        $data = [
            'title' => 'Create New Ward',
            'validation' => \Config\Services::validation() // Load validation service for form errors
        ];
        return view('hospital_resources/wards/create', $data);
    }

    /**
     * Stores a new ward and generates beds based on capacity and prefix.
     */
    public function store()
    {
        // Validate the incoming request data
        // Assuming WardModel has validation rules defined
        if (!$this->validate($this->wardModel->validationRules)) {
            // If validation fails, redirect back to the form with input and errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data for ward insertion
        $wardData = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'capacity'    => (int)$this->request->getPost('capacity'),
            'bed_prefix'  => strtoupper($this->request->getPost('bed_prefix')), // Ensure prefix is uppercase
            'status'      => $this->request->getPost('status') ?? 'Active', // Default status to 'Active'
        ];

        // Insert the ward data into the database
        $wardId = $this->wardModel->insert($wardData);

        if ($wardId) {
            // If ward is successfully created, generate beds for it
            $this->generateBedsForWard($wardId, $wardData['bed_prefix'], $wardData['capacity']);

            // Set success flash message
            session()->setFlashdata('success', 'Ward and its beds created successfully!');
            return redirect()->to(base_url('wards'));
        } else {
            // Set error flash message if ward creation fails
            session()->setFlashdata('error', 'Failed to create ward. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Displays the form to edit an existing ward.
     *
     * @param int $id The ID of the ward to edit.
     */
    public function edit($id)
    {
        $ward = $this->wardModel->find($id); // Find the ward by ID

        if (!$ward) {
            // If ward not found, set error message and redirect
            session()->setFlashdata('error', 'Ward not found.');
            return redirect()->to(base_url('wards'));
        }

        $data = [
            'title'      => 'Edit Ward',
            'ward'       => $ward,
            'validation' => \Config\Services::validation()
        ];
        return view('hospital_resources/wards/edit', $data);
    }

    /**
     * Updates an existing ward and adjusts beds based on capacity changes.
     *
     * @param int $id The ID of the ward to update.
     */
    public function update($id)
    {
        // Find the existing ward
        $oldWard = $this->wardModel->find($id);
        if (!$oldWard) {
            session()->setFlashdata('error', 'Ward not found.');
            return redirect()->to(base_url('wards'));
        }

        // IMPORTANT: Temporarily set the validation rule for 'name' to allow self-update
        // This assumes your WardModel has a 'name' rule.
        $validationRules = $this->wardModel->validationRules;
        if (isset($validationRules['name'])) {
            $validationRules['name'] = "required|min_length[3]|max_length[100]|is_unique[wards.name,id,{$id}]";
        }
        
        // Validate the incoming request data using the modified rules
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data for ward update
        $newCapacity = (int)$this->request->getPost('capacity');
        $newPrefix   = strtoupper($this->request->getPost('bed_prefix'));
        $wardData = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'capacity'    => $newCapacity,
            'bed_prefix'  => $newPrefix,
            'status'      => $this->request->getPost('status'),
        ];

        // Update the ward data in the database
        if ($this->wardModel->update($id, $wardData)) {
            // Handle bed capacity changes
            $oldCapacity = (int)$oldWard['capacity'];
            $oldPrefix   = $oldWard['bed_prefix'];

            if ($newCapacity > $oldCapacity) {
                // Capacity increased: Add new beds
                $this->addBedsToWard($id, $newPrefix, $newCapacity, $oldCapacity);
            } elseif ($newCapacity < $oldCapacity) {
                // Capacity decreased: Remove (soft delete) highest numbered beds
                $this->removeBedsFromWard($id, $newPrefix, $newCapacity, $oldCapacity);
            }
            // If prefix changed, regenerate all beds for this ward
            if ($newPrefix !== $oldPrefix) {
                $this->regenerateAllBedsForWard($id, $newPrefix, $newCapacity);
            }

            session()->setFlashdata('success', 'Ward and its beds updated successfully!');
            return redirect()->to(base_url('wards'));
        } else {
            session()->setFlashdata('error', 'Failed to update ward. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Deletes (soft deletes) a ward and its associated beds.
     *
     * @param int $id The ID of the ward to delete.
     */
    public function delete($id)
    {
        // Soft delete the ward
        if ($this->wardModel->delete($id)) {
            // Soft delete all beds associated with this ward
            $this->bedModel->where('ward_id', $id)->delete();

            session()->setFlashdata('success', 'Ward and associated beds deleted successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to delete ward. Please try again.');
        }
        return redirect()->to(base_url('wards'));
    }

    /**
     * Generates beds for a newly created ward.
     *
     * @param int $wardId The ID of the ward.
     * @param string $prefix The bed prefix.
     * @param int $capacity The total capacity of beds.
     */
    protected function generateBedsForWard(int $wardId, string $prefix, int $capacity)
    {
        // Ensure BedModel has generateBedNumbers method or replace with direct logic
        // For now, assuming generateBedNumbers exists and returns an array of bed numbers
        // Example: ['BED-1', 'BED-2']
        $bedNumbers = [];
        for ($i = 1; $i <= $capacity; $i++) {
            $bedNumbers[] = strtoupper($prefix) . '-' . $i;
        }

        $bedsToInsert = [];
        foreach ($bedNumbers as $bedNum) {
            $bedsToInsert[] = [
                'ward_id'    => $wardId,
                'bed_number' => $bedNum,
                'status'     => 'Available', // Default status for new beds
            ];
        }
        if (!empty($bedsToInsert)) {
            $this->bedModel->insertBatch($bedsToInsert);
        }
    }

    /**
     * Adds new beds to a ward when capacity increases.
     * New beds continue the increment from the highest existing bed number.
     *
     * @param int $wardId The ID of the ward.
     * @param string $prefix The bed prefix.
     * @param int $newCapacity The new total capacity.
     * @param int $oldCapacity The old total capacity.
     */
    protected function addBedsToWard(int $wardId, string $prefix, int $newCapacity, int $oldCapacity)
    {
        // Get the highest bed number ever used for this ward, including soft-deleted ones
        // This method should be in BedModel
        $highestExistingBed = $this->bedModel->where('ward_id', $wardId)
                                             ->withDeleted() // Include soft-deleted records
                                             ->orderBy('id', 'DESC') // Order by ID to get the latest created bed
                                             ->first();

        $highestExistingNumber = 0;
        if ($highestExistingBed) {
            // Extract numeric part from bed_number (e.g., "BED-5" -> 5)
            $parts = explode('-', $highestExistingBed['bed_number']);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                $highestExistingNumber = (int)end($parts);
            }
        }

        $startNumberForNewBeds = $highestExistingNumber > 0 ? $highestExistingNumber + 1 : 1;

        $bedsToInsert = [];
        for ($i = $startNumberForNewBeds; $i <= $newCapacity; $i++) { // Loop up to newCapacity
            $fullBedNumber = strtoupper($prefix) . '-' . $i;

            // Check if a bed with this full bed number already exists (even if soft-deleted)
            $existingBed = $this->bedModel->where('ward_id', $wardId)
                                          ->where('bed_number', $fullBedNumber)
                                          ->withDeleted() // Include soft-deleted records in search
                                          ->first();

            if ($existingBed) {
                // If exists and is soft-deleted, restore it and update its status to Available
                if ($existingBed['deleted_at'] !== null) {
                    $this->bedModel->update($existingBed['id'], ['deleted_at' => null, 'status' => 'Available']);
                }
            } else {
                // If not exists, create a new one
                $bedsToInsert[] = [
                    'ward_id'    => $wardId,
                    'bed_number' => $fullBedNumber,
                    'status'     => 'Available',
                ];
            }
        }

        if (!empty($bedsToInsert)) {
            $this->bedModel->insertBatch($bedsToInsert);
        }
    }


    /**
     * Removes beds from a ward when capacity decreases.
     * Soft deletes the highest numbered beds.
     *
     * @param int $wardId The ID of the ward.
     * @param string $prefix The bed prefix.
     * @param int $newCapacity The new total capacity.
     * @param int $oldCapacity The old total capacity.
     */
    protected function removeBedsFromWard(int $wardId, string $prefix, int $newCapacity, int $oldCapacity)
    {
        // Get all currently active beds for the ward, ordered by bed number descending
        $beds = $this->bedModel->where('ward_id', $wardId)
                               ->orderBy('bed_number', 'DESC') // Order by bed_number to remove highest first
                               ->findAll();

        $bedsToDeleteIds = [];
        $bedsToKeepCount = 0;

        foreach ($beds as $bed) {
            // Extract the numeric part of the bed_number (e.g., from GEN-5 get 5)
            $numericPart = (int)str_replace($prefix . '-', '', $bed['bed_number']);

            if ($numericPart > $newCapacity) {
                // If the numeric part is greater than the new capacity, mark for deletion
                // But only if it's not currently occupied
                if ($bed['status'] === 'Available' || $bed['status'] === 'Under Maintenance' || $bed['status'] === 'Dirty') {
                     $bedsToDeleteIds[] = $bed['id'];
                } else {
                    // Log or handle: Cannot delete occupied bed
                    session()->setFlashdata('error', "Cannot reduce capacity. Bed " . $bed['bed_number'] . " is currently occupied.");
                    // You might want to prevent the update entirely or just skip this bed
                }
            }
        }

        // Perform soft delete for the identified beds
        if (!empty($bedsToDeleteIds)) {
            $this->bedModel->delete($bedsToDeleteIds);
        }
    }


    /**
     * Regenerates all beds for a ward, typically when the prefix changes.
     * This will soft delete existing beds and create new ones with the new prefix.
     *
     * @param int $wardId The ID of the ward.
     * @param string $newPrefix The new bed prefix.
     * @param int $capacity The current capacity.
     */
    protected function regenerateAllBedsForWard(int $wardId, string $newPrefix, int $capacity)
    {
        // Soft delete all existing beds for this ward
        $this->bedModel->where('ward_id', $wardId)->delete();

        // Generate and insert new beds with the new prefix
        $this->generateBedsForWard($wardId, $newPrefix, $capacity);
    }

    /**
     * AJAX endpoint to get all wards.
     * Used for populating dropdowns in other parts of the system.
     * @return \CodeIgniter\HTTP\Response
     */
    public function getWards()
    {
        // Ensure it's an AJAX request to prevent direct access
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $wards = $this->wardModel->findAll();
        return $this->response->setJSON($wards);
    }
}
