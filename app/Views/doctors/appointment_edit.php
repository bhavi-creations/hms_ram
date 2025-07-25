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
                    <?php if (isset($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($appointment)): ?>
                        <!-- Added enctype="multipart/form-data" for file uploads -->
                        <?= form_open_multipart('doctor/appointments/update/' . esc($appointment['id'])) ?>
                            <?= csrf_field() ?>

                            <input type="hidden" name="doctor_id" value="<?= esc($appointment['doctor_id']) ?>">
                            <input type="hidden" name="patient_id" value="<?= esc($appointment['patient_id']) ?>">

                            <div class="form-group">
                                <label for="patientName">Patient Name</label>
                                <input type="text" class="form-control" id="patientName" value="<?= esc($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label for="doctorName">Doctor Name</label>
                                <select class="form-control" id="doctorName" name="doctor_id" disabled>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?= esc($doc['id']) ?>" <?= ($doc['id'] == $appointment['doctor_id']) ? 'selected' : '' ?>>
                                            Dr. <?= esc($doc['first_name'] . ' ' . $doc['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Doctors can only update their own appointments.</small>
                            </div>

                            <div class="form-group">
                                <label for="appointment_date">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= esc($appointment['appointment_date']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="appointment_time">Appointment Time</label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?= esc($appointment['appointment_time']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="reason_for_visit">Reason for Visit</label>
                                <textarea class="form-control" id="reason_for_visit" name="reason_for_visit" rows="3"><?= esc($appointment['reason_for_visit']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Pending" <?= ($appointment['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="Confirmed" <?= ($appointment['status'] == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Completed" <?= ($appointment['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= ($appointment['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>

                            <hr>
                            <h5>Manage Patient Reports</h5>
                            <p class="text-muted">Upload new reports or delete existing ones.</p>

                            <!-- Display Existing Reports with Delete Option -->
                            <?php if (!empty($patient_reports)): ?>
                                <div class="form-group">
                                    <label>Existing Reports:</label>
                                    <div class="row">
                                        <?php foreach ($patient_reports as $fileName): ?>
                                            <?php
                                                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                                $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                                            ?>
                                            <div class="col-auto mb-2">
                                                <div class="card p-2 text-center" style="width: 150px;">
                                                    <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none">
                                                        <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                            <img src="<?= $fileUrl ?>" alt="Report Image" class="img-fluid rounded" style="max-height: 100px; object-fit: contain;">
                                                        <?php elseif (strtolower($ext) === 'pdf'): ?>
                                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                        <?php endif; ?>
                                                        <small class="d-block mt-1 text-muted text-truncate" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                    </a>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" name="delete_reports[]" value="<?= esc($fileName) ?>" id="deleteReport<?= esc($fileName) ?>">
                                                        <label class="form-check-label" for="deleteReport<?= esc($fileName) ?>">Delete</label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p>No existing reports.</p>
                            <?php endif; ?>

                            <!-- Upload New Reports -->
                            <div class="form-group">
                                <label for="patient_reports">Upload New Reports (PDF, JPG, PNG, GIF - Max 5MB per file)</label>
                                <input type="file" class="form-control-file" id="patient_reports" name="patient_reports[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif">
                                <small class="form-text text-muted">You can select multiple files.</small>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">Update Appointment & Reports</button>
                            </div>
                        <?= form_close() ?>
                    <?php else: ?>
                        <div class="alert alert-info">Appointment not found or you do not have permission to edit this appointment.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
