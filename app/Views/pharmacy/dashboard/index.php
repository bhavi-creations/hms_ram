<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Pharmacy Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item active">Pharmacy Dashboard</li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= esc($totalMedicines ?? 0) ?></h3>
                        <p>Total Medicines</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-medkit"></i>
                    </div>
                    <a href="<?= site_url('pharmacy/medicines') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= esc($totalSales ?? 0) ?></h3>
                        <p>Total Sales (Today)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="<?= site_url('pharmacy/sales/listToday') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= esc($lowStockItems ?? 0) ?></h3>
                        <p>Low Stock Items</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-alert-circled"></i>
                    </div>
                    <a href="<?= site_url('pharmacy/reports/stock?low_stock_threshold=20') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= esc($expiringSoonItems ?? 0) ?></h3>
                        <p>Expiring Soon</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-calendar"></i>
                    </div>
                    <a href="<?= site_url('pharmacy/reports/expiry') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Recent Sales</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Patient</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentSales)) : ?>
                                    <?php foreach ($recentSales as $sale) : ?>
                                        <tr>
                                            <td><a href="<?= site_url('pharmacy/sales/view/' . $sale['invoice_number']) ?>"><?= esc($sale['invoice_number']) ?></a></td>
                                            <td><?= esc($sale['patient_name']) ?></td>
                                            <td><?= esc(number_format($sale['total_amount'], 2)) ?></td>
                                            <td><?= esc(date('M d, Y', strtotime($sale['created_at']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No recent sales found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <a href="<?= site_url('pharmacy/sales/list') ?>" class="btn btn-sm btn-primary mt-3">View All Sales</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Upcoming Expiry</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Generic Name</th>
                                    <th>Batch #</th>
                                    <th>Stock</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($expiringSoonBatches)) : ?>
                                    <?php foreach ($expiringSoonBatches as $batch) : ?>
                                        <tr>
                                            <td><?= esc($batch['generic_name']) ?></td>
                                            <td><?= esc($batch['batch_number']) ?></td>
                                            <td><?= esc($batch['current_stock']) ?></td>
                                            <td><?= esc(date('M d, Y', strtotime($batch['expiry_date']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No batches found to be expiring soon.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <a href="<?= site_url('pharmacy/reports/expiry') ?>" class="btn btn-sm btn-danger mt-3">View Expiry Report</a>
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
        console.log('Pharmacy Dashboard loaded.');
    });
</script>
<?= $this->endSection() ?>