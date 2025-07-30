<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Medicine Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicine Categories List</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('pharmacy/categories/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Category
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
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories) && is_array($categories)) : ?>
                                <?php foreach ($categories as $category) : ?>
                                    <tr>
                                        <td><?= esc($category['id']) ?></td>
                                        <td><?= esc($category['name']) ?></td>
                                        <td><?= esc($category['description']) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/categories/edit/' . $category['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                            <form action="<?= site_url('pharmacy/categories/delete/' . $category['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="text-center">No categories found.</td>
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