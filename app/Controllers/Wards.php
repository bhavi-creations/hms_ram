<?php

namespace App\Controllers;

use App\Models\WardModel;
use App\Models\BedModel;
use CodeIgniter\Controller;

class Wards extends BaseController
{
    protected $wardModel;
    protected $bedModel;

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

        // Set the ID for unique validation rule to ignore current record
        $this->wardModel->setValidationRule('name', "required|min_length[3]|max_length[100]|is_unique[wards.name,id,{$id}]");

        // Validate the incoming request data
        if (!$this->validate($this->wardModel->validationRules)) {
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
        $bedNumbers = $this->bedModel->generateBedNumbers($prefix, $capacity, 1); // Start from 1 for new ward

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
        $highestExistingNumber = $this->bedModel->getHighestBedNumber($wardId, $prefix);

        // Calculate the starting number for new beds. If no beds exist, start from 1.
        // Otherwise, start from highestExistingNumber + 1
        $startNumberForNewBeds = $highestExistingNumber > 0 ? $highestExistingNumber + 1 : 1;

        $bedsToInsert = [];
        for ($i = $startNumberForNewBeds; $i <= $startNumberForNewBeds + ($newCapacity - $oldCapacity) -1 ; $i++) {
             // Check if a bed with this number and prefix already exists (even if soft-deleted)
            $existingBed = $this->bedModel->where('ward_id', $wardId)
                                          ->where('bed_number', strtoupper($prefix) . '-' . $i)
                                          ->withDeleted() // Include soft-deleted records in search
                                          ->first();

            if ($existingBed) {
                // If exists, restore it (undelete) and update its status to Available
                $this->bedModel->update($existingBed['id'], ['deleted_at' => null, 'status' => 'Available']);
            } else {
                // If not exists, create a new one
                $bedsToInsert[] = [
                    'ward_id'    => $wardId,
                    'bed_number' => strtoupper($prefix) . '-' . $i,
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
        // Get all beds for the ward, ordered by bed number descending
        // We need to fetch all beds to correctly identify which ones to remove based on the new capacity.
        $bedsToRemove = $this->bedModel->where('ward_id', $wardId)
                                       ->orderBy('id', 'DESC') // Order by ID to ensure consistent removal
                                       ->findAll();

        // Filter out beds that should remain based on the new capacity.
        // We need to parse the bed_number to get the numeric part for comparison.
        $bedsToDeleteIds = [];
        $currentBedCount = 0;

        // Sort beds by their numeric part in descending order
        usort($bedsToRemove, function($a, $b) use ($prefix) {
            $numA = (int)str_replace($prefix . '-', '', $a['bed_number']);
            $numB = (int)str_replace($prefix . '-', '', $b['bed_number']);
            return $numB <=> $numA; // Descending order
        });

        foreach ($bedsToRemove as $bed) {
            // Extract the numeric part of the bed_number (e.g., from GEN-5 get 5)
            $numericPart = (int)str_replace($prefix . '-', '', $bed['bed_number']);

            // If the numeric part is greater than the new capacity, mark for deletion
            if ($numericPart > $newCapacity) {
                $bedsToDeleteIds[] = $bed['id'];
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
}
