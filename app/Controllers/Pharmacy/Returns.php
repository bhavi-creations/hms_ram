<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql; // For direct SQL in update

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacyReturnModel;
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacySaleItemModel;
use App\Models\Pharmacy\PharmacyBatchModel; // To update stock
use App\Models\Pharmacy\PharmacyMedicineModel; // For medicine details
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacyBillingPaymentModel;

class Returns extends BaseController
{
    protected $returnModel;
    protected $salesModel;
    protected $saleItemModel;
    protected $batchModel;
    protected $medicineModel;
    protected $billingModel;  // Add this
    protected $billingPaymentModel;

    public function __construct()
    {
        $this->returnModel = new PharmacyReturnModel();
        $this->salesModel = new PharmacySalesModel();
        $this->saleItemModel = new PharmacySaleItemModel();
        $this->batchModel = new PharmacyBatchModel();
        $this->medicineModel = new PharmacyMedicineModel();
        $this->billingModel = new PharmacyBillingModel();   // Initialize it
        $this->billingPaymentModel = new PharmacyBillingPaymentModel();
    }

    /**
     * Displays a list of all sales returns (pending, approved, rejected).
     */
    public function index()
    {
        // Select columns, joining pharmacy_sales and pharmacy_billings
        $returns = $this->returnModel
            ->select('
            pharmacy_returns.*,
            pharmacy_returns.quantity_returned as quantity,
            pharmacy_returns.return_reason as reason,
            pharmacy_returns.approval_status as status,
            pm.generic_name as medicine_name,
            pb.batch_number,
            ps.invoice_number,
            pbill.bill_id as billing_invoice_number
        ')
            ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id')
            ->join('pharmacy_batches pb', 'pb.id = pharmacy_returns.batch_id', 'left')
            ->join('pharmacy_sales ps', 'ps.id = pharmacy_returns.sale_id', 'left')
            ->join('pharmacy_billings pbill', 'pbill.id = pharmacy_returns.billing_id', 'left')
            ->orderBy('return_date', 'DESC')
            ->findAll();

        // Prepare returns with correct invoice number
        foreach ($returns as &$return) {
            // Prefer billing invoice if exists; else sales invoice
            $return['invoice_number'] = $return['billing_invoice_number'] ?? $return['invoice_number'] ?? 'N/A';
        }

        $data = [
            'title' => 'Manage Sales Returns',
            'returns' => $returns
        ];
        return view('pharmacy/returns/index', $data);
    }


    public function create()
    {
        $data = [
            'title'      => 'Initiate New Return',
            'validation' => service('validation')
        ];

        // You might want to add AJAX search for sales/sale items here in the view
        return view('pharmacy/returns/create', $data);
    }
    public function store()
    {
        log_message('debug', 'POST DATA: ' . json_encode($this->request->getPost()));

        $rules = [
            'sale_item_id'       => 'required|integer|is_not_unique[pharmacy_sale_items.id]',
            'quantity_returned'  => 'required|integer|greater_than[0]',
            'return_reason'      => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $saleId          = $this->request->getPost('sale_id');
        $billingId       = $this->request->getPost('billing_id');
        $saleItemId      = $this->request->getPost('sale_item_id');
        $quantityReturned = $this->request->getPost('quantity_returned');
        $returnReason    = $this->request->getPost('return_reason');
        $notes           = $this->request->getPost('notes');

        if (empty($saleId) && empty($billingId)) {
            return redirect()->back()->withInput()->with('error', 'Either Sale ID or Billing ID must be provided.');
        }

        $saleItem = $this->saleItemModel->find($saleItemId);

        if (empty($saleItem)) {
            return redirect()->back()->withInput()->with('error', 'Invalid Sale Item selected for return.');
        }

        $validSaleItem = false;
        if (!empty($saleItem['sale_id']) && intval($saleItem['sale_id']) === intval($saleId)) {
            $validSaleItem = true;
        }
        if (!empty($saleItem['billing_id']) && intval($saleItem['billing_id']) === intval($billingId)) {
            $validSaleItem = true;
        }

        if (!$validSaleItem) {
            return redirect()->back()->withInput()->with('error', 'Invalid Sale Item selected for return.');
        }

        if ($quantityReturned > $saleItem['quantity']) {
            return redirect()->back()->withInput()->with('error', 'Quantity returned cannot exceed the original sold quantity (' . $saleItem['quantity'] . ').');
        }

        $cleanSaleId = ($saleId !== '') ? $saleId : null;
        $cleanBillingId = ($billingId !== '') ? $billingId : null;

        $returnData = [
            'sale_id'           => $cleanSaleId,
            'billing_id'        => $cleanBillingId,
            'sale_item_id'      => $saleItemId,
            'medicine_id'       => $saleItem['medicine_id'],
            'batch_id'          => $saleItem['batch_id'],
            'quantity_returned' => $quantityReturned,
            'return_date'       => date('Y-m-d H:i:s'),
            'return_reason'     => $returnReason,
            'requested_by_user_id' => session()->get('user_id'),
            'approval_status'   => 'pending',
            'notes'             => $notes,
        ];

        if ($this->returnModel->insert($returnData)) {
            return redirect()->to(site_url('pharmacy/returns'))->with('success', 'Return request submitted successfully. Awaiting approval.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to submit return request.');
        }
    }




    public function approve($id = null)
    {
        $returnRequest = $this->returnModel
            ->select('pharmacy_returns.*, ps.invoice_number, pm.generic_name as medicine_name, pm.brand_name, pb.batch_number, psale_item.quantity as sold_quantity')
            ->join('pharmacy_sales ps', 'ps.id = pharmacy_returns.sale_id', 'left')
            ->join('pharmacy_sale_items psale_item', 'psale_item.id = pharmacy_returns.sale_item_id', 'left')
            ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id', 'left')
            ->join('pharmacy_batches pb', 'pb.id = pharmacy_returns.batch_id', 'left')
            ->where('pharmacy_returns.id', $id)
            ->first();

        if (empty($returnRequest)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Return request not found: ' . $id);
        }

        if ($returnRequest['approval_status'] !== 'pending') {
            return redirect()->to(site_url('pharmacy/returns'))->with('info', 'This return request has already been processed.');
        }

        $data = [
            'title' => 'Approve/Reject Return Request',
            'returnRequest' => $returnRequest,
            'validation' => service('validation')
        ];
        return view('pharmacy/returns/approve', $data);
    }

    public function processApproval($id = null)
    {
        $returnRequest = $this->returnModel->find($id);
        if (empty($returnRequest) || $returnRequest['approval_status'] !== 'pending') {
            return redirect()->to(site_url('pharmacy/returns'))->with('error', 'Return request not found or already processed.');
        }

        $rules = [
            'approval_status' => 'required|in_list[approved,rejected]',
            'approval_notes'  => 'permit_empty|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newStatus = $this->request->getPost('approval_status');
        $approvalNotes = $this->request->getPost('approval_notes');

        $this->returnModel->db->transStart();

        try {
            $approvalNotes = trim($this->request->getPost('approval_notes'));
            $existingNotes = trim($returnRequest['notes'] ?? '');

            // Append approval notes only if not empty
            if ($approvalNotes !== '') {
                $updatedNotes = $existingNotes . "\nApproval/Rejection Notes: " . $approvalNotes;
            } else {
                $updatedNotes = $existingNotes;
            }

            $updateData = [
                'approval_status'      => $newStatus,
                'approved_by_user_id'  => session()->get('user_id'),
                'approval_date'        => date('Y-m-d H:i:s'),
                'notes'                => $updatedNotes,
            ];

            if (!$this->returnModel->update($id, $updateData)) {
                throw new \Exception('Failed to update return request status.');
            }

            if ($newStatus === 'approved') {
                // Update batch stock
                $batch = $this->batchModel->find($returnRequest['batch_id']);
                if (empty($batch)) {
                    throw new \Exception('Associated batch not found for stock adjustment.');
                }
                $this->batchModel->update($returnRequest['batch_id'], [
                    'current_stock' => new RawSql('current_stock + ' . $returnRequest['quantity_returned']),
                ]);

                // Update sale item quantity
                $saleItem = $this->saleItemModel->find($returnRequest['sale_item_id']);
                if (empty($saleItem)) {
                    throw new \Exception('Associated sale item not found for quantity adjustment.');
                }
                $newQuantity = max(0, $saleItem['quantity'] - $returnRequest['quantity_returned']);
                $this->saleItemModel->update($returnRequest['sale_item_id'], ['quantity' => $newQuantity]);

                // Update totals for sale or billing
                if (isset($returnRequest['billing_id']) && !empty($returnRequest['billing_id'])) {
                    // In-hospital billing update
                    $billingItems = $this->saleItemModel->where('billing_id', $returnRequest['billing_id'])->findAll();
                    $totalAmount = 0;
                    $netAmount = 0;
                    foreach ($billingItems as $item) {
                        $itemTotal = $item['quantity'] * $item['unit_selling_price'];
                        $itemSubTotal = $itemTotal - ($item['discount_per_item'] ?? 0);
                        $totalAmount += $itemTotal;
                        $netAmount += $itemSubTotal;
                    }

                    if (!$this->billingModel->update($returnRequest['billing_id'], [
                        'total_amount' => $totalAmount,
                        'net_amount' => $netAmount,
                    ])) {
                        throw new \Exception('Failed to update billing totals.');
                    }

                    // Recalculate total paid amount
                    $billingPaymentModel = new PharmacyBillingPaymentModel();
                    $payments = $billingPaymentModel->where('bill_id', $returnRequest['billing_id'])->findAll();

                    $totalPaid = 0;
                    foreach ($payments as $payment) {
                        $totalPaid += $payment['payment_amount'];
                    }

                    // Update due amount
                    $newDueAmount = max(0, $totalAmount - $totalPaid);

                    if (!$this->billingModel->update($returnRequest['billing_id'], [
                        'due_amount' => $newDueAmount,
                    ])) {
                        throw new \Exception('Failed to update billing due amount.');
                    }

                    $billingRecord = $this->billingModel->find($returnRequest['billing_id']);
                    $invoiceNumber = $billingRecord['bill_id'] ?? null;
                    $redirectUrl = $invoiceNumber
                        ? site_url('pharmacy/sales/invoice/' . $invoiceNumber)
                        : site_url('pharmacy/returns');
                } elseif (isset($returnRequest['sale_id']) && !empty($returnRequest['sale_id'])) {
                    // Outside sale update
                    $saleItems = $this->saleItemModel->where('sale_id', $returnRequest['sale_id'])->findAll();
                    $totalAmount = 0;
                    $netAmount = 0;
                    foreach ($saleItems as $item) {
                        $itemTotal = $item['quantity'] * $item['unit_selling_price'];
                        $itemSubTotal = $itemTotal - ($item['discount_per_item'] ?? 0);
                        $totalAmount += $itemTotal;
                        $netAmount += $itemSubTotal;
                    }

                    if (!$this->salesModel->update($returnRequest['sale_id'], [
                        'total_amount' => $totalAmount,
                        'net_amount' => $netAmount,
                    ])) {
                        throw new \Exception('Failed to update sale totals.');
                    }

                    $invoiceNumber = $this->salesModel->find($returnRequest['sale_id'])['invoice_number'] ?? null;
                    $redirectUrl = $invoiceNumber
                        ? site_url('pharmacy/sales/invoice/' . $invoiceNumber)
                        : site_url('pharmacy/returns');
                } else {
                    $redirectUrl = site_url('pharmacy/returns');
                }
            } else {
                $redirectUrl = site_url('pharmacy/returns');
            }

            $this->returnModel->db->transComplete();

            if ($this->returnModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed during commit.');
            }

            // Debugging logs
            log_message('debug', 'Redirecting to: ' . $redirectUrl);
            log_message('debug', 'Return approval info - billing_id: ' . ($returnRequest['billing_id'] ?? 'NULL') . ', sale_id: ' . ($returnRequest['sale_id'] ?? 'NULL'));
            log_message('debug', 'Invoice numbers - billing invoice: ' . ($billingRecord['bill_id'] ?? 'NULL') . ', sale invoice: ' . ($invoiceNumber ?? 'NULL'));
            log_message('debug', 'Redirect URL chosen: ' . $redirectUrl);

            return redirect()->to($redirectUrl)->with('success', 'Return request ' . $newStatus . ' successfully.');
        } catch (\Exception $e) {
            $this->returnModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error processing return approval: ' . $e->getMessage());
        }
    }


    public function getMedicinesByInvoice($invoiceNumber = null)
    {
        // Only accept AJAX requests and ensure invoice number is provided
        if (!$this->request->isAJAX() || $invoiceNumber === null) {
            log_message('error', 'Invalid AJAX request or missing invoice number');
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
        }

        $invoiceNumber = urldecode($invoiceNumber);
        log_message('debug', "Fetching medicines for invoice: {$invoiceNumber}");

        $medicines = [];

        // Try to fetch sale for outside sale invoices
        $outsideSale = $this->salesModel->where('invoice_number', $invoiceNumber)->first();

        if ($outsideSale) {
            log_message('debug', "Outside sale found with ID: {$outsideSale['id']}");
            $saleItems = $this->saleItemModel->where('sale_id', $outsideSale['id'])->findAll();
            log_message('debug', 'Found ' . count($saleItems) . ' sale items for outside sale');
            foreach ($saleItems as $item) {
                $medicine = $this->medicineModel->find($item['medicine_id']);
                $batch = $this->batchModel->find($item['batch_id']);
                if ($medicine && $batch) {
                    $medicines[] = [
                        'sale_item_id' => $item['id'],
                        'medicine_name' => $medicine['generic_name'],
                        'batch_number' => $batch['batch_number'],
                        'quantity' => $item['quantity'], // Total sold quantity
                        'sale_id' => $item['sale_id'], // Add this for JS use
                    ];
                } else {
                    log_message('warning', "Medicine or batch not found for sale item ID: {$item['id']}");
                }
            }
        } else {
            // Try in-hospital billing
            $billing = $this->billingModel->where('bill_id', $invoiceNumber)->first();
            if ($billing) {
                log_message('debug', "In-hospital billing found with ID: {$billing['id']}");
                $billingItems = $this->saleItemModel->where('billing_id', $billing['id'])->findAll();
                log_message('debug', 'Found ' . count($billingItems) . ' sale items for in-hospital billing');
                foreach ($billingItems as $item) {
                    $medicine = $this->medicineModel->find($item['medicine_id']);
                    $batch = $this->batchModel->find($item['batch_id']);
                    if ($medicine && $batch) {
                        $medicines[] = [
                            'sale_item_id' => $item['id'],
                            'medicine_name' => $medicine['generic_name'],
                            'batch_number' => $batch['batch_number'],
                            'quantity' => $item['quantity'],
                            'billing_id' => $item['billing_id'], // Add this for JS use
                        ];
                    } else {
                        log_message('warning', "Medicine or batch not found for billing item ID: {$item['id']}");
                    }
                }
            } else {
                log_message('debug', "No outside sale or in-hospital billing found for invoice: {$invoiceNumber}");
            }
        }


        if (empty($medicines)) {
            return $this->response->setJSON([
                'status' => 'success',
                'medicines' => [],
                'message' => 'No medicines found for this invoice'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'medicines' => $medicines
        ]);
    }
}
