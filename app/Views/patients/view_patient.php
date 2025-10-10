<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?>: <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit Patient">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?= base_url('patients') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Patient Demographics Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Patient Information</h4>
                            <dl class="row">
                                <dt class="col-sm-4">Patient ID Code:</dt>
                                <dd class="col-sm-8"><?= esc($patient['patient_id_code']) ?></dd>

                                <dt class="col-sm-4">Patient Type:</dt>
                                <dd class="col-sm-8">
                                    <?= esc($patient['patient_type']) ?>
                                </dd>

                                <!-- Always display ID labels, show N/A if null -->
                                <dt class="col-sm-4">OPD ID:</dt>
                                <dd class="col-sm-8"><?= esc($patient['opd_id_code'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">IPD ID:</dt>
                                <dd class="col-sm-8"><?= esc($patient['ipd_id_code'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">General ID:</dt>
                                <dd class="col-sm-8"><?= esc($patient['gen_id_code'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">Casualty ID:</dt>
                                <dd class="col-sm-8"><?= esc($patient['cus_id_code'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">Full Name:</dt>
                                <dd class="col-sm-8"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></dd>

                                <dt class="col-sm-4">Date of Birth:</dt>
                                <dd class="col-sm-8"><?= esc(date('M d, Y', strtotime($patient['date_of_birth']))) ?></dd>

                                <dt class="col-sm-4">Gender:</dt>
                                <dd class="col-sm-8"><?= esc($patient['gender']) ?></dd>

                                <dt class="col-sm-4">Blood Group:</dt>
                                <dd class="col-sm-8"><?= esc($patient['blood_group']) ?></dd>

                                <dt class="col-sm-4">Marital Status:</dt>
                                <dd class="col-sm-8"><?= esc($patient['marital_status']) ?></dd>

                                <dt class="col-sm-4">Occupation:</dt>
                                <dd class="col-sm-8"><?= esc($patient['occupation']) ?></dd>

                                <dt class="col-sm-4">Address:</dt>
                                <dd class="col-sm-8"><?= esc($patient['address']) ?></dd>

                                <dt class="col-sm-4">Phone Number:</dt>
                                <dd class="col-sm-8"><?= esc($patient['phone_number']) ?></dd>

                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8"><?= esc($patient['email']) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h4>Emergency Contact & Medical History</h4>
                            <dl class="row">
                                <dt class="col-sm-5">Emergency Contact Name:</dt>
                                <dd class="col-sm-7"><?= esc($patient['emergency_contact_name']) ?></dd>

                                <dt class="col-sm-5">Emergency Contact Phone:</dt>
                                <dd class="col-sm-7"><?= esc($patient['emergency_contact_phone']) ?></dd>

                                <dt class="col-sm-5">Known Allergies:</dt>
                                <dd class="col-sm-7"><?= esc($patient['known_allergies']) ?></dd>

                                <dt class="col-sm-5">Pre-existing Conditions:</dt>
                                <dd class="col-sm-7"><?= esc($patient['pre_existing_conditions']) ?></dd>

                                <dt class="col-sm-5">Referred to Doctor:</dt>
                                <dd class="col-sm-7">
                                    <?php if ($referredDoctor) : ?>
                                        <?= esc($referredDoctor['first_name'] . ' ' . $referredDoctor['last_name']) ?> (ID: <?= esc($referredDoctor['doctor_id_code']) ?>)
                                    <?php else : ?>
                                        N/A
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-5">Referred By:</dt>
                                <dd class="col-sm-7">
                                    <?php if ($referredByPerson) : ?>
                                        <?= esc($referredByPerson['name']) ?> (ID: <?= esc($referredByPerson['id']) ?>)
                                    <?php else : ?>
                                        N/A
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-5">Remarks:</dt>
                                <dd class="col-sm-7"><?= esc($patient['remarks']) ?></dd>

                                <dt class="col-sm-5">Registration Fee:</dt>
                                <dd class="col-sm-7">₹<?= esc(number_format($patient['fee'], 2)) ?></dd>

                                <dt class="col-sm-5">Discount Percentage:</dt>
                                <dd class="col-sm-7"><?= esc($patient['discount_percentage']) ?>%</dd>

                                <dt class="col-sm-5">Final Amount:</dt>
                                <dd class="col-sm-7">₹<?= esc(number_format($patient['final_amount'], 2)) ?></dd>

                                <dt class="col-sm-5">Registered On:</dt>
                                <dd class="col-sm-7"><?= esc(date('M d, Y h:i A', strtotime($patient['created_at']))) ?></dd>

                                <dt class="col-sm-5">Last Updated:</dt>
                                <dd class="col-sm-7"><?= esc(date('M d, Y h:i A', strtotime($patient['updated_at']))) ?></dd>
                            </dl>
                        </div>
                    </div>

                    <hr>

                    <div class="card card-primary card-outline card-tabs">
                        <div class="card-header p-0 pt-1 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="lab-tab" data-toggle="pill" href="#lab-content" role="tab" aria-controls="lab-content" aria-selected="true"><i class="fas fa-flask"></i> Lab Reports</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="diag-tab" data-toggle="pill" href="#diag-content" role="tab" aria-controls="diag-content" aria-selected="false"><i class="fas fa-x-ray"></i> Diagnostic Reports</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="files-tab" data-toggle="pill" href="#files-content" role="tab" aria-controls="files-content" aria-selected="false"><i class="fas fa-file-upload"></i> Uploaded Files</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-three-tabContent">

                                <div class="tab-pane fade show active" id="lab-content" role="tabpanel" aria-labelledby="lab-tab">
                                    <?php if (!empty($labs)): ?>
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%;">Order Code</th>
                                                    <th style="width: 15%;">Date</th>
                                                    <th>Tests Ordered</th>
                                                    <th style="width: 15%;">Status</th>
                                                    <th style="width: 15%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($labs as $lab): ?>
                                                    <tr>
                                                        <td><?= esc($lab['order_id_code']) ?></td>
                                                        <td><?= esc(date('Y-m-d', strtotime($lab['order_date']))) ?></td>
                                                        <td><span class="text-sm font-weight-bold"><?= esc($lab['test_names'] ?? 'N/A') ?></span></td>
                                                        <td>
                                                            <?php
                                                            $status = esc($lab['status']);
                                                            $badgeClass = ($status == 'Completed') ? 'badge-success' : (($status == 'Pending') ? 'badge-warning' : 'badge-info');
                                                            ?>
                                                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $filePaths = explode(',', $lab['report_file_paths'] ?? '');
                                                            $firstFile = trim($filePaths[0] ?? '');
                                                            ?>
                                                            <?php if ($lab['status'] == 'Completed' && !empty($firstFile)): ?>
                                                                <a
                                                                    href="<?= base_url('public/uploads/laboratory/' . $firstFile) ?>"
                                                                    target="_blank"
                                                                    class="btn btn-xs btn-outline-primary"
                                                                    title="View Lab Report (Opens PDF Directly)">
                                                                    <i class="fas fa-file-pdf"></i> View
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted text-xs">Report Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No laboratory reports found for this patient yet.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="diag-content" role="tabpanel" aria-labelledby="diag-tab">
                                    <?php if (!empty($diagnostics)): ?>
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%;">Order Code</th>
                                                    <th style="width: 15%;">Date</th>
                                                    <th>Services Ordered</th>
                                                    <th style="width: 15%;">Status</th>
                                                    <th style="width: 15%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($diagnostics as $diagnostic): ?>
                                                    <tr>
                                                        <td><?= esc($diagnostic['order_id_code']) ?></td>
                                                        <td><?= esc(date('Y-m-d', strtotime($diagnostic['order_date']))) ?></td>
                                                        <td><span class="text-sm font-weight-bold"><?= esc($diagnostic['procedure_name'] ?? 'N/A') ?></span></td>
                                                        <td>
                                                            <?php
                                                            $status = esc($diagnostic['status']);
                                                            $badgeClass = ($status == 'Completed') ? 'badge-success' : (($status == 'Pending') ? 'badge-warning' : 'badge-info');
                                                            ?>
                                                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $filePaths = explode(',', $diagnostic['report_file_path'] ?? '');
                                                            $firstFile = trim($filePaths[0] ?? '');
                                                            ?>
                                                            <?php if ($diagnostic['status'] == 'Completed' && !empty($firstFile)): ?>
                                                                <a
                                                                    href="<?= base_url('public/uploads/patient_reports/' . $firstFile) ?>"
                                                                    target="_blank"
                                                                    class="btn btn-xs btn-outline-primary"
                                                                    title="View Diagnostic Report (Opens PDF Directly)">
                                                                    <i class="fas fa-file-pdf"></i> View
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted text-xs">Report Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No diagnostic reports found for this patient yet.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="files-content" role="tabpanel" aria-labelledby="files-tab">
                                    <div class="row">
                                        <?php
                                        $reportFiles = json_decode($patient['reports'] ?? '[]', true);
                                        ?>

                                        <?php if (!empty($reportFiles)): ?>
                                            <?php foreach ($reportFiles as $fileName): ?>
                                                <?php
                                                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                                $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                                                ?>
                                                <div class="col-auto mb-3">
                                                    <div class="card p-2 text-center" style="width: 220px; height: auto;">
                                                        <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
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
                                                        <button type="button" class="btn btn-danger btn-sm delete-report-btn mt-2" data-patient-id="<?= esc($patient['patient_id_code']) ?>" data-filename="<?= esc($fileName) ?>" title="Delete Report">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <div class="alert alert-secondary">
                                                    <i class="fas fa-info-circle"></i> No generic report files have been uploaded for this patient.
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.container-fluid -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // SweetAlert2 for delete report confirmation
        $('.delete-report-btn').on('click', function(e) {
            e.preventDefault();
            const patientId = $(this).data('patient-id');
            const filename = $(this).data('filename');
            const deleteButton = $(this); // Store reference to the button clicked

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! This will permanently delete the report file.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get fresh CSRF token and hash
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

                    $.ajax({
                        url: '<?= base_url('patients/deleteReportFile') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            patient_id: patientId,
                            filename: filename,
                            [csrfToken]: csrfHash // Include CSRF token
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                // Remove the list item (col-auto mb-3 div) from the DOM
                                deleteButton.closest('.col-auto').remove();
                                // Update the CSRF hash in the meta tag for subsequent requests
                                $('meta[name="csrf-hash"]').attr('content', response.csrfHash);
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the file.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>