<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="card shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Patient Details</h4>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <p><strong>Primary ID:</strong> <?= esc($patient['patient_id_code']) ?></p>
                    <p><strong>Patient Type:</strong> <?= esc($patient['patient_type']) ?></p>
                    <p><strong>OPD ID:</strong> <?= esc($patient['opd_id_code'] ?? 'N/A') ?></p>
                    <p><strong>IPD ID:</strong> <?= esc($patient['ipd_id_code'] ?? 'N/A') ?></p>
                    <p><strong>General ID:</strong> <?= esc($patient['gen_id_code'] ?? 'N/A') ?></p>
                    <p><strong>Casualty ID:</strong> <?= esc($patient['cus_id_code'] ?? 'N/A') ?></p>
                    <p><strong>Full Name:</strong> <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
                    <p><strong>Date of Birth:</strong> <?= esc(date('M d, Y', strtotime($patient['date_of_birth']))) ?></p>
                    <p><strong>Gender:</strong> <?= esc($patient['gender']) ?></p>
                    <p><strong>Blood Group:</strong> <?= esc($patient['blood_group'] ?? 'N/A') ?></p>
                </div>

                <div class="col-md-6">
                    <p><strong>Marital Status:</strong> <?= esc($patient['marital_status'] ?? 'N/A') ?></p>
                    <p><strong>Occupation:</strong> <?= esc($patient['occupation'] ?? 'N/A') ?></p>
                    <p><strong>Phone:</strong> <?= esc($patient['phone_number']) ?></p>
                    <p><strong>Email:</strong> <?= esc($patient['email'] ?? 'N/A') ?></p>
                    <p><strong>Emergency Contact:</strong> <?= esc($patient['emergency_contact_name']) ?> (<?= esc($patient['emergency_contact_phone']) ?>)</p>
                    <p><strong>Allergies:</strong> <?= esc($patient['known_allergies'] ?? 'None') ?></p>
                    <p><strong>Pre-existing Conditions:</strong> <?= esc($patient['pre_existing_conditions'] ?? 'None') ?></p>
                    <p><strong>Referred To Doctor:</strong>
                        <?php if ($referredDoctor): ?>
                            <?= esc($referredDoctor['first_name'] . ' ' . $referredDoctor['last_name']) ?>
                            (<?= esc($referredDoctor['specialization']) ?>)
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                    <p><strong>Referred By:</strong>
                        <?= $referredByPerson ? esc($referredByPerson['name'] . ' - ' . $referredByPerson['contact_info']) : 'N/A' ?>
                    </p>
                    <p><strong>Remarks:</strong> <?= esc($patient['remarks'] ?? '-') ?></p>
                </div>
            </div>

            <?php
            $reportFiles = json_decode($patient['reports'] ?? '[]', true);
            ?>

            <?php if (!empty($reportFiles)): ?>
                <hr>
                <h5 class="mb-3">Uploaded Reports:</h5>
                <div class="row"> <?php foreach ($reportFiles as $fileName): ?>
                        <?php
                                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                        // Construct the correct public URL for the file
                                        $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                        ?>
                        <div class="col-auto mb-3">
                            <div class="card p-2 text-center" style="width: 220px; height: auto;"> <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
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
                <p><strong>Uploaded Reports:</strong> None</p>
            <?php endif; ?>

        </div>
        <div class="card-footer text-right">
            <a href="<?= base_url('patients') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>