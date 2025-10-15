<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Register New User <i class="fas fa-user-plus text-primary"></i></h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('users') ?>">Users</a></li>
                    <li class="breadcrumb-item active">Register New</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card card-outline card-primary shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-id-card-alt mr-1"></i> New Staff/User Registration Form</h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Error!</h5>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Errors!</h5>
                                <ul>
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('users/save') ?>" method="post">
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary"><i class="fas fa-user"></i> Personal Information</legend>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-pen"></i></span></div>
                                            <input type="text" name="first_name" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" required value="<?= old('first_name') ?>" placeholder="Enter first name">
                                            <?php if (session('errors.first_name')): ?><div class="invalid-feedback"><?= session('errors.first_name') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-pen"></i></span></div>
                                            <input type="text" name="last_name" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" required value="<?= old('last_name') ?>" placeholder="Enter last name">
                                            <?php if (session('errors.last_name')): ?><div class="invalid-feedback"><?= session('errors.last_name') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number">Phone Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                            <input type="text" name="phone_number" class="form-control <?= session('errors.phone_number') ? 'is-invalid' : '' ?>" value="<?= old('phone_number') ?>" placeholder="e.g., +1234567890">
                                            <?php if (session('errors.phone_number')): ?><div class="invalid-feedback"><?= session('errors.phone_number') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="address">Address</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                                            <textarea name="address" class="form-control" rows="1" placeholder="Enter full address..."><?= old('address') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-info"><i class="fas fa-lock"></i> Credentials & Access</legend>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                            <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" required value="<?= old('email') ?>" placeholder="Enter email address">
                                            <?php if (session('errors.email')): ?><div class="invalid-feedback"><?= session('errors.email') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
                                            <input type="password" name="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" required placeholder="Enter password">
                                            <?php if (session('errors.password')): ?><div class="invalid-feedback"><?= session('errors.password') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-success"><i class="fas fa-briefcase"></i> Role & Status</legend>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role_id">Role <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tag"></i></span></div>
                                            <select name="role_id" class="form-control select2 <?= session('errors.role_id') ? 'is-invalid' : '' ?>" required>
                                                <option value="">Select a Role</option>
                                                <?php if (isset($roles) && is_array($roles)) : ?>
                                                    <?php foreach ($roles as $role) : ?>
                                                        <option value="<?= esc($role['id']) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php if (session('errors.role_id')): ?><div class="invalid-feedback"><?= session('errors.role_id') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-circle-notch"></i></span></div>
                                            <select name="status" class="form-control select2-status <?= session('errors.status') ? 'is-invalid' : '' ?>" required>
                                                <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                            </select>
                                            <?php if (session('errors.status')): ?><div class="invalid-feedback"><?= session('errors.status') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="card-footer clearfix">
                                <div class="float-right">
                                    <a href="<?= base_url('users') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for the Role dropdown
        $('.select2').select2({
            theme: 'bootstrap4', 
            placeholder: 'Select a Role',
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for the Status dropdown (use a different class to distinguish)
        $('.select2-status').select2({
            theme: 'bootstrap4', 
            minimumResultsForSearch: Infinity, // Hides search box for simple options
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>