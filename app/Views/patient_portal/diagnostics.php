<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Diagnostic Orders</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('patient-portal/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Diagnostics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Diagnostic Imaging Orders</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($diagnostics)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Code</th>
                                <th>Order Date</th>
                                <th>Service/Test</th>
                                <!-- <th>Prescribed By</th> -->
                                <th>Status</th>
                                <th>Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($diagnostics as $diag): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($diag['order_id_code']) ?></td>
                                <td><?= esc($diag['order_date']) ?></td>
                                <!-- Now correctly shows single or multiple concatenated service names -->
                                <td><?= esc($diag['procedure_name'] ?? 'N/A') ?></td>
                                <!-- <td><?= esc($diag['doctor_id'] ?? 'N/A') ?></td> -->
                                <td>
                                    <?php 
                                        $status = esc($diag['status']);
                                        $badgeClass = 'badge-secondary';
                                        if ($status == 'Scheduled') $badgeClass = 'badge-info';
                                        if ($status == 'Completed') $badgeClass = 'badge-success';
                                        if ($status == 'Awaiting Scan') $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <td>
                                    <?php 
                                    // The model returns a comma-separated list in report_file_path.
                                    $rawReportPath = $diag['report_file_path'] ?? null; 
                                    
                                    // We split the string and take only the first file path for the single link.
                                    $allPaths = explode(',', $rawReportPath);
                                    $reportPath = trim($allPaths[0] ?? '');
                                    $hasMultipleFiles = count(array_filter($allPaths, 'trim')) > 1;
                                    ?>
                                    <?php if ($status == 'Completed' && !empty($reportPath)): ?>
                                        <!-- UPDATED: Use a secure route to serve the file -->
                                        <a href="<?= base_url('patient-portal/view-report/' . esc($reportPath)) ?>" target="_blank" class="btn btn-sm btn-success">
                                            View Report 
                                            <?php if ($hasMultipleFiles): ?>
                                                <i class="fas fa-copy" title="Multiple files attached"></i>
                                            <?php else: ?>
                                                <i class="fas fa-file-alt"></i>
                                            <?php endif; ?>
                                        </a>
                                        <?php if ($hasMultipleFiles): ?>
                                            <small class="text-info d-block mt-1">First file only</small>
                                        <?php endif; ?>
                                    <?php elseif ($status == 'Completed' && empty($reportPath)): ?>
                                        <span class="text-danger">Report File Missing</span>
                                    <?php else: ?>
                                        <span class="text-muted">Awaiting Report</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        You have no active or completed diagnostic orders.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
