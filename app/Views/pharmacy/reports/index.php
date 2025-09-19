<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pharmacy Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sales Reports</span>
                                    <a href="<?= site_url('pharmacy/reports/sales') ?>" class="info-box-number text-sm">View Sales Data <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Stock Reports</span>
                                    <a href="<?= site_url('pharmacy/reports/stock') ?>" class="info-box-number text-sm">Monitor Stock Levels <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expiry Reports</span>
                                    <a href="<?= site_url('pharmacy/reports/expiry') ?>" class="info-box-number text-sm">Track Expiring Medicines <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Purchase Reports</span>
                                    <a href="<?= site_url('pharmacy/purchases') ?>" class="info-box-number text-sm">Analyze Purchase Data <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>