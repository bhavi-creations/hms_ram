<?php
// C:\xampp\htdocs\hms_ram\app\Views\pharmacy\medicines\batches.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/medicines') ?>">Medicines</a></li>
                <li class="breadcrumb-item active"><?= esc($medicine['brand_name']) ?> Batches</li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Batches for: <?= esc($medicine['brand_name']) ?> (<?= esc($medicine['generic_name']) ?> - <?= esc($medicine['strength']) ?>)</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/medicines/add-batch/' . esc($medicine['id'])) ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-plus-circle mr-1"></i> Add New Batch
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

                        <?php if (!empty($batches) && is_array($batches)): ?>
                            <?php $batch_count = count($batches); ?>
                            <?php $s_no = 1; ?>
                            <table id="batchesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Batch Number</th>
                                        <th>Supplier</th>
                                        <th>Manufacture Date</th>
                                        <th>Expiry Date</th>
                                        <th>Initial Stock</th>
                                        <th>Current Stock</th>
                                        <th>Cost Price (per unit)</th>
                                        <th>Selling Price (per unit)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batches as $batch): ?>
                                        <tr>
                                            <td><?= $s_no++ ?></td>
                                            <td><?= esc($batch['batch_number']) ?></td>
                                            <td><?= esc($batch['supplier_name'] ?? 'N/A') ?></td>
                                            <td><?= esc(date('M d, Y', strtotime($batch['manufacturing_date']))) ?></td>
                                            <td>
                                                <span class="badge <?= (strtotime($batch['expiry_date']) < time()) ? 'bg-danger' : (strtotime($batch['expiry_date']) < strtotime('+3 months') ? 'bg-warning' : 'bg-success') ?>">
                                                    <?= esc(date('M d, Y', strtotime($batch['expiry_date']))) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($batch['initial_quantity']) ?></td>
                                            <td>
                                                <span class="badge <?= ($batch['current_stock'] <= ($medicine['reorder_level'] * ($batch['initial_quantity'] / ($batch_count > 0 ? $batch_count : 1)))) ? 'bg-danger' : 'bg-success' ?>">
                                                    <?= esc($batch['current_stock']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc(number_format($batch['purchase_price'], 2)) ?></td>
                                            <td><?= esc(number_format($batch['selling_price'], 2)) ?></td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Batch Actions">
                                                    <a href="<?= site_url('pharmacy/medicines/edit-batch/' . esc($batch['id'])) ?>" class="btn btn-primary btn-sm" title="Edit Batch">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-batch" data-id="<?= esc($batch['id']) ?>" title="Delete Batch">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <p>No batches found for this medicine.</p>
                                <a href="<?= site_url('pharmacy/medicines/add-batch/' . esc($medicine['id'])) ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus-circle mr-1"></i> Add New Batch
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Function to re-index the S.No column
        function reIndexTableRows() {
            const table = $('#batchesTable').DataTable();
            // Get all visible rows in the table
            const visibleRows = table.rows({
                page: 'current'
            }).nodes();

            // Loop through each visible row and update the S.No
            visibleRows.each((row, index) => {
                const rowElement = $(row);
                // Find the first td (the S.No column) and set its text to the new index
                rowElement.find('td:eq(0)').text(index + 1);
            });
        }

        // Only initialize DataTable if the table element exists
        if ($.fn.DataTable.isDataTable('#batchesTable')) {
            $('#batchesTable').DataTable().destroy();
        }

        <?php if (!empty($batches) && is_array($batches)) : ?>
            $("#batchesTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "drawCallback": function(settings) {
                    // Re-index rows after every draw event (e.g., sort, paginate)
                    reIndexTableRows();
                }
            }).buttons().container().appendTo('#batchesTable_wrapper .col-md-6:eq(0)');
            
            // Initial re-indexing on page load
            reIndexTableRows();
        <?php endif; ?>

        // SweetAlert for delete confirmation
        $(document).on('click', '.delete-batch', function(e) {
            e.preventDefault();
            const batchId = $(this).data('id');
            const rowElement = $(this).closest('tr'); // Get the table row element

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! Deleting a batch will permanently remove its stock.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get the CSRF token and hash from CodeIgniter's globals
                    const csrfTokenName = '<?= csrf_token() ?>';
                    const csrfHash = '<?= csrf_hash() ?>';

                    // Prepare the data to be sent
                    const postData = {
                        [csrfTokenName]: csrfHash,
                        'batchId': batchId
                    };

                    // Send an AJAX POST request
                    $.ajax({
                        url: '<?= site_url('pharmacy/medicines/delete-batch/') ?>' + batchId,
                        type: 'POST',
                        data: postData,
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'The batch has been successfully deleted.',
                                'success'
                            );
                            
                            // Get the DataTable instance
                            const batchesTable = $('#batchesTable').DataTable();

                            // Use the DataTables API to remove the row and re-draw the table
                            // This also handles the re-indexing of the S.No column automatically
                            batchesTable.row(rowElement).remove().draw(false);
                            
                            // Manually call the re-indexing function as a fallback
                            // to ensure the S.No is updated on a row-by-row basis
                            reIndexTableRows();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                            let errorMessage = 'An error occurred. Please try again.';
                            try {
                                const response = JSON.parse(jqXHR.responseText);
                                if (response.error) {
                                    errorMessage = response.error;
                                } else if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                // Ignore parsing errors
                            }
                            Swal.fire(
                                'Failed!',
                                errorMessage,
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
