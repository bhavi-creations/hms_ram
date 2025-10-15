<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Schedule New Appointment <i class="fas fa-calendar-plus text-primary"></i></h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('appointments') ?>">Appointments</a></li>
                    <li class="breadcrumb-item active">Schedule New</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-lg rounded-lg">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-1"></i>
                    Appointment Registration Form
                </h3>
            </div>
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

                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading"><i class="icon fas fa-ban"></i> Validation Error!</h4>
                        <ul class="mb-0 pl-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?= form_open('appointments/store') ?>
                    <?= csrf_field() ?>

                    <fieldset class="mb-4 p-3 border border-secondary rounded-lg">
                        <legend class="w-auto px-2 h5 text-primary">Patient Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Patient Mobile Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter patient mobile number" value="<?= old('phone_number') ?>" required>
                                    </div>
                                    <small class="form-text text-muted">Type mobile number to find associated patients.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        </div>
                                        <select class="form-control select2" id="patient_id" name="patient_id" required disabled>
                                            <option value="">-- Select Patient --</option>
                                            </select>
                                    </div>
                                    <small class="form-text text-muted">Select patient from the filtered list.</small>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="mb-4 p-3 border border-secondary rounded-lg">
                        <legend class="w-auto px-2 h5 text-primary">Appointment Details</legend>
                        <div class="form-group">
                            <label for="doctor_id">Doctor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                                </div>
                                <select class="form-control select2" id="doctor_id" name="doctor_id" required>
                                    <option value="">-- Select Doctor --</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= esc($doctor['id']) ?>" <?= old('doctor_id') == $doctor['id'] ? 'selected' : '' ?>>
                                            Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> (<?= esc($doctor['specialization']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= old('appointment_date', date('Y-m-d')) ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?= old('appointment_time', date('H:i')) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason_for_visit">Reason for Visit</label>
                            <textarea class="form-control" id="reason_for_visit" name="reason_for_visit" rows="3" placeholder="Enter brief reason for visit or symptoms..."><?= old('reason_for_visit') ?></textarea>
                        </div>
                    </fieldset>

                    <div class="card-footer clearfix">
                        <div class="float-right">
                            <a href="<?= base_url('appointments') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Schedule Appointment</button>
                        </div>
                    </div>
                <?= form_close() ?>
                </div>
            </div>
        </div>
</section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 on the doctor dropdown for a premium look (assuming Select2 is available)
        $('#doctor_id').select2({
            theme: 'bootstrap4', // Assuming AdminLTE/Bootstrap theme for Select2
            placeholder: "-- Select Doctor --",
            allowClear: true
        });

        // Initialize Select2 on the patient dropdown, but keep it disabled initially
        $('#patient_id').select2({
            theme: 'bootstrap4',
            placeholder: "-- Select Patient --",
            allowClear: true
        }).prop('disabled', true);

        let typingTimer;
        const doneTypingInterval = 500; // milliseconds

        // Function to fetch patients by phone number
        function fetchPatientsByPhone() {
            const phoneNumber = $('#phone_number').val().trim();
            const patientDropdown = $('#patient_id');
            // Select2 requires a different way to update options
            
            // Clear existing options, keep the first one (placeholder)
            patientDropdown.find('option:gt(0)').remove();
            patientDropdown.val('').trigger('change'); // Reset selection and notify Select2
            
            if (phoneNumber.length >= 5) { // Minimum characters to start searching
                patientDropdown.prop('disabled', true).trigger('change'); // Disable while loading
                patientDropdown.append(new Option('Loading patients...', '')).trigger('change'); // Add loading message

                $.ajax({
                    url: "<?= base_url('patients/getPatientsByPhone') ?>",
                    type: "GET",
                    data: { phone: phoneNumber },
                    dataType: "json",
                    success: function(response) {
                        // Clear all dynamically added options
                        patientDropdown.find('option:gt(0)').remove();

                        if (response.length > 0) {
                            $.each(response, function(index, patient) {
                                patientDropdown.append(new Option(
                                    patient.first_name + ' ' + patient.last_name + ' (' + patient.patient_id_code + ')', 
                                    patient.id
                                ));
                            });
                            patientDropdown.prop('disabled', false).trigger('change'); // Enable dropdown
                        } else {
                            patientDropdown.append(new Option('No patients found for this number', '')).trigger('change');
                            patientDropdown.prop('disabled', true).trigger('change'); // Keep disabled if no patients
                        }

                        // If there was an old selected patient, try to re-select it
                        const oldPatientId = "<?= old('patient_id') ?>";
                        if (oldPatientId && patientDropdown.find(`option[value='${oldPatientId}']`).length) {
                            patientDropdown.val(oldPatientId).trigger('change');
                            patientDropdown.prop('disabled', false).trigger('change'); // Re-enable if an old patient is found
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        patientDropdown.find('option:gt(0)').remove();
                        patientDropdown.append(new Option('Error loading patients', '')).trigger('change');
                        patientDropdown.prop('disabled', true).trigger('change');
                    }
                });
            } else {
                patientDropdown.prop('disabled', true).trigger('change'); // Disable if phone number is too short
            }
        }

        // Event listener for phone number input
        $('#phone_number').on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchPatientsByPhone, doneTypingInterval);
        });

        // Initial load check if old input exists (e.g., after validation error)
        if ($('#phone_number').val().trim().length > 0) {
            fetchPatientsByPhone();
        }
    });
</script>
<?= $this->endSection() ?>