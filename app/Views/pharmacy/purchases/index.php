<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Purchases</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Purchases</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Orders List</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('pharmacy/purchases/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create New Purchase
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchases) && is_array($purchases)) : ?>
                                <?php foreach ($purchases as $purchase) : ?>
                                    <tr>
                                        <td><?= esc($purchase['id']) ?></td>
                                        <td><?= esc($purchase['supplier_name']) ?></td> <td><?= esc(date('Y-m-d', strtotime($purchase['purchase_date']))) ?></td>
                                        <td><?= esc(number_format($purchase['total_amount'], 2)) ?></td>
                                        <td><?= esc($purchase['status']) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/purchases/view/' . $purchase['id']) ?>" class="btn btn-sm btn-info">View</a>
                                            <a href="<?= site_url('pharmacy/purchases/receive-stock/' . $purchase['id']) ?>" class="btn btn-sm btn-success">Receive Stock</a>
                                            </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No purchase orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    </div>
<?= $this->endSection() ?>