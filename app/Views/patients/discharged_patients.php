<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark" style="font-weight: 700;"><i class="fas fa-sign-out-alt mr-2 text-success"></i><?= esc($title) ?></h1> 
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card card-light card-outline rounded-xl shadow-lg"> 
                
                <div class="card-header border-0"> 
                    <h3 class="card-title text-xl font-weight-bold text-secondary"><i class="fas fa-history mr-2"></i> Discharged Patient History</h3>
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

                    <table id="dischargedPatientsTable" class="table table-striped table-hover table-bordered table-valign-middle">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 50px;">S.No</th>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Discharge Date</th>
                                <th>Status</th>
                                <th style="width: 200px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients)) : ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr>
                                        <td><?= $sno++ ?>.</td>
                                        <td>
                                            <span class="badge bg-primary p-2">
                                                <strong><?= esc($patient['patient_id_code']) ?></strong>
                                            </span>
                                        </td>
                                        <td><h5 class="font-weight-bold mb-0"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></h5></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><a href="tel:<?= esc($patient['phone_number']) ?>"><?= esc($patient['phone_number']) ?></a></td>
                                        <td>
                                            <span class="badge bg-secondary p-1">
                                                <?php
                                                // Assuming 'updated_at' often reflects the discharge time
                                                echo date('Y-m-d H:i', strtotime($patient['updated_at']));
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success p-2">Discharged</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Discharge Actions">
                                                <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-outline-info btn-sm" title="View Detailed Record">
                                                    <i class="fas fa-file-invoice"></i> Record
                                                </a>
                                                <button type="button" class="btn btn-outline-primary btn-sm re-admit-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Re-Admit Patient">
                                                    <i class="fas fa-ambulance"></i> Re-Admit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted"> <i class="fas fa-history fa-2x d-block mb-2"></i>
                                        No discharged patient records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top border-light">
                    <p class="text-muted mb-0">Records last updated on <?= date('Y-m-d H:i:s') ?></p>
                </div>
            </div>
            </div>
        </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // DataTables Initialization with custom colored buttons and premium DOM
        $("#dischargedPatientsTable").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            
            // Custom DOM to arrange search and length filters cleanly
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            
            // Color-coded button classes for a professional look
            "buttons": [
                { extend: 'copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', className: 'btn btn-sm btn-danger' },
                { extend: 'print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', className: 'btn btn-sm btn-warning' }
            ],
            "columnDefs": [ 
                { "orderable": false, "targets": 0 }, // Disable sorting on S.No
                { "responsivePriority": 1, "targets": 2 }, // Prioritize Name
                { "responsivePriority": 2, "targets": -1 } // Prioritize Actions
            ]
        }).buttons().container().appendTo('#dischargedPatientsTable_wrapper .col-md-6:eq(0)');
        
        
        // --- Custom Logic for Re-Admit (Using SweetAlert2 for Confirmation) ---
        $(document).on('click', '.re-admit-btn', function() {
            const patientId = $(this).data('patient-id');
            // Ensure Swal is loaded for this functionality
            if (typeof Swal !== 'undefined') {
                 Swal.fire({
                    title: 'Confirm Re-Admission',
                    text: 'Are you sure you want to re-admit this patient? This will initiate a new admission record.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff', // Bootstrap primary color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Re-Admit!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the re-admission form/controller action
                        window.location.href = '<?= base_url('patients/reAdmit') ?>/' + patientId;
                    }
                });
            } else {
                 console.error("SweetAlert2 not loaded. Cannot show confirmation dialog.");
                 // Fallback to simple redirect if Swal is missing
                 window.location.href = '<?= base_url('patients/reAdmit') ?>/' + patientId;
            }
        });
    });
</script>
<?= $this->endSection() ?>