<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Test Types Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Test Types</li> 
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline rounded-lg shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list-alt mr-2"></i> List of Test Types</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('laboratory/types/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Add Test Type
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <table id="typesTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No.</th>
                                    <th style="width: 10%;">ID</th>
                                    <th style="width: 30%;">Type Name</th>
                                    <th>Description</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($types)): ?>
                                    <?php $sno = 1; ?>
                                    <?php foreach($types as $type): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td> 
                                            <td><?= esc($type['id']) ?></td>
                                            <td><?= esc($type['name']) ?></td>
                                            <td><?= esc($type['description']) ?></td>
                                            <td>
                                                <a href="<?= base_url('laboratory/types/edit/' . $type['id']) ?>" class="btn btn-sm btn-warning" title="Edit Type">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger delete-type-btn" data-id="<?= $type['id'] ?>" data-name="<?= esc($type['name']) ?>" title="Delete Type">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">No test types found.</td></tr>
                                <?php endif; ?>
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
        // Initialize DataTables
        var table = $('#typesTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "dom": 'Bfrtip', // Enable buttons
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            // Default sort by ID column (index 1) in ascending order
            "order": [
                [1, 'asc']
            ],
            "columnDefs": [
                { 
                    "orderable": false, 
                    "searchable": false, 
                    "targets": [0, 4] // Disable sorting/searching on S.No. (0) and Actions (4)
                }
            ]
        }).buttons().container().appendTo('#typesTable_wrapper .col-md-6:eq(0)');

        // Custom function to re-number the S.No column after sort/search/paging
        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // SweetAlert2 for delete confirmation
        $('.delete-type-btn').on('click', function(e) {
            e.preventDefault();
            const typeId = $(this).data('id');
            const typeName = $(this).data('name');
            const deleteUrl = '<?= base_url('laboratory/types/delete/') ?>' + typeId;

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Swal.fire({
                title: 'Confirm Deletion',
                text: "Are you sure you want to delete the test type '" + typeName + "'? All associated tests might be affected.",
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