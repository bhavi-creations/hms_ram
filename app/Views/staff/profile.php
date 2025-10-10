<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Profile</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column: Profile Card -->
            <div class="col-md-3">
                <!-- Profile Image Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <!-- Use a placeholder or actual user avatar -->
                            <img class="profile-user-img img-fluid img-circle"
                                src="<?= base_url('public/assets/img/default-avatar.png') ?>" 
                                alt="User profile picture" style="height: 100px; width: 100px;">
                        </div>

                        <h3 class="profile-username text-center"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h3>

                        <p class="text-muted text-center"><?= esc($user['role_name']) ?></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="float-right badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                    <?= esc(ucfirst($user['status'])) ?>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>User ID</b> <a class="float-right"><?= esc($user['id']) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Tabs -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="profileTabs">
                            <!-- Note the default active tab is `details` -->
                            <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Personal Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab">Security (Change Password)</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <!-- Flash Message Display (for successful profile update) -->
                        <?php if (session()->getFlashdata('success') && session()->getFlashdata('active_tab') != 'security'): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Success!</h5>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <!-- Global Error Message Display (for failed profile update) -->
                

                        <div class="tab-content">
                            <!-- 1. Personal Details Tab (Now Editable Form) -->
                            <div class="tab-pane active" id="details">
                                <form class="form-horizontal" action="<?= site_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="active_tab" value="details">

                                    <!-- First Name -->
                                    <div class="form-group row">
                                        <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                                                   id="first_name" name="first_name" 
                                                   value="<?= old('first_name', esc($user['first_name'])) ?>" required>
                                            <?php if (session('errors.first_name')): ?>
                                                <span class="invalid-feedback"><?= session('errors.first_name') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="form-group row">
                                        <label for="last_name" class="col-sm-2 col-form-label">Last Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                                                   id="last_name" name="last_name" 
                                                   value="<?= old('last_name', esc($user['last_name'])) ?>" required>
                                            <?php if (session('errors.last_name')): ?>
                                                <span class="invalid-feedback"><?= session('errors.last_name') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Username (Display Only) -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Username</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static form-control"><?= esc($user['username']) ?></p>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                                   id="email" name="email" 
                                                   value="<?= old('email', esc($user['email'])) ?>" required>
                                            <?php if (session('errors.email')): ?>
                                                <span class="invalid-feedback"><?= session('errors.email') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Phone Number -->
                                    <div class="form-group row">
                                        <label for="phone_number" class="col-sm-2 col-form-label">Phone</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control <?= session('errors.phone_number') ? 'is-invalid' : '' ?>" 
                                                   id="phone_number" name="phone_number" 
                                                   value="<?= old('phone_number', esc($user['phone_number'])) ?>">
                                            <?php if (session('errors.phone_number')): ?>
                                                <span class="invalid-feedback"><?= session('errors.phone_number') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-2 col-form-label">Address</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                                                      id="address" name="address" rows="3"><?= old('address', esc($user['address'])) ?></textarea>
                                            <?php if (session('errors.address')): ?>
                                                <span class="invalid-feedback"><?= session('errors.address') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Last Login (Display Only) -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Last Login</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static form-control"><?= esc($user['last_login'] ?? 'Never') ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- 2. Security Tab (Change Password Form) -->
                            <div class="tab-pane" id="security">
                                <!-- Display session flash messages for security tab -->
                                <?php if (session()->getFlashdata('success') && session()->getFlashdata('active_tab') == 'security'): ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                                        <?= session()->getFlashdata('success') ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (session('errors') && session()->getFlashdata('active_tab') == 'security'): ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-ban"></i> Validation Error!</h5>
                                        <?php 
                                            // Only show password-related errors here
                                            $securityErrors = array_intersect_key(session('errors'), array_flip(['current_password', 'new_password', 'confirm_password']));
                                            if (!empty($securityErrors)) {
                                                echo implode('<br>', $securityErrors);
                                            } else if (session()->getFlashdata('error')) {
                                                echo session()->getFlashdata('error');
                                            }
                                        ?>
                                    </div>
                                <?php endif; ?>


                                <!-- Password Change Form -->
                                <form class="form-horizontal" action="<?= site_url('profile/update_password') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="active_tab" value="security">

                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-3 col-form-label">Current Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control <?= session('errors.current_password') ? 'is-invalid' : '' ?>" 
                                                   id="current_password" name="current_password" required>
                                            <?php if (session('errors.current_password') && session()->getFlashdata('active_tab') == 'security'): ?>
                                                <span class="invalid-feedback"><?= session('errors.current_password') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-3 col-form-label">New Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control <?= session('errors.new_password') ? 'is-invalid' : '' ?>" 
                                                   id="new_password" name="new_password" required>
                                            <?php if (session('errors.new_password') && session()->getFlashdata('active_tab') == 'security'): ?>
                                                <span class="invalid-feedback"><?= session('errors.new_password') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-sm-3 col-form-label">Confirm New Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" 
                                                   id="confirm_password" name="confirm_password" required>
                                            <?php if (session('errors.confirm_password') && session()->getFlashdata('active_tab') == 'security'): ?>
                                                <span class="invalid-feedback"><?= session('errors.confirm_password') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if a specific tab needs to be activated after redirect (e.g., due to validation errors)
    const activeTab = "<?= session()->getFlashdata('active_tab') ?? '' ?>";
    
    // Check if we have errors for the details tab
    const detailErrors = <?= json_encode(array_intersect_key(session('errors') ?? [], array_flip(['first_name', 'last_name', 'email', 'phone_number', 'address']))) ?>;
    
    // If we have detail errors, force the 'details' tab to be active
    const shouldActivateDetails = Object.keys(detailErrors).length > 0;

    if (activeTab === 'security' || shouldActivateDetails) {
        let tabToActivate = (shouldActivateDetails) ? 'details' : activeTab;

        // Deactivate all tabs
        document.querySelectorAll('#profileTabs a').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
            pane.classList.remove('active', 'show');
        });

        // Activate the desired tab header
        const header = document.querySelector(`#profileTabs a[href="#${tabToActivate}"]`);
        if (header) {
            header.classList.add('active');
        }

        // Activate the desired tab content pane
        const pane = document.querySelector(`#${tabToActivate}`);
        if (pane) {
            pane.classList.add('active', 'show');
        }
    }
});
</script>

<?= $this->endSection() ?>
