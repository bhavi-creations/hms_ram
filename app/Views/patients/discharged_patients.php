<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?></h3>
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

                    <table id="dischargedPatientsTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Discharge Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients)) : ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr>
                                        <td><?= $sno++ ?></td>
                                        <td><?= esc($patient['patient_id_code']) ?></td>
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td>
                                            <?php
                                            // Assuming 'updated_at' often reflects the discharge time for simplicity
                                            // If you store a specific 'discharge_date' in patient_admissions, you'd fetch it here.
                                            echo date('Y-m-d H:i', strtotime($patient['updated_at']));
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-success" style="color: #000 !important;"><?= esc($patient['patient_type']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- You might not want edit/delete for discharged patients directly from here,
                                                 but rather a medical record view or re-admission option.
                                                 Keeping them commented out for now. -->
                                            <!--
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-primary btn-sm" title="Edit Patient">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('patients/delete/' . $patient['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this patient?');" title="Delete Patient">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No discharged patients found.</td>
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
        $("#dischargedPatientsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "columnDefs": [ // Disable sorting on the first column (S.No)
                { "orderable": false, "targets": 0 }
            ]
        }).buttons().container().appendTo('#dischargedPatientsTable_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>
