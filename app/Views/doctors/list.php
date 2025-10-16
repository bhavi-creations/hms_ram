<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark" style="font-weight: 700;"><i class="fas fa-user-md mr-2 text-primary"></i><?= esc($title) ?></h1>
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
            <div class="row">
                <div class="col-12">
                    <div class="card card-light card-outline rounded-xl shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title text-xl font-weight-bold text-secondary">
                                <i class="fas fa-users-medical mr-2"></i> Registered Medical Staff
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('doctors/new') ?>" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Add New Doctor
                                </a>
                           
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show rounded-lg" role="alert">
                                    <?= session()->getFlashdata('success') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show rounded-lg" role="alert">
                                    <?= session()->getFlashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($doctors) && is_array($doctors)): ?>
                                <table id="doctorsTable" class="table table-striped table-hover table-bordered table-valign-middle">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th style="width: 100px;">ID Code</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Department</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th style="width: 150px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary p-2">
                                                        <strong><?= esc($doctor['doctor_id_code']) ?></strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <h5 class="font-weight-bold mb-0"><?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h5>
                                                </td>
                                                    <td><span class="badge bg-secondary p-1"><?= esc($doctor['specialization']) ?></span></td>
                                                
                                                <td><?= esc($doctor['department_name'] ?? 'N/A') ?></td>
                                                <td><a href="tel:<?= esc($doctor['phone_number']) ?>"><?= esc($doctor['phone_number']) ?></a></td>
                                                <td><a href="mailto:<?= esc($doctor['email']) ?>"><?= esc($doctor['email']) ?></a></td>
                                                <td>
                                                    <?php
                                                    $doctorStatus = $doctor['status'] ?? 'Unknown';
                                                    $badgeClass = 'bg-secondary';
                                                    if ($doctorStatus == 'Active') $badgeClass = 'bg-success';
                                                    else if ($doctorStatus == 'On Leave') $badgeClass = 'bg-warning text-dark';
                                                    else if ($doctorStatus == 'Suspended' || $doctorStatus == 'Terminated') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> p-2">
                                                        <?= esc($doctorStatus) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group" aria-label="Doctor Actions">
                                                        <a href="<?= base_url('doctors/view/' . $doctor['id']) ?>" class="btn btn-outline-info btn-sm" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="<?= $doctor['id'] ?>" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info rounded-lg text-center py-4">
                                    <i class="fas fa-user-times fa-3x d-block mb-2"></i>
                                    <h4 class="alert-heading">No Doctors Found</h4>
                                    <p>Click the **Add New Doctor** button to register a new medical staff member.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-top border-light">
                            <p class="text-muted mb-0">Manage and oversee all registered medical staff.</p>
                        </div>
                    </div>
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
        $("#doctorsTable").DataTable({
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
            "buttons": [{
                    extend: 'copy',
                    className: 'btn btn-sm btn-info'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-danger'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-primary'
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-sm btn-warning'
                }
            ],
            "columnDefs": [{
                    "responsivePriority": 1,
                    "targets": 1
                }, // Prioritize Name
                {
                    "responsivePriority": 2,
                    "targets": -1
                } // Prioritize Actions
            ]
        }).buttons().container().appendTo('#doctorsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert for Delete Confirmation
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const doctorId = $(this).data('id');
            const doctorName = $(this).closest('tr').find('h5').text(); // Get the doctor's name

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Doctor?',
                    html: `You are about to delete the record for **${doctorName}**. This action cannot be reversed!`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Red
                    cancelButtonColor: '#6c757d', // Gray
                    confirmButtonText: 'Yes, Delete Permanently!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Dynamically create a form for POST request deletion (to handle CSRF)
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= base_url('doctors/delete/') ?>' + doctorId;

                        // Add CSRF token inputs (assuming CodeIgniter 4 standard)
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '<?= csrf_token() ?>';
                        csrfInput.value = '<?= csrf_hash() ?>';
                        form.appendChild(csrfInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else {
                console.error("SweetAlert2 not loaded. Cannot show confirmation dialog.");
                // Fallback to simple window redirection if Swal is missing
                window.location.href = '<?= base_url('doctors/delete/') ?>' + doctorId;
            }
        });
    });
</script>
<?= $this->endSection() ?>