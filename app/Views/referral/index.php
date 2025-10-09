<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header with Title and Add button -->
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="<?= base_url('referred-persons/create') ?>" class="btn btn-primary btn-flat">
                <i class="fas fa-plus-circle"></i> Add New Person
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">List of Referral Persons</h3>
        </div>
        <div class="card-body">
            <!-- DataTables Table -->
            <table id="referredPersonsTable" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact Info</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($persons) && is_array($persons)): ?>
                        <?php $serialNo = 1; ?>
                        <?php foreach ($persons as $person): ?>
                            <tr>
                                <td><?= $serialNo++ ?></td>
                                <td><?= esc($person['name']) ?></td>
                                <td><?= esc($person['type']) ?></td>
                                <td><?= esc($person['contact_info']) ?></td>
                                <td><?= esc(date('Y-m-d H:i', strtotime($person['created_at']))) ?></td>
                                <td class="text-center">
                                    <!-- Edit action button -->
                                    <a href="<?= site_url('referred-persons/edit/' . $person['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Delete button triggers SweetAlert confirmation -->
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $person['id'] ?>" data-name="<?= esc($person['name']) ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <!-- Hidden form for actual DELETE request (CI4 requires POST with _method=DELETE) -->
                                    <form id="delete-form-<?= $person['id'] ?>" action="<?= site_url('referred-persons/delete/' . $person['id']) ?>" method="post" style="display:none;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">No referred persons found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Execute after jQuery is loaded (which is guaranteed by main.php)
    $(function() {
        // --- DataTables Initialization ---
        // This targets the table ID and initializes the DataTables plugin
        $("#referredPersonsTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 5
                }, // Disable ordering on the Actions column
                {
                    "width": "120px",
                    "targets": 5
                } // Set a fixed width for the Actions column
            ]
        });

        // --- SweetAlert2 for Flash Messages (Success/Error) ---
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Operation Successful!',
                text: '<?= session()->getFlashdata('success') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Operation Failed!',
                text: '<?= session()->getFlashdata('error') ?>',
                showConfirmButton: true,
            });
        <?php endif; ?>

        // --- SweetAlert2 for Delete Confirmation ---
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const personId = $(this).data('id');
            const personName = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete " + personName + ". This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the hidden delete form which performs the deletion
                    $('#delete-form-' + personId).submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>