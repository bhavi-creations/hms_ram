<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= esc($title ?? 'Edit Doctor') ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('doctors') ?>">Doctors</a></li>
                    <li class="breadcrumb-item active">Edit Doctor</li>
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
                        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i> Doctor Edit Form (All Fields Visible)</h3>
                    </div>

                    <form action="<?= base_url('doctors/update/' . $doctor['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT" />
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')) : ?>
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
                                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= set_value('first_name', $doctor['first_name']) ?>" placeholder="Enter first name" required>
                                                    <?= session('errors.first_name') ? '<div class="text-danger small mt-1">' . session('errors.first_name') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= set_value('last_name', $doctor['last_name']) ?>" placeholder="Enter last name" required>
                                                    <?= session('errors.last_name') ? '<div class="text-danger small mt-1">' . session('errors.last_name') . '</div>' : '' ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="gender">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="" disabled <?= empty(set_value('gender', $doctor['gender'])) ? 'selected' : '' ?>>Select Gender</option>
                                                        <option value="Male" <?= set_value('gender', $doctor['gender']) == 'Male' ? 'selected' : '' ?>>Male</option>
                                                        <option value="Female" <?= set_value('gender', $doctor['gender']) == 'Female' ? 'selected' : '' ?>>Female</option>
                                                        <option value="Other" <?= set_value('gender', $doctor['gender']) == 'Other' ? 'selected' : '' ?>>Other</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= set_value('date_of_birth', $doctor['date_of_birth']) ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="experience_years">Years of Experience</label>
                                                    <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?= set_value('experience_years', $doctor['experience_years']) ?>" placeholder="e.g., 10" min="0">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="bio">Biography / Professional Summary</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Brief professional summary..."><?= set_value('bio', $doctor['bio']) ?></textarea>
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
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email', $doctor['email']) ?>" placeholder="Enter email">
                                                    <?= session('errors.email') ? '<div class="text-danger small mt-1">' . session('errors.email') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="phone_number">Phone Number</label>
                                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= set_value('phone_number', $doctor['phone_number']) ?>" placeholder="Enter phone number">
                                                    <?= session('errors.phone_number') ? '<div class="text-danger small mt-1">' . session('errors.phone_number') . '</div>' : '' ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter complete address"><?= set_value('address', $doctor['address']) ?></textarea>
                                            </div>
                                            <div class="row border-top pt-3 mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?= set_value('emergency_contact_name', $doctor['emergency_contact_name']) ?>" placeholder="Emergency contact name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?= set_value('emergency_contact_phone', $doctor['emergency_contact_phone']) ?>" placeholder="Emergency contact phone">
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
                                                    <select class="form-control" id="specialization" name="specialization" required>
                                                        <option value="" disabled <?= empty(set_value('specialization', $doctor['specialization'])) ? 'selected' : '' ?>>Select Specialization</option>
                                                        <?php foreach ($specializations as $spec): ?>
                                                            <option value="<?= esc($spec['id']) ?>" <?= set_value('specialization', $doctor['specialization']) == $spec['id'] ? 'selected' : '' ?>>
                                                                <?= esc($spec['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?= session('errors.specialization') ? '<div class="text-danger small mt-1">' . session('errors.specialization') . '</div>' : '' ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="qualification">Qualification</label>
                                                    <input type="text" class="form-control" id="qualification" name="qualification" value="<?= set_value('qualification', $doctor['qualification']) ?>" placeholder="e.g., MBBS, MD">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="medical_license_no">Medical License No.</label>
                                                    <input type="text" class="form-control" id="medical_license_no" name="medical_license_no" value="<?= set_value('medical_license_no', $doctor['medical_license_no']) ?>" placeholder="License number">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="registration_number">Registration Number</label>
                                                    <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= set_value('registration_number', $doctor['registration_number']) ?>" placeholder="Registration number">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="medical_council">Medical Council/Authority</label>
                                                <input type="text" class="form-control" id="medical_council" name="medical_council" value="<?= set_value('medical_council', $doctor['medical_council']) ?>" placeholder="e.g., Medical Council of India">
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
                                                    <option value="" disabled <?= empty(set_value('department_id', $doctor['department_id'])) ? 'selected' : '' ?>>Select Department</option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?= esc($department['id']) ?>" <?= set_value('department_id', $doctor['department_id']) == $department['id'] ? 'selected' : '' ?>>
                                                            <?= esc($department['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?= session('errors.department_id') ? '<div class="text-danger small mt-1">' . session('errors.department_id') . '</div>' : '' ?>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="joining_date">Joining Date</label>
                                                    <input type="date" class="form-control" id="joining_date" name="joining_date" value="<?= set_value('joining_date', $doctor['joining_date']) ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="designation">Designation</label>
                                                    <input type="text" class="form-control" id="designation" name="designation" value="<?= set_value('designation', $doctor['designation']) ?>" placeholder="e.g., Consultant">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="employment_status">Employment Status</label>
                                                    <select class="form-control" id="employment_status" name="employment_status">
                                                        <option value="" disabled <?= empty(set_value('employment_status', $doctor['employment_status'])) ? 'selected' : '' ?>>Select Status</option>
                                                        <option value="Full-time" <?= set_value('employment_status', $doctor['employment_status']) == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                                        <option value="Part-time" <?= set_value('employment_status', $doctor['employment_status']) == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                                        <option value="Contract" <?= set_value('employment_status', $doctor['employment_status']) == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="contract_type">Contract Type</label>
                                                    <input type="text" class="form-control" id="contract_type" name="contract_type" value="<?= set_value('contract_type', $doctor['contract_type']) ?>" placeholder="e.g., Permanent">
                                                </div>
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
                                                    <input type="number" step="0.01" class="form-control" id="opd_fee" name="opd_fee" value="<?= set_value('opd_fee', $doctor['opd_fee']) ?>" placeholder="e.g., 800.00">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ipd_charge_percentage">IPD Charge Percentage (%)</label>
                                                    <input type="number" step="0.01" class="form-control" id="ipd_charge_percentage" name="ipd_charge_percentage" value="<?= set_value('ipd_charge_percentage', $doctor['ipd_charge_percentage']) ?>" placeholder="e.g., 15.00">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="bank_account_number">Bank Account Number</label>
                                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="<?= set_value('bank_account_number', $doctor['bank_account_number']) ?>" placeholder="Enter bank account number">
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="bank_name">Bank Name</label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= set_value('bank_name', $doctor['bank_name']) ?>" placeholder="Enter bank name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ifsc_code">IFSC Code</label>
                                                    <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?= set_value('ifsc_code', $doctor['ifsc_code']) ?>" placeholder="Enter IFSC code">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="pan_number">PAN Number</label>
                                                <input type="text" class="form-control" id="pan_number" name="pan_number" value="<?= set_value('pan_number', $doctor['pan_number']) ?>" placeholder="Enter PAN number">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row mt-4 pt-3 border-top">
                                <div class="col-md-12">
                                    <h4 class="text-danger"><i class="fas fa-paperclip mr-1"></i> Required Documents & Files</h4>
                                </div>

                                <!-- Profile Picture Upload and Display -->
                                <div class="form-group col-md-3">
                                    <label for="profile_picture">Profile Picture</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" accept="image/*">
                                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.profile_picture') ? '<div class="text-danger small mt-1">' . session('errors.profile_picture') . '</div>' : '' ?>

                                    <div id="profile_picture_container" class="mt-2">
                                        <?php if (!empty($doctor['profile_picture'])): ?>
                                            <img src="<?= base_url('public/uploads/doctors/' . $doctor['profile_picture']) ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 100px;">
                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="deleteFile('profile_picture', <?= $doctor['id'] ?>)">Delete</button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Digital Signature Upload and Display -->
                                <div class="form-group col-md-3">
                                    <label for="signature_image">Digital Signature</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="signature_image" name="signature_image" accept="image/png">
                                            <label class="custom-file-label" for="signature_image">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.signature_image') ? '<div class="text-danger small mt-1">' . session('errors.signature_image') . '</div>' : '' ?>

                                    <div id="signature_image_container" class="mt-2">
                                        <?php if (!empty($doctor['signature_image'])): ?>
                                            <img src="<?= base_url('public/uploads/doctors/' . $doctor['signature_image']) ?>" alt="Signature Image" class="img-thumbnail" style="max-width: 100px;">
                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="deleteFile('signature_image', <?= $doctor['id'] ?>)">Delete</button>
                                        <?php endif; ?>
                                    </div>
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

                                    <div id="resume_file_container" class="mt-2">
                                        <?php if (!empty($doctor['resume_path'])):
                                            $filePath = base_url('public/uploads/doctors/' . $doctor['resume_path']);
                                            $fileExtension = pathinfo($doctor['resume_path'], PATHINFO_EXTENSION);
                                        ?>
                                            <?php if (strtolower($fileExtension) === 'pdf'): ?>
                                                <iframe src="<?= $filePath ?>" style="width:100%; height:200px; border: 1px solid #ccc;"></iframe>
                                                <p class="text-muted small mt-1">
                                                    <a href="<?= $filePath ?>" target="_blank">View in New Tab</a>
                                                </p>
                                            <?php else: ?>
                                                <a href="<?= $filePath ?>" target="_blank"><?= esc($doctor['resume_path']) ?></a>
                                                <p class="text-muted small mt-1">
                                                    Click to download/view the file.
                                                </p>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="deleteFile('resume_path', <?= $doctor['id'] ?>)">Delete</button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Highest Degree Certificate Upload and Display -->
                                <div class="form-group col-md-3">
                                    <label for="degree_certificate_file">Highest Degree Cert.</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="degree_certificate_file" name="degree_certificate_file" accept=".pdf,image/*">
                                            <label class="custom-file-label" for="degree_certificate_file">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.degree_certificate_file') ? '<div class="text-danger small mt-1">' . session('errors.degree_certificate_file') . '</div>' : '' ?>

                                    <div id="degree_certificate_file_container" class="mt-2">
                                        <?php if (!empty($doctor['degree_certificate_path'])):
                                            $filePath = base_url('public/uploads/doctors/' . $doctor['degree_certificate_path']);
                                            $fileExtension = strtolower(pathinfo($doctor['degree_certificate_path'], PATHINFO_EXTENSION));
                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                            <?php if ($isImage): ?>
                                                <img src="<?= $filePath ?>" alt="Degree Certificate" class="img-thumbnail" style="max-width: 100px;">
                                            <?php elseif ($fileExtension === 'pdf'): ?>
                                                <iframe src="<?= $filePath ?>" style="width:100%; height:200px; border: 1px solid #ccc;"></iframe>
                                                <p class="text-muted small mt-1">
                                                    <a href="<?= $filePath ?>" target="_blank">View in New Tab</a>
                                                </p>
                                            <?php else: ?>
                                                <a href="<?= $filePath ?>" target="_blank"><?= esc($doctor['degree_certificate_path']) ?></a>
                                                <p class="text-muted small mt-1">
                                                    Click to view or download the file.
                                                </p>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="deleteFile('degree_certificate_path', <?= $doctor['id'] ?>)">Delete</button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Medical License Certificate Upload and Display -->
                                <div class="form-group col-md-3">
                                    <label for="license_certificate_file">Medical License Cert.</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="license_certificate_file" name="license_certificate_file" accept=".pdf,image/*">
                                            <label class="custom-file-label" for="license_certificate_file">Choose file</label>
                                        </div>
                                    </div>
                                    <?= session('errors.license_certificate_file') ? '<div class="text-danger small mt-1">' . session('errors.license_certificate_file') . '</div>' : '' ?>

                                    <div id="license_certificate_file_container" class="mt-2">
                                        <?php if (!empty($doctor['license_certificate_path'])):
                                            $filePath = base_url('public/uploads/doctors/' . $doctor['license_certificate_path']);
                                            $fileExtension = strtolower(pathinfo($doctor['license_certificate_path'], PATHINFO_EXTENSION));
                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                            <?php if ($isImage): ?>
                                                <img src="<?= $filePath ?>" alt="License Certificate" class="img-thumbnail" style="max-width: 100px;">
                                            <?php elseif ($fileExtension === 'pdf'): ?>
                                                <iframe src="<?= $filePath ?>" style="width:100%; height:200px; border: 1px solid #ccc;"></iframe>
                                                <p class="text-muted small mt-1">
                                                    <a href="<?= $filePath ?>" target="_blank">View in New Tab</a>
                                                </p>
                                            <?php else: ?>
                                                <a href="<?= $filePath ?>" target="_blank"><?= esc($doctor['license_certificate_path']) ?></a>
                                                <p class="text-muted small mt-1">
                                                    Click to view or download the file.
                                                </p>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="deleteFile('license_certificate_path', <?= $doctor['id'] ?>)">Delete</button>
                                        <?php endif; ?>
                                    </div>
                                </div>


                                <!-- Other Certificates Multi-files -->
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

                                    <div id="other_certificate_files_container" class="mt-2">
                                        <?php if (!empty($doctor['other_certificates_array'])): ?>
                                            <?php foreach ($doctor['other_certificates_array'] as $file):
                                                $filePath = base_url('public/uploads/doctors/' . $file);
                                                $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                                            ?>
                                                <div id="file_<?= esc($file) ?>" class="d-flex align-items-center flex-wrap mb-2 p-1 border rounded">
                                                    <?php if ($isImage): ?>
                                                        <img src="<?= $filePath ?>" alt="Certificate" class="img-thumbnail mr-2" style="max-width: 80px;">
                                                        <span class="text-truncate mr-2"><?= esc($file) ?></span>
                                                    <?php elseif ($fileExtension === 'pdf'): ?>
                                                        <a href="<?= $filePath ?>" target="_blank" class="mr-2">PDF: <?= esc($file) ?></a>
                                                        <p class="text-muted small w-100 mb-0">Click to view in new tab.</p>
                                                    <?php else: ?>
                                                        <a href="<?= $filePath ?>" target="_blank" class="mr-2">File: <?= esc($file) ?></a>
                                                        <p class="text-muted small w-100 mb-0">Click to download/view.</p>
                                                    <?php endif; ?>

                                                    <button type="button" class="btn btn-sm btn-danger ml-auto" onclick="deleteFile('other_certificates_path', <?= $doctor['id'] ?>, '<?= esc($file) ?>')">Delete</button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Available and Status fields remain unchanged -->
                                <div class="form-group col-md-3">
                                    <label for="is_available">Available for Duty?</label>
                                    <select class="form-control" id="is_available" name="is_available">
                                        <option value="1" <?= set_value('is_available', $doctor['is_available']) == '1' ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= set_value('is_available', $doctor['is_available']) == '0' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="status">Account Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" <?= set_value('status', $doctor['status']) == 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Inactive" <?= set_value('status', $doctor['status']) == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="On Leave" <?= set_value('status', $doctor['status']) == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                        <option value="Suspended" <?= set_value('status', $doctor['status']) == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="card-footer text-right">
                            <a href="<?= base_url('doctors') ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times-circle mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>




<script>
    function deleteFile(fieldName, doctorId, filename = '') {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        const data = {
            doctor_id: doctorId,
            field: fieldName
        };
        if (filename) {
            data.filename = filename;
        }

        fetch('<?= base_url('doctors/deleteFile') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(json => {
                if (json.success) {
                    if (filename) {
                        // For multi-file fields, remove the specific file div
                        const elem = document.getElementById('file_' + filename);
                        if (elem) elem.remove();
                    } else {
                        // For single file fields, remove entire container
                        const container = document.getElementById(fieldName + '_container');
                        if (container) container.innerHTML = '';
                    }
                } else {
                    alert(json.message || 'Failed to delete the file.');
                }
            })
            .catch(() => {
                alert('Error deleting the file.');
            });
    }
</script>





<?= $this->endSection() ?>