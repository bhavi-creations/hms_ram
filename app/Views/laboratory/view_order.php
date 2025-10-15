<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-file-invoice mr-2 text-info"></i> Lab Order Details
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/orders') ?>">Lab Orders</a></li>
                    <li class="breadcrumb-item active">Order: <?= esc($order['order_id_code']) ?></li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <div class="card card-outline card-info shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">
                            Order Reference: <span class="text-primary font-weight-bold"><?= esc($order['order_id_code']) ?></span>
                        </h3>
                        <div class="card-tools">
                             <?php 
                                // Determine badge color for Status
                                $status = esc($order['status']);
                                $status_color = match(strtolower($status)) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary',
                                };
                            ?>
                            <span class="badge bg-<?= $status_color ?> text-lg p-2"><i class="fas fa-circle-notch mr-1"></i> <?= ucfirst($status) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fas fa-user-injured mr-1"></i> Patient Information</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Patient ID</b> <a class="float-right text-muted"><?= esc($order['patient_id_code']) ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Name</b> <a class="float-right text-muted"><?= esc($order['patient_name']) ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Phone Number</b> <a class="float-right text-muted"><?= esc($order['phone_number']) ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fas fa-clock mr-1"></i> Order Summary</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Ordered By</b> <a class="float-right text-muted"><?= esc($order['ordered_by_name']) ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Doctor</b> <a class="float-right text-muted"><?= esc($order['doctor_name']) ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Order Date</b> <a class="float-right text-muted"><?= esc($order['order_date']) ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> <h4 class="mt-4 mb-2"><i class="fas fa-vials mr-1 text-info"></i> Ordered Tests & Cost Breakdown</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Test Name</th>
                                        <th style="width: 15%;">Price (INR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalPrice = 0; ?>
                                    <?php foreach ($testItems as $item): ?>
                                        <tr>
                                            <td><?= esc($item['test_name']) ?></td>
                                            <td class="text-right"><?= number_format(esc($item['price']), 2) ?></td>
                                        </tr>
                                        <?php $totalPrice += $item['price']; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right"><strong>Total Price</strong></td>
                                        <td class="text-right bg-info text-white"><strong>₹<?= number_format(esc($totalPrice), 2) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if (!empty($order['remarks'])): ?>
                            <h4 class="mt-4 mb-2"><i class="fas fa-notes-medical mr-1 text-secondary"></i> Clinical Remarks</h4>
                            <div class="callout callout-info">
                                <p class="text-muted mb-0"><?= nl2br(esc($order['remarks'])) ?></p>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-default mr-2"><i class="fas fa-arrow-circle-left"></i> Back to Orders</a>
                            <?php if (strtolower($order['status']) === 'completed'): ?>
                                <a href="<?= base_url('laboratory/results/print/' . $order['id']) ?>" target="_blank" class="btn btn-success"><i class="fas fa-print"></i> Print Results</a>
                            <?php endif; ?>
                            <?php if (strtolower($order['status']) === 'pending'): ?>
                                <a href="<?= base_url('laboratory/orders/edit/' . $order['id']) ?>" class="btn btn-info"><i class="fas fa-edit"></i> Edit Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>