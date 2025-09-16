<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Brands</h1>
        <a href="<?= site_url('pharmacy/brands/create') ?>" class="btn btn-primary mb-3">Add New Brand</a>
    </section>

    <section class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (!empty($brands)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Brand Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn=1; foreach($brands as $brand): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= esc($brand['brand_name']) ?></td>
                            <td>
                                <a href="<?= site_url('pharmacy/brands/edit/' . $brand['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= site_url('pharmacy/brands/delete/' . $brand['id']) ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No brands found.</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
