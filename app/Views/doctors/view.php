<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-md mr-2 text-primary"></i>
                    Doctor Profile: <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('doctors') ?>">Doctors</a></li>
                    <li class="breadcrumb-item active">View Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline card-primary shadow-lg">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab"><i class="fas fa-user-circle mr-1"></i> Profile & Credentials</a></li>
                            <li class="nav-item"><a class="nav-link" href="#employment" data-toggle="tab"><i class="fas fa-briefcase mr-1"></i> Employment & Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="#financial" data-toggle="tab"><i class="fas fa-wallet mr-1"></i> Financial Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab"><i class="fas fa-file-alt mr-1"></i> Documents</a></li>
                        </ul>
                        <div class="card-tools pt-2 pr-2">
                            <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" class="btn btn-warning btn-sm" title="Edit Doctor">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="<?= base_url('doctors') ?>" class="btn btn-secondary btn-sm ml-1">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">

                            <div class="tab-pane active" id="profile">
                                <div class="row">

                                    <div class="col-md-3 text-center border-right">
                                        <?php
                                        // Use consistent paths from the previous files
                                        $profilePictureUrl = !empty($doctor['profile_picture'])
                                            ? base_url('public/uploads/doctors/' . urlencode($doctor['profile_picture']))
                                            : base_url('dist/img/default-avatar.png');
                                        ?>
                                        <img src="<?= esc($profilePictureUrl) ?>" class="img-fluid rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Picture">

                                        <h4><?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h4>
                                        <p class="text-primary text-bold"><?= esc($doctor['specialization'] ?? 'Specialist') ?></p>
                                        <p class="text-muted">ID: **<?= esc($doctor['doctor_id_code'] ?? 'N/A') ?>**</p>

                                        <p>
                                            <span class="badge 
                                                <?php
                                                $doctorStatus = $doctor['status'] ?? 'Unknown';
                                                if ($doctorStatus == 'Active') echo 'bg-success';
                                                else if ($doctorStatus == 'On Leave') echo 'bg-warning';
                                                else if ($doctorStatus == 'Suspended' || $doctorStatus == 'Terminated') echo 'bg-danger';
                                                else echo 'bg-secondary';
                                                ?>
                                            ">
                                                <i class="fas fa-circle mr-1"></i> Account Status: <?= esc($doctorStatus) ?>
                                            </span>
                                        </p>

                                        <?php if (!empty($doctor['signature_image'])): ?>
                                            <hr>
                                            <h5 class="text-secondary"><i class="fas fa-signature mr-1"></i> Signature</h5>
                                            <?php $signatureImageUrl = base_url('public/uploads/doctors/' . urlencode($doctor['signature_image'])); ?>
                                            <img src="<?= esc($signatureImageUrl) ?>" class="img-fluid" style="max-width: 120px; border: 1px solid #ddd; padding: 5px; background: #fff;" alt="Signature">
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-9">

                                        <h5 class="text-info"><i class="fas fa-info-circle mr-1"></i> Personal & Contact Information</h5>
                                        <hr class="mt-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-unbordered mb-3">
                                                    <li class="list-group-item"><b>Gender:</b> <span class="float-right"><?= esc($doctor['gender'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Date of Birth:</b> <span class="float-right"><?= esc($doctor['date_of_birth'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Email:</b> <span class="float-right text-truncate" title="<?= esc($doctor['email'] ?? 'N/A') ?>"><?= esc($doctor['email'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Phone Number:</b> <span class="float-right"><?= esc($doctor['phone_number'] ?? 'N/A') ?></span></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-unbordered mb-3">
                                                    <li class="list-group-item"><b>Emergency Contact:</b> <span class="float-right"><?= esc($doctor['emergency_contact_name'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Emergency Phone:</b> <span class="float-right"><?= esc($doctor['emergency_contact_phone'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>User ID:</b> <span class="float-right"><?= esc($doctor['user_id'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Last Login:</b> <span class="float-right"><?= esc($doctor['last_login_at'] ?? 'N/A') ?></span></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="callout callout-secondary pt-2 pb-2 mt-0">
                                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                                    <p class="text-muted mb-0"><?= nl2br(esc($doctor['address'] ?? 'N/A')) ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="mt-4 text-success"><i class="fas fa-graduation-cap mr-1"></i> Professional Credentials</h5>
                                        <hr class="mt-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-unbordered mb-3">
                                                    <li class="list-group-item"><b>Qualification:</b> <span class="float-right"><?= esc($doctor['qualification'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Medical License No.:</b> <span class="float-right"><?= esc($doctor['medical_license_no'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Registration Number:</b> <span class="float-right"><?= esc($doctor['registration_number'] ?? 'N/A') ?></span></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-unbordered mb-3">
                                                    <li class="list-group-item"><b>Medical Council:</b> <span class="float-right"><?= esc($doctor['medical_council'] ?? 'N/A') ?></span></li>
                                                    <li class="list-group-item"><b>Years of Experience:</b> <span class="float-right"><?= esc($doctor['experience_years'] ?? 'N/A') ?> Years</span></li>
                                                    <li class="list-group-item"><b>Created On:</b> <span class="float-right"><?= esc($doctor['created_at'] ?? 'N/A') ?></span></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="callout callout-primary pt-2 pb-2 mt-0">
                                            <strong><i class="fas fa-book mr-1"></i> Biography / Summary</strong>
                                            <p class="text-muted mb-0">
                                                <?= nl2br(esc($doctor['bio'] ?? 'N/A')) ?>
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="employment">
                                <h5 class="text-warning"><i class="fas fa-briefcase mr-1"></i> Employment Details</h5>
                                <hr class="mt-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item"><b>Department:</b> <span class="float-right text-bold"><?= esc($doctor['department_name'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Designation:</b> <span class="float-right"><?= esc($doctor['designation'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Specialization:</b> <span class="float-right"><?= esc($doctor['specialization_name'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Employment Status:</b> <span class="float-right"><?= esc($doctor['employment_status'] ?? 'N/A') ?></span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item"><b>Joining Date:</b> <span class="float-right"><?= esc($doctor['joining_date'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Contract Type:</b> <span class="float-right"><?= esc($doctor['contract_type'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Available for Duty:</b> <span class="float-right">
                                                    <span class="badge <?= ($doctor['is_available'] ?? 0) ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= ($doctor['is_available'] ?? 0) ? 'Available' : 'Not Available' ?>
                                                    </span>
                                                </span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="financial">
                                <h5 class="text-indigo"><i class="fas fa-money-check-alt mr-1"></i> Compensation & Banking</h5>
                                <hr class="mt-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item"><b>OPD Consultation Fee:</b> <span class="float-right text-bold text-success">₹<?= esc(number_format($doctor['opd_fee'] ?? 0, 2)) ?></span></li>
                                            <li class="list-group-item"><b>IPD Charge Percentage:</b> <span class="float-right"><?= esc($doctor['ipd_charge_percentage'] ?? 'N/A') ?>%</span></li>
                                            <li class="list-group-item"><b>PAN Number:</b> <span class="float-right"><?= esc($doctor['pan_number'] ?? 'N/A') ?></span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item"><b>Bank Name:</b> <span class="float-right"><?= esc($doctor['bank_name'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>Bank Account Number:</b> <span class="float-right"><?= esc($doctor['bank_account_number'] ?? 'N/A') ?></span></li>
                                            <li class="list-group-item"><b>IFSC Code:</b> <span class="float-right"><?= esc($doctor['ifsc_code'] ?? 'N/A') ?></span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="documents">
                                <h5 class="text-danger"><i class="fas fa-folder-open mr-1"></i> Uploaded Documents Overview</h5>
                                <hr class="mt-0">
                                <div class="row">
                                    <?php
                                    // Consolidated document fields for clearer loop
                                    $allDoctorDocumentFields = [
                                        'resume_file'             => ['label' => 'Resume / CV', 'dbColumn' => 'resume_path'],
                                        'degree_certificate_file' => ['label' => 'Degree Certificate', 'dbColumn' => 'degree_certificate_path'],
                                        'license_certificate_file' => ['label' => 'License/Registration', 'dbColumn' => 'license_certificate_path'],
                                    ];

                                    // Display SINGLE documents
                                    foreach ($allDoctorDocumentFields as $key => $doc):
                                        $fileName = $doctor[$doc['dbColumn']] ?? null;
                                        $fileUrl = !empty($fileName) ? base_url('public/uploads/doctors/' . urlencode($fileName)) : '';
                                        $ext = !empty($fileName) ? pathinfo($fileName, PATHINFO_EXTENSION) : '';
                                    ?>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="card p-2 text-center document-card" style="height: auto; min-height: 180px;">
                                                <small class="text-bold text-muted mb-2"><?= esc($doc['label']) ?></small>
                                                <?php if (!empty($fileName)): ?>
                                                    <?php
                                                    $iconClass = 'fas fa-file-alt text-info';
                                                    $linkText = 'View Document';
                                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $iconClass = 'fas fa-image text-primary';
                                                        $linkText = 'View Image';
                                                    } elseif (strtolower($ext) === 'pdf') {
                                                        $iconClass = 'fas fa-file-pdf text-danger';
                                                        $linkText = 'View PDF';
                                                    }
                                                    ?>
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                                        <i class="<?= $iconClass ?> fa-3x mb-2"></i>
                                                        <small class="text-dark text-truncate" style="width: 90%;" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                        <a href="<?= esc($fileUrl) ?>" target="_blank" class="btn btn-xs btn-outline-info mt-2"><i class="fas fa-download me-1"></i> <?= $linkText ?></a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100px; background-color: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;">
                                                        <i class="fas fa-times-circle fa-2x text-secondary mb-1"></i>
                                                        <small class="text-muted">Not Uploaded</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="col-md-12">
                                        <h6 class="mt-3 mb-2 text-primary border-top pt-3"><i class="fas fa-scroll mr-1"></i> Other Certificates/Awards (Multiple Files)</h6>
                                    </div>

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
                                        <?php foreach ($otherCertificates as $index => $fileName):
                                            $fileUrl = base_url('public/uploads/doctors/' . urlencode($fileName));
                                            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                        ?>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <div class="card p-2 text-center document-card" style="height: auto; min-height: 180px;">
                                                    <small class="text-bold text-muted mb-2">Certificate #<?= $index + 1 ?></small>
                                                    <?php
                                                    $iconClass = 'fas fa-file-alt text-info';
                                                    $linkText = 'View Document';
                                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $iconClass = 'fas fa-image text-primary';
                                                        $linkText = 'View Image';
                                                    } elseif (strtolower($ext) === 'pdf') {
                                                        $iconClass = 'fas fa-file-pdf text-danger';
                                                        $linkText = 'View PDF';
                                                    }
                                                    ?>
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                                                        <i class="<?= $iconClass ?> fa-3x mb-2"></i>
                                                        <small class="text-dark text-truncate" style="width: 90%;" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                        <a href="<?= esc($fileUrl) ?>" target="_blank" class="btn btn-xs btn-outline-primary mt-2"><i class="fas fa-download me-1"></i> <?= $linkText ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-md-12">
                                            <div class="callout callout-secondary">
                                                <p class="mb-0 text-muted"><i class="fas fa-exclamation-circle mr-1"></i> No additional certificates or awards have been uploaded for this doctor.</p>
                                            </div>
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
</section>
<?= $this->endSection() ?>