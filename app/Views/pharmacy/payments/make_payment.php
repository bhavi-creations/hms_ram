<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Make Payment for Bill: <?= esc($bill['bill_id']) ?></h2>
    <p>Total Bill Amount: ₹ <?= number_format($bill['total_amount'], 2) ?></p>
    <p>Amount Due: ₹ <?= number_format($dueAmount, 2) ?></p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('pharmacy/payments/processPayment') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="bill_id" value="<?= esc($bill['bill_id']) ?>" />

        <div class="form-group">
            <label for="payment_amount">Payment Amount</label>
            <input type="number" step="0.01" min="0.01" max="<?= esc($dueAmount) ?>" class="form-control" id="payment_amount" name="payment_amount" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="">Select Method</option>
                <option>Cash</option>
                <option>Card</option>
                <option>UPI</option>
                <option>Insurance</option>
                <option>Other</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit Payment</button>
        <a href="<?= site_url('pharmacy/sales/invoice/' . $bill['bill_id']) ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?= $this->endSection() ?>