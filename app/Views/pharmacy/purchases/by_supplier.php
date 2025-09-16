<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/purchases') ?>">Purchases</a></li>
                <li class="breadcrumb-item active"><?= esc($supplier['name']) ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Purchased Stock Details</h3>
                    </div>
                    <div class="card-body">
                        <?php if(session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <table id="batchesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Batch Number</th>
                                    <th>Generic Name</th>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($batches) && is_array($batches)): ?>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($batches as $batch): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td><?= esc($batch['batch_number']) ?></td>
                                            <td><?= esc($batch['generic_name']) ?></td>
                                            <td><?= esc($batch['brand_name']) ?></td>
                                            <td><?= esc($batch['total_purchased_qty']) ?></td>
                                            <td><?= esc($batch['remaining_qty']) ?></td>
                                            <td><?= number_format($batch['purchase_price'], 2) ?></td>
                                            <td><?= esc($batch['strength']) ?> <?= esc($batch['unit_of_measure_name']) ?></td>
                                            <td><?= esc($batch['dosage_form_name']) ?></td>
                                            <td><?= esc($batch['category_name']) ?></td>
                                            <td><?= esc($batch['manufacturer_name']) ?></td>
                                            <td><?= esc($batch['manufacturing_date']) ?></td>
                                            <td><?= esc($batch['expiry_date']) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('pharmacy/purchases/viewBatch/' . $batch['id']) ?>" class="btn btn-sm btn-primary" title="View Batch">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('pharmacy/purchases/byGeneric/' . $batch['generic_id']) ?>" class="btn btn-sm btn-info" title="View Generic">
                                                        <i class="fas fa-box"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="14" class="text-center">No purchase details found for this supplier.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
 

<script>
$(document).ready(function() {
    $('#batchesTable').DataTable({
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
