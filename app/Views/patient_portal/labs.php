<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Lab Orders</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('patient-portal/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Lab Orders</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Laboratory Tests and Orders</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($labs)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Code</th>
                                <th>Order Date</th>
                                <th>Service/Test</th>
                                <th>Prescribed By</th>
                                <th>Status</th>
                                <th>Results</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($labs as $lab): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($lab['order_id_code']) ?></td>
                                <td><?= esc($lab['order_date']) ?></td>
                                <td><?= esc($lab['test_name'] ?? 'N/A') ?></td>
                                <td><?= esc($lab['doctor_id'] ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        $status = esc($lab['status']);
                                        $badgeClass = 'badge-secondary';
                                        if ($status == 'Pending') $badgeClass = 'badge-warning';
                                        if ($status == 'Processing') $badgeClass = 'badge-info';
                                        if ($status == 'Completed') $badgeClass = 'badge-success';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <td>
                                    <?php if ($status == 'Completed'): ?>
                                        <button class="btn btn-sm btn-success" onclick="alert('Lab Results for Order: <?= esc($lab['order_id_code']) ?> are ready.')">View Results</button>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        You have no active or completed lab orders.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
