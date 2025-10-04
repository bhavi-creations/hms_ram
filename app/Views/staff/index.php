<?= $this->extend('App\Views\layouts\main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Staff</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Staff Directory</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('staff/register') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Register New Staff
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="staffTable" class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($staff)): ?>
                                        <?php $i = 1; ?>
                                        <?php foreach ($staff as $s): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= esc($s['staff_code']) ?></td>
                                                <td><?= esc($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                                <td><?= esc($s['role_name']) ?></td>
                                                <td><?= esc($s['department']) ?></td>
                                                <td><?= esc($s['phone']) ?></td>
                                                <td>
                                                    <span class="badge <?= $s['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('staff/view/' . $s['id']) ?>" class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('staff/edit/' . $s['id']) ?>" class="btn btn-warning btn-sm" title="Edit Staff">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('staff/delete/' . $s['id']) ?>" class="btn btn-danger btn-sm delete-btn" title="Delete Staff" data-id="<?= $s['id'] ?>" data-name="<?= esc($s['first_name'] . ' ' . $s['last_name']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No staff members have been registered yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#staffTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": [7] } // Disable sorting on the Actions column
            ]
        });

        // SweetAlert Confirmation for Delete
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete staff member: " + name + ". This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
