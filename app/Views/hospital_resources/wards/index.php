<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Wards List</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('wards/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Ward
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
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

                    <table id="wardsTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th> <!-- Changed from ID to S.No -->
                                <th>Ward Name</th>
                                <th>Description</th>
                                <th>Capacity</th>
                                <th>Bed Prefix</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wards)) : ?>
                                <?php $sno = 1; // Initialize S.No counter ?>
                                <?php foreach ($wards as $ward) : ?>
                                    <tr>
                                        <td><?= $sno++ ?></td> <!-- Display S.No and increment -->
                                        <td><?= esc($ward['name']) ?></td>
                                        <td><?= esc($ward['description']) ?></td>
                                        <td><?= $ward['capacity'] ?></td>
                                        <td><?= esc($ward['bed_prefix']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $textColorStyle = ''; // Added for custom text color
                                            switch ($ward['status']) {
                                                case 'Active':
                                                    $statusClass = 'badge badge-success';
                                                    $textColorStyle = 'color: #000 !important;'; // Make text black for visibility
                                                    break;
                                                case 'Inactive':
                                                    $statusClass = 'badge badge-secondary';
                                                    $textColorStyle = 'color: #000 !important;'; // Make text black for visibility

                                                    break;
                                                case 'Under Maintenance':
                                                    $statusClass = 'badge badge-warning';
                                                    $textColorStyle = 'color: #000 !important;'; // Make text black for visibility

                                                    break;
                                                default:
                                                    $statusClass = 'badge badge-info';
                                                    $textColorStyle = 'color: #000 !important;'; // Make text black for visibility

                                                    break;
                                            }
                                            ?>
                                            <span class="<?= $statusClass ?>" style="<?= $textColorStyle ?>"><?= esc($ward['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('wards/edit/' . $ward['id']) ?>" class="btn btn-info btn-sm" title="Edit Ward">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm delete-ward-btn" data-id="<?= $ward['id'] ?>" title="Delete Ward">
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
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.container-fluid -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables & Plugins -->
<script src="<?= base_url('public/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>

<script>
    $(function() {
        // Initialize DataTables
        $("#wardsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "columnDefs": [ // Adjust DataTables to not sort by S.No initially
                { "orderable": false, "targets": 0 } // Disable sorting on the first column (S.No)
            ]
        }).buttons().container().appendTo('#wardsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for delete confirmation
        $('.delete-ward-btn').on('click', function(e) {
            e.preventDefault();
            const wardId = $(this).data('id');
            const deleteUrl = '<?= base_url('wards/delete/') ?>' + wardId;

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
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
