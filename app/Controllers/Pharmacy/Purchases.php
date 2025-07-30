<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql; // For direct SQL in update

// Import all necessary Pharmacy Models
use App\Models\Pharmacy\PharmacyPurchaseModel;
use App\Models\Pharmacy\PharmacyPurchaseItemModel;
use App\Models\Pharmacy\PharmacySupplierModel;
use App\Models\Pharmacy\PharmacyMedicineModel;
use App\Models\Pharmacy\PharmacyBatchModel;

class Purchases extends BaseController
{
    protected $purchaseModel;
    protected $purchaseItemModel;
    protected $supplierModel;
    protected $medicineModel;
    protected $batchModel;

    public function __construct()
    {
        // Ensure parent constructor runs for session, etc.
        // parent::__construct();

        $this->purchaseModel     = new PharmacyPurchaseModel();
        $this->purchaseItemModel = new PharmacyPurchaseItemModel();
        $this->supplierModel     = new PharmacySupplierModel();
        $this->medicineModel     = new PharmacyMedicineModel();
        $this->batchModel        = new PharmacyBatchModel();
    }

    /**
     * Displays a list of all purchase orders.
     */
    public function index()
    {
        $purchases = $this->purchaseModel
                            ->select('pharmacy_purchases.*, ps.name as supplier_name, u_ordered.first_name as ordered_by_first_name, u_ordered.last_name as ordered_by_last_name')
                            ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_purchases.supplier_id')
                            ->join('users u_ordered', 'u_ordered.id = pharmacy_purchases.ordered_by_user_id')
                            ->orderBy('purchase_date', 'DESC')
                            ->findAll();

        $data = [
            'title'     => 'Manage Purchases',
            'purchases' => $purchases
        ];
        return view('pharmacy/purchases/index', $data);
    }

    /**
     * Displays the form to create a new purchase order.
     */
    public function create()
    {
        $data = [
            'title'      => 'Create New Purchase Order',
            'suppliers'  => $this->supplierModel->findAll(),
            'medicines'  => $this->medicineModel->findAll(), // For selecting items
            'validation' => service('validation')
        ];
        return view('pharmacy/purchases/create', $data);
    }

    /**
     * Handles the submission of a new purchase order form.
     */
    public function store()
    {
        $rules = [
            'supplier_id'         => 'required|integer',
            'invoice_number'      => 'permit_empty|max_length[100]|is_unique[pharmacy_purchases.invoice_number]',
            'items.*.medicine_id' => 'required|integer',
            'items.*.ordered_quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_purchase_price' => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postData = $this->request->getPost();
        $purchaseItems = $postData['items'];

        $totalAmount = 0;
        foreach ($purchaseItems as $item) {
            $totalAmount += ($item['ordered_quantity'] * $item['unit_purchase_price']);
        }

        $purchaseData = [
            'supplier_id'        => $postData['supplier_id'],
            'purchase_date'      => date('Y-m-d H:i:s'),
            'invoice_number'     => !empty($postData['invoice_number']) ? $postData['invoice_number'] : null,
            'total_amount'       => $totalAmount,
            'status'             => 'pending', // Initial status
            'ordered_by_user_id' => session()->get('user_id'), // User creating the order
            'notes'              => $postData['notes'] ?? null,
        ];

        // Start transaction for atomicity
        $this->purchaseModel->db->transStart();

        try {
            $purchaseId = $this->purchaseModel->insert($purchaseData);
            if (!$purchaseId) {
                throw new \Exception('Failed to create purchase order.');
            }

            foreach ($purchaseItems as $item) {
                $itemSubTotal = $item['ordered_quantity'] * $item['unit_purchase_price'];
                $purchaseItemData = [
                    'purchase_id'         => $purchaseId,
                    'medicine_id'         => $item['medicine_id'],
                    'ordered_quantity'    => $item['ordered_quantity'],
                    'received_quantity'   => 0, // Initially 0
                    'unit_purchase_price' => $item['unit_purchase_price'],
                    'sub_total'           => $itemSubTotal,
                ];
                if (!$this->purchaseItemModel->insert($purchaseItemData)) {
                    throw new \Exception('Failed to add purchase item for medicine ID: ' . $item['medicine_id']);
                }
            }

            $this->purchaseModel->db->transComplete();

            if ($this->purchaseModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed after completion check.');
            }

            return redirect()->to(site_url('pharmacy/purchases/view/' . $purchaseId))->with('success', 'Purchase order created successfully!');

        } catch (\Exception $e) {
            $this->purchaseModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Displays details of a specific purchase order.
     */
    public function view($id = null)
    {
        $purchase = $this->purchaseModel
                            ->select('pharmacy_purchases.*, ps.name as supplier_name, u_ordered.first_name as ordered_by_first_name, u_ordered.last_name as ordered_by_last_name, u_received.first_name as received_by_first_name, u_received.last_name as received_by_last_name')
                            ->join('pharmacy_suppliers ps', 'ps.id = pharmacy_purchases.supplier_id')
                            ->join('users u_ordered', 'u_ordered.id = pharmacy_purchases.ordered_by_user_id')
                            ->join('users u_received', 'u_received.id = pharmacy_purchases.received_by_user_id', 'left') // Left join as received_by can be null
                            ->find($id);

        if (empty($purchase)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found: ' . $id);
        }

        $purchaseItems = $this->purchaseItemModel
                                ->select('pharmacy_purchase_items.*, pm.generic_name, pm.brand_name, pm.strength')
                                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_purchase_items.medicine_id')
                                ->where('purchase_id', $id)
                                ->findAll();

        $data = [
            'title'         => 'Purchase Order Details',
            'purchase'      => $purchase,
            'purchaseItems' => $purchaseItems
        ];
        return view('pharmacy/purchases/view', $data);
    }

    /**
     * Displays form/handles receiving stock for a purchase order.
     */
    public function receiveStock($id = null)
    {
        $purchase = $this->purchaseModel->find($id);
        if (empty($purchase)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found: ' . $id);
        }

        if ($purchase['status'] === 'received') {
            return redirect()->to(site_url('pharmacy/purchases/view/' . $id))->with('info', 'This purchase order has already been fully received.');
        }

        $purchaseItems = $this->purchaseItemModel
                                ->select('pharmacy_purchase_items.*, pm.generic_name, pm.brand_name, pm.strength')
                                ->join('pharmacy_medicines pm', 'pm.id = pharmacy_purchase_items.medicine_id')
                                ->where('purchase_id', $id)
                                ->findAll();

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'received_items.*.item_id'         => 'required|integer',
                'received_items.*.received_quantity' => 'required|integer|greater_than_equal_to[0]',
                'received_items.*.batch_number'    => 'required_with[received_items.*.received_quantity]|max_length[100]',
                'received_items.*.manufacturing_date' => 'required_with[received_items.*.received_quantity]|valid_date',
                'received_items.*.expiry_date'     => 'required_with[received_items.*.received_quantity]|valid_date|after_current_date',
                'received_items.*.selling_price'   => 'required_with[received_items.*.received_quantity]|decimal|greater_than[0]',
            ];

            // Filter out items with 0 received quantity if not strictly required
            $receivedItems = array_filter($this->request->getPost('received_items'), function($item) {
                return (int)$item['received_quantity'] > 0;
            });

            if (empty($receivedItems)) {
                return redirect()->back()->withInput()->with('error', 'No items marked as received. Please enter quantities for items being received.');
            }

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $this->purchaseModel->db->transStart();

            try {
                $allReceived = true; // Flag to check if all items are fully received

                foreach ($receivedItems as $receivedItem) {
                    $purchaseItem = $this->purchaseItemModel->find($receivedItem['item_id']);

                    if (empty($purchaseItem) || $purchaseItem['purchase_id'] != $id) {
                        throw new \Exception('Invalid purchase item for receiving.');
                    }

                    $totalReceivedSoFar = $purchaseItem['received_quantity'];
                    $newlyReceived = (int)$receivedItem['received_quantity'];
                    $orderedQuantity = $purchaseItem['ordered_quantity'];

                    if (($totalReceivedSoFar + $newlyReceived) > $orderedQuantity) {
                        throw new \Exception("Received quantity for medicine " . $purchaseItem['medicine_id'] . " exceeds ordered quantity.");
                    }

                    // Update purchase item's received quantity
                    $this->purchaseItemModel->update($purchaseItem['id'], [
                        'received_quantity' => $totalReceivedSoFar + $newlyReceived
                    ]);

                    // Check if a batch with this medicine_id and batch_number already exists from this supplier
                    $existingBatch = $this->batchModel
                                            ->where('medicine_id', $purchaseItem['medicine_id'])
                                            ->where('batch_number', $receivedItem['batch_number'])
                                            ->first();

                    if ($existingBatch) {
                        // Update existing batch stock
                        $this->batchModel->update($existingBatch['id'], [
                            'current_stock' => new RawSql('current_stock + ' . $newlyReceived),
                            'selling_price' => $receivedItem['selling_price'] // Update selling price if needed
                        ]);
                        $batchId = $existingBatch['id'];
                    } else {
                        // Create new batch
                        $batchData = [
                            'medicine_id'        => $purchaseItem['medicine_id'],
                            'batch_number'       => $receivedItem['batch_number'],
                            'manufacturing_date' => $receivedItem['manufacturing_date'],
                            'expiry_date'        => $receivedItem['expiry_date'],
                            'initial_quantity'   => $newlyReceived, // Initial for this new batch
                            'current_stock'      => $newlyReceived,
                            'purchase_price'     => $purchaseItem['unit_purchase_price'], // Use price from purchase item
                            'selling_price'      => $receivedItem['selling_price'],
                            'supplier_id'        => $purchase['supplier_id'],
                            'status'             => 'available'
                        ];
                        $batchId = $this->batchModel->insert($batchData);
                        if (!$batchId) {
                            throw new \Exception('Failed to create new batch for medicine ID: ' . $purchaseItem['medicine_id']);
                        }
                    }

                    // Link the batch_id to the purchase_item IF it's not already linked (can be updated later)
                    // If a purchase item is received in multiple batches, this part would need more advanced logic
                    if (empty($purchaseItem['batch_id'])) {
                         $this->purchaseItemModel->update($purchaseItem['id'], ['batch_id' => $batchId]);
                    }

                    if (($totalReceivedSoFar + $newlyReceived) < $orderedQuantity) {
                        $allReceived = false; // Not all items fully received
                    }
                }

                // Update purchase order status
                $newStatus = $allReceived ? 'received' : 'partially_received';
                $this->purchaseModel->update($id, [
                    'status'            => $newStatus,
                    'received_by_user_id' => session()->get('user_id'),
                    'received_at'       => date('Y-m-d H:i:s')
                ]);

                $this->purchaseModel->db->transComplete();

                if ($this->purchaseModel->db->transStatus() === false) {
                    throw new \Exception('Transaction failed after completion check.');
                }

                return redirect()->to(site_url('pharmacy/purchases/view/' . $id))->with('success', 'Stock received and updated successfully!');

            } catch (\Exception $e) {
                $this->purchaseModel->db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Error receiving stock: ' . $e->getMessage());
            }
        }

        // For GET request: show the receiving form
        $data = [
            'title'         => 'Receive Stock for PO ' . $purchase['invoice_number'],
            'purchase'      => $purchase,
            'purchaseItems' => $purchaseItems,
            'validation'    => service('validation')
        ];
        return view('pharmacy/purchases/receive_stock', $data);
    }

    /**
     * Placeholder for deleting a purchase order.
     * Consider soft deletes or restrictions if items have been received.
     */
    public function delete($id = null)
    {
        $purchase = $this->purchaseModel->find($id);
        if (empty($purchase)) {
            return redirect()->to(site_url('pharmacy/purchases'))->with('error', 'Purchase order not found.');
        }

        // Check if any items have been received before allowing deletion
        if ($purchase['status'] === 'received' || $purchase['status'] === 'partially_received') {
            return redirect()->to(site_url('pharmacy/purchases'))->with('error', 'Cannot delete purchase order with received items. Consider cancelling or adjusting.');
        }

        $this->purchaseModel->db->transStart();
        try {
            // Delete associated purchase items first
            $this->purchaseItemModel->where('purchase_id', $id)->delete();
            // Then delete the purchase order itself
            $this->purchaseModel->delete($id);

            $this->purchaseModel->db->transComplete();
            if ($this->purchaseModel->db->transStatus() === false) {
                throw new \Exception('Failed to delete purchase order due to transaction error.');
            }

            return redirect()->to(site_url('pharmacy/purchases'))->with('success', 'Purchase order deleted successfully.');
        } catch (\Exception $e) {
            $this->purchaseModel->db->transRollback();
            return redirect()->to(site_url('pharmacy/purchases'))->with('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }
}