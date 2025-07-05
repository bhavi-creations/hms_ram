<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title">All Registered Patients</h3>
                <div class="card-tools">
                    <a href="<?= base_url('patients/register') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Register New Patient
                    </a>
                </div>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> -->
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> -->
                    </div>
                <?php endif; ?>

                <!-- Filter Form -->
                <form id="filterForm" class="row mb-3">
                    <div class="col-md-6 d-flex">
                        <select class="form-control mr-2" id="filterField" name="filter_field" required>
                            <option value="">-- Select Field --</option>
                            <option value="patient_id_code">Primary ID</option>
                            <option value="patient_type">Type</option>
                            <option value="opd_id_code">OPD ID</option>
                            <option value="ipd_id_code">IPD ID</option>
                            <option value="gen_id_code">General ID</option>
                            <option value="cus_id_code">Casualty ID</option>
                            <option value="full_name">Full Name</option>
                            <option value="gender">Gender</option>
                            <option value="date_of_birth">DOB</option>
                            <option value="phone_number">Phone</option>
                            <option value="created_at">Registered On</option>
                        </select>

                        <input type="text" class="form-control" id="filterValue" name="filter_value" placeholder="Enter value..." required>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
                    </div>
                </form>


                <!-- Results Table Container -->
                <div id="patientResults">
                    <?= view('patients/partials/patient_table', ['patients' => $patients]) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let typingTimer;
        const doneTypingInterval = 300; // milliseconds

        function fetchResults() {
            const field = $('#filterField').val();
            const value = $('#filterValue').val();

            if (!field || !value) return;

            $.ajax({
                url: "<?= base_url('patients/filter') ?>",
                type: 'GET',
                data: {
                    field: field,
                    value: value
                },
                success: function(response) {
                    $('#patientResults').html(response);
                },
                error: function() {
                    console.error('Error filtering patients');
                }
            });
        }

        // Auto-filter on typing
        $('#filterValue').on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchResults, doneTypingInterval);
        });

        // Also run when filter field changes (in case someone changes the dropdown after typing)
        $('#filterField').on('change', function() {
            if ($('#filterValue').val().length > 0) {
                fetchResults();
            }
        });

        // Clear button
        $('#clearBtn').click(function() {
            $('#filterField').val('');
            $('#filterValue').val('');
            $.ajax({
                url: "<?= base_url('patients/filter') ?>",
                success: function(response) {
                    $('#patientResults').html(response);
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>

<?= $this->endSection() ?>