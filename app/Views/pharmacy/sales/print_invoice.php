<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Invoice</title>
    <style>
        body {
            background: #fff !important;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .invoice-card {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            border: none;
            box-shadow: none;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }


        /* NEW FLEXBOX STYLES FOR TOTALS SECTION */
        .totals-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            /* Aligns items to the bottom */
        }

        .grand-total-words {
            flex-grow: 1;
            /* Allows this section to take up available space */
            margin-right: 20px;
            align-self: flex-end;
            /* Explicitly aligns this item to the end of the cross axis */
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

        .invoice-totals table {
            width: 300px;
        }

        .invoice-totals th,
        .invoice-totals td {
            padding: 5px;
            text-align: right;
        }

        @media print {

            body,
            .invoice-card {
                margin: 0 !important;
                padding: 1px !important;
                box-shadow: none !important;
                border: none !important;
                background: #fff !important;

            }
        }
    </style>
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
</head>

<body>
    <div class="invoice-card bg-white">
        <!-- Copy your invoice content here: header, items, totals, etc.
         Use the same PHP blocks and logic as your main invoice.php.
         Example below... -->
        <div class="invoice-header">
            <h2>Sales Invoice</h2>
            <p>Invoice #
                <?= esc($sale['invoice_number'] ?? $sale['bill_id'] ?? $sale['id']) ?>
            </p>
            <p>Date:
                <?php
                if (isset($sale['prescription_type']) && $sale['prescription_type'] === 'in_hospital' && !empty($sale['bill_date'])) {
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
                        <td><?= esc($item['brand_name']) ?> (<?= esc($item['strength']) ?>)</td>
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
            <h4 style="margin-top: 30px; color: #c0392b;">Returned Items</h4>
            <table class="invoice-table" style="background: #fcf8e3;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Quantity Returned</th>
                        <th>Unit Price</th>
                        <th>Discount/Unit</th>
                        <th>Refund/Unit</th>
                        <th>Refunded Amount</th>
                        <th>Date of Return</th>
                    </tr>
                </thead>
                <tbody>
                                    <?php
                                    $ri = 1;
                                    $totalReturnAmount = 0;
                                    ?>
                                    <?php foreach ($returns as $ret): ?>
                                        <?php
                                        $qty = $ret['quantity_returned'];
                                        $price = $ret['unit_selling_price'];
                                        $disc = $ret['discount_per_item'] ?? 0;
                                        $returnPerUnit = $price - $disc;
                                        $amt = $returnPerUnit * $qty;
                                        $totalReturnAmount += $amt;
                                        ?>
                                        <tr>
                                            <td><?= $ri++ ?></td>
                                            <td><?= esc($ret['medicine_name']) ?></td>
                                            <td><?= esc($qty) ?></td>
                                            <td><?= number_format($price, 2) ?></td>
                                            <td><?= number_format($disc, 2) ?></td>
                                            <td><?= number_format($returnPerUnit, 2) ?></td>
                                            <td><?= number_format($amt, 2) ?></td>
                                            <td><?= esc(date('M d, Y', strtotime($ret['approval_date'] ?? $ret['return_date']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6">Total Amount Returned</th>
                                        <th colspan="2"><?= number_format($totalReturnAmount, 2) ?></th>
                                    </tr>
                                </tfoot>
            </table>



        <?php endif; ?>


        <?php if (!empty($returns) && $totalReturnAmount > 0): ?>
            <div class="payment-summary mt-4" style="border:1px solid #ccc; padding:15px; border-radius:8px; background:#fafafa; font-weight:bold;">
                <?php if ($sale['prescription_type'] === 'in_hospital'): ?>
                    <p>Total Amount Returned (Excl. GST): ₹ <?= number_format($totalReturnAmount, 2) ?></p>

                    <?php if (isset($extraPaidBeforeReturn) && $extraPaidBeforeReturn > 0): ?>
                        <p>Extra Amount Paid: ₹ <?= number_format($extraPaidBeforeReturn, 2) ?></p>
                    <?php endif; ?>

                    <?php if (isset($dueAmount) && $dueAmount > 0): ?>
                        <p style="color: red;">Due Amount after all returns: ₹ <?= number_format($dueAmount, 2) ?></p>
                    <?php elseif (isset($excessPaidAmount) && $excessPaidAmount > 0): ?>
                        <p style="color: green;">Excess Paid (Refund Due to Patient): ₹ <?= number_format($excessPaidAmount, 2) ?></p>
                        <p style="color: #1e7e34;">
                            <strong>Final Amount to Return: <?= number_format($totalReturnAmount, 2) ?> + <?= number_format($excessPaidAmount, 2) ?> = ₹ <?= number_format($finalRefundAmount, 2) ?></strong>
                        </p>
                    <?php else: ?>
                        <p style="color: #666;">No refund due or outstanding amount.</p>
                    <?php endif; ?>

                <?php else: /* OP bill block updated */ ?>
                    <p>Total Amount Returned (Excl. GST): ₹ <?= number_format($totalReturnAmount, 2) ?></p>

                    <?php if (isset($totalReturnGST) && $totalReturnGST > 0): ?>
                        <p>GST Included in Returned Items: ₹ <?= number_format($totalReturnGST, 2) ?></p>
                    <?php endif; ?>

                    <p><strong>Total Refund Amount (Incl. GST): ₹ <?= number_format($totalReturnAmount + $totalReturnGST, 2) ?></strong></p>

                    <p><em>Note: OP bills are fully paid — no due amount.</em></p>
                <?php endif; ?>

            </div>
        <?php endif; ?>


        <div class="totals-section">
            <div class="grand-total-words">
                <p><strong>Grand Total (in words): </strong><?= esc($grandTotalWords) ?></p>
            </div>
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
                        <td>- <?= number_format(esc($totalDiscount ?? 0), 2) ?></td>
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
                            <td>₹ <?= number_format(esc($paidAmount ?? 0), 2) ?></td>
                        </tr>
                        <tr>
                            <th>Due Amount</th>
                            <td>₹ <?= number_format(esc($dueAmount ?? $grandTotal), 2) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>





        <?php if (isset($payments) && count($payments) > 0): ?>
            <h5 class="mt-4">Payment Installments</h5>
            <table class="table table-bordered  invoice-table">
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


    </div>
    <script>
        // Automatically print when this page loads (optional)
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>