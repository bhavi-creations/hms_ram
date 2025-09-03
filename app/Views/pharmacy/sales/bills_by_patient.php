<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($title) ?></h1>
    <p><strong>Patient ID:</strong> <?= esc($patient['id']) ?></p>
    <p><strong>Patient Name:</strong> <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
    <p><strong>IPD Code:</strong> <?= esc($patient['ipd_code'] ?? $patient['ipd_id_code'] ?? 'N/A') ?></p>
    <p><strong>Phone:</strong> <?= esc($patient['phone'] ?? $patient['phone_number'] ?? 'N/A') ?></p>

    <table class="table table-bordered table-striped" id="patientBillsTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Bill ID</th>
                <th>Bill Date</th>
                <th>Total Amount (₹)</th>
                <th>Paid Amount (₹)</th>
                <th>Due Amount (₹)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bills)): ?>
                <?php $count = 1; ?>
                <?php foreach ($bills as $bill): ?>
                    <?php
                    // Fetch payments for this bill
                    $payments = $payments ?? null;
                    $paymentsModel = new \App\Models\Pharmacy\PharmacyBillingPaymentModel();
                    $payments = $paymentsModel->where('bill_id', $bill['bill_id'])->findAll();

                    $totalPaid = $payments ? array_sum(array_column($payments, 'payment_amount')) : 0;
                    $dueAmount = $bill['total_amount'] - $totalPaid;

                    if ($dueAmount <= 0) {
                        $status = '<span class="badge badge-success">Paid</span>';
                    } elseif ($totalPaid > 0) {
                        $status = '<span class="badge badge-warning">Partial</span>';
                    } else {
                        $status = '<span class="badge badge-danger">Pending</span>';
                    }
                    ?>
                    <tr>
                        <td><?= esc($count++) ?></td>
                        <td><?= esc($bill['bill_id']) ?></td>
                        <td><?= esc(date('d-M-Y', strtotime($bill['bill_date']))) ?></td>
                        <td><?= number_format($bill['total_amount'], 2) ?></td>
                        <td><?= number_format($totalPaid, 2) ?></td>
                        <td><?= number_format($dueAmount, 2) ?></td>
                        <td><?= $status ?></td>
                        <td>
                            <a href="<?= site_url('pharmacy/sales/invoice/' . urlencode($bill['bill_id'])) ?>" class="btn btn-info btn-sm">View Invoice</a>
                            <?php if ($dueAmount > 0): ?>
                                <a href="<?= site_url('pharmacy/payments/makePayment/' . urlencode($bill['bill_id'])) ?>" class="btn btn-primary btn-sm">Make Payment</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Paid</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No bills found for this patient.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="<?= site_url('pharmacy/sales/listBills/patients') ?>" class="btn btn-secondary mt-3">Back to Patients List</a>
</div>





<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $(function() {
        $('#patientBillsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            order: [
                [2, "desc"]
            ],
            responsive: true,
            lengthChange: false,
            info: true,
            autoWidth: false
        });
    });
</script>
<?= $this->endSection() ?>