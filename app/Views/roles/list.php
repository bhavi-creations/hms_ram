<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Roles List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Roles List</li> 
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary card-outline rounded-lg shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>All Roles</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('roles/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Add New Role
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <table id="rolesTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.no</th>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Management Level</th>
                                    <th>Reports To Role</th> 
                                    <th>Description</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <?php $sn = 1; // Initialize the serial number counter ?>
                                <?php foreach ($roles as $role) : ?>
                                    <tr>
                                        <td class="text-center"><?= $sn++ ?></td>
                                        <td><?= esc($role['id']) ?></td>
                                        <td><?= esc($role['name']) ?></td>
                                        <td><span class="badge badge-info"><?= esc($role['management_level']) ?></span></td>
                                        <td>
                                            <?php 
                                            // Note: 'reports_to_role_name' must be fetched via a JOIN in your controller
                                            if ($role['management_level'] === 'Team Member' && !empty($role['reports_to_role_name'])): 
                                            ?>
                                                <span class="badge badge-secondary"><?= esc($role['reports_to_role_name']) ?></span>
                                            <?php elseif ($role['management_level'] === 'Team Member'): ?>
                                                <span class="badge badge-danger">Not Assigned</span>
                                            <?php else: ?>
                                                ---
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($role['description']) ?></td>
                                        <td>
                                            <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-info" title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-danger delete-role-btn" data-id="<?= $role['id'] ?>" title="Delete Role">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Initialize DataTables with customized, icon-based buttons
        $("#rolesTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // Add DataTables Export Buttons
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            // Default sort on ID column (index 1)
            "order": [[ 1, "asc" ]],
            "columnDefs": [ 
                // Disable sorting on S.No (0) and Actions (6)
                { "orderable": false, "targets": [0, 6] } 
            ]
        }).buttons().container().appendTo('#rolesTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for delete confirmation
        $('.delete-role-btn').on('click', function(e) {
            e.preventDefault();
            const roleId = $(this).data('id');
            const deleteUrl = '<?= base_url('roles/delete/') ?>' + roleId;

            // Mixin for the Toast notification style
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! Deleting a role might affect associated users.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                } else {
                     Toast.fire({
                        icon: 'info',
                        title: 'Deletion cancelled.'
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>