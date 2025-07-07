<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                <form action="<?= base_url('doctors/save') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <h4>Personal Information</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name') ?>" placeholder="Enter first name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name') ?>" placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= old('gender') == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= old('date_of_birth') ?>">
                            </div>


                        </div>


                        <h4 class="mt-4">Login Credentials</h4>
                        <div class="row">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="username">Login Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>"  >
                                    <?php if (session('errors.username')): ?>
                                        <div class="text-danger"><?= session('errors.username') ?></div>
                                    <?php endif ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password">Login Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"  >
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (session('errors.password')): ?>
                                        <div class="text-danger"><?= session('errors.password') ?></div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4">Contact Information</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="Enter email">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone_number">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= old('phone_number') ?>" placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address"><?= old('address') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="emergency_contact_name">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?= old('emergency_contact_name') ?>" placeholder="Emergency contact name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?= old('emergency_contact_phone') ?>" placeholder="Emergency contact phone">
                            </div>
                        </div>

                        <h4 class="mt-4">Professional Details</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="specialization">Specialization <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="specialization" name="specialization" value="<?= old('specialization') ?>" placeholder="e.g., Cardiology, Pediatrics" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="qualification">Qualification</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" value="<?= old('qualification') ?>" placeholder="e.g., MBBS, MD, DM">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="medical_license_no">Medical License No.</label>
                                <input type="text" class="form-control" id="medical_license_no" name="medical_license_no" value="<?= old('medical_license_no') ?>" placeholder="Enter medical license number">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="registration_number">Registration Number</label>
                                <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= old('registration_number') ?>" placeholder="Enter national/state registration number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="medical_council">Medical Council/Authority</label>
                            <input type="text" class="form-control" id="medical_council" name="medical_council" value="<?= old('medical_council') ?>" placeholder="e.g., Medical Council of India">
                        </div>
                        <div class="form-group">
                            <label for="experience_years">Years of Experience</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?= old('experience_years') ?>" placeholder="e.g., 10">
                        </div>
                        <div class="form-group">
                            <label for="bio">Biography / Professional Summary</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Brief professional summary"><?= old('bio') ?></textarea>
                        </div>

                        <h4 class="mt-4">Employment Details</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="department_id">Department</label>
                                <select class="form-control" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= esc($department['id']) ?>"
                                            <?= old('department_id') == $department['id'] ? 'selected' : '' ?>>
                                            <?= esc($department['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.department_id')): ?>
                                    <div class="text-danger"><?= session('errors.department_id') ?></div>
                                <?php endif ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date" value="<?= old('joining_date') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="employment_status">Employment Status</label>
                                <select class="form-control" id="employment_status" name="employment_status">
                                    <option value="">Select Status</option>
                                    <option value="Full-time" <?= old('employment_status') == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                    <option value="Part-time" <?= old('employment_status') == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                    <option value="Consultant" <?= old('employment_status') == 'Consultant' ? 'selected' : '' ?>>Consultant</option>
                                    <option value="On-Leave" <?= old('employment_status') == 'On-Leave' ? 'selected' : '' ?>>On-Leave</option>
                                    <option value="Resigned" <?= old('employment_status') == 'Resigned' ? 'selected' : '' ?>>Resigned</option>
                                    <option value="Terminated" <?= old('employment_status') == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contract_type">Contract Type</label>
                                <input type="text" class="form-control" id="contract_type" name="contract_type" value="<?= old('contract_type') ?>" placeholder="e.g., Permanent, Temporary">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="designation">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="<?= old('designation') ?>" placeholder="e.g., Senior Consultant, Resident Doctor">
                        </div>

                        <h4 class="mt-4">Fee Structure</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="opd_fee">OPD Consultation Fee</label>
                                <input type="number" step="0.01" class="form-control" id="opd_fee" name="opd_fee" value="<?= old('opd_fee') ?>" placeholder="e.g., 800.00">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="ipd_charge_percentage">IPD Charge Percentage (%)</label>
                                <input type="number" step="0.01" class="form-control" id="ipd_charge_percentage" name="ipd_charge_percentage" value="<?= old('ipd_charge_percentage') ?>" placeholder="e.g., 15.00">
                            </div>
                        </div>

                        <h4 class="mt-4">Bank & Tax Details</h4>
                        <div class="form-group">
                            <label for="bank_account_number">Bank Account Number</label>
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="<?= old('bank_account_number') ?>" placeholder="Enter bank account number">
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= old('bank_name') ?>" placeholder="Enter bank name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?= old('ifsc_code') ?>" placeholder="Enter IFSC code">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pan_number">PAN Number</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" value="<?= old('pan_number') ?>" placeholder="Enter PAN number">
                        </div>

                        <h4 class="mt-4">Documents & Files</h4>
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture (JPG, PNG - Max 2MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" accept="image/*">
                                    <label class="custom-file-label" for="profile_picture">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signature_image">Digital Signature (PNG - Max 1MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="signature_image" name="signature_image" accept="image/png">
                                    <label class="custom-file-label" for="signature_image">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="resume_file">Resume/CV (PDF, DOC, DOCX - Max 5MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="resume_file" name="resume_file" accept=".pdf,.doc,.docx">
                                    <label class="custom-file-label" for="resume_file">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="degree_certificate_file">Highest Degree Certificate (PDF, JPG, PNG - Max 5MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="degree_certificate_file" name="degree_certificate_file" accept=".pdf,image/*">
                                    <label class="custom-file-label" for="degree_certificate_file">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="license_certificate_file">Medical License Certificate (PDF, JPG, PNG - Max 5MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="license_certificate_file" name="license_certificate_file" accept=".pdf,image/*">
                                    <label class="custom-file-label" for="license_certificate_file">Choose file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="other_certificate_file">Other Certificate/Award (PDF, JPG, PNG, DOC, DOCX - Max 5MB)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="other_certificate_file" name="other_certificate_file" accept=".pdf,image/*,.doc,.docx">
                                    <label class="custom-file-label" for="other_certificate_file">Choose file</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">If you need to upload multiple other certificates, you would require more advanced client-side handling and server-side processing to store paths (e.g., as a JSON array of filenames).</small>
                        </div>


                        <h4 class="mt-4">System Settings</h4>
                        <div class="row">

                            <div class="form-group col-md-6">
                                <label for="is_available">Available for Duty?</label>
                                <select class="form-control" id="is_available" name="is_available">
                                    <option value="1" <?= old('is_available', 1) == '1' ? 'selected' : '' ?>>Yes</option>
                                    <option value="0" <?= old('is_available') == '0' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Account Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="Active" <?= old('status') == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= old('status') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="On Leave" <?= old('status') == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="Suspended" <?= old('status') == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Add Doctor</button>
                        <a href="<?= base_url('doctors') ?>" class="btn btn-secondary float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>








<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {

                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle the eye icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');

            });
        }
    });
</script>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize custom file input
    document.addEventListener('DOMContentLoaded', function() {
        bsCustomFileInput.init();
    });
</script>
<?= $this->endSection() ?>