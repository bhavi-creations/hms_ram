<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-weight: 600;"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('patients') ?>">Patient Management</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-light card-outline card-widget rounded-xl shadow-lg p-4">

            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php $errors = session()->getFlashdata('errors'); ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                        <ul>
                            <?php foreach ($errors as $field => $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php $isEditMode = isset($patient) && !empty($patient); ?>

                <?= form_open_multipart('patients/save') ?>

                <?php if ($isEditMode): ?>
                    <input type="hidden" name="id" value="<?= esc($patient['id']) ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-user-circle mr-2"></i>Personal Information</h4>

                        <div class="form-group">
                            <label for="patient_id_code">Patient Primary ID</label>
                            <input type="text" id="patient_id_code" class="form-control-plaintext font-weight-bold" value="<?= $isEditMode ? esc($patient['patient_id_code']) : 'Will be auto-generated' ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-lg <?= $validation->hasError('first_name') ? 'is-invalid' : '' ?>" id="first_name" placeholder="Enter first name" value="<?= old('first_name', $isEditMode ? $patient['first_name'] : '') ?>" required>
                            <?php if ($validation->hasError('first_name')): ?><div class="invalid-feedback"><?= $validation->getError('first_name') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control form-control-lg <?= $validation->hasError('last_name') ? 'is-invalid' : '' ?>" id="last_name" placeholder="Enter last name" value="<?= old('last_name', $isEditMode ? $patient['last_name'] : '') ?>" required>
                            <?php if ($validation->hasError('last_name')): ?><div class="invalid-feedback"><?= $validation->getError('last_name') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control <?= $validation->hasError('date_of_birth') ? 'is-invalid' : '' ?>" id="date_of_birth" value="<?= old('date_of_birth', $isEditMode ? $patient['date_of_birth'] : '') ?>" required>
                            <?php if ($validation->hasError('date_of_birth')): ?><div class="invalid-feedback"><?= $validation->getError('date_of_birth') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-control select2 <?= $validation->hasError('gender') ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?= old('gender', $isEditMode ? $patient['gender'] : '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= old('gender', $isEditMode ? $patient['gender'] : '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= old('gender', $isEditMode ? $patient['gender'] : '') == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if ($validation->hasError('gender')): ?><div class="invalid-feedback"><?= $validation->getError('gender') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="patient_type">Patient Type <span class="text-danger">*</span></label>
                            <select name="patient_type" id="patient_type" class="form-control select2 <?= $validation->hasError('patient_type') ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Patient Type</option>
                                <option value="General" <?= old('patient_type', $isEditMode ? $patient['patient_type'] : '') == 'General' ? 'selected' : '' ?>>General Patient</option>
                                <option value="OPD" <?= old('patient_type', $isEditMode ? $patient['patient_type'] : '') == 'OPD' ? 'selected' : '' ?>>OPD Patient</option>
                                <option value="Casualty" <?= old('patient_type', $isEditMode ? $patient['patient_type'] : '') == 'Casualty' ? 'selected' : '' ?>>Casualty Patient</option>
                            </select>
                            <?php if ($validation->hasError('patient_type')): ?><div class="invalid-feedback"><?= $validation->getError('patient_type') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="blood_group">Blood Group</label>
                            <select name="blood_group" id="blood_group" class="form-control select2">
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= old('blood_group', $isEditMode ? $patient['blood_group'] : '') == 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="marital_status">Marital Status</label>
                            <input type="text" name="marital_status" class="form-control" id="marital_status" placeholder="e.g., Married" value="<?= old('marital_status', $isEditMode ? $patient['marital_status'] : '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="occupation">Occupation</label>
                            <input type="text" name="occupation" class="form-control" id="occupation" placeholder="e.g., Engineer" value="<?= old('occupation', $isEditMode ? $patient['occupation'] : '') ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-id-card-alt mr-2"></i>Contact Information</h4>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" class="form-control" id="address" rows="3" placeholder="Enter patient address"><?= old('address', $isEditMode ? $patient['address'] : '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control <?= $validation->hasError('phone_number') ? 'is-invalid' : '' ?>" id="phone_number" placeholder="Enter phone number" value="<?= old('phone_number', $isEditMode ? $patient['phone_number'] : '') ?>">
                            <?php if ($validation->hasError('phone_number')): ?><div class="invalid-feedback"><?= $validation->getError('phone_number') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : '' ?>" id="email" placeholder="Enter email" value="<?= old('email', $isEditMode ? $patient['email'] : '') ?>">
                            <?php if ($validation->hasError('email')): ?><div class="invalid-feedback"><?= $validation->getError('email') ?></div><?php endif; ?>
                        </div>

                        <h4 class="mb-4 mt-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-ambulance mr-2"></i>Emergency Contact</h4>
                        <div class="form-group">
                            <label for="emergency_contact_name">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control <?= $validation->hasError('emergency_contact_name') ? 'is-invalid' : '' ?>" id="emergency_contact_name" placeholder="Emergency contact person" value="<?= old('emergency_contact_name', $isEditMode ? $patient['emergency_contact_name'] : '') ?>">
                            <?php if ($validation->hasError('emergency_contact_name')): ?><div class="invalid-feedback"><?= $validation->getError('emergency_contact_name') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="emergency_contact_phone">Emergency Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" class="form-control <?= $validation->hasError('emergency_contact_phone') ? 'is-invalid' : '' ?>" id="emergency_contact_phone" placeholder="Emergency contact phone" value="<?= old('emergency_contact_phone', $isEditMode ? $patient['emergency_contact_phone'] : '') ?>">
                            <?php if ($validation->hasError('emergency_contact_phone')): ?><div class="invalid-feedback"><?= $validation->getError('emergency_contact_phone') ?></div><?php endif; ?>
                        </div>

                        <h4 class="mb-4 mt-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-notes-medical mr-2"></i>Medical History (Optional)</h4>
                        <div class="form-group">
                            <label for="known_allergies">Known Allergies</label>
                            <textarea name="known_allergies" class="form-control" id="known_allergies" rows="2" placeholder="e.g., Penicillin, Peanuts"><?= old('known_allergies', $isEditMode ? $patient['known_allergies'] : '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pre_existing_conditions">Pre-existing Conditions</label>
                            <textarea name="pre_existing_conditions" class="form-control" id="pre_existing_conditions" rows="2" placeholder="e.g., Diabetes, Hypertension"><?= old('pre_existing_conditions', $isEditMode ? $patient['pre_existing_conditions'] : '') ?></textarea>
                        </div>
                    </div>
                </div>
                <hr class="my-5">

                <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-calendar-alt mr-2"></i>Appointment Details</h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date</label>
                            <input type="date" name="appointment_date"
                                class="form-control datetimepicker-input <?= $validation->hasError('appointment_date') ? 'is-invalid' : '' ?>"
                                value="<?= old('appointment_date', $appointment_date ?? '') ?>">

                            <?php if ($validation->hasError('appointment_date')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('appointment_date') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time</label>
                            <input type="time" name="appointment_time"
                                class="form-control datetimepicker-input <?= $validation->hasError('appointment_time') ? 'is-invalid' : '' ?>"
                                value="<?= old('appointment_time', $appointment_time ?? '') ?>">

                            <?php if ($validation->hasError('appointment_time')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('appointment_time') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reason_for_visit">Reason for Visit</label>
                            <textarea name="reason_for_visit"
                                class="form-control <?= $validation->hasError('reason_for_visit') ? 'is-invalid' : '' ?>"
                                rows="3" placeholder="Brief reason for the visit..."><?= old('reason_for_visit', $reason_for_visit ?? '') ?></textarea>

                            <?php if ($validation->hasError('reason_for_visit')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('reason_for_visit') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-share-alt mr-2"></i>Referral & Remarks</h4>
                        <div class="form-group">
                            <label for="referred_to_doctor_id">Referred To Doctor</label>
                            <select name="referred_to_doctor_id" id="referred_to_doctor_id" class="form-control select2">
                                <option value="">-- Select Doctor --</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option
                                        value="<?= esc($doctor['id']) ?>"
                                        data-opd-fee="<?= esc($doctor['opd_fee']) ?>"
                                        <?= old('referred_to_doctor_id', $isEditMode ? $patient['referred_to_doctor_id'] : '') == $doctor['id'] ? 'selected' : '' ?>>
                                        <!-- UPDATED: Displaying specialization name (specialization_name) -->
                                        <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> (<?= esc($doctor['specialization_name'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="referred_by_id">Referred By</label>
                            <select name="referred_by_id" id="referred_by_id" class="form-control select2 <?= $validation->hasError('referred_by_id') ? 'is-invalid' : '' ?>">
                                <option value="">Select Referred By (Optional)</option>
                                <?php foreach ($referred_persons as $person): ?>
                                    <option value="<?= esc($person['id']) ?>" <?= old('referred_by_id', $isEditMode ? $patient['referred_by_id'] : '') == $person['id'] ? 'selected' : '' ?>>
                                        <?= esc($person['name']) ?> (<?= esc($person['type']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($validation->hasError('referred_by_id')): ?><div class="invalid-feedback"><?= $validation->getError('referred_by_id') ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control <?= $validation->hasError('remarks') ? 'is-invalid' : '' ?>" id="remarks" rows="3" placeholder="Any additional notes or remarks..."><?= old('remarks', $isEditMode ? $patient['remarks'] : '') ?></textarea>
                            <?php if ($validation->hasError('remarks')): ?><div class="invalid-feedback"><?= $validation->getError('remarks') ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-file-invoice-dollar mr-2"></i>Financial Details</h4>

                        <div class="card-deck">
                            <div class="card bg-light border-info mb-3 rounded-lg shadow-sm">
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label for="fee">Fee (INR)</label>
                                        <input type="number" step="0.01" name="fee" class="form-control form-control-lg font-weight-bold text-primary <?= $validation->hasError('fee') ? 'is-invalid' : '' ?>" id="fee" placeholder="e.g., 500.00" value="<?= old('fee', $isEditMode ? $patient['fee'] : '0.00') ?>">
                                        <?php if ($validation->hasError('fee')): ?><div class="invalid-feedback"><?= $validation->getError('fee') ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card bg-light border-warning mb-3 rounded-lg shadow-sm">
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label for="discount_percentage">Discount (%)</label>
                                        <input type="number" step="0.01" name="discount_percentage" class="form-control form-control-lg font-weight-bold text-warning <?= $validation->hasError('discount_percentage') ? 'is-invalid' : '' ?>" id="discount_percentage" placeholder="e.g., 10.00" value="<?= old('discount_percentage', $isEditMode ? $patient['discount_percentage'] : '0.00') ?>">
                                        <?php if ($validation->hasError('discount_percentage')): ?><div class="invalid-feedback"><?= $validation->getError('discount_percentage') ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="final_amount">Final Amount (INR)</label>
                            <input type="number" step="0.01" name="final_amount" class="form-control form-control-lg bg-success text-white text-center font-weight-bolder" id="final_amount" value="<?= old('final_amount', $isEditMode ? $patient['final_amount'] : '0.00') ?>" readonly style="font-size: 1.5rem;">
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                <h4 class="mb-4 pb-2 border-bottom text-secondary" style="font-weight: 500;"><i class="fas fa-upload mr-2"></i>Report Upload</h4>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="upload_reports">Upload Reports</label>
                            <div class="custom-file">
                                <input type="file" name="upload_reports[]" id="upload_reports" class="custom-file-input <?= $validation->hasError('upload_reports.*') ? 'is-invalid' : '' ?>" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                                <label class="custom-file-label" for="upload_reports">Choose files...</label>
                            </div>
                            <?php if ($validation->hasError('upload_reports.*')): ?>
                                <div class="invalid-feedback d-block"><?= $validation->getError('upload_reports.*') ?></div>
                            <?php elseif ($validation->hasError('upload_reports')): ?>
                                <div class="invalid-feedback d-block"><?= $validation->getError('upload_reports') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Max 5MB each. Allowed: PDF, DOC, JPG, PNG.</small>

                            <?php if ($isEditMode && !empty($patient['reports'])):
                                $existingReports = json_decode($patient['reports'], true);
                                if (is_array($existingReports) && count($existingReports) > 0): ?>
                                    <div class="mt-4">
                                        <h5 class="mb-3">Existing Report(s):</h5>
                                        <div class="row" id="existingReportsContainer"> <?php foreach ($existingReports as $fileName): ?>
                                                <?php
                                                                                            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                                                                            $fileUrl = base_url('public/uploads/patient_reports/' . urlencode($fileName));
                                                ?>
                                                <div class="col-auto mb-3" id="report-<?= esc(str_replace('.', '-', $fileName)) ?>">
                                                    <div class="card p-3 text-center position-relative shadow-sm rounded-lg" style="width: 220px; height: auto;">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-report-btn"
                                                            data-patient-id="<?= esc($patient['patient_id_code']) ?>"
                                                            data-filename="<?= esc($fileName) ?>"
                                                            title="Delete Report">
                                                            <i class="fas fa-times"></i>
                                                        </button>

                                                        <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                            <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none pt-3"> <img src="<?= $fileUrl ?>" alt="Report Image" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;">
                                                                <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></small>
                                                            </a>
                                                        <?php elseif (strtolower($ext) === 'pdf'): ?>
                                                            <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none pt-3"> <embed src="<?= $fileUrl ?>" type="application/pdf" width="100%" height="200px" style="border: 1px solid #ddd; border-radius: 4px;" />
                                                                <small class="d-block mt-2 text-muted text-truncate" title="<?= esc($fileName) ?>"><i class="fas fa-file-pdf text-danger me-1"></i> <?= esc($fileName) ?></small>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?= $fileUrl ?>" target="_blank" class="d-block text-decoration-none pt-3">
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
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const deleteButtons = document.querySelectorAll('.delete-report-btn');
                                            // Ensure csrf-token is retrieved correctly if in the head
                                            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                                            const csrfHashElement = document.querySelector('meta[name="csrf-hash"]');

                                            // Fallback/Safety Check
                                            if (!csrfTokenElement || !csrfHashElement) {
                                                console.error("CSRF tokens not found in meta tags. Delete functionality may fail.");
                                            }

                                            const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';
                                            const csrfHash = csrfHashElement ? csrfHashElement.getAttribute('content') : '';

                                            deleteButtons.forEach(button => {
                                                button.addEventListener('click', function() {
                                                    const patientId = this.dataset.patientId;
                                                    const filename = this.dataset.filename;
                                                    const cardId = `report-${filename.replace(/\./g, '-')}`;

                                                    if (confirm(`Are you sure you want to delete the report "${filename}"? This action cannot be undone.`)) {
                                                        const postData = {
                                                            patient_id: patientId,
                                                            filename: filename,
                                                        };
                                                        // Add dynamic CSRF tokens to the request body
                                                        if (csrfHash && csrfToken) {
                                                            postData[csrfHash] = csrfToken;
                                                        }

                                                        fetch('<?= base_url('patients/deleteReportFile') ?>', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-Requested-With': 'XMLHttpRequest',
                                                                    // Only include this if your main.php defined a custom header, otherwise rely on body/cookies
                                                                    // '<?= csrf_header() ?>': csrfToken 
                                                                },
                                                                body: JSON.stringify(postData)
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    alert(data.message);
                                                                    const cardToRemove = document.getElementById(cardId);
                                                                    if (cardToRemove) {
                                                                        cardToRemove.remove();
                                                                        const container = document.getElementById('existingReportsContainer');
                                                                        if (container && container.children.length === 0) {
                                                                            const parentDiv = container.closest('.mt-4');
                                                                            if (parentDiv) parentDiv.style.display = 'none';
                                                                        }
                                                                    }
                                                                } else {
                                                                    alert('Error: ' + data.message);
                                                                    console.error('Delete failed:', data.error);
                                                                }
                                                            })
                                                            .catch(error => {
                                                                alert('An error occurred during deletion.');
                                                                console.error('Fetch error:', error);
                                                            });
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                            <?php endif;
                            endif; ?>
                        </div>
                    </div>
                </div>


                <div class="card-footer bg-transparent d-flex justify-content-end pt-4">
                    <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-check-circle mr-2"></i><?= $isEditMode ? 'Update Patient' : 'Register Patient' ?></button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('upload_reports').addEventListener('change', function() {
        const count = this.files.length;
        const label = count === 1 ? this.files[0].name : `${count} files selected`;
        this.nextElementSibling.innerText = label;
    });
</script>


<script>
    // Must be inside the document.ready block to ensure it runs after jQuery is ready
    $(document).ready(function() {

        // 1. Element References (Using vanilla JS for reliability)
        const feeInput = document.getElementById('fee');
        const finalAmountInput = document.getElementById('final_amount');
        const doctorSelect = document.getElementById('referred_to_doctor_id');
        const discountPercentageInput = document.getElementById('discount_percentage');

        // 2. Financial Calculation Logic
        function calculateFinalAmount() {
            const fee = parseFloat(feeInput.value) || 0;
            const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
            let calculatedFinalAmount = fee;

            if (discountPercentage > 0) {
                const discountAmount = fee * (discountPercentage / 100);
                calculatedFinalAmount = fee - discountAmount;
            }
            if (calculatedFinalAmount < 0) {
                calculatedFinalAmount = 0;
            }

            finalAmountInput.value = calculatedFinalAmount.toFixed(2);
        }

        // 3. OPD Fee Fetching Logic
        function updateFeeFromDoctor() {
            // Get the currently selected <option> element
            const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];

            // Read the fee from the data attribute. Use '0.00' if no doctor is selected.
            const opdFee = selectedOption ? selectedOption.getAttribute('data-opd-fee') : '0.00';

            // Set the fee input value
            feeInput.value = parseFloat(opdFee || 0).toFixed(2);

            // Recalculate the Final Amount
            calculateFinalAmount();
        }

        // 4. Attach Event Listeners (The key fix: using jQuery's .on('change'))
        // This is necessary because Select2 overrides the native 'change' event.
        $(doctorSelect).on('change', updateFeeFromDoctor);

        // Listeners for financial fields
        feeInput.addEventListener('input', calculateFinalAmount);
        discountPercentageInput.addEventListener('input', calculateFinalAmount);

        // 5. Initial Run (Handles selected value on page load/edit mode)
        // Run this check to set the fee if a doctor is already selected.
        if (doctorSelect.value !== "") {
            // Use a short delay just in case the Select2 initialization is still finishing.
            setTimeout(updateFeeFromDoctor, 100);
        }
    });

    // Also include your file upload script here
    document.getElementById('upload_reports').addEventListener('change', function() {
        const count = this.files.length;
        const label = count === 1 ? this.files[0].name : `${count} files selected`;
        this.nextElementSibling.innerText = label;
    });
</script>


<?= $this->section('scripts') ?>

    <script>
        // All code runs once the page elements (DOM) and jQuery are fully ready.
        $(document).ready(function() {

            // 1. Element References 
            const feeInput = document.getElementById('fee');
            const finalAmountInput = document.getElementById('final_amount');
            const doctorSelect = document.getElementById('referred_to_doctor_id');
            const discountPercentageInput = document.getElementById('discount_percentage');
            const uploadReportsInput = document.getElementById('upload_reports'); // Reference for File Upload

            // 2. Financial Calculation Logic
            function calculateFinalAmount() {
                const fee = parseFloat(feeInput.value) || 0;
                const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
                let calculatedFinalAmount = fee;

                if (discountPercentage > 0) {
                    const discountAmount = fee * (discountPercentage / 100);
                    calculatedFinalAmount = fee - discountAmount;
                }
                if (calculatedFinalAmount < 0) {
                    calculatedFinalAmount = 0;
                }

                finalAmountInput.value = calculatedFinalAmount.toFixed(2);
            }

            // 3. OPD Fee Fetching Logic
            function updateFeeFromDoctor() {
                // Get the currently selected <option> element
                const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];

                // Read the fee from the data attribute.
                const opdFee = selectedOption ? selectedOption.getAttribute('data-opd-fee') : '0.00';

                // Set the fee input value
                feeInput.value = parseFloat(opdFee || 0).toFixed(2);

                // Recalculate the Final Amount
                calculateFinalAmount();
            }

            // 4. Attach Event Listeners 

            // A. Doctor Dropdown (Select2 compatible)
            $(doctorSelect).on('change', updateFeeFromDoctor);

            // B. Financial fields
            feeInput.addEventListener('input', calculateFinalAmount);
            discountPercentageInput.addEventListener('input', calculateFinalAmount);

            // C. File Upload Handler (Now correctly inside the ready block)
            if (uploadReportsInput) {
                uploadReportsInput.addEventListener('change', function() {
                    const count = this.files.length;
                    const label = count === 1 ? this.files[0].name : `${count} files selected`;
                    this.nextElementSibling.innerText = label;
                });
            }

            // 5. Initial Run (Handles selected value on page load/edit mode)
            // Run this check to set the fee if a doctor is already selected.
            if (doctorSelect.value !== "") {
                // Delay to ensure Select2 is fully ready
                setTimeout(updateFeeFromDoctor, 100);
            }
        });
    </script>

<?= $this->endSection() ?>


<?= $this->endSection() ?>