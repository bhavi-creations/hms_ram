<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Generics</h1>
        <a href="<?= site_url('pharmacy/generics/create') ?>" class="btn btn-primary mb-3">Add New Generic</a>
    </section>

    <section class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (!empty($generics)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Generic Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn=1; foreach($generics as $generic): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= esc($generic['generic_name']) ?></td>
                            <td>
                                <a href="<?= site_url('pharmacy/generics/edit/' . $generic['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= site_url('pharmacy/generics/delete/' . $generic['id']) ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No generics found.</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
