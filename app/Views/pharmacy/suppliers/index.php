<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Suppliers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Suppliers</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Suppliers List</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('pharmacy/suppliers/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Supplier
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
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($suppliers) && is_array($suppliers)) : ?>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <tr>
                                        <td><?= esc($supplier['id']) ?></td>
                                        <td><?= esc($supplier['name']) ?></td>
                                        <td><?= esc($supplier['contact_person']) ?></td>
                                        <td><?= esc($supplier['phone']) ?></td>
                                        <td><?= esc($supplier['email']) ?></td>
                                        <td><?= esc($supplier['address']) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                            <form action="<?= site_url('pharmacy/suppliers/delete/' . $supplier['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No suppliers found.</td>
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