<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create New Salesperson</h1>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <?= implode('<br>', session()->getFlashdata('errors')) ?>
                    </div>
                <?php endif; ?>

                <?= form_open('pharmacy/salespersons/store') ?>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?= old('address') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Salesperson</button>
                <?= form_close() ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>