<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('doctor/appointments') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to My Appointments
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <?php if (!empty($appointment)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Appointment ID:</strong> <?= esc($appointment['id']) ?></p>
                                <p><strong>Patient Name:</strong> <?= esc($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?></p>
                                <p><strong>Doctor Name:</strong> Dr. <?= esc($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></p>
                                <p><strong>Appointment Date:</strong> <?= esc(date('M d, Y', strtotime($appointment['appointment_date']))) ?></p>
                                <p><strong>Appointment Time:</strong> <?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Reason for Visit:</strong> <?= esc($appointment['reason_for_visit']) ?></p>
                                <p>
                                    <strong>Status:</strong> 
                                    <span class="badge 
                                        <?php 
                                            if ($appointment['status'] == 'Completed') echo 'bg-success';
                                            else if ($appointment['status'] == 'Confirmed') echo 'bg-primary';
                                            else if ($appointment['status'] == 'Pending') echo 'bg-warning';
                                            else if ($appointment['status'] == 'Cancelled') echo 'bg-danger';
                                            else echo 'bg-secondary';
                                        ?>
                                    "><?= esc($appointment['status']) ?></span>
                                </p>
                                <p><strong>Created At:</strong> <?= esc(date('M d, Y h:i A', strtotime($appointment['created_at']))) ?></p>
                                <p><strong>Last Updated:</strong> <?= esc(date('M d, Y h:i A', strtotime($appointment['updated_at']))) ?></p>
                            </div>
                        </div>
                        <hr>

                        <!-- Patient Reports Section -->
                        <h5 class="mb-3">Patient Reports:</h5>
                        <?php if (!empty($patient_reports)): ?>
                            <div class="row">
                                <?php foreach ($patient_reports as $fileName): ?>
                                    <?php
                                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                        $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                                    ?>
                                    <div class="col-auto mb-3">
                                        <div class="card p-2 text-center" style="width: 220px; height: auto;">
                                            <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none">
                                                    <img src="<?= $fileUrl ?>" alt="Report Image" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;">
                                                    <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                </a>
                                            <?php elseif (strtolower($ext) === 'pdf'): ?>
                                                <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none">
                                                    <embed src="<?= $fileUrl ?>" type="application/pdf" width="100%" height="200px" style="border: 1px solid #ddd; border-radius: 4px;" />
                                                    <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><i class="fas fa-file-pdf text-danger me-1"></i> <?= esc($fileName) ?></small>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none">
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 200px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                                                        <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                        <small class="text-muted text-center px-1 text-truncate" style="width: 100%;" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                    </div>
                                                    <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No reports uploaded for this patient.</p>
                        <?php endif; ?>
                        <!-- End Patient Reports Section -->

                        <hr>
                        <div class="text-right">
                            <a href="<?= base_url('doctor/appointments/edit/' . $appointment['id']) ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Appointment details not found or you do not have permission to view this appointment.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
