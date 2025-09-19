<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expiring and Expired Medicines</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped" id="expiryTable">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Medicine</th>
                                <th>Batch No.</th>
                                <th>Manufacturer</th>
                                <th>Stock Qty</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($batches) && is_array($batches)) : ?>
                                <?php $s_no = 1; ?>
                                <?php foreach ($batches as $batch) : ?>
                                    <tr class="<?= (new DateTime($batch['expiry_date']))->format('Y-m-d') < date('Y-m-d') ? 'table-danger' : '' ?>">
                                        <td><?= $s_no++ ?></td>
                                        <td><?= esc($batch['generic_name']) ?></td>
                                        <td><?= esc($batch['batch_number']) ?></td>
                                        <td><?= esc($batch['manufacturer_name']) ?></td>
                                        <td><?= esc($batch['current_stock']) ?></td>
                                        <td><?= esc(date('M Y', strtotime($batch['expiry_date']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No expiring or expired medicines found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#expiryTable').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            order: [
                [5, 'asc']
            ] // Sort by expiry date column
        });
    });
</script>
<?= $this->endSection() ?>
