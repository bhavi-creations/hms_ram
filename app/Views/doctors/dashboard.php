<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Welcome, Dr. <?= esc($doctor_name) ?>!</h2>
            <hr>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">My Upcoming Appointments</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($appointments)): ?>
                        <table id="doctorAppointmentsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Patient Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1; ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= $sno++ ?></td>
                                        <td><?= esc($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?></td>
                                        <td><?= esc(date('M d, Y', strtotime($appointment['appointment_date']))) ?></td>
                                        <td><?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></td>
                                        <td><?= esc($appointment['reason_for_visit']) ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                    if ($appointment['status'] == 'Completed') echo 'bg-success';
                                                    else if ($appointment['status'] == 'Confirmed') echo 'bg-primary';
                                                    else if ($appointment['status'] == 'Pending') echo 'bg-warning';
                                                    else if ($appointment['status'] == 'Cancelled') echo 'bg-danger';
                                                    else echo 'bg-secondary';
                                                ?>
                                            "><?= esc($appointment['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('doctor/appointments/view/' . $appointment['id']) ?>" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('doctor/appointments/edit/' . $appointment['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Doctors typically don't delete appointments, but can cancel them -->
                                            <!-- You might want a specific 'cancel' action instead of 'delete' -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No appointments found for you.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables Core and ALL Plugins loaded from CDN for this specific page -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

<script>
    $(function () {
        $("#doctorAppointmentsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#doctorAppointmentsTable_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables Core and ALL Plugins CSS loaded from CDN for this specific page -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<?= $this->endSection() ?>
