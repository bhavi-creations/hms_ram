<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Returns</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Returns</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicine Returns List</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('pharmacy/returns/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Initiate New Return
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
                                <th>Sale ID</th>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Return Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($returns) && is_array($returns)) : ?>
                                <?php foreach ($returns as $return) : ?>
                                    <tr>
                                        <td><?= esc($return['id']) ?></td>
                                        <td><?= esc($return['sale_id']) ?></td>
                                        <td><?= esc($return['medicine_name']) ?></td> <td><?= esc($return['quantity']) ?></td>
                                        <td><?= esc($return['reason']) ?></td>
                                        <td><?= esc($return['status']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($return['return_date']))) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/returns/approve/' . $return['id']) ?>" class="btn btn-sm btn-warning">Approve/Process</a>
                                            </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No returns found.</td>
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