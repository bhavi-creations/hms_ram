<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of OPD Patients</h3>
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

                    <table id="opdPatientsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>OPD ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients) && is_array($patients)) : ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr>
                                        <td><?= esc($patient['opd_id_code'] ?? 'N/A') ?></td>
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td>
                                            <!-- View Patient Button (Icon Only) -->
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View Patient">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Edit Patient Button (Icon Only) -->
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit Patient">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($patient['patient_type'] === 'IPD'): ?>
                                                <!-- Patient Added to IPD Button (Icon + Text) -->
                                                <button class="btn btn-sm btn-secondary d-inline-flex align-items-center" disabled>
                                                    <i class="fas fa-check me-2"></i> Patient Added to IPD
                                                </button>
                                            <?php else: ?>
                                                <!-- Add to IPD Button (Icon + Text) -->
                                                <button type="button" class="btn btn-success btn-sm admit-to-ipd-btn d-inline-flex align-items-center" data-patient-id="<?= esc($patient['id']) ?>" title="Admit to IPD">
                                                    <i class="fas fa-bed me-2"></i> Add to IPD
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No OPD patients found.</td>
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
<script src="<?= base_url('public/plugins/datatables/datatables.min.js') ?>"></script>
<script>
    $(function () {
        // Initialize DataTable
        $("#opdPatientsTable").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#opdPatientsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for "Add to IPD" using Event Delegation
        // Attach the click listener to a static parent (like document or table_wrapper)
        // This ensures the listener works even if DataTables re-renders rows.
        $(document).on('click', '.admit-to-ipd-btn', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).closest('tr').find('td:eq(1)').text();
            const $clickedButton = $(this); // Store reference to the clicked button

            // Log patient ID to console for debugging
            console.log('Attempting to admit patient ID:', patientId, 'Name:', patientName);

            // Basic validation for patientId
            if (!patientId) {
                Swal.fire(
                    'Error!',
                    'Patient ID not found for admission.',
                    'error'
                );
                console.error('Patient ID is missing for the clicked button.');
                return; // Stop execution if patient ID is invalid
            }

            Swal.fire({
                title: 'Confirm Admission to IPD',
                text: `Are you sure you want to admit ${patientName} to IPD?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Admit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('patients/admitToIPD') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { patient_id: patientId },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>' 
                            
                             
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Admitted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Update the button directly without reloading the page
                                    $clickedButton.html('<i class="fas fa-check me-2"></i> Patient Added to IPD')
                                                    .prop('disabled', true)
                                                    .removeClass('btn-success')
                                                    .addClass('btn-secondary');
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    response.message || 'An error occurred while admitting the patient.',
                                    'error'
                                );
                                console.error("Server responded with failure:", response);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Log full XHR object for detailed debugging
                            console.error("AJAX Error:", status, error, xhr);
                            Swal.fire(
                                'Error!',
                                'Could not admit patient. Server error. Check console for details.',
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
