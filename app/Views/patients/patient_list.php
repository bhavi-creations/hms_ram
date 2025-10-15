<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-weight: 700;"><?= $title ?></h1>
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
        
        <div class="card card-light card-outline rounded-xl shadow-lg mb-4 p-3">
            <div class="card-body p-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    
                    <div class="mb-3 mb-md-0 mr-3">
                        <a href="<?= base_url('patients/register') ?>" class="btn btn-primary btn-lg px-4 shadow-lg">
                            <i class="fas fa-user-plus mr-1"></i> Register New Patient
                        </a>
                    </div>
                    
                    <form id="filterForm" class="flex-grow-1 d-flex align-items-center" style="max-width: 70%;">
                        <div class="input-group input-group-lg">
                            
                            <select class="form-control select2" id="filterField" name="filter_field" style="width: 30%; min-width: 150px;" required data-placeholder="Filter By Field">
                                <option value="">-- Filter By --</option>
                                <option value="patient_id_code">Primary ID</option>
                                <option value="full_name">Full Name</option>
                                <option value="phone_number">Phone</option>
                                <option value="patient_type">Type</option>
                                <option value="opd_id_code">OPD ID</option>
                                <option value="ipd_id_code">IPD ID</option>
                                <option value="gender">Gender</option>
                                <option value="created_at">Registered On</option>
                            </select>

                            <input type="text" class="form-control" id="filterValue" name="filter_value" placeholder="Type to search value..." required>
                            
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-info shadow-sm" title="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearBtn" title="Clear Filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="card card-outline card-white rounded-xl shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title text-xl font-weight-bold text-secondary">Patient Records</h3>
            </div>
            
            <div class="card-body p-0">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-lg mx-3 mt-3" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-lg mx-3 mt-3" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div id="patientResults">
                    <?= view('patients/partials/patient_table', ['patients' => $patients]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for the filter dropdown
        $('#filterField').select2({
            theme: 'bootstrap4',
            placeholder: "-- Filter By Field --",
            allowClear: true 
        });
        
        // --- Filtering Logic ---
        let typingTimer;
        const doneTypingInterval = 400; // milliseconds

        function fetchResults() {
            const field = $('#filterField').val();
            const value = $('#filterValue').val();
            
            // Only search if field is selected AND value has content
            if (!field || value.length === 0) return;

            // Show loading spinner
            $('#patientResults').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2">Loading Patients...</p></div>');

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
                    $('#patientResults').html('<div class="alert alert-danger">Error fetching results. Please try again.</div>');
                }
            });
        }

        // Auto-filter on keyup (debounce)
        $('#filterValue').on('keyup', function() {
            clearTimeout(typingTimer);
            if ($('#filterField').val()) {
                typingTimer = setTimeout(fetchResults, doneTypingInterval);
            }
        });
        
        // Manual form submit
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            fetchResults();
        });


        // Run when filter field changes
        $('#filterField').on('change', function() {
            if ($('#filterValue').val().length > 0) {
                fetchResults();
            }
        });

        // Clear button functionality
        $('#clearBtn').click(function() {
            $('#filterField').val('').trigger('change'); // Clear select2
            $('#filterValue').val('');
            
            // Fetch all records
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