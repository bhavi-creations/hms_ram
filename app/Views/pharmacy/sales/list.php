<?= $this->extend('layouts/main') ?> // Ensure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Medicine Expiry Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Expiry</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicines Expiring Within <?= esc($monthsAhead ?? 3) ?> Months</h3>
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

                    <form class="form-inline mb-3" action="<?= site_url('pharmacy/reports/expiry') ?>" method="get">
                        <label for="months_ahead" class="mr-2">Expiring within:</label>
                        <input type="number" class="form-control mr-2" id="months_ahead" name="months_ahead" value="<?= esc($monthsAhead ?? 3) ?>" min="1">
                        <label class="mr-3">months</label>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="expiryReportTable">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Generic Name</th>
                                <th>Strength</th>
                                <th>Supplier</th>
                                <th>Batch Number</th>
                                <th>Current Stock</th>
                                <th>Expiry Date</th>
                                <th>Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expiringBatches) && is_array($expiringBatches)) : ?>
                                <?php foreach ($expiringBatches as $batch) :
                                    $expiryDateObj = new DateTime($batch['expiry_date']);
                                    $currentDateObj = new DateTime();
                                    $interval = $currentDateObj->diff($expiryDateObj);
                                    $daysRemaining = $interval->days;
                                    if ($interval->invert) { // If expiry date is in the past
                                        $daysRemaining = -$daysRemaining;
                                    }
                                ?>
                                    <tr>
                                        <td><?= esc($batch['brand_name']) ?></td>
                                        <td><?= esc($batch['generic_name']) ?></td>
                                        <td><?= esc($batch['strength']) ?></td>
                                        <td><?= esc($batch['supplier_name']) ?></td>
                                        <td><?= esc($batch['batch_number']) ?></td>
                                        <td><?= esc($batch['current_stock']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($batch['expiry_date']))) ?></td>
                                        <td>
                                            <?php if ($daysRemaining > 0) : ?>
                                                <span class="badge badge-warning"><?= esc($daysRemaining) ?> days</span>
                                            <?php elseif ($daysRemaining == 0) : ?>
                                                <span class="badge badge-danger">Today</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger"><?= esc(abs($daysRemaining)) ?> days Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No medicines found expiring within the specified period or no stock.</td>
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
    $(function () {
        // Initialize DataTables if you're using it
        if ($.fn.DataTable) {
            $('#expiryReportTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[7, 'asc']] // Order by Days Remaining column (index 7) ascending
            });
        }
    });
</script>
<?= $this->endSection() ?>