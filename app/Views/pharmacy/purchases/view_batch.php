<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($title) ?></h1>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Batch Number</th><td><?= esc($batch['batch_number']) ?></td></tr>
                <tr><th>Generic</th><td><?= esc($medicine['generic_name']) ?></td></tr>
                <tr><th>Brand</th><td><?= esc($medicine['brand_name']) ?></td></tr>
                <tr><th>Strength</th><td><?= esc($medicine['strength']) ?> <?= esc($medicine['unit_of_measure_name']) ?></td></tr>
                <tr><th>Form</th><td><?= esc($medicine['dosage_form_name']) ?></td></tr>
                <tr><th>Category</th><td><?= esc($medicine['category_name']) ?></td></tr>
                <tr><th>Manufacturer</th><td><?= esc($medicine['manufacturer_name']) ?></td></tr>
                <tr><th>Supplier</th><td><?= esc($supplier['name']) ?></td></tr>
                <tr><th>Initial Quantity</th><td><?= esc($batch['initial_quantity']) ?></td></tr>
                <tr><th>Current Stock</th><td><?= esc($batch['current_stock']) ?></td></tr>
                <tr><th>Purchase Price</th><td>₹ <?= number_format($batch['purchase_price'], 2) ?></td></tr>
                <tr><th>Selling Price</th><td>₹ <?= number_format($batch['selling_price'], 2) ?></td></tr>
                <tr><th>Manufacturing Date</th><td><?= esc($batch['manufacturing_date']) ?></td></tr>
                <tr><th>Expiry Date</th><td><?= esc($batch['expiry_date']) ?></td></tr>
                <tr><th>Status</th><td><?= esc($batch['status']) ?></td></tr>
            </table>
            <a href="<?= site_url('pharmacy/purchases/bySupplier/' . $supplier['id']) ?>" class="btn btn-secondary mt-3">Back to Purchases</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
