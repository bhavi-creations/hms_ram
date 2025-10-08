<!--
    File: app/Views/patient_portal/appointments_list.php
    Description: Displays a list of the patient's upcoming (Scheduled/Confirmed) appointments,
    using AdminLTE styling.
-->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Appointments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('patient-portal/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Appointments</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Scheduled Appointments</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($appointments)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Appointment ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <!-- FIX: Changed header to reflect the joined name, not the raw ID -->
                                <th>Doctor Name</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <!-- <th>Actions</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <!-- FIX: Changed array key from 'appointment_id' to the correct primary key 'id' -->
                                <td><?= esc($appointment['id'] ?? 'N/A') ?></td>
                                <td><?= esc($appointment['appointment_date'] ?? 'N/A') ?></td>
                                <td><?= esc($appointment['appointment_time'] ?? 'N/A') ?></td>
                                <!-- FIX: Changed array key from 'doctor_id' to the joined 'doctor_name' for display -->
                                <td><?= esc($appointment['doctor_name'] ?? 'Doctor Unknown') ?></td>
                                <td><?= esc(substr($appointment['reason'] ?? 'No reason provided', 0, 50)) . (strlen($appointment['reason'] ?? '') > 50 ? '...' : '') ?></td>
                                <td>
                                    <?php 
                                        $status = esc($appointment['status'] ?? 'Pending');
                                        $badgeClass = 'badge-secondary';
                                        if ($status == 'Scheduled') $badgeClass = 'badge-primary';
                                        if ($status == 'Completed') $badgeClass = 'badge-success';
                                        if ($status == 'Cancelled') $badgeClass = 'badge-danger';
                                        if ($status == 'Confirmed') $badgeClass = 'badge-info'; 
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <!-- <td>
                                   
                                    <a href="<?= base_url('patient-portal/appointments/view/' . esc($appointment['id'] ?? '0')) ?>" class="btn btn-sm btn-info">View</a>
                                </td> -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        You have no appointments scheduled at this time.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
