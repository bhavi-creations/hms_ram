<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql; // For direct SQL in update

// Import Pharmacy Models
use App\Models\Pharmacy\PharmacyReturnModel;
use App\Models\Pharmacy\PharmacySalesModel;
use App\Models\Pharmacy\PharmacySaleItemModel;
use App\Models\Pharmacy\PharmacyBatchModel; // To update stock
use App\Models\Pharmacy\PharmacyMedicineModel; // For medicine details

// Import User Model if needed for return approval users, etc.
// use App\Models\UserModel; // Assuming your UserModel is in App\Models

class Returns extends BaseController
{
    protected $returnModel;
    protected $salesModel;
    protected $saleItemModel;
    protected $batchModel;
    protected $medicineModel;
    // protected $userModel; // If you need to fetch user details for approval

    public function __construct()
    {
        // parent::__construct();  

        $this->returnModel   = new PharmacyReturnModel();
        $this->salesModel    = new PharmacySalesModel();
        $this->saleItemModel = new PharmacySaleItemModel();
        $this->batchModel    = new PharmacyBatchModel();
        $this->medicineModel = new PharmacyMedicineModel();
        // $this->userModel     = new UserModel();
    }

    /**
     * Displays a list of all sales returns (pending, approved, rejected).
     */
    public function index()
    {
        $returns = $this->returnModel
                        ->select('pharmacy_returns.*, ps.invoice_number, pm.generic_name, pm.brand_name, pb.batch_number')
                        ->join('pharmacy_sales ps', 'ps.id = pharmacy_returns.sale_id', 'left')
                        ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id')
                        ->join('pharmacy_batches pb', 'pb.id = pharmacy_returns.batch_id', 'left')
                        ->orderBy('return_date', 'DESC')
                        ->findAll();

        $data = [
            'title'   => 'Manage Sales Returns',
            'returns' => $returns
        ];
        return view('pharmacy/returns/index', $data);
    }

    /**
     * Shows the form to initiate a new return for a specific sale item.
     * User can search by invoice or directly input item details.
     */
    public function create()
    {
        $data = [
            'title'      => 'Initiate New Return',
            'validation' => service('validation')
        ];

        // You might want to add AJAX search for sales/sale items here in the view
        return view('pharmacy/returns/create', $data);
    }

    /**
     * Handles the submission of a new return request.
     */
    public function store()
    {
        $rules = [
            'sale_id'           => 'required|integer|is_not_unique[pharmacy_sales.id]', // Ensure sale exists
            'sale_item_id'      => 'required|integer|is_not_unique[pharmacy_sale_items.id]', // Ensure sale item exists
            'quantity_returned' => 'required|integer|greater_than[0]',
            'return_reason'     => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $saleId          = $this->request->getPost('sale_id');
        $saleItemId      = $this->request->getPost('sale_item_id');
        $quantityReturned= $this->request->getPost('quantity_returned');
        $returnReason    = $this->request->getPost('return_reason');
        $notes           = $this->request->getPost('notes');

        $saleItem = $this->saleItemModel->find($saleItemId);

        if (empty($saleItem) || $saleItem['sale_id'] != $saleId) {
            return redirect()->back()->withInput()->with('error', 'Invalid Sale Item selected for return.');
        }

        if ($quantityReturned > $saleItem['quantity']) {
            return redirect()->back()->withInput()->with('error', 'Quantity returned cannot exceed the original sold quantity (' . $saleItem['quantity'] . ').');
        }

        $returnData = [
            'sale_id'            => $saleId,
            'sale_item_id'       => $saleItemId,
            'medicine_id'        => $saleItem['medicine_id'], // From sale item
            'batch_id'           => $saleItem['batch_id'],    // From sale item
            'quantity_returned'  => $quantityReturned,
            'return_date'        => date('Y-m-d H:i:s'),
            'return_reason'      => $returnReason,
            'requested_by_user_id' => session()->get('user_id'), // User initiating the return
            'approval_status'    => 'pending', // Requires approval
            'notes'              => $notes,
        ];

        if ($this->returnModel->insert($returnData)) {
            return redirect()->to(site_url('pharmacy/returns'))->with('success', 'Return request submitted successfully. Awaiting approval.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to submit return request.');
        }
    }

    /**
     * Displays a return request for approval/rejection.
     * Only accessible by users with appropriate roles (e.g., Pharmacy_Manager).
     */
    public function approve($id = null)
    {
        $returnRequest = $this->returnModel
                                ->select('pharmacy_returns.*, ps.invoice_number, pm.generic_name, pm.brand_name, pb.batch_number, psale_item.quantity as sold_quantity')
                                ->join('pharmacy_sales ps', 'ps.id = pharmacy_returns.sale_id')
                                ->join('pharmacy_sale_items psale_item', 'psale_item.id = pharmacy_returns.sale_item_id')
                                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_returns.medicine_id')
                                ->join('pharmacy_batches pb', 'pb.id = pharmacy_returns.batch_id', 'left')
                                ->find($id);

        if (empty($returnRequest)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Return request not found: ' . $id);
        }

        if ($returnRequest['approval_status'] !== 'pending') {
            return redirect()->to(site_url('pharmacy/returns'))->with('info', 'This return request has already been processed.');
        }

        $data = [
            'title'         => 'Approve/Reject Return Request',
            'returnRequest' => $returnRequest,
            'validation'    => service('validation')
        ];
        return view('pharmacy/returns/approve', $data);
    }

    /**
     * Processes the approval or rejection of a return request.
     */
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

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newStatus = $this->request->getPost('approval_status');
        $approvalNotes = $this->request->getPost('approval_notes');

        $this->returnModel->db->transStart();

        try {
            $updateData = [
                'approval_status'   => $newStatus,
                'approved_by_user_id' => session()->get('user_id'), // User approving/rejecting
                'approval_date'     => date('Y-m-d H:i:s'),
                'notes'             => $returnRequest['notes'] . "\nApproval/Rejection Notes: " . $approvalNotes // Append notes
            ];

            if (!$this->returnModel->update($id, $updateData)) {
                throw new \Exception('Failed to update return request status.');
            }

            // If approved, add stock back to batch
            if ($newStatus === 'approved') {
                $batch = $this->batchModel->find($returnRequest['batch_id']);
                if (empty($batch)) {
                    throw new \Exception('Associated batch not found for stock adjustment.');
                }
                $this->batchModel->update($returnRequest['batch_id'], [
                    'current_stock' => new RawSql('current_stock + ' . $returnRequest['quantity_returned'])
                ]);

                // Optionally, update the original sale's total amounts if full refund is given
                // This would be complex and depend on your refund policy (partial refund, store credit etc.)
                // For simplicity, we just update stock and mark return as approved.
            }

            $this->returnModel->db->transComplete();

            if ($this->returnModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/returns'))->with('success', 'Return request ' . $newStatus . ' successfully.');

        } catch (\Exception $e) {
            $this->returnModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error processing return approval: ' . $e->getMessage());
        }
    }
}