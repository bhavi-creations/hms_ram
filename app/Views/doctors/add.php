<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= esc($title ?? 'Register New Doctor') ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('doctors') ?>">Doctors</a></li>
                    <li class="breadcrumb-item active">New Doctor</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card card-outline card-primary shadow-lg">
                    <div class="card-header border-bottom-0">
                        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i> Doctor Registration Form (All Fields Visible)</h3>
                    </div>
                    
                    <form action="<?= base_url('doctors/save') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="card-body">
                            <?php // Display flash messages if any
                            if (session()->getFlashdata('error')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= session()->getFlashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                
                                <div class="col-md-7 pr-md-4 border-right">

                                    <div class="card card-light card-outline mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title text-primary"><i class="fas fa-user-circle mr-1"></i> Personal Information <span class="text-danger">*</span></h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name') ?>" placeholder="Enter first name" required>
                                                    <?= session('errors.first_name') ? '<div class="text-danger small mt-1">' . session('errors.first_name') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name') ?>" placeholder="Enter last name" required>
                                                    <?= session('errors.last_name') ? '<div class="text-danger small mt-1">' . session('errors.last_name') . '</div>' : '' ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="gender">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="" disabled selected>Select Gender</option>
                                                        <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                                        <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                                        <option value="Other" <?= old('gender') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= old('date_of_birth') ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="experience_years">Years of Experience</label>
                                                    <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?= old('experience_years') ?>" placeholder="e.g., 10" min="0">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="bio">Biography / Professional Summary</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Brief professional summary..."><?= old('bio') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card card-light card-outline mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title text-info"><i class="fas fa-phone-alt mr-1"></i> Contact & Emergency</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="Enter email">
                                                    <?= session('errors.email') ? '<div class="text-danger small mt-1">' . session('errors.email') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="phone_number">Phone Number</label>
                                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= old('phone_number') ?>" placeholder="Enter phone number">
                                                    <?= session('errors.phone_number') ? '<div class="text-danger small mt-1">' . session('errors.phone_number') . '</div>' : '' ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter complete address"><?= old('address') ?></textarea>
                                            </div>
                                            <div class="row border-top pt-3 mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?= old('emergency_contact_name') ?>" placeholder="Emergency contact name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?= old('emergency_contact_phone') ?>" placeholder="Emergency contact phone">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-light card-outline mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title text-success"><i class="fas fa-user-md mr-1"></i> Professional Credentials</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="specialization">Specialization <span class="text-danger">*</span></label>
                                                    <!-- START: UPDATED Specialization Dropdown -->
                                                    <select class="form-control" id="specialization" name="specialization" required>
                                                        <option value="" disabled selected>Select Specialization</option>
                                                        <?php foreach ($specializations as $spec): ?>
                                                            <option value="<?= esc($spec['id']) ?>"
                                                                <?= old('specialization') == $spec['id'] ? 'selected' : '' ?>>
                                                                <?= esc($spec['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <!-- END: UPDATED Specialization Dropdown -->
                                                    <?= session('errors.specialization') ? '<div class="text-danger small mt-1">' . session('errors.specialization') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="qualification">Qualification</label>
                                                    <input type="text" class="form-control" id="qualification" name="qualification" value="<?= old('qualification') ?>" placeholder="e.g., MBBS, MD">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="medical_license_no">Medical License No.</label>
                                                    <input type="text" class="form-control" id="medical_license_no" name="medical_license_no" value="<?= old('medical_license_no') ?>" placeholder="License number">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="registration_number">Registration Number</label>
                                                    <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= old('registration_number') ?>" placeholder="Registration number">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="medical_council">Medical Council/Authority</label>
                                                <input type="text" class="form-control" id="medical_council" name="medical_council" value="<?= old('medical_council') ?>" placeholder="e.g., Medical Council of India">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                
                                <div class="col-md-5 pl-md-4">
                                    
                                    <div class="card card-light card-outline mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title text-warning"><i class="fas fa-briefcase mr-1"></i> Employment & Account</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="department_id">Department</label>
                                                <select class="form-control" id="department_id" name="department_id" required>
                                                    <option value="" disabled selected>Select Department</option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?= esc($department['id']) ?>"
                                                            <?= old('department_id') == $department['id'] ? 'selected' : '' ?>>
                                                            <?= esc($department['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?= session('errors.department_id') ? '<div class="text-danger small mt-1">' . session('errors.department_id') . '</div>' : '' ?>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="joining_date">Joining Date</label>
                                                    <input type="date" class="form-control" id="joining_date" name="joining_date" value="<?= old('joining_date') ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="designation">Designation</label>
                                                    <input type="text" class="form-control" id="designation" name="designation" value="<?= old('designation') ?>" placeholder="e.g., Consultant">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="employment_status">Employment Status</label>
                                                    <select class="form-control" id="employment_status" name="employment_status">
                                                        <option value="" disabled selected>Select Status</option>
                                                        <option value="Full-time" <?= old('employment_status') == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                                        <option value="Part-time" <?= old('employment_status') == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                                        <option value="Contract" <?= old('employment_status') == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="contract_type">Contract Type</label>
                                                    <input type="text" class="form-control" id="contract_type" name="contract_type" value="<?= old('contract_type') ?>" placeholder="e.g., Permanent">
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="text-muted">Login/System Access</h6>
                                            <div class="form-group">
                                                <label for="username">Login Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" placeholder="Enter login username" required>
                                                <?= session('errors.username') ? '<div class="text-danger small mt-1">' . session('errors.username') . '</div>' : '' ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Login Password <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i></span>
                                                    </div>
                                                </div>
                                                <?= session('errors.password') ? '<div class="text-danger small mt-1">' . session('errors.password') . '</div>' : '' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-light card-outline mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title text-indigo"><i class="fas fa-money-check-alt mr-1"></i> Financial & Banking</h5>
                                        </div>
                                        <div class="card-body">
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
                                        </div>
                                    </div>
                                    
                                </div>
                            </div> <div class="row mt-4 pt-3 border-top">
                                <div class="col-md-12">
                                    <h4 class="text-danger"><i class="fas fa-paperclip mr-1"></i> Required Documents & Files</h4>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="profile_picture">Profile Picture</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" accept="image/*">
                                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.profile_picture') ? '<div class="text-danger small mt-1">' . session('errors.profile_picture') . '</div>' : '' ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="signature_image">Digital Signature</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="signature_image" name="signature_image" accept="image/png">
                                            <label class="custom-file-label" for="signature_image">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.signature_image') ? '<div class="text-danger small mt-1">' . session('errors.signature_image') . '</div>' : '' ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="resume_file">Resume/CV</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="resume_file" name="resume_file" accept=".pdf,.doc,.docx">
                                            <label class="custom-file-label" for="resume_file">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.resume_file') ? '<div class="text-danger small mt-1">' . session('errors.resume_file') . '</div>' : '' ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="degree_certificate_file">Highest Degree Cert.</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="degree_certificate_file" name="degree_certificate_file" accept=".pdf,image/*">
                                            <label class="custom-file-label" for="degree_certificate_file">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.degree_certificate_file') ? '<div class="text-danger small mt-1">' . session('errors.degree_certificate_file') . '</div>' : '' ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="license_certificate_file">Medical License Cert.</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="license_certificate_file" name="license_certificate_file" accept=".pdf,image/*">
                                            <label class="custom-file-label" for="license_certificate_file">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.license_certificate_file') ? '<div class="text-danger small mt-1">' . session('errors.license_certificate_file') . '</div>' : '' ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="other_certificate_file">Other Certificates (Multi)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="other_certificate_file" name="other_certificate_file[]" accept=".pdf,image/*,.doc,.docx" multiple>
                                            <label class="custom-file-label" for="other_certificate_file">Choose files</label>
                                        </div>
                                    </div>
                                    <?php if (session('errors.other_certificate_file')): ?>
                                        <div class="text-danger small mt-1"><?= implode('<br>', session('errors.other_certificate_file')) ?></div>
                                    <?php elseif (session('errors')['other_certificate_file.*'] ?? false): ?>
                                        <div class="text-danger small mt-1">One or more uploaded files failed validation.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="is_available">Available for Duty?</label>
                                    <select class="form-control" id="is_available" name="is_available">
                                        <option value="1" <?= old('is_available', 1) == '1' ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= old('is_available') == '0' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
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
                        <div class="card-footer text-right">
                            <a href="<?= base_url('doctors') ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times-circle mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 CSS and JS for enhanced dropdowns -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize custom file input
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }

        // Initialize Select2 on all dropdowns with the .form-control class
        // This includes: gender, specialization, department_id, employment_status, is_available, status
        $('select.form-control').select2({
            theme: 'bootstrap4', // Use the AdminLTE compatible theme
            width: '100%',
            // Allow clearing the selection if it's not a required field
            allowClear: true 
        });

        // Toggle Password Visibility
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
