<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    /* Custom styles for the invoice page */
    .invoice-card {
        max-width: 900px;
        margin: 20px auto;
        padding: 30px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .invoice-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .invoice-header h2 {
        margin-bottom: 5px;
    }

    .invoice-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    .invoice-details .left-side,
    .invoice-details .right-side {
        width: 48%;
    }

    .invoice-details strong {
        display: block;
        margin-bottom: 5px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .invoice-table th,
    .invoice-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .invoice-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .invoice-totals {
        display: flex;
        justify-content: flex-end;
    }

    .invoice-totals table {
        width: 300px;
    }

    .invoice-totals th,
    .invoice-totals td {
        padding: 5px;
        text-align: right;
    }

    .print-button {
        text-align: center;
        margin-top: 20px;
    }
</style>

<div class="container-fluid">
    <div class="row mb-2 no-print">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                <li class="breadcrumb-item active"><?= esc($title) ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="invoice-card bg-white">
                    <div class="invoice-header">
                        <h2>Sales Invoice</h2>
                        <p>Invoice #
                            <?= esc($sale['invoice_number'] ?? $sale['bill_id'] ?? $sale['id']) ?>
                        </p>

                        <p>Date:
                            <?php
                            if (!empty($sale['bill_date'])) {
                                echo esc(date('M d, Y', strtotime($sale['bill_date'])));
                            } elseif (!empty($sale['sale_date'])) {
                                echo esc(date('M d, Y', strtotime($sale['sale_date'])));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </p>






                        <?php if (!isset($sale['prescription_type']) || $sale['prescription_type'] !== 'in_hospital'): ?>
                            <p>Payment Method: <?= esc($sale['payment_method'] ?? 'N/A') ?></p>
                        <?php endif; ?>




                    </div>



                    <div class="invoice-details">
                        <div class="left-side">
                            <address>
                                <strong>From:</strong><br>
                                <?= esc(session()->get('company_name') ?? 'Your Company Name') ?><br>
                                <?= esc(session()->get('company_address') ?? '123 Business St.') ?><br>
                                Phone: <?= esc(session()->get('company_phone') ?? '123-456-7890') ?>
                            </address>
                        </div>
                        <div class="right-side">
                            <address>
                                <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'in_hospital'): ?>
                                    <strong>Bill To:</strong><br>
                                    Patient: <?= esc($patientDetails['name'] ?? 'N/A') ?><br>
                                    IPD-ID: <?= esc($patientDetails['ipd_id_code'] ?? 'N/A') ?><br>
                                    Phone: <?= esc($patientDetails['phone_number'] ?? 'N/A') ?><br>
                                    Address: <?= nl2br(esc($patientDetails['address'] ?? 'N/A')) ?><br>


                                    <?php if (!empty($doctorDetails)): ?>
                                        Doctor: <?= esc($doctorDetails['name'] ?? 'N/A') ?><br>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <strong>Bill To:</strong><br>
                                    Patient Name: <?= esc($sale['outside_patient_name'] ?? 'N/A') ?><br>
                                    Phone: <?= esc($sale['outside_patient_phone'] ?? 'N/A') ?><br>
                                    Address: <?= nl2br(esc($sale['outside_patient_address'] ?? 'N/A')) ?><br>
                                <?php endif; ?>
                            </address>
                        </div>

                    </div>

                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Batch</th>
                                <th>Exp. Date</th>
                                <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'outside_sale'): ?>
                                    <th>HSN</th>

                                <?php endif; ?>

                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Disc.</th>
                                <th>Subtotal</th>
                                <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'outside_sale'): ?>
                                    <th>GST Amount</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($saleItems as $item): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($item['generic_name'] ?? $item['medicine_name'] ?? '') ?> (<?= esc($item['strength'] ?? '') ?>)</td>
                                    <td><?= esc($item['batch_number']) ?></td>
                                    <td><?= esc(date('M Y', strtotime($item['expiry_date']))) ?></td>
                                    <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'outside_sale'): ?>
                                        <td><?= esc($item['hsn_code']) ?></td>
                                    <?php endif; ?>

                                    <td><?= esc($item['quantity']) ?></td>
                                    <td><?= number_format(esc($item['unit_selling_price']), 2) ?></td>
                                    <td><?= number_format(esc($item['discount_per_item']), 2) ?></td>
                                    <td><?= number_format(esc($item['item_sub_total']), 2) ?></td>
                                    <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'outside_sale'): ?>
                                        <td><?= number_format($item['gst_amount'], 2) ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if (!empty($returns)): ?>
                        <hr>
                        <h5 class="mt-3" style="color:#c0392b;">Returned Items</h5>
                        <table class="table table-bordered" style="background:#fcf8e3;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>Quantity Returned</th>
                                    <th>Unit Price</th>
                                    <th>Subtracted Amount</th>
                                    <th>Date of Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalReturnAmount = 0;
                                $ri = 1; ?>
                                <?php foreach ($returns as $ret): ?>
                                    <?php
                                    $amt = $ret['quantity_returned'] * $ret['unit_selling_price'];
                                    $totalReturnAmount += $amt;
                                    ?>
                                    <tr>
                                        <td><?= $ri++ ?></td>
                                        <td><?= esc($ret['medicine_name']) ?></td>
                                        <td><?= esc($ret['quantity_returned']) ?></td>
                                        <td><?= number_format((float)$ret['unit_selling_price'], 2) ?></td>
                                        <td><?= number_format((float)$amt, 2) ?></td>
                                        <td><?= esc(date('M d, Y', strtotime($ret['approval_date']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total Amount Returned</th>
                                    <th colspan="2"><?= number_format((float)$totalReturnAmount, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif; ?>



                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 d-flex flex-column  justify-content-end">
                                <div class=" ">

                                    <p><strong> Grand Total (in words) : </strong><?= esc($grandTotalInWords) ?> </p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="invoice-totals">
                                    <table>
                                        <tr>
                                            <th>Total Items</th>
                                            <td><?= esc($totalItems) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Quantity</th>
                                            <td><?= esc($totalQuantity) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount</th>
                                            <td><?= number_format(esc($subTotal), 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Discount</th>
                                            <td>- <?= number_format(esc(isset($sale['discount_amount']) ? $sale['discount_amount'] : 0), 2) ?></td>

                                        </tr>
                                        <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'outside_sale'): ?>
                                            <tr>
                                                <th>GST Amount</th>
                                                <td>+ <?= number_format(esc($gstAmount), 2) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>Grand Total</th>
                                            <td>₹ <?= number_format(esc($grandTotal), 2) ?></td>
                                        </tr>


                                        <?php if (!isset($sale['prescription_type']) || $sale['prescription_type'] !== 'outside_sale'): ?>
                                            <tr>
                                                <th>Paid Amount</th>
                                                <td>₹ <?= number_format(esc($sale['paid_amount']), 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Due Amount</th>
                                                <td>₹ <?= number_format(esc($dueAmount ?? $grandTotal), 2) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>





                    <?php if (isset($payments) && count($payments) > 0): ?>
                        <h5 class="mt-4">Payment Installments</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= esc(date('M d, Y', strtotime($payment['payment_date']))) ?></td>
                                        <td>₹ <?= number_format($payment['payment_amount'], 2) ?></td>
                                        <td><?= esc($payment['payment_method'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'in_hospital'): ?>
                            <p class="mt-4"><em>No installment payments recorded yet.</em></p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'in_hospital'): ?>
                        <div class="invoice-totals mt-3">
                            <table>
                                <tr>
                                    <th>Paid Amount</th>
                                    <td>₹ <?= number_format(esc($paidAmount ?? 0), 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Due Amount</th>
                                    <td>₹ <?= number_format(esc($dueAmount ?? $grandTotal), 2) ?></td>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>


                    <div class="print-button no-print">
                        <a href="<?= site_url('pharmacy/sales/printInvoice/' . urlencode($sale['bill_id'] ?? $sale['invoice_number'] ?? $sale['id'])) ?>"
                            class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Print Invoice
                        </a>

                    </div>

                    <div class="back-button no-print mt-2">
                        <a href="<?= site_url('pharmacy/sales/listBills/patients') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>