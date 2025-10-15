<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-file-pdf mr-2 text-primary"></i> <?= esc($title) ?>
                </h1>
                <h4 class="text-primary">Order Reference: #<?= esc($order['order_id_code']) ?></h4>
            </div><div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/results') ?>">Results</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header border-bottom-0">
                        <h3 class="card-title">Report Details</h3>
                        <div class="card-tools">
                             <button type="button" class="btn btn-sm btn-default" onclick="window.print()">
                                <i class="fas fa-print mr-1"></i> Print Report
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        
                        <div class="row mb-4">
                            <div class="col-md-6 border-right">
                                <h4 class="text-primary"><i class="fas fa-user-injured mr-1"></i> Patient Details</h4>
                                <dl class="row mt-3">
                                    <dt class="col-sm-4 text-muted">Patient Name:</dt>
                                    <dd class="col-sm-8 font-weight-bold"><?= esc($order['patient_first_name'] . ' ' . $order['patient_last_name']) ?></dd>

                                    <dt class="col-sm-4 text-muted">Patient ID:</dt>
                                    <dd class="col-sm-8"><?= esc($order['patient_id_code']) ?></dd>

                                    <dt class="col-sm-4 text-muted">Phone Number:</dt>
                                    <dd class="col-sm-8"><?= esc($order['phone_number']) ?></dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h4 class="text-info"><i class="fas fa-notes-medical mr-1"></i> Order & Doctor Details</h4>
                                <dl class="row mt-3">
                                    <dt class="col-sm-4 text-muted">Doctor Name:</dt>
                                    <dd class="col-sm-8 font-weight-bold"><?= esc($order['doctor_first_name'] . ' ' . $order['doctor_last_name']) ?></dd>

                                    <dt class="col-sm-4 text-muted">Order Date:</dt>
                                    <dd class="col-sm-8"><?= esc(date('Y-m-d H:i', strtotime($order['created_at']))) ?></dd>
                                    
                                    <dt class="col-sm-4 text-muted">Status:</dt>
                                    <dd class="col-sm-8">
                                        <?php 
                                            $status = esc($order['status']);
                                            $badgeClass = 'badge-secondary';
                                            if ($status == 'Completed') $badgeClass = 'badge-success'; 
                                            elseif ($status == 'In Progress') $badgeClass = 'badge-warning'; 
                                            else $badgeClass = 'badge-secondary'; 
                                        ?>
                                        <span class="badge <?= $badgeClass ?> text-md"><?= $status ?></span>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3 text-dark"><i class="fas fa-microscope mr-1"></i> Diagnostic Test Results</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-valign-middle">
                                <thead class="bg-gradient-gray">
                                    <tr>
                                        <th style="width: 25%;">Test Name</th>
                                        <th style="width: 15%;">Test Type</th>
                                        <th style="width: 35%;">Result / Notes</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 15%;">Files</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orderItems)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No tests found for this order.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= esc($item['test_name']) ?></td>
                                                <td><?= esc($item['test_type'] ?? 'N/A') ?></td> 
                                                <td><?= nl2br(esc($item['result'] ?? 'No result entered.')) ?></td>
                                                <td>
                                                    <?php 
                                                        $itemStatus = esc($item['status']);
                                                        $itemBadgeClass = 'badge-secondary';
                                                        if ($itemStatus == 'Completed') $itemBadgeClass = 'badge-success'; 
                                                        elseif ($itemStatus == 'In Progress') $itemBadgeClass = 'badge-info'; 
                                                    ?>
                                                    <span class="badge <?= $itemBadgeClass ?>"><?= $itemStatus ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($item['files'])): ?>
                                                        <ul class="list-unstyled mb-0">
                                                            <?php foreach ($item['files'] as $file): ?>
                                                                <li>
                                                                    <a href="<?= base_url('diagnostics/reports/file/' . $file['id']) ?>" target="_blank" class="text-primary text-sm" title="<?= esc($file['file_name']) ?>">
                                                                        <i class="fas fa-file-download mr-1"></i> <?= esc(substr($file['file_name'], 0, 15)) . '...' ?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span class="text-muted">None</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($order['remarks'])): ?>
                            <div class="callout callout-secondary mt-4">
                                <h5 class="text-secondary"><i class="fas fa-clipboard-list mr-1"></i> Global Order Remarks:</h5>
                                <p class="mb-0"><?= nl2br(esc($order['remarks'])) ?></p>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="#" onclick="window.history.back();" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i> Back</a>
                        <a href="<?= base_url('diagnostics/results/enter/' . $order['id']) ?>" class="btn btn-info">
                            <i class="fas fa-edit mr-1"></i> Edit Results
                        </a>
                    </div>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>