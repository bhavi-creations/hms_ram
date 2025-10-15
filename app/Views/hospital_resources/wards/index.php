<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Wards List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Wards List</li> 
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
                        <h3 class="card-title"><i class="fas fa-hospital-alt mr-2"></i>All Wards</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('wards/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Add New Ward
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

                        <table id="wardsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No</th>
                                    <th style="width: 20%;">Ward Name</th>
                                    <th style="width: 35%;">Description</th>
                                    <th style="width: 10%;">Capacity</th>
                                    <th style="width: 10%;">Bed Prefix</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($wards)) : ?>
                                    <?php $sno = 1; // Initialize S.No counter ?>
                                    <?php foreach ($wards as $ward) : ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= esc($ward['name']) ?></td>
                                            <td><?= esc($ward['description']) ?></td>
                                            <td><?= $ward['capacity'] ?></td>
                                            <td><?= esc($ward['bed_prefix']) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $textColorStyle = ''; 
                                                switch ($ward['status']) {
                                                    case 'Active':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'Inactive':
                                                        $statusClass = 'badge bg-secondary';
                                                        break;
                                                    case 'Under Maintenance':
                                                        $statusClass = 'badge bg-warning';
                                                        $textColorStyle = 'color: #000 !important;'; // Ensure dark text on warning
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-info';
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>" style="<?= $textColorStyle ?>"><?= esc($ward['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('wards/edit/' . $ward['id']) ?>" class="btn btn-sm btn-info" title="Edit Ward">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger delete-ward-btn" data-id="<?= $ward['id'] ?>" title="Delete Ward">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No wards found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div></section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Initialize DataTables with customized, icon-based buttons
        $("#wardsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            "columnDefs": [ 
                { "orderable": false, "targets": [0, 6] } // Disable sorting on S.No (0) and Actions (6)
            ]
        }).buttons().container().appendTo('#wardsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for delete confirmation
        $('.delete-ward-btn').on('click', function(e) {
            e.preventDefault();
            const wardId = $(this).data('id');
            const deleteUrl = '<?= base_url('wards/delete/') ?>' + wardId;

            // Use SweetAlert2 from AdminLTE (Toastr style)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! All associated beds will also be deleted.",
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