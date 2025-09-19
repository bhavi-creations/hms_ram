<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyGenericModel;
use App\Models\PatientModel; // New import

class Dashboard extends BaseController
{
    protected $medicineModel;
    protected $salesModel;
    protected $billingModel;
    protected $batchModel;
    protected $genericModel;
    protected $patientModel; // New property

    /**
     * Constructor to initialize models.
     */
    public function __construct()
    {
        $this->medicineModel = new PharmacyMedicineModel();
        $this->salesModel    = new PharmacySalesModel();
        $this->billingModel  = new PharmacyBillingModel();
        $this->batchModel    = new PharmacyBatchModel();
        $this->genericModel  = new PharmacyGenericModel();
        $this->patientModel  = new PatientModel(); // New instantiation
    }

    /**
     * Displays the main Pharmacy Dashboard with key metrics.
     */
    public function index()
    {
        // 1. Fetch Total Medicines
        $totalMedicines = $this->medicineModel->countAllResults();

        // 2. Fetch Total Sales for Today
        $today = Time::today()->toDateString();
        $totalSalesToday = $this->salesModel
            ->selectSum('total_amount')
            ->where('DATE(created_at)', $today)
            ->first();

        $totalBillingToday = $this->billingModel
            ->selectSum('total_amount')
            ->where('DATE(created_at)', $today)
            ->first();
        $totalSales = ($totalSalesToday['total_amount'] ?? 0) + ($totalBillingToday['total_amount'] ?? 0);

        // 3. Fetch Low Stock Items
        $lowStockThreshold = 10;
        $lowStockItems = $this->medicineModel
            ->select('pharmacy_medicines.id')
            ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
            ->selectSum('pharmacy_batches.current_stock', 'total_stock')
            ->having('total_stock <', $lowStockThreshold)
            ->groupBy('pharmacy_medicines.id')
            ->countAllResults();

        // 4. Fetch Items Expiring Soon (within the next 6 months)
        $expiringSoonItems = $this->batchModel
            ->where('expiry_date <', Time::now()->addMonths(6)->toDateString())
            ->countAllResults();

        // 5. Fetch Recent Sales Transactions (e.g., last 5)
        // Corrected to join the 'patients' table to get the patient's name
        $recentSales = $this->salesModel
            ->select('pharmacy_sales.invoice_number, pharmacy_sales.total_amount, pharmacy_sales.created_at, CONCAT(patients.first_name, " ", patients.last_name) AS patient_name')
            ->join('patients', 'patients.id = pharmacy_sales.patient_id')
            ->orderBy('pharmacy_sales.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // 6. Fetch Upcoming Expiry Batches (e.g., next 5)
        $expiringSoonBatches = $this->batchModel
            ->select('pharmacy_batches.batch_number, pharmacy_batches.expiry_date, pharmacy_batches.current_stock, pharmacy_generics.generic_name')
            ->join('pharmacy_medicines', 'pharmacy_medicines.id = pharmacy_batches.medicine_id')
            ->join('pharmacy_generics', 'pharmacy_generics.id = pharmacy_medicines.generic_id')
            ->where('expiry_date <', Time::now()->addMonths(6)->toDateString())
            ->orderBy('expiry_date', 'ASC')
            ->limit(5)
            ->findAll();

        // Pass all data to the dashboard view
        $data = [
            'title'                 => 'Pharmacy Dashboard',
            'totalMedicines'        => $totalMedicines,
            'totalSales'            => number_format($totalSales, 2),
            'lowStockItems'         => $lowStockItems,
            'expiringSoonItems'     => $expiringSoonItems,
            'recentSales'           => $recentSales,
            'expiringSoonBatches'   => $expiringSoonBatches,
        ];

        return view('pharmacy/dashboard/index', $data);
    }
}
