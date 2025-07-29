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

                    <h4>Patient Reports</h4>
                    <div class="row">
                        <div class="col-12">
                            <?php
                            $reportFiles = json_decode($patient['reports'] ?? '[]', true);
                            ?>

                            <?php if (!empty($reportFiles)): ?>
                                <div class="row">
                                    <?php foreach ($reportFiles as $fileName): ?>
                                        <?php
                                            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                            // Construct the correct public URL for the file
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
                                </div>
                            <?php else: ?>
                                <p>No reports uploaded for this patient.</p>
                            <?php endif; ?>
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
