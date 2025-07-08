C:\xampp\htdocs\hms_ram\app\Views\doctors\edit.php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?>: <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('doctors') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?= form_open_multipart('doctors/save') ?>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= esc($doctor['id']) ?>">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="<?= old('first_name', $doctor['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="<?= old('last_name', $doctor['last_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_id">User ID (for Login)</label>
                                <input type="text" id="user_id_display" class="form-control" value="<?= esc($doctor['user_id'] ?? 'N/A') ?>" disabled>
                                <input type="hidden" name="user_id" value="<?= esc($doctor['user_id']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="specialization">Specialization <span class="text-danger">*</span></label>
                                <input type="text" name="specialization" id="specialization" class="form-control" value="<?= old('specialization', $doctor['specialization']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="qualification">Qualification</label>
                                <input type="text" name="qualification" id="qualification" class="form-control" value="<?= old('qualification', $doctor['qualification']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="medical_license_no">Medical License No.</label>
                                <input type="text" name="medical_license_no" id="medical_license_no" class="form-control" value="<?= old('medical_license_no', $doctor['medical_license_no']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="experience_years">Experience (Years)</label>
                                <input type="number" name="experience_years" id="experience_years" class="form-control" value="<?= old('experience_years', $doctor['experience_years']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select name="department_id" id="department_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= esc($department['id']) ?>"
                                            <?= old('department_id', $doctor['department_id']) == $department['id'] ? 'selected' : '' ?>>
                                            <?= esc($department['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= old('gender', $doctor['gender']) == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= old('gender', $doctor['gender']) == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= old('gender', $doctor['gender']) == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?= old('date_of_birth', $doctor['date_of_birth']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?= old('phone_number', $doctor['phone_number']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= old('email', $doctor['email']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3"><?= old('address', $doctor['address']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="bio">Biography</label>
                        <textarea name="bio" id="bio" class="form-control" rows="5"><?= old('bio', $doctor['bio']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emergency_contact_name">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="<?= old('emergency_contact_name', $doctor['emergency_contact_name']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="<?= old('emergency_contact_phone', $doctor['emergency_contact_phone']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="registration_number">Registration Number</label>
                                <input type="text" name="registration_number" id="registration_number" class="form-control" value="<?= old('registration_number', $doctor['registration_number']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="medical_council">Medical Council</label>
                                <input type="text" name="medical_council" id="medical_council" class="form-control" value="<?= old('medical_council', $doctor['medical_council']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="joining_date">Joining Date</label>
                                <input type="date" name="joining_date" id="joining_date" class="form-control" value="<?= old('joining_date', $doctor['joining_date']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employment_status">Employment Status</label>
                                <select name="employment_status" id="employment_status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="Full-time" <?= old('employment_status', $doctor['employment_status']) == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                    <option value="Part-time" <?= old('employment_status', $doctor['employment_status']) == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                    <option value="Consultant" <?= old('employment_status', $doctor['employment_status']) == 'Consultant' ? 'selected' : '' ?>>Consultant</option>
                                    <option value="On-Leave" <?= old('employment_status', $doctor['employment_status']) == 'On-Leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="Resigned" <?= old('employment_status', $doctor['employment_status']) == 'Resigned' ? 'selected' : '' ?>>Resigned</option>
                                    <option value="Terminated" <?= old('employment_status', $doctor['employment_status']) == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="contract_type">Contract Type</label>
                                <input type="text" name="contract_type" id="contract_type" class="form-control" value="<?= old('contract_type', $doctor['contract_type']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="designation">Designation</label>
                                <input type="text" name="designation" id="designation" class="form-control" value="<?= old('designation', $doctor['designation']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="opd_fee">OPD Consultation Fee</label>
                                <input type="number" step="0.01" name="opd_fee" id="opd_fee" class="form-control" value="<?= old('opd_fee', $doctor['opd_fee']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ipd_charge_percentage">IPD Charge Percentage (%)</label>
                                <input type="number" step="0.01" name="ipd_charge_percentage" id="ipd_charge_percentage" class="form-control" value="<?= old('ipd_charge_percentage', $doctor['ipd_charge_percentage']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Current Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Active" <?= old('status', $doctor['status']) == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="On Leave" <?= old('status', $doctor['status']) == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="Suspended" <?= old('status', $doctor['status']) == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                    <option value="Terminated" <?= old('status', $doctor['status']) == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" <?= old('is_available', $doctor['is_available']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_available">Is Available for Consultation?</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_login_at">Last Login At (for User)</label>
                                <input type="datetime-local" name="last_login_at" id="last_login_at" class="form-control" value="<?= old('last_login_at', str_replace(' ', 'T', $doctor['last_login_at'])) ?>">
                            </div>
                        </div>
                    </div>

                    ---
                    <h5>Bank Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bank_account_number">Bank Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" value="<?= old('bank_account_number', $doctor['bank_account_number']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= old('bank_name', $doctor['bank_name']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="<?= old('ifsc_code', $doctor['ifsc_code']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pan_number">PAN Number</label>
                                <input type="text" name="pan_number" id="pan_number" class="form-control" value="<?= old('pan_number', $doctor['pan_number']) ?>">
                            </div>
                        </div>
                    </div>

                    ---
                    <h5>Document Uploads (Leave blank to keep current files)</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profile_picture">Profile Picture (Max 2MB, JPG/PNG)</label>
                                <div id="current_profile_picture_container" class="mt-2">
                                    <?php if (!empty($doctor['profile_picture'])): ?>
                                        <div class="d-flex flex-column align-items-start border p-2 rounded bg-light">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-image me-2 text-primary"></i>
                                                <span class="text-muted me-2">Current:</span>
                                                <a href="<?= base_url('public/uploads/doctors/' . esc($doctor['profile_picture'])) ?>" target="_blank" class="text-decoration-none">
                                                    <strong><?= esc($doctor['profile_picture']) ?></strong>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm ms-3"
                                                    onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'profile_picture', '<?= esc($doctor['profile_picture']) ?>', 'current_profile_picture_container', 'profile_picture')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            <?php
                                            $profilePicExtension = pathinfo($doctor['profile_picture'], PATHINFO_EXTENSION);
                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                            if (in_array(strtolower($profilePicExtension), $imageExtensions)): ?>
                                                <img src="<?= base_url('public/uploads/doctors/' . esc($doctor['profile_picture'])) ?>"
                                                    alt="Profile Picture" class="img-thumbnail" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_profile_picture_display">No profile picture uploaded.</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control-file mt-2" accept="image/jpeg,image/png">
                                <small class="form-text text-muted">Upload a new picture to replace the current one.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="signature_image">Signature Image (Max 1MB, PNG)</label>
                                <div id="current_signature_image_container" class="mt-2">
                                    <?php if (!empty($doctor['signature_image'])): ?>
                                        <div class="d-flex flex-column align-items-start border p-2 rounded bg-light">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-signature me-2 text-info"></i>
                                                <span class="text-muted me-2">Current:</span>
                                                <a href="<?= base_url('public/uploads/doctors/' . esc($doctor['signature_image'])) ?>" target="_blank" class="text-decoration-none">
                                                    <strong><?= esc($doctor['signature_image']) ?></strong>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm ms-3"
                                                    onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'signature_image', '<?= esc($doctor['signature_image']) ?>', 'current_signature_image_container', 'signature_image')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            <?php
                                            $signatureExtension = pathinfo($doctor['signature_image'], PATHINFO_EXTENSION);
                                            $imageExtensions = ['png'];

                                            if (in_array(strtolower($signatureExtension), $imageExtensions)): ?>
                                                <img src="<?= base_url('public/uploads/doctors/' . esc($doctor['signature_image'])) ?>"
                                                    alt="Signature Image" class="img-thumbnail" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_signature_image_display">No signature image uploaded.</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="signature_image" id="signature_image" class="form-control-file mt-2" accept="image/png">
                                <small class="form-text text-muted">Upload a new image to replace the current one.</small>
                            </div>
                        </div>
                    </div>

                    ---

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="resume_file">Resume (Max 5MB, PDF/DOC/DOCX)</label>
                                <div id="current_resume_path_container" class="mt-2">
                                    <?php if (!empty($doctor['resume_path'])): ?>
                                        <div class="d-flex flex-column align-items-start border p-2 rounded bg-light">
                                            <div class="d-flex align-items-center mb-2">
                                                <?php
                                                $resumeExtension = pathinfo($doctor['resume_path'], PATHINFO_EXTENSION);
                                                $iconClass = 'fas fa-file-alt'; // Default icon
                                                $viewButton = '';

                                                if (strtolower($resumeExtension) === 'pdf') {
                                                    $iconClass = 'fas fa-file-pdf text-danger';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#pdfViewerModal" data-pdf-url="' . base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) . '">
                                                    <i class="fas fa-eye"></i> View PDF
                                                </button>';
                                                } elseif (in_array(strtolower($resumeExtension), ['doc', 'docx'])) {
                                                    $iconClass = 'fas fa-file-word text-primary';
                                                    $viewButton = '<a href="' . base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) . '" target="_blank" class="btn btn-info btn-sm ms-3">
                                                    <i class="fas fa-download"></i> Open Doc
                                                </a>';
                                                } elseif (in_array(strtolower($resumeExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    $iconClass = 'fas fa-image text-primary';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#imageViewerModal" data-image-url="' . base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) . '">
                                                    <i class="fas fa-eye"></i> View Image
                                                </button>';
                                                }
                                                ?>
                                                <i class="<?= $iconClass ?> me-2"></i>
                                                <span class="text-muted me-2">Current:</span>
                                                <a href="<?= base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) ?>" target="_blank" class="text-decoration-none">
                                                    <strong><?= esc($doctor['resume_path']) ?></strong>
                                                </a>
                                                <?= $viewButton ?>
                                                <button type="button" class="btn btn-danger btn-sm ms-2"
                                                    onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'resume_path', '<?= esc($doctor['resume_path']) ?>', 'current_resume_path_container', 'resume_file')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            <?php if (strtolower($resumeExtension) === 'pdf'): ?>
                                                <div class="mt-2 w-100" style="max-height: 300px; overflow-y: auto;">
                                                    <embed src="<?= base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) ?>" type="application/pdf" width="100%" height="280px" class="rounded">
                                                </div>
                                            <?php elseif (in_array(strtolower($resumeExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <img src="<?= base_url('public/uploads/doctors/' . esc($doctor['resume_path'])) ?>"
                                                    alt="Resume Image" class="img-thumbnail mt-2" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_resume_path_display">No resume uploaded.</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="resume_file" id="resume_file" class="form-control-file mt-2" accept=".pdf,.doc,.docx">
                                <small class="form-text text-muted">Upload a new file to replace the current one.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="degree_certificate_file">Degree Certificate (Max 5MB, PDF/JPG/PNG)</label>
                                <div id="current_degree_certificate_path_container" class="mt-2">
                                    <?php if (!empty($doctor['degree_certificate_path'])): ?>
                                        <div class="d-flex flex-column align-items-start border p-2 rounded bg-light">
                                            <div class="d-flex align-items-center mb-2">
                                                <?php
                                                $degreeCertExtension = pathinfo($doctor['degree_certificate_path'], PATHINFO_EXTENSION);
                                                $iconClass = 'fas fa-file-alt';
                                                $viewButton = '';

                                                if (strtolower($degreeCertExtension) === 'pdf') {
                                                    $iconClass = 'fas fa-file-pdf text-danger';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#pdfViewerModal" data-pdf-url="' . base_url('public/uploads/doctors/' . esc($doctor['degree_certificate_path'])) . '">
                                                    <i class="fas fa-eye"></i> View PDF
                                                </button>';
                                                } elseif (in_array(strtolower($degreeCertExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    $iconClass = 'fas fa-image text-primary';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#imageViewerModal" data-image-url="' . base_url('public/uploads/doctors/' . esc($doctor['degree_certificate_path'])) . '">
                                                    <i class="fas fa-eye"></i> View Image
                                                </button>';
                                                }
                                                ?>
                                                <i class="<?= $iconClass ?> me-2"></i>
                                                <span class="text-muted me-2">Current:</span>
                                                <a href="<?= base_url('public/uploads/doctors/' . esc($doctor['degree_certificate_path'])) ?>" target="_blank" class="text-decoration-none">
                                                    <strong><?= esc($doctor['degree_certificate_path']) ?></strong>
                                                </a>
                                                <?= $viewButton ?>
                                                <button type="button" class="btn btn-danger btn-sm ms-2"
                                                    onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'degree_certificate_path', '<?= esc($doctor['degree_certificate_path']) ?>', 'current_degree_certificate_path_container', 'degree_certificate_file')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            <?php if (strtolower($degreeCertExtension) === 'pdf'): ?>
                                                <div class="mt-2 w-100" style="max-height: 300px; overflow-y: auto;">
                                                    <embed src="<?= base_url('public/uploads/doctors/' . esc($doctor['degree_certificate_path'])) ?>" type="application/pdf" width="100%" height="280px" class="rounded">
                                                </div>
                                            <?php elseif (in_array(strtolower($degreeCertExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <img src="<?= base_url('public/uploads/doctors/' . esc($doctor['degree_certificate_path'])) ?>"
                                                    alt="Degree Certificate" class="img-thumbnail mt-2" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_degree_certificate_path_display">No degree certificate uploaded.</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="degree_certificate_file" id="degree_certificate_file" class="form-control-file mt-2" accept=".pdf,image/jpeg,image/png">
                                <small class="form-text text-muted">Upload a new file to replace the current one.</small>
                            </div>
                        </div>
                    </div>

                    ---

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="license_certificate_file">License Certificate (Max 5MB, PDF/JPG/PNG)</label>
                                <div id="current_license_certificate_path_container" class="mt-2">
                                    <?php if (!empty($doctor['license_certificate_path'])): ?>
                                        <div class="d-flex flex-column align-items-start border p-2 rounded bg-light">
                                            <div class="d-flex align-items-center mb-2">
                                                <?php
                                                $licenseCertExtension = pathinfo($doctor['license_certificate_path'], PATHINFO_EXTENSION);
                                                $iconClass = 'fas fa-id-card-alt';
                                                $viewButton = '';

                                                if (strtolower($licenseCertExtension) === 'pdf') {
                                                    $iconClass = 'fas fa-file-pdf text-danger';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#pdfViewerModal" data-pdf-url="' . base_url('public/uploads/doctors/' . esc($doctor['license_certificate_path'])) . '">
                                                    <i class="fas fa-eye"></i> View PDF
                                                </button>';
                                                } elseif (in_array(strtolower($licenseCertExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    $iconClass = 'fas fa-image text-primary';
                                                    $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#imageViewerModal" data-image-url="' . base_url('public/uploads/doctors/' . esc($doctor['license_certificate_path'])) . '">
                                                    <i class="fas fa-eye"></i> View Image
                                                </button>';
                                                }
                                                ?>
                                                <i class="<?= $iconClass ?> me-2"></i>
                                                <span class="text-muted me-2">Current:</span>
                                                <a href="<?= base_url('public/uploads/doctors/' . esc($doctor['license_certificate_path'])) ?>" target="_blank" class="text-decoration-none">
                                                    <strong><?= esc($doctor['license_certificate_path']) ?></strong>
                                                </a>
                                                <?= $viewButton ?>
                                                <button type="button" class="btn btn-danger btn-sm ms-2"
                                                    onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'license_certificate_path', '<?= esc($doctor['license_certificate_path']) ?>', 'current_license_certificate_path_container', 'license_certificate_file')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                            <?php if (strtolower($licenseCertExtension) === 'pdf'): ?>
                                                <div class="mt-2 w-100" style="max-height: 300px; overflow-y: auto;">
                                                    <embed src="<?= base_url('public/uploads/doctors/' . esc($doctor['license_certificate_path'])) ?>" type="application/pdf" width="100%" height="280px" class="rounded">
                                                </div>
                                            <?php elseif (in_array(strtolower($licenseCertExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <img src="<?= base_url('public/uploads/doctors/' . esc($doctor['license_certificate_path'])) ?>"
                                                    alt="License Certificate" class="img-thumbnail mt-2" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_license_certificate_path_display">No license certificate uploaded.</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="license_certificate_file" id="license_certificate_file" class="form-control-file mt-2" accept=".pdf,image/jpeg,image/png">
                                <small class="form-text text-muted">Upload a new file to replace the current one.</small>
                            </div>
                        </div>


                    </div>

                    <div class="row mt-5">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="other_certificate_file">Other Certificates/Awards (Max 5MB per file, PDF/JPG/PNG/DOC/DOCX)</label>
                                <input type="file" name="other_certificate_file[]" id="other_certificate_file" class="form-control-file" accept=".pdf,image/jpeg,image/png,.doc,.docx" multiple>
                                <small class="form-text text-muted">Select new files to add. Existing files are listed below.</small>

                                <div id="other_certificates_list_container" class="mt-2 col-md-6"> <?php
                                                                                                    $otherCertificates = $doctor['other_certificates_array'] ?? [];
                                                                                                    ?>
                                    <?php if (!empty($otherCertificates)): ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($otherCertificates as $index => $fileName): ?>
                                                <li class="list-group-item bg-light mb-2 rounded" id="other_certificate_item_<?= $index ?>" data-filename="<?= esc($fileName) ?>">
                                                    <div class="d-flex flex-column align-items-start p-2">
                                                        <div class="d-flex align-items-center mb-2 w-100">
                                                            <?php
                                                            $otherCertExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                                                            $otherCertIconClass = 'fas fa-file';
                                                            $otherCertTextColor = 'text-secondary';
                                                            $viewButton = '';
                                                            $inlineContent = '';

                                                            if (strtolower($otherCertExtension) === 'pdf') {
                                                                $otherCertIconClass = 'fas fa-file-pdf';
                                                                $otherCertTextColor = 'text-danger';
                                                                $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#pdfViewerModal" data-pdf-url="' . base_url('public/uploads/doctors/' . urlencode($fileName)) . '">
                                            <i class="fas fa-eye"></i> View PDF
                                        </button>';
                                                                $inlineContent = '<div class="mt-2 w-100" style="max-height: 200px; overflow-y: auto;"><embed src="' . base_url('public/uploads/doctors/' . esc($fileName)) . '" type="application/pdf" width="100%" height="180px" class="rounded"></div>';
                                                            } elseif (in_array(strtolower($otherCertExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                                $otherCertIconClass = 'fas fa-image';
                                                                $otherCertTextColor = 'text-primary';
                                                                $inlineContent = '<img src="' . base_url('public/uploads/doctors/' . esc($fileName)) . '" alt="Certificate Image" class="img-thumbnail mt-2" style="max-width: 120px; height: auto;">';
                                                                $viewButton = '<button type="button" class="btn btn-info btn-sm ms-3" data-bs-toggle="modal" data-bs-target="#imageViewerModal" data-image-url="' . base_url('public/uploads/doctors/' . urlencode($fileName)) . '">
                                            <i class="fas fa-eye"></i> View Image
                                        </button>';
                                                            } elseif (in_array(strtolower($otherCertExtension), ['doc', 'docx'])) {
                                                                $otherCertIconClass = 'fas fa-file-word';
                                                                $otherCertTextColor = 'text-primary';
                                                                $viewButton = '<a href="' . base_url('public/uploads/doctors/' . urlencode($fileName)) . '" target="_blank" class="btn btn-info btn-sm ms-3">
                                            <i class="fas fa-download"></i> Open Doc
                                        </a>';
                                                            }
                                                            ?>
                                                            <i class="<?= $otherCertIconClass ?> me-2 <?= $otherCertTextColor ?>"></i>
                                                            <a href="<?= base_url('public/uploads/doctors/' . urlencode($fileName)) ?>" target="_blank" class="text-decoration-none flex-grow-1">
                                                                <strong><?= esc($fileName) ?></strong>
                                                            </a>
                                                            <?= $viewButton ?>
                                                            <button type="button" class="btn btn-danger btn-sm ms-2"
                                                                onclick="deleteDocument(<?= esc($doctor['id']) ?>, 'other_certificates_path', '<?= esc($fileName) ?>', 'other_certificate_item_<?= $index ?>', 'other_certificate_file')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                        <?= $inlineContent ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted" id="no_other_certificates_display">No other certificates currently uploaded.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pdfViewerModalLabel">Document Viewer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe id="pdfFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-labelledby="imageViewerModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imageViewerModalLabel">Image Viewer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img id="imageFrame" src="" class="img-fluid" alt="Viewer Image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const pdfViewerModal = document.getElementById('pdfViewerModal');
                                if (pdfViewerModal) {
                                    pdfViewerModal.addEventListener('show.bs.modal', function(event) {
                                        const button = event.relatedTarget;
                                        const pdfUrl = button.getAttribute('data-pdf-url');
                                        const pdfFrame = pdfViewerModal.querySelector('#pdfFrame');
                                        pdfFrame.src = pdfUrl;
                                    });
                                    pdfViewerModal.addEventListener('hide.bs.modal', function() {
                                        const pdfFrame = pdfViewerModal.querySelector('#pdfFrame');
                                        pdfFrame.src = '';
                                    });
                                }

                                const imageViewerModal = document.getElementById('imageViewerModal');
                                if (imageViewerModal) {
                                    imageViewerModal.addEventListener('show.bs.modal', function(event) {
                                        const button = event.relatedTarget;
                                        const imageUrl = button.getAttribute('data-image-url');
                                        const imageFrame = imageViewerModal.querySelector('#imageFrame');
                                        imageFrame.src = imageUrl;
                                    });
                                    imageViewerModal.addEventListener('hide.bs.modal', function() {
                                        const imageFrame = imageViewerModal.querySelector('#imageFrame');
                                        imageFrame.src = '';
                                    });
                                }
                            });
                        </script>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Doctor</button>
                            <a href="<?= base_url('doctors') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
     

        function deleteDocument(doctorId, documentType, fileName, containerId, inputId) {
            if (!confirm('Are you sure you want to delete this document?')) {
                return;
            }

            const deleteUrl = `<?= base_url('doctors/delete_document_ajax') ?>`; // Verify this URL matches your route
            const postData = {
                id: doctorId,
                field: documentType,
                fileName: fileName
            };

            console.log('Attempting to delete:', {
                url: deleteUrl,
                data: postData,
                container: containerId,
                input: inputId
            });

            // If you have CSRF protection enabled in CodeIgniter 4
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                document.querySelector('meta[name="csrf-token"]').getAttribute('content') :
                '';

            fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken // Or include in body as 'csrf_test_name'
                    },
                    body: JSON.stringify(postData)
                })
                .then(response => {
                    console.log('Server Response:', response); // Log the raw response object
                    if (!response.ok) {
                        // If you get here, it means the server responded with an HTTP error code (e.g., 404, 500)
                        // The error message from CodeIgniter's "Cannot find route" would show here.
                        return response.text().then(text => {
                            throw new Error(`HTTP error! Status: ${response.status}. Response: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Parsed JSON Data:', data); // Log the parsed JSON data
                    // ... rest of your success/error handling
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while deleting the document. Please check the console and server logs.');
                });
        }
    </script>

    <?= $this->endSection() ?>