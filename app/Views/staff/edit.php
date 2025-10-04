<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Staff Member: <?= esc($staff['first_name'] . ' ' . $staff['last_name']) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('staff') ?>">Staff List</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Update Details for Staff ID: <?= esc($staff['id']) ?></h5>
                    </div>
                    <!-- Form points to the update method in the Staff controller -->
                    <?= form_open('staff/update/' . $staff['id']) ?>
                    <div class="card-body">
                        <!-- Display Validation Errors/Success -->
                        <?php if (session()->get('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session()->get('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $staff['first_name']) ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $staff['last_name']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="role_id">Staff Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= old('role_id', $staff['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                            <?= esc($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">Username (for login) <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="<?= old('username', $staff['username']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= old('email', $staff['email']) ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="<?= old('phone_number', $staff['phone_number']) ?>">
                            </div>
                        </div>

                        <div class="row">
                             <div class="form-group col-md-6">
                                <label for="password">Change Password (Leave blank to keep current)</label>
                                <!-- The password field is optional on edit -->
                                <input type="password" name="password" class="form-control">
                                <small class="form-text text-muted">Enter a new password only if you wish to change it.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Account Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="active" <?= old('status', $staff['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= old('status', $staff['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="suspended" <?= old('status', $staff['status']) == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" class="form-control"><?= old('address', $staff['address']) ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sync-alt"></i> Update Staff
                        </button>
                        <a href="<?= base_url('staff') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
