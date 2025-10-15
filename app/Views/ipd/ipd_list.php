<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?= $title ?></h1> 
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
            
            <div class="card card-outline card-light rounded-lg shadow-lg"> 
                
                <div class="card-header border-0"> <h3 class="card-title"><i class="fas fa-hospital-user text-secondary mr-2"></i> List of In-Patient Department Patients</h3>
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

                    <table id="ipdPatientsTable" class="table table-striped table-hover table-bordered"> 
                        <thead>
                            <tr class="bg-light text-dark"> <th style="width: 50px;">S.No</th>
                                <th>IPD ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Ward</th>
                                <th>Bed No.</th>
                                <th class="text-center" style="width: 280px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients) && is_array($patients)) : ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr id="patient-row-<?= esc($patient['id']) ?>">
                                        <td><?= $sno++ ?>.</td> <td>
                                            <span class="badge bg-info p-2 font-weight-bold">
                                                <?= esc($patient['ipd_id_code'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td><?= esc($patient['ward_name'] ?? 'Unassigned') ?></td>
                                        <td><?= esc($patient['bed_number'] ?? 'Unassigned') ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-outline-info btn-sm" title="View Patient"><i class="fas fa-eye"></i></a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-outline-warning btn-sm" title="Edit Patient"><i class="fas fa-edit"></i></a>
                                            
                                            <button type="button" class="btn btn-outline-primary btn-sm assign-ward-bed-btn"
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
                                                     <i class="fas fa-undo"></i> Remove  IPD
                                                 </button>
                                                 <button type="button" class="btn btn-success btn-sm discharge-patient-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Discharge Patient">
                                                     <i class="fas fa-sign-out-alt"></i> Discharge
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
                                    <td colspan="8" class="text-center text-muted">No IPD patients found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-transparent border-top border-light">
                    <p class="text-muted mb-0">List generated on <?= date('Y-m-d H:i:s') ?></p>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // DataTables Initialization with custom colored buttons
        $("#ipdPatientsTable").DataTable({
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
            ]
        }).buttons().container().appendTo('#ipdPatientsTable_wrapper .col-md-6:eq(0)');

        // --- Existing JavaScript Logic for Assign/Remove/Discharge (Preserved) ---
        // Note: You need to ensure the logic for 'assign-ward-bed-btn', 'remove-from-ipd-btn', and 'discharge-patient-btn' is available elsewhere in your scripts. 
        
        // Example structure for the Assign Ward/Bed Modal (Placeholder):
        $(document).on('click', '.assign-ward-bed-btn', function() {
            // Logic to open modal and populate forms using data-* attributes
            console.log('Assign Ward/Bed button clicked for patient ID:', $(this).data('patient-id'));
            // Example: $('#assignModal').modal('show');
        });
        
        // Example structure for Remove/Discharge (Placeholder):
        $(document).on('click', '.remove-from-ipd-btn, .discharge-patient-btn', function() {
            const patientId = $(this).data('patient-id');
            const action = $(this).hasClass('discharge-patient-btn') ? 'Discharge' : 'Remove';
            
            Swal.fire({
                title: `Confirm ${action}`,
                text: `Are you sure you want to ${action} this patient from IPD?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'Discharge' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX call for remove/discharge
                    // Example: $.ajax({...})
                    console.log(`${action} confirmed for patient ID: ${patientId}`);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>