<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Schedule New Appointment</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('appointments') ?>">Appointments</a></li>
                    <li class="breadcrumb-item active">Schedule New</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Appointment Details</h3>
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
                        <ul>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number">Patient Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter patient mobile number" value="<?= old('phone_number') ?>" required>
                                <small class="form-text text-muted">Type mobile number to find associated patients.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patient_id">Patient Name <span class="text-danger">*</span></label>
                                <select class="form-control" id="patient_id" name="patient_id" required disabled>
                                    <option value="">-- Select Patient --</option>
                                    <!-- Options will be loaded dynamically via AJAX -->
                                </select>
                                <small class="form-text text-muted">Select patient from the filtered list.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="doctor_id">Doctor <span class="text-danger">*</span></label>
                        <select class="form-control" id="doctor_id" name="doctor_id" required>
                            <option value="">-- Select Doctor --</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= esc($doctor['id']) ?>" <?= old('doctor_id') == $doctor['id'] ? 'selected' : '' ?>>
                                    Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> (<?= esc($doctor['specialization']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= old('appointment_date', date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?= old('appointment_time', date('H:i')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason_for_visit">Reason for Visit</label>
                        <textarea class="form-control" id="reason_for_visit" name="reason_for_visit" rows="3" placeholder="Enter reason for visit"><?= old('reason_for_visit') ?></textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                        <a href="<?= base_url('appointments') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let typingTimer;
        const doneTypingInterval = 500; // milliseconds

        // Function to fetch patients by phone number
        function fetchPatientsByPhone() {
            const phoneNumber = $('#phone_number').val().trim();
            const patientDropdown = $('#patient_id');
            patientDropdown.empty().append('<option value="">-- Select Patient --</option>'); // Clear and add default

            if (phoneNumber.length >= 5) { // Minimum characters to start searching
                patientDropdown.prop('disabled', true); // Disable while loading
                patientDropdown.append('<option value="">Loading patients...</option>');

                $.ajax({
                    url: "<?= base_url('patients/getPatientsByPhone') ?>",
                    type: "GET",
                    data: { phone: phoneNumber },
                    dataType: "json",
                    success: function(response) {
                        patientDropdown.empty().append('<option value="">-- Select Patient --</option>'); // Clear again
                        if (response.length > 0) {
                            $.each(response, function(index, patient) {
                                patientDropdown.append($('<option>', {
                                    value: patient.id,
                                    text: patient.first_name + ' ' + patient.last_name + ' (' + patient.patient_id_code + ')'
                                }));
                            });
                            patientDropdown.prop('disabled', false); // Enable dropdown
                        } else {
                            patientDropdown.append('<option value="">No patients found for this number</option>');
                            patientDropdown.prop('disabled', true); // Keep disabled if no patients
                        }

                        // If there was an old selected patient, try to re-select it
                        const oldPatientId = "<?= old('patient_id') ?>";
                        if (oldPatientId) {
                            patientDropdown.val(oldPatientId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        patientDropdown.empty().append('<option value="">Error loading patients</option>');
                        patientDropdown.prop('disabled', true);
                    }
                });
            } else {
                patientDropdown.prop('disabled', true); // Disable if phone number is too short
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

        // Handle case where patient_id was previously selected (e.g., after validation error)
        // This will be handled inside fetchPatientsByPhone after options are loaded.
    });
</script>
<?= $this->endSection() ?>
