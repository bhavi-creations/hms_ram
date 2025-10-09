<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($page_title ?? 'Departments') ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('departments/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Department
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <table id="departmentsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($departments as $department): ?>
                            <tr>
                                <td><?= $i++ ?>.</td>
                                <td><?= esc($department['name']) ?></td>
                                <td><?= esc(substr($department['description'], 0, 100)) . (strlen($department['description']) > 100 ? '...' : '') ?></td>
                                <td><?= date('Y-m-d H:i:s', strtotime($department['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('departments/edit/' . $department['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $department['id'] ?>" data-name="<?= esc($department['name']) ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // 1. Initialize Datatable
        $('#departmentsTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });

        // 2. SweetAlert2 Delete Confirmation & AJAX Request
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const departmentId = $(this).data('id');
            const departmentName = $(this).data('name');
            const row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete the department: " + departmentName,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // --- FIX: Get the dynamic CSRF token name and hash ---
                    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Gets the token NAME (e.g., csrf_hash_...)
                    const csrfHash = $('meta[name="csrf-hash"]').attr('content');  // Gets the token VALUE
                    
                    // Prepare the data payload required for a CI4 POST request
                    let postData = {
                        // Dynamically set the token name as the key with the hash as the value
                        [csrfToken]: csrfHash,
                        // Note: The '_method': 'DELETE' override is not strictly necessary 
                        // because your route is defined as a POST, but we'll remove the 
                        // incorrect "headers" section to simplify and fix the CSRF issue.
                    };


                    $.ajax({
                        url: '<?= base_url('departments/delete/') ?>' + departmentId,
                        type: 'POST', // Matches your route $routes->POST('delete/(:num)', ...)
                        dataType: 'json',
                        // REMOVED 'headers' block, as sending CSRF in data is sufficient for POST routes
                        data: postData, // Use the correctly built data payload
                        
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Remove the row from the DOM and redraw the DataTable
                                    $('#departmentsTable').DataTable().row(row).remove().draw(false);
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            // IMPROVED ERROR MESSAGE: Display the HTTP status code (e.g., 403, 500)
                            let errorMessage = 'An error occurred while deleting the department.';
                            if (xhr.status) {
                                errorMessage += ` (Status: ${xhr.status} ${xhr.statusText})`;
                            }

                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                            console.error("AJAX Error:", status, error, xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>