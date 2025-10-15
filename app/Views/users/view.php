<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    User Profile: <span class="text-primary"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></span>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('users') ?>">Users</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card card-primary card-outline shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-id-badge mr-1"></i>
                            User Details
                        </h3>
                        <!-- <div class="card-tools">
                            <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit mr-1"></i> Edit User
                            </a>
                        </div> -->
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">ID</th>
                                    <td><span class="badge bg-primary">#<?= esc($user['id']) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Full Name</th>
                                    <td><i class="fas fa-user-circle mr-1 text-muted"></i> <?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><i class="fas fa-envelope mr-1 text-muted"></i> <?= esc($user['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <?php 
                                            // Determine badge color for Role
                                            $role_name = esc($user['role_name'] ?? 'N/A'); // Assuming 'role_name' is passed if 'role_id' is an ID
                                            $role_color = match(strtolower($role_name)) {
                                                'admin', 'administrator' => 'danger',
                                                'doctor', 'physician' => 'success',
                                                'nurse', 'staff' => 'info',
                                                default => 'secondary',
                                            };
                                        ?>
                                        <span class="badge bg-<?= $role_color ?>"><i class="fas fa-user-tag"></i> <?= $role_name ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php 
                                            // Determine badge color for Status
                                            $status = esc($user['status']);
                                            $status_color = match(strtolower($status)) {
                                                'active' => 'success',
                                                'inactive' => 'warning',
                                                'suspended' => 'danger',
                                                default => 'secondary',
                                            };
                                        ?>
                                        <span class="badge bg-<?= $status_color ?>"><?= ucfirst($status) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td><i class="fas fa-phone mr-1 text-muted"></i> <?= esc($user['phone_number']) ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><i class="fas fa-map-marker-alt mr-1 text-muted"></i> <?= esc($user['address']) ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Last Login</th>
                                    <td><i class="fas fa-clock mr-1 text-muted"></i> <?= esc($user['last_login'] ?? 'Never') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('users') ?>" class="btn btn-default"><i class="fas fa-arrow-circle-left mr-1"></i> Back to Users List</a>
                    </div>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>