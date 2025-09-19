<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Sales Report</h1>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Report for <?= esc($salesPerson['first_name'] . ' ' . $salesPerson['last_name']) ?></h3>
            </div>
            <div class="card-body">
                <!-- Date Filter Form -->
                <div class="mb-4">
                    <form action="<?= site_url('pharmacy/salespersons/profile/' . esc($user['id'])) ?>" method="get" class="form-inline">
                        <label for="start_date" class="mr-2">From:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control mr-2" value="<?= esc($startDate) ?>">
                        
                        <label for="end_date" class="mr-2">To:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control mr-2" value="<?= esc($endDate) ?>">
                        
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </form>
                </div>
                
                <?php
                    // Calculate total capital for both sales types
                    $totalCapital = 0;
                    foreach ($inHospitalSales as $sale) {
                        $totalCapital += (float)$sale['total_amount'];
                    }
                    foreach ($outsideSales as $sale) {
                        $totalCapital += (float)$sale['total_amount'];
                    }
                ?>
                
                <!-- Sales Summary -->
                <div class="callout callout-info">
                    <h5 class="mb-0">Total Sales Capital:</h5>
                    <h3 class="font-weight-bold">Rs. <?= number_format($totalCapital, 2) ?></h3>
                    <small>For the period from <?= esc($startDate) ?> to <?= esc($endDate) ?></small>
                </div>
                
                <h4 class="mt-4 mb-3">In-Hospital Sales</h4>
                <div class="table-responsive">
                    <?php if (empty($inHospitalSales)): ?>
                        <p class="text-center text-muted">No in-hospital sales found for this period.</p>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Bill ID</th>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Total Amount (Rs.)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inHospitalSales as $sale): ?>
                                    <tr>
                                        <td><?= esc($sale['bill_id']) ?></td>
                                        <td><?= date('M d, Y', strtotime($sale['bill_date'])) ?></td>
                                        <td><?= esc($sale['first_name'] . ' ' . $sale['last_name']) ?></td>
                                        <td><?= number_format($sale['total_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <h4 class="mt-4 mb-3">Outside Sales</h4>
                <div class="table-responsive">
                    <?php if (empty($outsideSales)): ?>
                        <p class="text-center text-muted">No outside sales found for this period.</p>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Total Amount (Rs.)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($outsideSales as $sale): ?>
                                    <tr>
                                        <td><?= esc($sale['invoice_number']) ?></td>
                                        <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                                        <td><?= esc($sale['outside_patient_name']) ?></td>
                                        <td><?= number_format($sale['total_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
