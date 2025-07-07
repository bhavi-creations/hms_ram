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
                        <input type="hidden" name="id" value="<?= esc($doctor['id']) ?>"> <div class="row">
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
                                    <label for="user_id">User ID (Optional - for Login)</label>
                                    <input type="number" name="user_id" id="user_id" class="form-control" value="<?= old('user_id', $doctor['user_id']) ?>">
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
                                    <label for="department_id">Department ID</label>
                                    <input type="number" name="department_id" id="department_id" class="form-control" value="<?= old('department_id', $doctor['department_id']) ?>">
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

                        <hr>
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

                        <hr>
                        <h5>Document Uploads (Leave blank to keep current)</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_picture">Profile Picture (Max 2MB, JPG/PNG)</label>
                                    <?php if ($doctor['profile_picture']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['profile_picture'])) ?>" target="_blank"><?= esc($doctor['profile_picture']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control-file">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="signature_image">Signature Image (Max 1MB, PNG)</label>
                                    <?php if ($doctor['signature_image']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['signature_image'])) ?>" target="_blank"><?= esc($doctor['signature_image']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="signature_image" id="signature_image" class="form-control-file">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resume_file">Resume (Max 5MB, PDF/DOC/DOCX)</label>
                                    <?php if ($doctor['resume_path']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['resume_path'])) ?>" target="_blank"><?= esc($doctor['resume_path']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="resume_file" id="resume_file" class="form-control-file">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="degree_certificate_file">Degree Certificate (Max 5MB, PDF/JPG/PNG)</label>
                                    <?php if ($doctor['degree_certificate_path']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['degree_certificate_path'])) ?>" target="_blank"><?= esc($doctor['degree_certificate_path']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="degree_certificate_file" id="degree_certificate_file" class="form-control-file">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="license_certificate_file">License Certificate (Max 5MB, PDF/JPG/PNG)</label>
                                    <?php if ($doctor['license_certificate_path']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['license_certificate_path'])) ?>" target="_blank"><?= esc($doctor['license_certificate_path']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="license_certificate_file" id="license_certificate_file" class="form-control-file">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="other_certificate_file">Other Certificates (Max 5MB, PDF/JPG/PNG/DOC/DOCX)</label>
                                    <?php if ($doctor['other_certificates_path']): ?>
                                        <p class="text-muted">Current: <a href="<?= base_url('uploads/doctors/' . esc($doctor['other_certificates_path'])) ?>" target="_blank"><?= esc($doctor['other_certificates_path']) ?></a></p>
                                    <?php endif; ?>
                                    <input type="file" name="other_certificate_file" id="other_certificate_file" class="form-control-file">
                                </div>
                            </div>
                        </div>

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
<?= $this->endSection() ?>