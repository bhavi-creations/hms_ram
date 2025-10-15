<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Users List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Users List</h3>
                <div class="card-tools">
                    <a href="<?= base_url('users/register') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus mr-1"></i> Add New User
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <table id="usersTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">S.no</th> 
                            <th style="width: 5%;">ID</th>
                            <th style="width: 25%;">Name</th>
                            <th style="width: 20%;">Email</th>
                            <th style="width: 15%;">Role</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; // Initialize the serial number counter ?>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td class="text-center"><?= $sn++ ?></td> 
                                <td><?= esc($user['id']) ?></td>
                                <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['role_name']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'bg-secondary';
                                    if (strtolower($user['status']) == 'active') {
                                        $statusClass = 'bg-success';
                                    } elseif (strtolower($user['status']) == 'inactive' || strtolower($user['status']) == 'blocked') {
                                        $statusClass = 'bg-danger';
                                    } elseif (strtolower($user['status']) == 'pending') {
                                        $statusClass = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= esc($user['status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('users/view/' . $user['id']) ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?= base_url('users/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the user: <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>?');" title="Delete User">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $("#usersTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // Custom buttons with icons and AdminLTE styling
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            // Order by Name (column 2) initially
            "order": [[ 2, "asc" ]], 
            // Disable ordering on S.No (0) and Actions (6)
            "columnDefs": [
                { "orderable": false, "targets": [0, 6] }
            ]
        }).buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>