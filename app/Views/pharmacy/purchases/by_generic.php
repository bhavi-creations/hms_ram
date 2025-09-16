<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($title) ?></h1>
    <div class="card">
        <div class="card-body">
            <?php if (!empty($batches)): ?>
                <table id="genericBatchesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Batch Number</th>
                            <th>Brand Name</th>
                            <th>Total Purchased Qty</th>
                            <th>Remaining Qty</th>
                            <th>Unit Purchase Price (₹)</th>
                            <th>Strength</th>
                            <th>Form</th>
                            <th>Category</th>
                            <th>Manufacturer</th>
                            <th>Manufacturing Date</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($batches as $batch): ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td><?= esc($batch['batch_number']) ?></td>
                                <td><?= esc($batch['brand_name']) ?></td>
                                <td><?= esc($batch['initial_quantity']) ?></td>
                                <td><?= esc($batch['current_stock']) ?></td>
                                <td>₹ <?= number_format($batch['purchase_price'], 2) ?></td>
                                <td><?= esc($batch['strength']) ?> <?= esc($batch['unit_of_measure_name']) ?></td>
                                <td><?= esc($batch['dosage_form_name']) ?></td>
                                <td><?= esc($batch['category_name']) ?></td>
                                <td><?= esc($batch['manufacturer_name']) ?></td>
                                <td><?= esc($batch['manufacturing_date']) ?></td>
                                <td><?= esc($batch['expiry_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No batches found for this generic.</p>
            <?php endif; ?>
            <a href="<?= site_url('pharmacy/purchases/bySupplier/' . $batches[0]['supplier_id'] ?? '') ?>" class="btn btn-secondary mt-3">Back to Purchases</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
    $('#genericBatchesTable').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        searching: true,
        ordering: true,
        paging: true,
        info: true,
        order: [[1, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>
