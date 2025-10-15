<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-file-pdf mr-2 text-primary"></i> Lab Report
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/results') ?>">Lab Results</a></li>
                    <li class="breadcrumb-item active">Report: <?= esc($order['id']) ?></li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                
                <div class="card card-outline card-primary shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Report Details for Order #<span class="text-info font-weight-bold"><?= esc($order['order_id_code'] ?? $order['id']) ?></span></h3>
                        
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-4">
                            
                            <div class="col-md-6">
                                <div class="p-3 border rounded-lg bg-light h-100">
                                    <h5 class="text-primary"><i class="fas fa-user-injured mr-1"></i> Patient Information</h5>
                                    <hr class="mt-1 mb-2">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Patient Name:</dt>
                                        <dd class="col-sm-8"><?= esc($order['patient_first_name'] . ' ' . $order['patient_last_name']) ?></dd>

                                        <dt class="col-sm-4">Patient ID:</dt>
                                        <dd class="col-sm-8"><?= esc($order['patient_id_code']) ?></dd>

                                        <dt class="col-sm-4">Phone Number:</dt>
                                        <dd class="col-sm-8"><?= esc($order['phone_number']) ?></dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 border rounded-lg bg-light h-100">
                                    <h5 class="text-secondary"><i class="fas fa-hospital-user mr-1"></i> Order Details</h5>
                                    <hr class="mt-1 mb-2">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Doctor Name:</dt>
                                        <dd class="col-sm-8"><?= esc($order['doctor_first_name'] . ' ' . $order['doctor_last_name']) ?></dd>

                                        <dt class="col-sm-4">Order Date:</dt>
                                        <dd class="col-sm-8"><?= esc(date('Y-m-d H:i', strtotime($order['order_date']))) ?></dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            <?php 
                                                $status = esc($order['status']);
                                                $badge_class = match(strtolower($status)) {
                                                    'completed' => 'badge-success',
                                                    'in progress' => 'badge-warning text-dark',
                                                    default => 'badge-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $badge_class ?> p-2"><?= esc($status) ?></span>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3 text-info"><i class="fas fa-vials mr-1"></i> Detailed Test Results</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="bg-info text-white">
                                    <tr>
                                        <th>Test Name</th>
                                        <th style="width: 15%;">Test Type</th>
                                        <th style="width: 30%;">Result / Interpretation</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 15%;">Attached Files</th>
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
                                                <td><span class="font-weight-bold"><?= esc($item['test_name']) ?></span></td>
                                                <td><?= esc($item['test_type_name']) ?></td>
                                                <td><?= nl2br(esc($item['result'] ?? 'N/A')) ?></td>
                                                <td>
                                                    <?php 
                                                        $item_status = esc($item['status']);
                                                        $item_badge_class = match(strtolower($item_status)) {
                                                            'completed' => 'badge-success',
                                                            'in progress' => 'badge-warning text-dark',
                                                            default => 'badge-secondary',
                                                        };
                                                    ?>
                                                    <span class="badge <?= $item_badge_class ?>"><?= esc($item_status) ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($item['files'])): ?>
                                                        <ul class="list-unstyled mb-0">
                                                            <?php foreach ($item['files'] as $file): ?>
                                                                <li>
                                                                    <a href="<?= base_url('public/uploads/laboratory/' . $file['file_path']) ?>" target="_blank" class="text-sm">
                                                                        <i class="fas fa-file-pdf mr-1"></i> <?= esc($file['file_name']) ?>
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

                        <div class="mt-5">
                            <h4 class="text-secondary"><i class="fas fa-notes-medical mr-1"></i> General Remarks</h4>
                            <div class="callout callout-info">
                                <p class="mb-0"><?= esc($order['remarks']) ?? 'No general remarks recorded for this order.' ?></p>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-right">
                             <a href="#" onclick="window.history.back();" class="btn btn-default"><i class="fas fa-arrow-circle-left mr-1"></i> Back to Results</a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>