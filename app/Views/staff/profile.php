<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-user-circle mr-2"></i> My Profile</h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div></div></div></div>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                src="<?= base_url('public/assets/img/default-avatar.png') ?>" 
                                alt="User profile picture" style="height: 100px; width: 100px;">
                        </div>

                        <h3 class="profile-username text-center mt-3"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h3>

                        <p class="text-muted text-center"><?= esc($user['role_name']) ?></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="float-right badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                    <?= esc(ucfirst($user['status'])) ?>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Username</b> <a class="float-right text-muted"><?= esc($user['username']) ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Last Login</b> <a class="float-right text-muted"><?= esc($user['last_login'] ?? 'Never') ?></a>
                            </li>
                        </ul>
                        </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="card shadow-lg">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="profileTabs">
                            <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab"><i class="fas fa-info-circle mr-1"></i> Personal Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab"><i class="fas fa-lock mr-1"></i> Security (Change Password)</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <div class="tab-content">
                            <div class="tab-pane active" id="details">
                                <form class="form-horizontal" action="<?= site_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="active_tab" value="details">

                                    <div class="form-group row">
                                        <label for="first_name" class="col-sm-2 col-form-label">First Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                                <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                                                       id="first_name" name="first_name" 
                                                       value="<?= old('first_name', esc($user['first_name'])) ?>" required>
                                                <?php if (session('errors.first_name')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.first_name') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="last_name" class="col-sm-2 col-form-label">Last Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                                <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                                                       id="last_name" name="last_name" 
                                                       value="<?= old('last_name', esc($user['last_name'])) ?>" required>
                                                <?php if (session('errors.last_name')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.last_name') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                                <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                                       id="email" name="email" 
                                                       value="<?= old('email', esc($user['email'])) ?>" required>
                                                <?php if (session('errors.email')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.email') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="phone_number" class="col-sm-2 col-form-label">Phone</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                                <input type="text" class="form-control <?= session('errors.phone_number') ? 'is-invalid' : '' ?>" 
                                                       id="phone_number" name="phone_number" 
                                                       value="<?= old('phone_number', esc($user['phone_number'])) ?>" placeholder="Optional">
                                                <?php if (session('errors.phone_number')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.phone_number') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-2 col-form-label">Address</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                                                <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                                                        id="address" name="address" rows="3" placeholder="Optional"><?= old('address', esc($user['address'])) ?></textarea>
                                                <?php if (session('errors.address')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.address') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row border-top pt-3 mt-4">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane" id="security">
                                <form class="form-horizontal" action="<?= site_url('profile/update_password') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="active_tab" value="security">

                                    <div class="alert alert-info border-left-info" role="alert">
                                        <i class="fas fa-info-circle mr-1"></i> To change your password, you must enter your current password first.
                                    </div>

                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-3 col-form-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
                                                <input type="password" class="form-control <?= session('errors.current_password') ? 'is-invalid' : '' ?>" 
                                                       id="current_password" name="current_password" required>
                                                <?php if (session('errors.current_password')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.current_password') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-3 col-form-label">New Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-unlock-alt"></i></span></div>
                                                <input type="password" class="form-control <?= session('errors.new_password') ? 'is-invalid' : '' ?>" 
                                                       id="new_password" name="new_password" required>
                                                <?php if (session('errors.new_password')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.new_password') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-sm-3 col-form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-check-double"></i></span></div>
                                                <input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" 
                                                       id="confirm_password" name="confirm_password" required>
                                                <?php if (session('errors.confirm_password')): ?>
                                                    <span class="invalid-feedback"><?= session('errors.confirm_password') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row border-top pt-3 mt-4">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt mr-1"></i> Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the active tab set by the controller after validation/success
    const activeTab = "<?= session()->getFlashdata('active_tab') ?? '' ?>";
    
    // Check if there are any validation errors
    const allErrors = <?= json_encode(session('errors') ?? []) ?>;
    
    // Determine the tab based on the session data
    let tabToActivate = 'details'; // Default to details

    if (activeTab === 'security' || allErrors.hasOwnProperty('current_password') || allErrors.hasOwnProperty('new_password') || allErrors.hasOwnProperty('confirm_password')) {
        // If the controller specified 'security' OR we have password errors, activate security tab
        tabToActivate = 'security';
    } else if (activeTab === 'details' || Object.keys(allErrors).length > 0) {
        // If the controller specified 'details' OR we have any other errors (e.g., name, email), activate details tab
        tabToActivate = 'details';
    }

    if (tabToActivate) {
        // Deactivate all tabs and panes
        document.querySelectorAll('#profileTabs a').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
            pane.classList.remove('active', 'show');
        });

        // Activate the desired tab header and content pane
        const header = document.querySelector(`#profileTabs a[href="#${tabToActivate}"]`);
        const pane = document.querySelector(`#${tabToActivate}`);
        
        if (header) {
            header.classList.add('active');
        }
        if (pane) {
            pane.classList.add('active', 'show');
        }
    }
});
</script>

<?= $this->endSection() ?>