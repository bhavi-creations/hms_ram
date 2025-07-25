<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title">All Current Appointments</h3>
                <div class="card-tools">
                    <a href="<?= base_url('appointments/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Schedule New Appointment
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

                <table id="appointmentsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Patient Name</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $index => $appt): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($appt['patient_first_name'] . ' ' . $appt['patient_last_name']) ?></td>
                                    <td><?= esc($appt['doctor_first_name'] . ' ' . $appt['doctor_last_name']) ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($appt['appointment_date']))) ?></td>
                                    <td><?= esc(date('h:i A', strtotime($appt['appointment_time']))) ?></td>
                                    <td>
                                        <span class="badge
                                            <?php
                                                if ($appt['status'] == 'Confirmed') echo 'bg-primary';
                                                else if ($appt['status'] == 'Pending') echo 'bg-warning';
                                                else if ($appt['status'] == 'Cancelled') echo 'bg-danger'; // Though not expected here, good to have
                                                else if ($appt['status'] == 'Completed') echo 'bg-success'; // Though not expected here, good to have
                                                else echo 'bg-secondary';
                                            ?>
                                        "><?= esc($appt['status']) ?></span>
                                    </td>
                                    <td><?= esc($appt['reason_for_visit']) ?></td>
                                    <td>
                                        <a href="<?= base_url('appointments/edit/' . $appt['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('appointments/delete/' . $appt['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">No current appointments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

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
        $("#appointmentsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#appointmentsTable_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables Core and ALL Plugins CSS loaded from CDN for this specific page -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<?= $this->endSection() ?>
