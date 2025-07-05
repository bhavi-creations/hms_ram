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
                                <th>IPD ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients) && is_array($patients)) : ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr id="patient-row-<?= esc($patient['id']) ?>">
                                        <td><?= esc($patient['ipd_id_code'] ?? 'N/A') ?></td>
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td>
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View Patient"><i class="fas fa-eye"></i></a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit Patient"><i class="fas fa-edit"></i></a>
                                            
                                            <!-- Conditional buttons based on patient_type -->
                                            <?php if ($patient['patient_type'] === 'IPD'): ?>
                                                <button type="button" class="btn btn-danger btn-sm remove-from-ipd-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Remove from IPD">
                                                    <i class="fas fa-undo"></i> Remove from IPD
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm discharge-patient-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Discharge Patient">
                                                    <i class="fas fa-sign-out-alt"></i> Discharged
                                                </button>
                                            <?php elseif ($patient['patient_type'] === 'Discharged'): ?>
                                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Patient Discharged">
                                                    <i class="fas fa-check"></i> Discharged
                                                </button>
                                            <?php else: ?>
                                                <!-- This case should ideally not happen if only IPD patients are shown -->
                                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Not an IPD Patient">
                                                    N/A
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No IPD patients found.</td>
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
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#ipdPatientsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 and AJAX for "Remove from IPD"
        $(document).on('click', '.remove-from-ipd-btn', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).closest('tr').find('td:eq(1)').text();
            const $clickedButton = $(this); // Reference to the "Remove from IPD" button
            const $dischargeButton = $clickedButton.next('.discharge-patient-btn'); // Reference to the "Discharged" button

            Swal.fire({
                title: 'Confirm Removal from IPD',
                text: `Are you sure you want to remove ${patientName} from IPD? They will revert to their original patient type.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Red for removal
                cancelButtonColor: '#6c757d', // Grey for cancel
                confirmButtonText: 'Yes, Remove!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('ipd/removeFromIPD') ?>', // New route for removal
                        type: 'POST',
                        dataType: 'json',
                        data: { patient_id: patientId },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Removed!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Remove the row from the IPD table immediately
                                    // This assumes you want them gone from the IPD list after removal
                                    $clickedButton.closest('tr').remove();
                                    // No need to update button on original page via JS here,
                                    // as that page will fetch its data fresh on next visit.
                                    // The backend logic will ensure the correct button state on original page.
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
                            console.error("AJAX Error:", status, error, xhr.responseText);
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
            const patientName = $(this).closest('tr').find('td:eq(1)').text();
            const $clickedButton = $(this); // Reference to the "Discharged" button
            const $removeButton = $clickedButton.prev('.remove-from-ipd-btn'); // Reference to the "Remove from IPD" button

            Swal.fire({
                title: 'Confirm Patient Discharge',
                text: `Are you sure you want to discharge ${patientName}? This action is usually final.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Green for discharge
                cancelButtonColor: '#6c757d', // Grey for cancel
                confirmButtonText: 'Yes, Discharge!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('ipd/dischargePatient') ?>', // New route for discharge
                        type: 'POST',
                        dataType: 'json',
                        data: { patient_id: patientId },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Discharged!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Update buttons to show "Discharged" and disable them
                                    $removeButton.remove(); // Remove "Remove from IPD" button
                                    $clickedButton.html('<i class="fas fa-check"></i> Discharged')
                                                 .prop('disabled', true)
                                                 .removeClass('btn-success')
                                                 .addClass('btn-secondary');
                                    // Optionally, you might want to remove the row from the table
                                    // if discharged patients shouldn't be in the active IPD list.
                                    // $clickedButton.closest('tr').remove();
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
                            console.error("AJAX Error:", status, error, xhr.responseText);
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
    });
</script>
<?= $this->endSection() ?>