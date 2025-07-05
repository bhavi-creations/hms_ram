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
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of Out-Patient Department Patients</h3>
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
                                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View Patient"><i class="fas fa-eye"></i></a>
                                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit Patient"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-success btn-sm admit-to-ipd-btn" data-patient-id="<?= $patient['id'] ?>" title="Admit to IPD">
                                                <i class="fas fa-bed"></i> Add to IPD
                                            </button>
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
            </div></section>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>

<script>
    $(function () {
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

        // SweetAlert2 for "Add to IPD"
        $('.admit-to-ipd-btn').on('click', function() {
            const patientId = $(this).data('patient-id');
            const patientName = $(this).closest('tr').find('td:eq(1)').text(); // Get patient name from the table row

            Swal.fire({
                title: 'Confirm Admission to IPD',
                text: `Are you sure you want to admit ${patientName} to IPD?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Green
                cancelButtonColor: '#dc3545', // Red
                confirmButtonText: 'Yes, Admit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('opd/admitToIpd/') ?>' + patientId,
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Admitted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Reload the page to reflect the change
                                    window.location.reload(); 
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