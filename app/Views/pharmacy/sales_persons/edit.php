<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Salesperson</h1>
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

                <?= form_open('pharmacy/salespersons/update/' . $salesperson['id']) ?>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $salesperson['first_name']) ?>">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $salesperson['last_name']) ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone', $salesperson['phone']) ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email', $salesperson['email']) ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?= old('address', $salesperson['address']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" <?= (old('status', $salesperson['status']) == 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (old('status', $salesperson['status']) == 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Salesperson</button>
                <?= form_close() ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>