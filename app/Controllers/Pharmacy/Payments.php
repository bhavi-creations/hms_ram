<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyBillingModel;
use App\Models\Pharmacy\PharmacyBillingPaymentModel;

class Payments extends BaseController
{
    protected $billingModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->billingModel = new PharmacyBillingModel();
        $this->paymentModel = new PharmacyBillingPaymentModel();
    }

    public function makePayment($billId)
    {
        $bill = $this->billingModel->where('bill_id', $billId)->first();
        if (!$bill) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Bill not found.');
        }

        // Calculate due amount
        $payments = $this->paymentModel->where('bill_id', $billId)->findAll();
        $totalPaid = array_sum(array_column($payments, 'payment_amount'));
        $dueAmount = $bill['total_amount'] - $totalPaid;

        return view('pharmacy/payments/make_payment', [
            'bill' => $bill,
            'dueAmount' => $dueAmount,
        ]);
    }

    public function processPayment()
    {
        $post = $this->request->getPost();

        $billId = $post['bill_id'];
        $paymentAmount = (float)$post['payment_amount'];
        $paymentMethod = $post['payment_method'];

        $bill = $this->billingModel->where('bill_id', $billId)->first();
        if (!$bill) {
            return redirect()->back()->with('error', 'Invalid bill.');
        }


        $payments = $this->paymentModel->where('bill_id', $billId)->findAll();
        $totalPaid = array_sum(array_column($payments, 'payment_amount'));
        $dueAmount = $bill['total_amount'] - $totalPaid;

        if ($paymentAmount <= 0 || $paymentAmount > $dueAmount) {
            return redirect()->back()->with('error', 'Invalid payment amount.');
        }

        // Insert payment record
        $this->paymentModel->insert([
            'bill_id' => $billId,
            'payment_amount' => $paymentAmount,
            'payment_method' => $paymentMethod,
            'payment_date' => date('Y-m-d H:i:s'),
        ]);

        // Update bill's paid amount, if you have a field or calculate on the fly

        return redirect()->to(site_url('pharmacy/sales/invoice/' . $billId))
            ->with('success', 'Payment recorded successfully.');
    }
}
