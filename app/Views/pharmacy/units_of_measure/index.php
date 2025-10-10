<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><?= esc($title) ?></h4>
            <a href="<?= site_url('pharmacy/units_of_measure/create') ?>" class="btn btn-primary">Add New Unit</a>
        </div>
        <div class="card-body">
            <!-- Flash messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
      

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Unit Name</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units) && is_array($units)): ?>
                        <?php $serial_number = 1; ?> <?php foreach ($units as $unit): ?>
                            <tr>
                                <td><?= $serial_number++ ?></td>
                                <td><?= esc($unit['name']) ?></td>
                                <td class="text-right">
                                    <a href="<?= site_url('pharmacy/units_of_measure/edit/' . $unit['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= site_url('pharmacy/units_of_measure/delete/' . $unit['id']) ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this unit?');" style="display:inline;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No units of measure found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>