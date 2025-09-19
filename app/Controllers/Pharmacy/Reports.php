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
use App\Models\Pharmacy\PharmacyPurchaseModel;
use CodeIgniter\I18n\Time;




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
    protected $purchaseModel;
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
        $this->purchaseModel     = new PharmacyPurchaseModel();
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
        $lowStockThreshold = $this->request->getGet('low_stock_threshold') ?? 10;

        $stockData = $this->medicineModel
            ->select('
                pharmacy_medicines.id,
                pharmacy_generics.generic_name,
                pharmacy_brands.brand_name,
                pharmacy_medicines.strength,
                pharmacy_medicines.reorder_level,
                pharmacy_manufacturers.name AS manufacturer_name,
                pharmacy_categories.name AS category_name,
                SUM(pharmacy_batches.current_stock) as total_stock,
                COUNT(pharmacy_batches.id) as num_batches
            ')
            ->join('pharmacy_generics', 'pharmacy_generics.id = pharmacy_medicines.generic_id', 'left')
            ->join('pharmacy_brands', 'pharmacy_brands.id = pharmacy_medicines.brand_id', 'left')
            ->join('pharmacy_manufacturers', 'pharmacy_manufacturers.id = pharmacy_medicines.manufacturer_id', 'left')
            ->join('pharmacy_categories', 'pharmacy_categories.id = pharmacy_medicines.category_id', 'left')
            ->join('pharmacy_batches', 'pharmacy_batches.medicine_id = pharmacy_medicines.id', 'left')
            ->groupBy('pharmacy_medicines.id')
            ->orderBy('total_stock', 'ASC')
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
        // Query to get expiring and expired batches
        // The error you encountered was because the query was likely trying to access
        // 'pm.generic_name' directly. We must join the 'pharmacy_generics' table.
        $batches = $this->batchModel
            ->select('
                pharmacy_batches.id,
                pharmacy_batches.batch_number,
                pharmacy_batches.expiry_date,
                pharmacy_batches.current_stock,
                pharmacy_generics.generic_name,
                pharmacy_manufacturers.name AS manufacturer_name
            ')
            ->join('pharmacy_medicines', 'pharmacy_medicines.id = pharmacy_batches.medicine_id')
            ->join('pharmacy_generics', 'pharmacy_generics.id = pharmacy_medicines.generic_id')
            ->join('pharmacy_manufacturers', 'pharmacy_manufacturers.id = pharmacy_medicines.manufacturer_id')
            // Filter for batches expiring in the next 6 months or already expired
            ->where('pharmacy_batches.expiry_date <', Time::now()->addMonths(6)->toDateString())
            ->orderBy('pharmacy_batches.expiry_date', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Expiry Reports',
            'batches' => $batches,
        ];

        return view('pharmacy/reports/expiry', $data);
    }


    public function purchases()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');
        $supplierId = $this->request->getGet('supplier_id');

        $purchases = $this->purchaseModel
            ->select('
                pharmacy_purchases.*,
                ps.name as supplier_name,
                u_ordered.first_name as ordered_by_first_name,
                u_ordered.last_name as ordered_by_last_name
            ')
            ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_purchases.supplier_id')
            ->join('users u_ordered', 'u_ordered.id = pharmacy_purchases.ordered_by_user_id')
            ->where('purchase_date >=', $startDate . ' 00:00:00')
            ->where('purchase_date <=', $endDate . ' 23:59:59')
            ->when($supplierId, function ($query, $supplierId) {
                return $query->where('supplier_id', $supplierId);
            })
            ->orderBy('purchase_date', 'DESC')
            ->findAll();

        $suppliers = $this->supplierModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'         => 'Purchase Reports',
            'purchases'     => $purchases,
            'suppliers'     => $suppliers,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'supplierId'    => $supplierId,
        ];

        return view('pharmacy/reports/purchases', $data);
    }
}
