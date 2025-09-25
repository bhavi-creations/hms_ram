<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Register New User</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('users/save') ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required value="<?= old('first_name') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required value="<?= old('last_name') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email">Email *</label>
                    <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password">Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?= old('phone_number') ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="address">Address</label>
                    <textarea name="address" class="form-control"><?= old('address') ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="role_id">Role *</label>
                    <select name="role_id" class="form-control select2" required>
                        <option value="">Select a Role</option>
                        <?php if (isset($roles) && is_array($roles)) : ?>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= esc($role['id']) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status">Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register User</button>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
