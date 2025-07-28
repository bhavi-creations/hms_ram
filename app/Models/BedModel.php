<?php

namespace App\Models;

use CodeIgniter\Model;

class BedModel extends Model
{
    protected $table            = 'beds'; // Table name for beds
    protected $primaryKey       = 'id'; // Primary key of the table
    protected $useAutoIncrement = true; // Auto-incrementing primary key
    protected $returnType       = 'array'; // Return type for query results (array or object)
    protected $useSoftDeletes   = true; // Enable soft deletes (uses 'deleted_at' column)

    // Fields that can be mass-assigned
    protected $allowedFields = [
        'ward_id',
        'bed_number',
        'status',
        'notes'
    ];

    // Dates that will be automatically managed by CodeIgniter
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // Type of date field (datetime, date, int)
    protected $createdField  = 'created_at'; // Column for creation timestamp
    protected $updatedField  = 'updated_at'; // Column for update timestamp
    protected $deletedField  = 'deleted_at'; // Column for soft delete timestamp

    // Validation rules for bed data
    protected $validationRules = [
        'ward_id'    => 'required|integer', // Must be a valid ward ID
        'bed_number' => 'required|min_length[1]|max_length[50]|is_unique[beds.bed_number,id,{id},ward_id,{ward_id}]', // Bed number must be unique per ward
        'status'     => 'required|in_list[Available,Occupied,Under Maintenance,Dirty]', // Status must be one of the allowed values
    ];

    protected $validationMessages = [
        'ward_id' => [
            'required' => 'Ward ID is required.',
            'integer'  => 'Ward ID must be an integer.',
        ],
        'bed_number' => [
            'required'    => 'Bed number is required.',
            'min_length'  => 'Bed number must be at least 1 character long.',
            'max_length'  => 'Bed number cannot exceed 50 characters.',
            'is_unique'   => 'This bed number already exists for this ward. Please choose a different one.',
        ],
        'status' => [
            'required'  => 'Status is required.',
            'in_list'   => 'Invalid status selected.',
        ],
    ];

    protected $skipValidation       = false; // Do not skip validation by default
    protected $cleanValidationRules = true; // Clean validation rules before each validation run

    /**
     * Generates bed numbers based on prefix and capacity.
     * This method is crucial for ensuring continuous numbering (e.g., GEN-1, GEN-2, GEN-3).
     *
     * @param string $prefix The bed prefix (e.g., 'GEN').
     * @param int $capacity The total number of beds to generate.
     * @param int $startNumber The starting number for the bed sequence (e.g., 1 or 6 for new additions).
     * @return array An array of generated bed numbers.
     */
    public function generateBedNumbers(string $prefix, int $capacity, int $startNumber = 1): array
    {
        $bedNumbers = [];
        for ($i = $startNumber; $i <= $capacity; $i++) {
            $bedNumbers[] = strtoupper($prefix) . '-' . $i;
        }
        return $bedNumbers;
    }

    /**
     * Gets the highest bed number for a given ward.
     * Used to determine the starting point for new bed additions.
     *
     * @param int $wardId The ID of the ward.
     * @param string $prefix The bed prefix for the ward.
     * @return int The highest bed number found, or 0 if no beds exist for the ward.
     */
    public function getHighestBedNumber(int $wardId, string $prefix): int
    {
        // Get all beds for the ward, including soft-deleted ones, to find the true highest number ever used.
        $beds = $this->where('ward_id', $wardId)
                     ->withDeleted() // Include soft-deleted records
                     ->findAll();

        $highestNumber = 0;
        $prefixLength = strlen($prefix) + 1; // Length of prefix + hyphen

        foreach ($beds as $bed) {
            // Check if bed_number starts with the correct prefix and contains a hyphen
            if (strpos($bed['bed_number'], $prefix . '-') === 0) {
                // Extract the numeric part after the prefix and hyphen
                $numberPart = substr($bed['bed_number'], $prefixLength);
                if (is_numeric($numberPart)) {
                    $bedNumber = (int)$numberPart;
                    if ($bedNumber > $highestNumber) {
                        $highestNumber = $bedNumber;
                    }
                }
            }
        }
        return $highestNumber;
    }
}
