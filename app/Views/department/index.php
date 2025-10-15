<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title ?? 'Departments List') ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Departments</li> 
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline rounded-lg shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sitemap mr-2"></i> List of Departments</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('departments/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Add New Department
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($departments)) : ?>
                            <div class="alert alert-info text-center">
                                No departments found. Click "Add New Department" to get started.
                            </div>
                        <?php else : ?>
                            <table id="departmentsTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">S.No.</th>
                                        <th style="width: 25%;">Name</th>
                                        <th style="width: 40%;">Description</th>
                                        <th style="width: 15%;">Created At</th>
                                        <th style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    <?php foreach ($departments as $department): ?>
                                        <tr>
                                            <td><?= $i++ ?>.</td>
                                            <td><?= esc($department['name']) ?></td>
                                            <td><?= esc(substr($department['description'], 0, 100)) . (strlen($department['description']) > 100 ? '...' : '') ?></td>
                                            <td><?= date('d M Y H:i', strtotime($department['created_at'])) ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a href="<?= base_url('departments/edit/' . $department['id']) ?>" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-btn ml-1" data-id="<?= $department['id'] ?>" data-name="<?= esc($department['name']) ?>" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // NOTE: Ensure your main layout file includes the CSRF token meta tags:
        // <meta name="csrf-token" content="<?= csrf_token() ?>">
        // <meta name="csrf-hash" content="<?= csrf_hash() ?>">
        
        // --- 1. Initialize Datatable with export buttons and custom S.No. logic ---
        var table = $('#departmentsTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "dom": 'Bfrtip', // Enable buttons
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            // Default sort by Created At column (index 3) in descending order
            "order": [
                [3, 'desc']
            ],
            "columnDefs": [
                { 
                    "orderable": false, 
                    "searchable": false, 
                    "targets": [0, 4] // Disable sorting/searching on S.No. (0) and Actions (4)
                }
            ]
        }).buttons().container().appendTo('#departmentsTable_wrapper .col-md-6:eq(0)');
        
        // Custom function to re-number the S.No column after sort/search/paging
        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
        
        
        // --- 2. SweetAlert2 Delete Confirmation & AJAX Request for CI4 CSRF ---
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const departmentId = $(this).data('id');
            const departmentName = $(this).data('name');
            const row = $(this).closest('tr');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete the department: " + departmentName + ". This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Retrieve CSRF token details from the global meta tags or hidden input
                    // We must send the token name and value in the POST data for CodeIgniter to validate it.
                    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_token() ?>'; 
                    const csrfHash = $('meta[name="csrf-hash"]').attr('content') || '<?= csrf_hash() ?>'; 
                    
                    let postData = {
                        // Dynamically set the token name as the key with the hash as the value
                        [csrfToken]: csrfHash, 
                        // Note: If your controller uses the DELETE method via method spoofing, 
                        // you would add: '_method': 'DELETE', but the current PHP code suggests a POST route is used.
                    };

                    $.ajax({
                        url: '<?= base_url('departments/delete/') ?>' + departmentId,
                        type: 'POST', 
                        dataType: 'json',
                        data: postData, 
                        
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    // Remove the row from the DataTable without full page refresh
                                    table.row(row).remove().draw(false);
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Deletion failed due to a server error.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'An AJAX error occurred while deleting the department.';
                            if (xhr.status === 403) {
                                errorMessage = 'Forbidden: CSRF token is invalid or expired. Please refresh the page.';
                            } else if (xhr.status) {
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