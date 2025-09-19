<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
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
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-pills"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Medicines</span>
                            <span class="info-box-number"><?= esc($totalMedicines) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Low Stock Items</span>
                            <span class="info-box-number"><?= esc($lowStockItems) ?></span>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-rupee-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sales Today</span>
                            <span class="info-box-number">₹ <?= esc($totalSales) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Expiring Soon</span>
                            <span class="info-box-number"><?= esc($expiringSoonItems) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales and Expiring Batches -->
            <div class="row">
                <!-- Recent Sales -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Recent Sales</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentSales)): ?>
                                <p class="text-center text-muted">No recent sales found.</p>
                            <?php else: ?>
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Bill No.</th>
                                            <th>Patient Name</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentSales as $sale): ?>
                                            <tr>
                                                <td><a href="<?= site_url('pharmacy/sales/view/' . $sale['bill_id']) ?>"><?= esc($sale['bill_id']) ?></a></td>
                                                <td><?= esc($sale['patient_name']) ?></td>
                                                <td>₹ <?= number_format($sale['total_amount'], 2) ?></td>
                                                <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                                                <td><?= esc($sale['sale_type']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Expiring Batches -->
                <div class="col-md-6">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Upcoming Expiring Batches</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($expiringSoonBatches)): ?>
                                <p class="text-center text-muted">No batches expiring in the next 6 months.</p>
                            <?php else: ?>
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Batch No.</th>
                                            <th>Generic Name</th>
                                            <th>Expiry Date</th>
                                            <th>Current Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expiringSoonBatches as $batch): ?>
                                            <tr>
                                                <td><?= esc($batch['batch_number']) ?></td>
                                                <td><?= esc($batch['generic_name']) ?></td>
                                                <td><?= date('M d, Y', strtotime($batch['expiry_date'])) ?></td>
                                                <td><?= esc($batch['current_stock']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
