<?php

namespace App\Controllers\Pharmacy;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\BaseController;

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacySaleItemModel;
use App\Models\Pharmacy\PharmacyBatchModel;
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyUnitOfMeasureModel;
use App\Models\Pharmacy\PharmacyCategoryModel;
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacyInvoiceSequenceModel;
use App\Models\Pharmacy\PharmacyBillingPaymentModel;
use App\Models\Pharmacy\PharmacyReturnModel;
use App\Models\Pharmacy\PharmacyBrandModel;
use App\Models\Pharmacy\PharmacyGenericModel;
use App\Models\Pharmacy\PharmacySupplierModel;
use App\Models\Pharmacy\PharmacyManufacturerModel;



// Import HMS Core Models
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\UserModel;

class Reports extends BaseController
{
    protected $salesModel;
    protected $batchModel;
    protected $medicineModel;
    protected $returnModel;
    protected $supplierModel;
    protected $manufacturerModel;
    protected $categoryModel;
    protected $billingModel;
    protected $patientModel;
    protected $userModel;

    public function __construct()
    {
        $this->salesModel       = new PharmacySalesModel();
        $this->batchModel       = new PharmacyBatchModel();
        $this->medicineModel    = new PharmacyMedicineModel();
        $this->returnModel      = new PharmacyReturnModel();
        $this->supplierModel    = new PharmacySupplierModel();
        $this->manufacturerModel = new PharmacyManufacturerModel();
        $this->categoryModel    = new PharmacyCategoryModel();
        $this->billingModel     = new PharmacyBillingModel();
        $this->patientModel     = new PatientModel();
        $this->userModel        = new UserModel();
    }

    public function sales($currentType = 'outside_sale')
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');
        $bills = [];

        // Fetch bills based on the current type
        switch ($currentType) {
            case 'outside_sale':
                $bills = $this->salesModel->getOutsideSalesByDateRange($startDate, $endDate);
                break;
            case 'in_hospital':
                $bills = $this->billingModel->getInHospitalSalesByDateRange($startDate, $endDate);
                break;
            case 'patients':
                // This is a summary view. The implementation depends on your schema.
                // Assuming a method exists in PharmacyBillingModel or a custom model for this summary.
                $bills = []; // Placeholder for now
                break;
            default:
                $bills = $this->salesModel->getOutsideSalesByDateRange($startDate, $endDate);
                $currentType = 'outside_sale';
                break;
        }

        $data = [
            'title'       => 'Sales Report',
            'bills'       => $bills,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'currentType' => $currentType,
        ];
        return view('pharmacy/reports/sales', $data);
    }

    /**
     * Displays a single sales invoice.
     * @param string $invoiceNumber
     */
    public function viewInvoice(string $invoiceNumber)
    {
        // Try to fetch from pharmacy_sales table first
        $invoice = $this->salesModel->getInvoiceDetails($invoiceNumber);

        // If not found, try to fetch from pharmacy_billing for in-hospital sales
        if (!$invoice) {
            $invoice = $this->billingModel->getInHospitalInvoiceDetails($invoiceNumber);
        }

        if (!$invoice) {
            throw PageNotFoundException::forPageNotFound('Invoice not found: ' . $invoiceNumber);
        }

        $data = [
            'title'   => 'Invoice: ' . $invoiceNumber,
            'invoice' => $invoice,
        ];

        return view('pharmacy/sales/invoice', $data);
    }




    public function index()
    {
        $data = [
            'title' => 'Pharmacy Reports Dashboard'
        ];
        return view('pharmacy/reports/index', $data);
    }


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
            'expiringBatches' => $expiringBatches,
            'monthsAhead'    => $monthsAhead,
            'expiryCutoff'   => $expiryDateCutoff
        ];
        return view('pharmacy/reports/expiry', $data);
    }


    // public function purchases()
    // {
    //     $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
    //     $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');
    //     $supplierId = $this->request->getGet('supplier_id');

    //     $purchases = $this->purchaseModel
    //         ->select('pharmacy_purchases.*, ps.name as supplier_name, u_ordered.first_name as ordered_by_first_name, u_ordered.last_name as ordered_by_last_name')
    //         ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_purchases.supplier_id')
    //         ->join('users u_ordered', 'u_ordered.id = pharmacy_purchases.ordered_by_user_id')
    //         ->where('purchase_date >=', $startDate . ' 00:00:00')
    //         ->where('purchase_date <=', $endDate . ' 23:59:59')
    //         ->when($supplierId, function ($query, $supplierId) {
    //             return $query->where('supplier_id', $supplierId);
    //         })
    //         ->orderBy('purchase_date', 'DESC')
    //         ->findAll();

    //     $data = [
    //         'title'     => 'Purchases Report',
    //         'purchases' => $purchases,
    //         'startDate' => $startDate,
    //         'endDate'   => $endDate,
    //         'suppliers' => $this->supplierModel->findAll(), // For filter dropdown
    //         'selectedSupplierId' => $supplierId
    //     ];
    //     return view('pharmacy/reports/purchases', $data);
    // }
}
