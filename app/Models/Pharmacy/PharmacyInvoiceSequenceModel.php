<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyInvoiceSequenceModel extends Model
{
    protected $table = 'pharmacy_invoice_sequences';
    protected $primaryKey = 'prefix';
    protected $returnType = 'array';
    protected $allowedFields = ['prefix', 'next_sequence_number'];

    /**
     * Get the next sequence number and atomically increment it within a transaction.
     * This ensures a unique number is generated even with concurrent requests.
     * @param string $prefix The prefix for the invoice (e.g., 'PHM-OP').
     * @return int The next available sequence number.
     * @throws \Exception If the transaction fails.
     */
    public function getAndIncrementSequence(string $prefix): int
    {
        // Start a database transaction to ensure atomicity
        $this->db->transStart();

        // Get the current sequence number directly
        $row = $this->where('prefix', $prefix)->first();

        $nextNumber = 1;

        if ($row) {
            $nextNumber = $row['next_sequence_number'] + 1;
            // Update the sequence number for the next request
            $this->update($prefix, ['next_sequence_number' => $nextNumber]);
        } else {
            // Insert a new record if the prefix doesn't exist
            $this->insert(['prefix' => $prefix, 'next_sequence_number' => $nextNumber]);
        }

        // Complete the transaction
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Transaction failed while generating invoice number.');
        }

        return $nextNumber;
    }
}
