<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><?= esc($title) ?></h4>
            <a href="<?= site_url('pharmacy/units_of_measure') ?>" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <!-- Display validation errors -->
            <?php if (isset($errors) && is_array($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('pharmacy/units_of_measure/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Unit Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Unit</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
