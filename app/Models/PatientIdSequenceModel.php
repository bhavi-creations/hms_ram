<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // Ensure this is imported for timestamps

class PatientIdSequenceModel extends Model
{
    protected $table            = 'patient_id_sequences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // CHANGED: 'last_sequence' to 'next_sequence_number'
    protected $allowedFields    = ['prefix', 'next_sequence_number']; 

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = ''; // No created_at in patient_id_sequences table
    protected $updatedField  = 'updated_at';

    /**
     * Increments and returns the next sequence number for a given prefix.
     * Ensures atomic update for concurrency safety using a combination of UPDATE and SELECT
     * within a CodeIgniter transaction.
     *
     * @param string $prefix The ID prefix (e.g., 'PAT', 'OPD', 'GEN', 'CUS, 'IPD')
     * @return int The next available sequential number
     * @throws \Exception If the database transaction fails.
     */
    public function getNextSequence(string $prefix): int
    {
        // Start a database transaction to ensure atomicity
        $this->db->transStart();

        $nextSequence = 1; // Default initial sequence

        // Attempt to increment the 'next_sequence_number' for the given prefix.
        // We use 'set(column, expression, escape)' where 'false' means don't escape the expression.
        $updatedRows = $this->db->table($this->table)
                                ->where('prefix', $prefix)
                                // CHANGED: 'last_sequence' to 'next_sequence_number'
                                ->set('next_sequence_number', 'next_sequence_number + 1', false) 
                                ->set('updated_at', new Time('now', TIMEZONE), true) // Update timestamp
                                ->update(); // Execute the update query

        if ($updatedRows === 0) {
            // If no rows were updated, it means the prefix does not exist in the table.
            // Insert it with an initial sequence of 1.
            // CHANGED: 'last_sequence' to 'next_sequence_number'
            $this->insert(['prefix' => $prefix, 'next_sequence_number' => 1]);
            // nextSequence remains 1 as already initialized
        } else {
            // If a row was updated (meaning the prefix existed and was incremented),
            // fetch the newly incremented value from the database.
            $row = $this->db->table($this->table)
                            // CHANGED: 'last_sequence' to 'next_sequence_number'
                            ->select('next_sequence_number') 
                            ->where('prefix', $prefix)
                            ->get()
                            ->getRowArray(); // Fetch the single result row as an array

            if (empty($row)) {
                // This scenario should ideally not happen if $updatedRows > 0,
                // but as a safeguard, roll back the transaction and throw an error.
                $this->db->transRollback();
                throw new \Exception('Failed to retrieve updated sequence after incrementing for prefix: ' . $prefix);
            }
            // CHANGED: 'last_sequence' to 'next_sequence_number'
            $nextSequence = $row['next_sequence_number']; 
        }

        // Complete the transaction. If any query failed within the transaction,
        // it will automatically roll back. Otherwise, it will commit.
        $this->db->transComplete();

        // Check the transaction status to ensure it was successful
        if ($this->db->transStatus() === false) {
            // Log the error for debugging purposes (check app/Logs directory)
            log_message('error', 'Database transaction failed for sequence generation for prefix: ' . $prefix . '. Last query: ' . $this->db->getLastQuery());
            // Throw a more descriptive exception for the calling code
            throw new \Exception('Failed to generate sequence ID due to a database transaction error for prefix: ' . $prefix);
        }

        return $nextSequence;
    }
}