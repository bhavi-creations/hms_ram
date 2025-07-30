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
                        <h3><?= esc($totalSales ?? 0) ?><sup style="font-size: 20px">%</sup></h3>
                        <p>Total Sales (Today)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="<?= site_url('pharmacy/sales/list') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="<?= site_url('pharmacy/sales') ?>" class="btn btn-lg btn-success btn-block">
                                    <i class="fas fa-cash-register mr-2"></i> New Sale
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="<?= site_url('pharmacy/medicines/create') ?>" class="btn btn-lg btn-info btn-block">
                                    <i class="fas fa-plus-circle mr-2"></i> Add Medicine
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="<?= site_url('pharmacy/purchases/create') ?>" class="btn btn-lg btn-warning btn-block">
                                    <i class="fas fa-truck mr-2"></i> New Purchase Order
                                </a>
                            </div>
                        </div>
                    </div>
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
                        <p>Display recent sales transactions here.</p>
                        <a href="<?= site_url('pharmacy/sales/list') ?>" class="btn btn-sm btn-primary">View All Sales</a>
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
                        <p>List medicines expiring soon.</p>
                        <a href="<?= site_url('pharmacy/reports/expiry') ?>" class="btn btn-sm btn-danger">View Expiry Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Example: Dynamically update dashboard stats or charts via AJAX
    // For this dashboard, the data is passed from the controller,
    // but for more dynamic updates, AJAX calls would be useful.
    $(document).ready(function() {
        console.log('Pharmacy Dashboard loaded.');
    });
</script>
<?= $this->endSection() ?>