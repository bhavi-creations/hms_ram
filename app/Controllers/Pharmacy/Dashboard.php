<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

// Import any models you might need for dashboard stats
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacyBatchModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Example: Fetch some quick stats for the dashboard
        $salesModel = new PharmacySalesModel();
        $batchModel = new PharmacyBatchModel();

        // Placeholder for real data fetching logic
        $totalSalesToday = $salesModel->where('DATE(sale_date)', date('Y-m-d'))->countAllResults();
        $expiredBatches = $batchModel->where('expiry_date <', date('Y-m-d'))->countAllResults();
        $lowStockItems = $batchModel->where('current_stock <=', 10)->countAllResults(); // Example low stock threshold

        $data = [
            'title'             => 'Pharmacy Dashboard',
            'totalSalesToday'   => $totalSalesToday,
            'expiredBatches'    => $expiredBatches,
            'lowStockItems'     => $lowStockItems,
            // You can pass more data here for charts, recent activities etc.
        ];

        return view('pharmacy/dashboard/index', $data);
    }
}