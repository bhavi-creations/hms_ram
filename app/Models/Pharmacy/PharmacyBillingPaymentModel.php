<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyBillingPaymentModel extends Model
{
    protected $table = 'pharmacy_billing_payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'bill_id',
        'payment_date',
        'payment_amount',
        'payment_method',
        'created_at',
        'updated_at',
    ];
}
