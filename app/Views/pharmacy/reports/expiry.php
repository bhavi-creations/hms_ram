<?= $this->extend('layouts/main') ?> // Ensure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Expiring Soon Medicines Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Expiring Soon</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicines Expiring Within <?= esc($daysThreshold ?? 90) ?> Days</h3>
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
                        <label for="days" class="mr-2">Expiring within:</label>
                        <input type="number" class="form-control mr-2" id="days" name="days" value="<?= esc($daysThreshold ?? 90) ?>" min="1">
                        <label class="mr-3">days</label>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="expiryReportTable">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Batch Number</th>
                                <th>Current Stock</th>
                                <th>Expiry Date</th>
                                <th>Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expiringItems) && is_array($expiringItems)) : ?>
                                <?php foreach ($expiringItems as $item) : ?>
                                    <tr>
                                        <td><?= esc($item['medicine_name']) ?></td>
                                        <td><?= esc($item['batch_number']) ?></td>
                                        <td><?= esc($item['current_stock']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($item['expiry_date']))) ?></td>
                                        <td><?= esc($item['days_remaining']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No medicines found expiring within the specified period.</td>
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
                "order": [[4, 'asc']] // Order by Days Remaining ascending
            });
        }
    });
</script>
<?= $this->endSection() ?>