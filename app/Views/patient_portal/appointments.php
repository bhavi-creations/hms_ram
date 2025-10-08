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
                                <th>Doctor ID</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($appointment['appointment_id']) ?></td>
                                <td><?= esc($appointment['appointment_date']) ?></td>
                                <td><?= esc($appointment['appointment_time']) ?></td>
                                <td><?= esc($appointment['doctor_id']) ?></td>
                                <td><?= esc(substr($appointment['reason'], 0, 50)) . (strlen($appointment['reason']) > 50 ? '...' : '') ?></td>
                                <td>
                                    <?php 
                                        $status = esc($appointment['status']);
                                        $badgeClass = 'badge-secondary';
                                        if ($status == 'Scheduled') $badgeClass = 'badge-primary';
                                        if ($status == 'Completed') $badgeClass = 'badge-success';
                                        if ($status == 'Cancelled') $badgeClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="alert('Viewing details for Appointment ID: <?= esc($appointment['appointment_id']) ?>')">View</button>
                                </td>
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
