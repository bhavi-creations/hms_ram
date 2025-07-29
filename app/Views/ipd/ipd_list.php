<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of In-Patient Department Patients</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <table id="ipdPatientsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>IPD ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Ward</th>
                                <th>Bed No.</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients) && is_array($patients)) : ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr id="patient-row-<?= esc($patient['id']) ?>">
                                        <td><?= $sno++ ?></td>
                                        <td><?= esc($patient['ipd_id_code'] ?? 'N/A') ?></td>
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td><?= esc($patient['ward_name'] ?? 'Unassigned') ?></td>
                                        <td><?= esc($patient['bed_number'] ?? 'Unassigned') ?></td>
                                        <td>
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View Patient"><i class="fas fa-eye"></i></a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit Patient"><i class="fas fa-edit"></i></a>
                                            
                                            <button type="button" class="btn btn-primary btn-sm assign-ward-bed-btn"
                                                    data-patient-id="<?= esc($patient['id']) ?>"
                                                    data-patient-name="<?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?>"
                                                    data-admission-id="<?= esc($patient['admission_id'] ?? '') ?>"
                                                    data-ward-id="<?= esc($patient['ward_id'] ?? '') ?>"
                                                    data-bed-id="<?= esc($patient['bed_id'] ?? '') ?>"
                                                    data-notes="<?= esc($patient['admission_notes'] ?? '') ?>"
                                                    title="Assign Ward and Bed">
                                                <i class="fas fa-bed"></i> Assign Ward/Bed
                                            </button>

                                            <?php if ($patient['admission_status'] === 'Admitted' || $patient['admission_status'] === 'Waiting Assignment'): ?>
                                                <button type="button" class="btn btn-danger btn-sm remove-from-ipd-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Remove from IPD">
                                                    <i class="fas fa-undo"></i> Remove from IPD
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm discharge-patient-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Discharge Patient">
                                                    <i class="fas fa-sign-out-alt"></i> Discharged
                                                </button>
                                            <?php elseif ($patient['admission_status'] === 'Discharged'): ?>
                                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Patient Discharged">
                                                    <i class="fas fa-check"></i> Discharged
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Not an IPD Patient">
                                                    N/A
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No IPD patients found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS (already in main.php, so no need for individual includes here) -->
<!-- <script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script> -->
<!-- ... other DataTables plugins ... -->

<script>
    $(function () {
        $("#ipdPatientsTable").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        }).buttons().container().appendTo('#ipdPatientsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 and AJAX for "Remove from IPD"
        $(document).on('click', '.remove-from-ipd-btn', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).closest('tr').find('td:eq(2)').text();
            
            Swal.fire({
                title: 'Confirm Removal from IPD',
                text: `Are you sure you want to remove ${patientName} from IPD? They will revert to their original patient type.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Remove!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

                    $.ajax({
                        url: '<?= base_url('ipd/removeFromIPD') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { 
                            patient_id: patientId,
                            [csrfToken]: csrfHash
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Removed!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    response.message || 'An error occurred while removing the patient from IPD.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (removeFromIPD):", status, error, xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Could not remove patient from IPD. Server error.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // SweetAlert2 and AJAX for "Discharged"
        $(document).on('click', '.discharge-patient-btn', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).closest('tr').find('td:eq(2)').text();
            
            Swal.fire({
                title: 'Confirm Patient Discharge',
                text: `Are you sure you want to discharge ${patientName}? This action is usually final.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Discharge!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

                    $.ajax({
                        url: '<?= base_url('ipd/dischargePatient') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { 
                            patient_id: patientId,
                            [csrfToken]: csrfHash
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Discharged!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    response.message || 'An error occurred while discharging the patient.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (dischargePatient):", status, error, xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Could not discharge patient. Server error.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // SweetAlert2 for Assign Ward/Bed
        $(document).on('click', '.assign-ward-bed-btn', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).data('patient-name');
            let admissionId = $(this).data('admission-id');
            let currentWardId = $(this).data('ward-id');
            let currentBedId = $(this).data('bed-id');
            let notes = $(this).data('notes');

            console.log("Assign Ward/Bed button clicked.");
            console.log("Patient ID:", patientId);
            console.log("Current Ward ID:", currentWardId);
            console.log("Current Bed ID:", currentBedId);
            console.log("Current Admission ID:", admissionId);

            // Fetch all wards to populate the ward dropdown
            let allWards = [];
            $.ajax({
                url: '<?= base_url('wards/getWards') ?>',
                type: 'GET',
                dataType: 'json',
                async: false, // Keep synchronous for now to ensure wards are loaded before modal opens
                success: function(response) {
                    allWards = response;
                    console.log("Wards fetched successfully:", allWards);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching wards (wards/getWards):", status, error, xhr.responseText);
                    Swal.showValidationMessage('Failed to load wards. Please try again.');
                }
            });

            // If no wards are fetched, show an error and prevent modal from opening fully
            if (allWards.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wards Not Found',
                    text: 'No wards are available. Please add wards in the Wards Management section first.',
                });
                return; // Stop execution if no wards
            }

            // Build ward options
            let wardOptions = { '': 'Select Ward (Optional)' };
            allWards.forEach(ward => {
                wardOptions[ward.id] = ward.name;
            });

            Swal.fire({
                title: `Assign Ward/Bed for ${patientName}`,
                html:
                    `<input type="hidden" id="swal-patient-id" value="${patientId}">` +
                    `<input type="hidden" id="swal-admission-id" value="${admissionId}">` +
                    `<div class="form-group text-left">` +
                        `<label for="swal-ward">Ward:</label>` +
                        `<select id="swal-ward" class="form-control swal2-input">` +
                            Object.entries(wardOptions).map(([id, name]) =>
                                `<option value="${id}" ${id == currentWardId ? 'selected' : ''}>${name}</option>`
                            ).join('') +
                        `</select>` +
                    `</div>` +
                    `<div class="form-group text-left">` +
                        `<label for="swal-bed">Bed Number:</label>` +
                        `<select id="swal-bed" class="form-control swal2-input">` +
                            `<option value="">Select Bed (Optional)</option>` +
                        `</select>` +
                    `</div>` +
                    `<div class="form-group text-left">` +
                        `<label for="swal-notes">Notes:</label>` +
                        `<textarea id="swal-notes" class="form-control swal2-textarea" placeholder="Add any notes...">${notes}</textarea>` +
                    `</div>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Save Assignment',
                showLoaderOnConfirm: true,
                didOpen: () => {
                    const wardSelect = Swal.getPopup().querySelector('#swal-ward');
                    const bedSelect = Swal.getPopup().querySelector('#swal-bed');
                    
                    // Function to load beds based on selected ward
                    const loadBeds = (selectedWardId, selectedBedId) => {
                        bedSelect.innerHTML = '<option value="">Select Bed (Optional)</option>'; // Clear existing
                        if (selectedWardId) {
                            console.log("Fetching beds for Ward ID:", selectedWardId);
                            $.ajax({
                                url: '<?= base_url('ipd/getAvailableBedsByWard/') ?>' + selectedWardId,
                                type: 'GET',
                                dataType: 'json',
                                success: function(bedsResponse) {
                                    console.log("Beds fetched successfully:", bedsResponse);
                                    bedsResponse.forEach(bed => {
                                        const option = document.createElement('option');
                                        option.value = bed.id;
                                        option.textContent = bed.bed_number;
                                        if (bed.id == selectedBedId) {
                                            option.selected = true;
                                        }
                                        bedSelect.appendChild(option);
                                    });
                                    // If current bed is not available (e.g., occupied by this patient), add it back
                                    if (selectedBedId && !bedsResponse.some(bed => bed.id == selectedBedId)) {
                                        console.log("Current bed not in available list, fetching details for:", selectedBedId);
                                        $.ajax({
                                            url: '<?= base_url('beds/getBedDetails/') ?>' + selectedBedId,
                                            type: 'GET',
                                            dataType: 'json',
                                            success: function(bedDetails) {
                                                console.log("Bed details fetched:", bedDetails);
                                                if (bedDetails.success && bedDetails.bed) {
                                                    const option = document.createElement('option');
                                                    option.value = bedDetails.bed.id;
                                                    option.textContent = bedDetails.bed.bed_number + ' (Current)';
                                                    option.selected = true;
                                                    bedSelect.appendChild(option);
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error("Error fetching single bed details (beds/getBedDetails):", status, error, xhr.responseText);
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error fetching beds (ipd/getAvailableBedsByWard):", status, error, xhr.responseText);
                                    Swal.showValidationMessage('Failed to load beds for the selected ward.');
                                }
                            });
                        }
                    };

                    // Load initial beds if a ward is already selected
                    if (currentWardId) {
                        loadBeds(currentWardId, currentBedId);
                    }

                    // Event listener for ward change
                    wardSelect.addEventListener('change', (event) => {
                        loadBeds(event.target.value, null); // Clear selected bed when ward changes
                    });
                },
                preConfirm: () => {
                    const patientId = Swal.getPopup().querySelector('#swal-patient-id').value;
                    const admissionId = Swal.getPopup().querySelector('#swal-admission-id').value;
                    const wardId = Swal.getPopup().querySelector('#swal-ward').value;
                    const bedId = Swal.getPopup().querySelector('#swal-bed').value;
                    const notes = Swal.getPopup().querySelector('#swal-notes').value;

                    if (!patientId) {
                        Swal.showValidationMessage('Patient ID is missing.');
                        return false;
                    }
                    
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

                    // Simplified URL: no admissionId in path
                    const requestUrl = '<?= base_url('ipd/assignWardBed') ?>'; 
                    console.log("Attempting to submit assignment via AJAX POST.");
                    console.log("URL:", requestUrl);
                    console.log("Data:", {patient_id: patientId, admission_id: admissionId, ward_id: wardId, bed_id: bedId, notes: notes, [csrfToken]: csrfHash});
                    
                    return $.ajax({
                        url: requestUrl,
                        type: 'POST', // Explicitly setting type to POST
                        dataType: 'json',
                        data: {
                            patient_id: patientId,
                            admission_id: admissionId, // Pass admissionId in data
                            ward_id: wardId,
                            bed_id: bedId,
                            notes: notes,
                            [csrfToken]: csrfHash
                        },
                        beforeSend: function(xhr) {
                            // Log the request method right before sending
                            console.log("AJAX preConfirm beforeSend: Request Method is " + this.type);
                        }
                    })
                    .done(function(response) {
                        if (response.success) {
                            $('meta[name="csrf-hash"]').attr('content', response.csrfHash);
                            return response;
                        } else {
                            Swal.showValidationMessage(`Error: ${response.message}`);
                            return false;
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error (assignWardBed):", jqXHR.status, textStatus, errorThrown);
                        console.error("AJAX Error Response Text:", jqXHR.responseText);
                        let errorMessage = `Request failed: ${textStatus}`;
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = `Error: ${jqXHR.responseJSON.message}`;
                        } else if (errorThrown) {
                            errorMessage = `Error: ${errorThrown}`;
                        }
                        Swal.showValidationMessage(errorMessage);
                        return false;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Assignment Saved!',
                        text: result.value.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
