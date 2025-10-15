<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-weight: 700;"><i class="fas fa-user-injured mr-2 text-info"></i><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-light card-outline rounded-xl shadow-lg">

            <div class="card-header border-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title text-xl font-weight-bold text-secondary">
                        List of Out-Patient Department Patients
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('patients/register') ?>" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-user-plus mr-1"></i> Register New Patient
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-lg" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-lg" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <table id="opdPatientsTable" class="table table-striped table-hover table-bordered">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th style="width: 5%;">S.No.</th>
                            <th>OPD ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patients) && is_array($patients)) : ?>
                            <?php $s_no = 1; // Initialize Serial Number counter 
                            ?>
                            <?php foreach ($patients as $patient) : ?>
                                <tr>
                                    <td><?= $s_no++ ?>.</td>
                                    <td>
                                        <span class="badge bg-info p-2 font-weight-bold"><?= esc($patient['opd_id_code'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <h5 class="font-weight-bold mb-0"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></h5>
                                        <small class="text-muted"><?= esc($patient['patient_id_code'] ?? 'Primary ID N/A') ?></small>
                                    </td>
                                    <td><?= esc($patient['gender']) ?></td>
                                    <td><a href="tel:<?= esc($patient['phone_number']) ?>"><?= esc($patient['phone_number']) ?></a></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Patient Actions">
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-sm btn-outline-info" title="View Patient Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit Patient Record">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($patient['patient_type'] === 'IPD'): ?>
                                                <button class="btn btn-sm btn-secondary" disabled title="Patient is already admitted as IPD">
                                                    <i class="fas fa-check"></i> Admitted
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-success admit-to-ipd-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Admit to In-Patient Department">
                                                    <i class="fas fa-bed"></i> Admit to IPD
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted"> <i class="fas fa-user-slash fa-2x d-block mb-2"></i>
                                    No OPD patients found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        // Initialize DataTables
        const opdPatientsTable = $("#opdPatientsTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 5
                }, // Disable ordering on the Actions column (index 5)
                {
                    "orderable": false,
                    "targets": 0
                } // Disable ordering on the S.No. column (index 0)
            ],
            // 🆕 Custom DOM for cleaner layout
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

            // 🎨 UPDATED: Button classes for a colored, representative look
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ]
        });

        // Append the buttons to a cleaner location (above the table)
        opdPatientsTable.buttons().container().appendTo('#opdPatientsTable_wrapper .col-md-6:eq(0)');


        // SweetAlert2 and AJAX for "Add to IPD"
        $(document).on('click', '.admit-to-ipd-btn', function() {
            const patientId = $(this).data('patient-id');
            // Correct index for patient name is now 2
            const patientName = $(this).closest('tr').find('td:eq(2) h5').text();
            const $clickedButton = $(this);

            if (!patientId) {
                Swal.fire('Error!', 'Patient ID not found for admission.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirm Admission to IPD',
                html: `Are you sure you want to admit <strong class="text-primary">${patientName}</strong> to IPD? This action will change the patient type.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-bed"></i> Yes, Admit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('patients/admitToIPD') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            patient_id: patientId
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            // Include CSRF token
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Admitted!', response.message, 'success');

                                // Update the button visually
                                $clickedButton.html('<i class="fas fa-check"></i> Admitted')
                                    .prop('disabled', true)
                                    .removeClass('btn-success')
                                    .addClass('btn-secondary');

                            } else {
                                Swal.fire('Failed!', response.message || 'An error occurred while admitting the patient.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not admit patient due to a server error.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>