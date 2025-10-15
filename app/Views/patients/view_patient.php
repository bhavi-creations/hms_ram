<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></h2>
                <div>
                    <span class="badge" style="background-color: var(--bs-primary-bg-subtle); color: var(--bs-primary-text-emphasis);">
                        ID: <?= esc($patient['patient_id_code']) ?>
                    </span>
                    <span class="badge" style="background-color: var(--bs-info-bg-subtle); color: var(--bs-info-text-emphasis);">
                        Type: <?= esc($patient['patient_type']) ?>
                    </span>
                </div>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-primary btn-sm" title="Edit Patient">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="<?= base_url('patients') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header bg-light">
                <h3 class="card-title h6 mb-0">Patient Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">OPD/IPD/Gen/Cas ID:</dt>
                    <dd class="col-sm-8 mb-3">
                        <?= esc($patient['opd_id_code'] ?? 'N/A') ?> /
                        <?= esc($patient['ipd_id_code'] ?? 'N/A') ?> /
                        <?= esc($patient['gen_id_code'] ?? 'N/A') ?> /
                        <?= esc($patient['cus_id_code'] ?? 'N/A') ?>
                    </dd>
                    <dt class="col-sm-4">Full Name:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></dd>
                    <dt class="col-sm-4">Date of Birth:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc(date('M d, Y', strtotime($patient['date_of_birth']))) ?></dd>
                    <dt class="col-sm-4">Gender:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['gender']) ?></dd>
                    <dt class="col-sm-4">Blood Group:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['blood_group']) ?></dd>
                    <dt class="col-sm-4">Marital Status:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['marital_status']) ?></dd>
                    <dt class="col-sm-4">Occupation:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['occupation']) ?></dd>
                    <dt class="col-sm-4">Phone / Email:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['phone_number']) ?> / <?= esc($patient['email']) ?></dd>
                    <dt class="col-sm-4">Address:</dt>
                    <dd class="col-sm-8 mb-3"><?= esc($patient['address']) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header bg-light">
                <h3 class="card-title h6 mb-0">Contact, Medical & Financial</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Emergency Contact:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['emergency_contact_name']) ?></dd>

                    <dt class="col-sm-5">Emergency Phone:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['emergency_contact_phone']) ?></dd>

                    <dt class="col-sm-5">Known Allergies:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['known_allergies'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-5">Pre-existing Conditions:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['pre_existing_conditions'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-5">Referred to Doctor:</dt>
                    <dd class="col-sm-7 mb-3">
                        <?php if ($referredDoctor) : ?>
                            <?= esc($referredDoctor['first_name'] . ' ' . $referredDoctor['last_name']) ?> (ID: <?= esc($referredDoctor['doctor_id_code']) ?>)
                        <?php else : ?>
                            N/A
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5">Referred By:</dt>
                    <dd class="col-sm-7 mb-3">
                        <?php if ($referredByPerson) : ?>
                            <?= esc($referredByPerson['name']) ?> (ID: <?= esc($referredByPerson['id']) ?>)
                        <?php else : ?>
                            N/A
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5">Remarks:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['remarks'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-5">Registration Fee:</dt>
                    <dd class="col-sm-7 mb-3">₹<?= esc(number_format($patient['fee'], 2)) ?></dd>

                    <dt class="col-sm-5">Discount Percentage:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc($patient['discount_percentage']) ?>%</dd>

                    <dt class="col-sm-5">Final Amount:</dt>
                    <dd class="col-sm-7 mb-3">₹<?= esc(number_format($patient['final_amount'], 2)) ?></dd>

                    <dt class="col-sm-5">Registered On:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc(date('M d, Y h:i A', strtotime($patient['created_at']))) ?></dd>

                    <dt class="col-sm-5">Last Updated:</dt>
                    <dd class="col-sm-7 mb-3"><?= esc(date('M d, Y h:i A', strtotime($patient['updated_at']))) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="patient-files-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="lab-tab" data-bs-toggle="tab" href="#lab-content" role="tab"><i class="fas fa-flask me-2"></i>Lab Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="diag-tab" data-bs-toggle="tab" href="#diag-content" role="tab"><i class="fas fa-x-ray me-2"></i>Diagnostic Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="files-tab" data-bs-toggle="tab" href="#files-content" role="tab"><i class="fas fa-file-upload me-2"></i>Uploaded Files</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="patient-files-tabContent">
            <div class="tab-pane fade show active" id="lab-content" role="tabpanel">
                <?php if (!empty($labs)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Code</th>
                                    <th>Date</th>
                                    <th>Tests Ordered</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($labs as $lab): ?>
                                    <tr>
                                        <td><?= esc($lab['order_id_code']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($lab['order_date']))) ?></td>
                                        <td><?= esc($lab['test_names'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            $status = esc($lab['status']);
                                            $badgeClass = ($status == 'Completed') ? 'bg-success' : (($status == 'Pending') ? 'bg-warning' : 'bg-info');
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php
                                            $filePaths = explode(',', $lab['report_file_paths'] ?? '');
                                            $firstFile = trim($filePaths[0] ?? '');
                                            if ($lab['status'] == 'Completed' && !empty($firstFile)): ?>
                                                <a href="<?= base_url('public/uploads/laboratory/' . $firstFile) ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="View Lab Report">
                                                    <i class="fas fa-file-pdf"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Report Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No laboratory reports found for this patient.</div>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="diag-content" role="tabpanel">
                <?php if (!empty($diagnostics)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Code</th>
                                    <th>Date</th>
                                    <th>Services Ordered</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($diagnostics as $diagnostic): ?>
                                    <tr>
                                        <td><?= esc($diagnostic['order_id_code']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($diagnostic['order_date']))) ?></td>
                                        <td><?= esc($diagnostic['procedure_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            $status = esc($diagnostic['status']);
                                            $badgeClass = ($status == 'Completed') ? 'bg-success' : (($status == 'Pending') ? 'bg-warning' : 'bg-info');
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php
                                            $filePaths = explode(',', $diagnostic['report_file_path'] ?? '');
                                            $firstFile = trim($filePaths[0] ?? '');
                                            if ($diagnostic['status'] == 'Completed' && !empty($firstFile)): ?>
                                                <a href="<?= base_url('public/uploads/patient_reports/' . $firstFile) ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="View Report">
                                                    <i class="fas fa-file-pdf"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Report Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No diagnostic reports found for this patient.</div>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="files-content" role="tabpanel">
                <div class="row g-4">
                    <?php
                    $reportFiles = json_decode($patient['reports'] ?? '[]', true);
                    if (!empty($reportFiles)):
                        foreach ($reportFiles as $fileName):
                            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                    ?>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <a href="<?= $fileUrl ?>" target="_blank" class="text-decoration-none d-flex flex-column text-center p-3" style="flex-grow: 1;">

                                        <div class="mb-2" style="flex-grow: 1; display: flex; align-items: center; justify-content: center; min-height: 120px;">
                                            <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <img src="<?= $fileUrl ?>" alt="<?= esc($fileName) ?>" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                                            <?php elseif ($ext === 'pdf'): ?>
                                                <i class="fas fa-file-pdf fa-4x text-danger"></i>
                                            <?php else: ?>
                                                <i class="fas fa-file-alt fa-4x text-secondary"></i>
                                            <?php endif; ?>
                                        </div>

                                        <p class="mb-0 small text-dark text-truncate" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></p>
                                    </a>

                                    <div class="card-footer bg-light p-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 delete-report-btn" data-patient-id="<?= esc($patient['patient_id_code']) ?>" data-filename="<?= esc($fileName) ?>" title="Delete Report">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-secondary">No generic report files have been uploaded for this patient.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // SweetAlert2 for delete report confirmation
        $('.delete-report-btn').on('click', function(e) {
            e.preventDefault();
            // ... Your original AJAX delete script ...
            const patientId = $(this).data('patient-id');
            const filename = $(this).data('filename');
            const deleteButton = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the report file!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('patients/deleteReportFile') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            patient_id: patientId,
                            filename: filename,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                deleteButton.closest('.col-md-3').remove();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>