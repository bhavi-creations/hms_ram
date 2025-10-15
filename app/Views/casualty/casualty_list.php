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
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-list-alt text-secondary mr-2"></i> List of Casualty / ER Patients</h3>
                    <div class="card-tools">
                        </div>
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

                    <table id="casualtyPatientsTable" class="table table-striped table-hover table-bordered"> 
                        <thead>
                            <tr class="bg-light text-dark"> <th style="width: 50px;">S.No.</th>
                                <th>Casualty ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th class="text-center" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sn = 1; // Initialize Serial Number counter
                            if (!empty($patients) && is_array($patients)) : ?>
                                <?php foreach ($patients as $patient) : ?>
                                    <tr>
                                        <td><?= $sn++ ?>.</td> <td>
                                            <span class="badge bg-info p-2 font-weight-bold">
                                                <?= esc($patient['cus_id_code'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        
                                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                                        <td><?= esc($patient['gender']) ?></td>
                                        <td><?= esc($patient['phone_number']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-sm btn-outline-info mr-1" title="View Patient">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-sm btn-outline-warning mr-1" title="Edit Patient">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($patient['patient_type'] === 'IPD'): ?>
                                                <button class="btn btn-sm btn-secondary disabled" title="Already Admitted to IPD">
                                                    <i class="fas fa-check"></i> IPD Added
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-success admit-to-ipd-btn" data-patient-id="<?= $patient['id'] ?>" title="Admit to IPD">
                                                    <i class="fas fa-bed"></i> Add to IPD
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No Casualty patients found.</td>
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
    $(function() {
        // DataTables Initialization
        $("#casualtyPatientsTable").DataTable({
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
            
            // Button classes for a colored, representative look
            "buttons": [
                { extend: 'copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', className: 'btn btn-sm btn-danger' },
                { extend: 'print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', className: 'btn btn-sm btn-warning' }
            ]
        }).buttons().container().appendTo('#casualtyPatientsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for "Add to IPD" (AJAX logic remains the same)
        $(document).on('click', '.admit-to-ipd-btn', function() {
            const patientId = $(this).data('patient-id');
            // Index 2 is the patient Name (after S.No and ID)
            const patientName = $(this).closest('tr').find('td:eq(2)').text(); 
            const $clickedButton = $(this); 

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
                        data: {
                            patient_id: patientId
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>' 
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Admitted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    $clickedButton.html('<i class="fas fa-check"></i> IPD Added')
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
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error, xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Could not admit patient. Server error.',
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