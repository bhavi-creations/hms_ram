<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-file-invoice mr-2 text-primary"></i> <?= esc($title) ?>: <span class="text-info"><?= esc($order['order_id_code']) ?></span>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/orders') ?>">Orders</a></li>
                    <li class="breadcrumb-item active">Order Details</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card card-outline card-primary shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Order Summary and Patient Details</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('diagnostics/orders/edit/' . $order['id']) ?>" class="btn btn-sm btn-info mr-2">
                                <i class="fas fa-edit mr-1"></i> Edit Order
                            </a>
                            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Orders
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-list-alt mr-1"></i> Order Information</h5>
                                <dl class="row mb-4 border-left border-info pl-3">
                                    <dt class="col-sm-4 text-secondary">Order ID:</dt>
                                    <dd class="col-sm-8 font-weight-bold text-lg text-primary"><?= esc($order['order_id_code']) ?></dd>

                                    <dt class="col-sm-4 text-secondary">Status:</dt>
                                    <dd class="col-sm-8">
                                        <?php 
                                            $status = esc($order['status']);
                                            $badgeClass = 'badge-secondary';
                                            if ($status == 'Completed') $badgeClass = 'badge-success';
                                            else if ($status == 'Pending') $badgeClass = 'badge-warning';
                                            else if ($status == 'Processing') $badgeClass = 'badge-info';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> text-md"><?= $status ?></span>
                                    </dd>
                                    
                                    <dt class="col-sm-4 text-secondary">Order Date:</dt>
                                    <dd class="col-sm-8"><?= esc($order['order_date']) ?></dd>

                                    <dt class="col-sm-4 text-secondary">Ordered By:</dt>
                                    <dd class="col-sm-8"><?= esc($order['created_by_name']) ?></dd>
                                </dl>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-user-injured mr-1"></i> Patient/Doctor Details</h5>
                                <dl class="row mb-4 border-left border-info pl-3">
                                    <dt class="col-sm-4 text-secondary">Patient ID:</dt>
                                    <dd class="col-sm-8"><?= esc($order['patient_id_code']) ?></dd>

                                    <dt class="col-sm-4 text-secondary">Patient Name:</dt>
                                    <dd class="col-sm-8 font-weight-bold"><?= esc($order['patient_name']) ?></dd>

                                    <dt class="col-sm-4 text-secondary">Referred Doctor:</dt>
                                    <dd class="col-sm-8"><?= esc($order['doctor_name']) ?></dd>

                                    <dt class="col-sm-4 text-secondary">Last Updated:</dt>
                                    <dd class="col-sm-8"><?= esc($order['updated_at']) ?></dd>
                                </dl>
                            </div>
                        </div>

                        <hr class="mt-0">

                        <h4 class="text-dark mb-3"><i class="fas fa-vials mr-1"></i> Ordered Tests</h4>
                        <?php if (empty($orderItems)) : ?>
                            <div class="alert alert-warning">No tests found for this order.</div>
                        <?php else : ?>
                            <?php $totalPrice = 0; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mt-3">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Test Name</th>
                                            <!-- <th>Test Type</th> -->
                                            <th class="text-right">Price (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $counter = 1; ?>
                                        <?php foreach ($orderItems as $item) : ?>
                                            <?php $totalPrice += (float)$item['price']; ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= esc($item['test_name']) ?></td>
                                                <!-- <td><?= esc($item['test_type'] ?? 'N/A') ?></td> -->
                                                <td class="text-right">₹<?= esc(number_format($item['price'] ?? 0, 2)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4 offset-md-8">
                                    <table class="table table-sm table-borderless">
                                        <tr class="text-lg">
                                            <th class="text-right border-top pt-3">Total Charges:</th>
                                            <td class="text-right font-weight-bold text-success border-top pt-3">
                                                ₹<?= esc(number_format($totalPrice, 2)) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order['remarks'])): ?>
                            <hr class="mt-4">
                            <h4 class="text-dark mb-3"><i class="fas fa-clipboard-list mr-1"></i> Remarks / Instructions</h4>
                            <div class="border p-3 bg-light rounded shadow-sm">
                                <p class="mb-0 text-muted"><?= nl2br(esc($order['remarks'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>