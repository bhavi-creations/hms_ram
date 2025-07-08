<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?>: <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" class="btn btn-warning btn-sm" title="Edit Doctor">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?= base_url('doctors') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <?php
                            $profilePictureUrl = !empty($doctor['profile_picture']) ? base_url('public/uploads/doctors/' . urlencode($doctor['profile_picture'])) : base_url('dist/img/default-avatar.png');
                            ?>
                            <img src="<?= esc($profilePictureUrl) ?>" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Picture">
                            <h4><?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h4>
                            <p class="text-muted"><?= esc($doctor['specialization']) ?></p>
                            <p class="text-muted">ID: <?= esc($doctor['doctor_id_code']) ?></p>

                            <?php if (!empty($doctor['signature_image'])): ?>
                                <hr>
                                <h5>Signature</h5>
                                <?php $signatureImageUrl = base_url('public/uploads/doctors/' . urlencode($doctor['signature_image'])); ?>
                                <img src="<?= esc($signatureImageUrl) ?>" class="img-fluid" style="max-width: 150px; border: 1px solid #ddd;" alt="Signature">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-9">
                            <h5><i class="fas fa-user-circle mr-1"></i> Personal Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Full Name :</b> <span class="float-right"><?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Doctor ID Code :</b> <span class="float-right"><?= esc($doctor['doctor_id_code']) ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Gender :</b> <span class="float-right"><?= esc($doctor['gender'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Date of Birth :</b> <span class="float-right"><?= esc($doctor['date_of_birth'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Address :</b> <span class="float-right"><?= esc($doctor['address'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>User ID :</b> <span class="float-right"><?= esc($doctor['user_id'] ?? 'N/A') ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Emergency Contact Name :</b> <span class="float-right"><?= esc($doctor['emergency_contact_name'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Emergency Contact Phone :</b> <span class="float-right"><?= esc($doctor['emergency_contact_phone'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Created At :</b> <span class="float-right"><?= esc($doctor['created_at'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Last Updated At :</b> <span class="float-right"><?= esc($doctor['updated_at'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Last Login At :</b> <span class="float-right"><?= esc($doctor['last_login_at'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Status</b> <span class="float-right">
                                                <span class="badge
                                                    <?php
                                                    $doctorStatus = $doctor['status'] ?? 'Unknown';
                                                    if ($doctorStatus == 'Active') echo 'bg-success';
                                                    else if ($doctorStatus == 'On Leave') echo 'bg-warning';
                                                    else if ($doctorStatus == 'Suspended' || $doctorStatus == 'Terminated') echo 'bg-danger';
                                                    else echo 'bg-secondary';
                                                    ?>">
                                                    <?= esc($doctorStatus) ?>
                                                </span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <hr>
                            <strong><i class="fas fa-book mr-1"></i> Bio</strong>
                            <p class="text-muted">
                                <?= esc($doctor['bio'] ?? 'N/A') ?>
                            </p>

                            <h5><i class="fas fa-briefcase-medical mr-1"></i> Professional Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Specialization :</b> <span class="float-right"><?= esc($doctor['specialization'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Qualification :</b> <span class="float-right"><?= esc($doctor['qualification'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Medical License No. :</b> <span class="float-right"><?= esc($doctor['medical_license_no'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Experience :</b> <span class="float-right"><?= esc($doctor['experience_years'] ?? 'N/A') ?> Years</span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Registration Number :</b> <span class="float-right"><?= esc($doctor['registration_number'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Medical Council :</b> <span class="float-right"><?= esc($doctor['medical_council'] ?? 'N/A') ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Department ID :</b> <span class="float-right"><?= esc($doctor['department_name'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Joining Date :</b> <span class="float-right"><?= esc($doctor['joining_date'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Employment Status :</b> <span class="float-right"><?= esc($doctor['employment_status'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Contract Type :</b> <span class="float-right"><?= esc($doctor['contract_type'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Designation :</b> <span class="float-right"><?= esc($doctor['designation'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Availability :</b> <span class="float-right"><?= ($doctor['is_available'] ?? 0) ? 'Available' : 'Not Available' ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h5><i class="fas fa-wallet mr-1"></i> Financial Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>OPD Consultation Fee :</b> <span class="float-right">₹<?= esc(number_format($doctor['opd_fee'] ?? 0, 2)) ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>IPD Charge Percentage :</b> <span class="float-right"><?= esc($doctor['ipd_charge_percentage'] ?? 'N/A') ?>%</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Bank Account Number :</b> <span class="float-right"><?= esc($doctor['bank_account_number'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Bank Name :</b> <span class="float-right"><?= esc($doctor['bank_name'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>IFSC Code :</b> <span class="float-right"><?= esc($doctor['ifsc_code'] ?? 'N/A') ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>PAN Number :</b> <span class="float-right"><?= esc($doctor['pan_number'] ?? 'N/A') ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h5><i class="fas fa-file-alt mr-1"></i> Documents</h5>
                            <hr>
                            <div class="row">
                                <?php
                                // Define all SINGLE file fields for doctors and their labels
                                $singleDoctorDocumentFields = [
                                    'resume_path'             => 'Resume',
                                    'degree_certificate_path' => 'Degree Certificate',
                                    'license_certificate_path' => 'License Certificate',
                                ];

                                foreach ($singleDoctorDocumentFields as $dbColumn => $label):
                                    $fileName = $doctor[$dbColumn] ?? null;
                                    $fileUrl = !empty($fileName) ? base_url('public/uploads/doctors/' . urlencode($fileName)) : '';
                                    $ext = !empty($fileName) ? pathinfo($fileName, PATHINFO_EXTENSION) : '';
                                ?>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card p-2 text-center" style="height: auto;">
                                            <?php if (!empty($fileName)): ?>
                                                <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <img src="<?= esc($fileUrl) ?>" alt="<?= esc($label) ?>" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($label) ?>"><i class="fas fa-image me-1"></i> <?= esc($label) ?></small>
                                                    </a>
                                                <?php elseif (strtolower($ext) === 'pdf'): ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <embed src="<?= esc($fileUrl) ?>" type="application/pdf" width="100%" height="150px" style="border: 1px solid #ddd; border-radius: 4px;" />
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($label) ?>"><i class="fas fa-file-pdf text-danger me-1"></i> <?= esc($label) ?></small>
                                                    </a>
                                                <?php else: // For doc, docx, etc. 
                                                ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 150px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                                                            <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                            <small class="text-muted text-center px-1 text-truncate" style="width: 100%;" title="<?= esc($label) ?>"><?= esc($label) ?></small>
                                                        </div>
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($label) ?>"><i class="fas fa-download me-1"></i> Download <?= esc($label) ?></small>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: // File not present 
                                            ?>
                                                <div class="d-flex flex-column align-items-center justify-content-center" style="height: 150px; background-color: #f0f0f0; border: 1px dashed #ccc; border-radius: 4px;">
                                                    <i class="fas fa-file-excel fa-3x text-muted mb-2"></i>
                                                    <small class="text-muted text-center px-1" style="width: 100%;"><?= esc($label) ?>: N/A</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php
                                $otherCertificates = [];
                                if (!empty($doctor['other_certificates_path'])) {
                                    $decoded = json_decode($doctor['other_certificates_path'], true);
                                    if (is_array($decoded)) {
                                        $otherCertificates = $decoded;
                                    }
                                }
                                ?>
                                <?php if (!empty($otherCertificates)): ?>
                                    <div class="col-md-12">
                                        <h6 class="mt-3 mb-2">Other Certificates/Awards:</h6>
                                    </div>
                                    <?php foreach ($otherCertificates as $fileName):
                                        // For each file in the array, reconstruct its URL and get its extension
                                        $fileUrl = base_url('public/uploads/doctors/' . urlencode($fileName));
                                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                    ?>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="card p-2 text-center" style="height: auto;">
                                                <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <img src="<?= esc($fileUrl) ?>" alt="<?= esc($fileName) ?>" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><i class="fas fa-image me-1"></i> <?= esc($fileName) ?></small>
                                                    </a>
                                                <?php elseif (strtolower($ext) === 'pdf'): ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <embed src="<?= esc($fileUrl) ?>" type="application/pdf" width="100%" height="150px" style="border: 1px solid #ddd; border-radius: 4px;" />
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><i class="fas fa-file-pdf text-danger me-1"></i> <?= esc($fileName) ?></small>
                                                    </a>
                                                <?php else: // For doc, docx, etc. 
                                                ?>
                                                    <a href="<?= esc($fileUrl) ?>" target="_blank" class="d-block text-decoration-none">
                                                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 150px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                                                            <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                            <small class="text-muted text-center px-1 text-truncate" style="width: 100%;" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                        </div>
                                                        <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><i class="fas fa-download me-1"></i> Download <?= esc($fileName) ?></small>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-md-12">
                                        <p class="text-muted">No other certificates uploaded.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>