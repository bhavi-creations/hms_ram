<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

// Import all necessary Pharmacy Models for reporting
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacyPurchaseModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyReturnModel;
use App\Models\Pharmacy\PharmacySupplierModel;
use App\Models\Pharmacy\PharmacyManufacturerModel;
use App\Models\Pharmacy\PharmacyCategoryModel;

class Reports extends BaseController
{
    protected $salesModel;
    protected $purchaseModel;
    protected $batchModel;
    protected $medicineModel;
    protected $returnModel;
    protected $supplierModel;
    protected $manufacturerModel;
    protected $categoryModel;

    public function __construct()
    {
        // parent::__construct();  

        $this->salesModel        = new PharmacySalesModel();
        $this->purchaseModel     = new PharmacyPurchaseModel();
        $this->batchModel        = new PharmacyBatchModel();
        $this->medicineModel     = new PharmacyMedicineModel();
        $this->returnModel       = new PharmacyReturnModel();
        $this->supplierModel     = new PharmacySupplierModel();
        $this->manufacturerModel = new PharmacyManufacturerModel();
        $this->categoryModel     = new PharmacyCategoryModel();
    }

    /**
     * Displays the main reports dashboard with links to specific reports.
     */
    public function index()
    {
        $data = [
            'title' => 'Pharmacy Reports Dashboard'
        ];
        return view('pharmacy/reports/index', $data);
    }

    /**
     * Generates and displays a sales report.
     * Can include filters like date range, sales person, patient type etc.
     */
    public function sales()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');

        $sales = $this->salesModel
                      ->select('pharmacy_sales.*, u.first_name as sales_person_first_name, u.last_name as sales_person_last_name')
                      ->join('users u', 'u.id = pharmacy_sales.sales_person_id')
                      ->where('sale_date >=', $startDate . ' 00:00:00')
                      ->where('sale_date <=', $endDate . ' 23:59:59')
                      ->orderBy('sale_date', 'DESC')
                      ->findAll();

        $data = [
            'title'     => 'Sales Report',
            'sales'     => $sales,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ];
        return view('pharmacy/reports/sales', $data);
    }

    /**
     * Generates and displays a stock report.
     * Can include filters for medicine, category, manufacturer, low stock etc.
     */
    public function stock()
    {
        $lowStockThreshold = $this->request->getGet('low_stock_threshold') ?? 10; // Default threshold

        // Example: Get current stock for all medicines by combining batch data
        $stockData = $this->medicineModel
                          ->select('pharmacy_medicines.id, pharmacy_medicines.generic_name, pharmacy_medicines.brand_name, pharmacy_medicines.strength, pharmacy_medicines.reorder_level,
                                    pm.name as manufacturer_name, pc.name as category_name,
                                    SUM(pb.current_stock) as total_stock, COUNT(pb.id) as num_batches')
                          ->join('pharmacy_batches pb', 'pb.medicine_id = pharmacy_medicines.id', 'left')
                          ->join('pharmacy_manufacturers pm', 'pm.id = pharmacy_medicines.manufacturer_id')
                          ->join('pharmacy_categories pc', 'pc.id = pharmacy_medicines.category_id')
                          ->groupBy('pharmacy_medicines.id')
                          ->findAll();

        $data = [
            'title'             => 'Current Stock Report',
            'stockData'         => $stockData,
            'lowStockThreshold' => $lowStockThreshold
        ];
        return view('pharmacy/reports/stock', $data);
    }

    /**
     * Generates and displays an expiry report for batches.
     * Can include filters for date range (e.g., expiring in next X months).
     */
    public function expiry()
    {
        $monthsAhead = $this->request->getGet('months_ahead') ?? 3; // Default: next 3 months
        $expiryDateCutoff = date('Y-m-d', strtotime('+' . $monthsAhead . ' months'));

        $expiringBatches = $this->batchModel
                                ->select('pharmacy_batches.*, pm.generic_name, pm.brand_name, pm.strength, ps.name as supplier_name')
                                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_batches.medicine_id')
                                ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_batches.supplier_id', 'left')
                                ->where('expiry_date <=', $expiryDateCutoff)
                                ->where('current_stock >', 0) // Only show if there's stock
                                ->orderBy('expiry_date', 'ASC')
                                ->findAll();

        $data = [
            'title'          => 'Medicine Expiry Report',
            'expiringBatches'=> $expiringBatches,
            'monthsAhead'    => $monthsAhead,
            'expiryCutoff'   => $expiryDateCutoff
        ];
        return view('pharmacy/reports/expiry', $data);
    }

    /**
     * Generates and displays a purchases report.
     * Can include filters like date range, supplier, status.
     */
    public function purchases()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');
        $supplierId= $this->request->getGet('supplier_id');

        $purchases = $this->purchaseModel
                          ->select('pharmacy_purchases.*, ps.name as supplier_name, u_ordered.first_name as ordered_by_first_name, u_ordered.last_name as ordered_by_last_name')
                          ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_purchases.supplier_id')
                          ->join('users u_ordered', 'u_ordered.id = pharmacy_purchases.ordered_by_user_id')
                          ->where('purchase_date >=', $startDate . ' 00:00:00')
                          ->where('purchase_date <=', $endDate . ' 23:59:59')
                          ->when($supplierId, function($query, $supplierId) {
                              return $query->where('supplier_id', $supplierId);
                          })
                          ->orderBy('purchase_date', 'DESC')
                          ->findAll();

        $data = [
            'title'     => 'Purchases Report',
            'purchases' => $purchases,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'suppliers' => $this->supplierModel->findAll(), // For filter dropdown
            'selectedSupplierId' => $supplierId
        ];
        return view('pharmacy/reports/purchases', $data);
    }

    // You can add more specific reports as needed, e.g., low stock report, sales by medicine, etc.
}