<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('doctors/add') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Doctor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($doctors) && is_array($doctors)): ?>
                        <table id="doctorsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Code</th>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>Department</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td><?= esc($doctor['doctor_id_code']) ?></td>
                                        <td><?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?></td>
                                        <td><?= esc($doctor['specialization']) ?></td>
                                        <td><?= esc($doctor['department_name'] ?? 'N/A') ?></td>
                                        <td><?= esc($doctor['phone_number']) ?></td> <!-- THIS WAS THE PROBLEM LINE, NOW FIXED -->
                                        <td><?= esc($doctor['email']) ?></td>
                                        <td>
                                            <span class="badge
                    <?php
                                    // Safely access 'status', defaulting to 'Unknown' if not set
                                    $doctorStatus = $doctor['status'] ?? 'Unknown';

                                    if ($doctorStatus == 'Active') echo 'bg-success';
                                    else if ($doctorStatus == 'On Leave') echo 'bg-warning';
                                    else if ($doctorStatus == 'Suspended' || $doctorStatus == 'Terminated') echo 'bg-danger';
                                    else echo 'bg-secondary'; // Default for 'Inactive', 'Unknown', or any other status
                    ?>">
                                                <?= esc($doctorStatus) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('doctors/view/' . $doctor['id']) ?>" class="btn btn-info btn-sm" title="View Details"><i class="fas fa-eye"></i></a>
                                            <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="<?= $doctor['id'] ?>" title="Delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No doctors found. Click "Add New Doctor" to get started.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $("#doctorsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#doctorsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert for Delete Confirmation
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const doctorId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically to send a POST request for deletion
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?= base_url('doctors/delete/') ?>' + doctorId; // Adjust if using PUT/DELETE

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '<?= csrf_token() ?>'; // Get CSRF token name
                    csrfInput.value = '<?= csrf_hash() ?>'; // Get CSRF token hash
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>