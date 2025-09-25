    <?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('users/save') ?>" method="post">
            <input type="hidden" name="id" value="<?= esc($user['id']) ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" value="<?= esc($user['first_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" value="<?= esc($user['last_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" value="<?= esc($user['email']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" value="<?= esc($user['phone_number']) ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="address">Address</label>
                    <input type="text" name="address" value="<?= esc($user['address']) ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status">Status</label>
                    <select name="status" class="form-control select2">
                        <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary mt-2">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
