<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Staff List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Staff List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">All Registered Staff</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('staff/register') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Register New Staff
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <table id="staffTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <!-- Changed ID to S.no -->
                                    <th style="width: 5%;">S.no</th> 
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($staff)): ?>
                                    <?php $sn = 1; // Initialize Serial Number counter ?>
                                    <?php foreach ($staff as $member): ?>
                                        <tr>
                                            <!-- Display and increment the serial number -->
                                            <td class="text-center"><?= $sn++ ?></td> 
                                            <td><?= esc($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                            <td>
                                                <!-- We joined the roles table in the controller to get the role_name -->
                                                <span class="badge bg-info"><?= esc($member['role_name'] ?? 'N/A') ?></span>
                                            </td>
                                            <td><?= esc($member['email']) ?></td>
                                            <td><?= esc($member['phone_number']) ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $member['status'] == 'active' ? 'bg-success' : ($member['status'] == 'inactive' ? 'bg-warning' : 'bg-danger') ?>">
                                                    <?= esc(ucfirst($member['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('staff/edit/' . $member['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('staff/delete/' . $member['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No staff members found with the defined roles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- You would need to include the DataTables assets in your main layout for this table to function -->
<?= $this->endSection() ?>
