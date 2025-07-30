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

                        <table id="batchesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                <?php if (!empty($batches) && is_array($batches)): ?>
                                    <?php foreach ($batches as $batch): ?>
                                        <tr>
                                            <td><?= esc($batch['id']) ?></td>
                                            <td><?= esc($batch['batch_number']) ?></td>
                                            <td><?= esc($batch['supplier_name'] ?? 'N/A') ?></td>
                                            <td><?= esc(date('M d, Y', strtotime($batch['manufacture_date']))) ?></td>
                                            <td>
                                                <span class="badge <?= (strtotime($batch['expiry_date']) < time()) ? 'bg-danger' : (strtotime($batch['expiry_date']) < strtotime('+3 months') ? 'bg-warning' : 'bg-success') ?>">
                                                    <?= esc(date('M d, Y', strtotime($batch['expiry_date']))) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($batch['initial_stock']) ?></td>
                                            <td>
                                                <span class="badge <?= ($batch['current_stock'] <= ($medicine['reorder_level'] * ($batch['initial_stock'] / ($batches['total_batches_count'] > 0 ? $batches['total_batches_count'] : 1)))) ? 'bg-danger' : 'bg-success' ?>">
                                                    <?= esc($batch['current_stock']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc(number_format($batch['cost_price_per_unit'], 2)) ?></td>
                                            <td><?= esc(number_format($batch['selling_price_per_unit'], 2)) ?></td>
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
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No batches found for this medicine.</td>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $("#batchesTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#batchesTable_wrapper .col-md-6:eq(0)');

        // SweetAlert for delete confirmation
        $(document).on('click', '.delete-batch', function (e) {
            e.preventDefault();
            const batchId = $(this).data('id');
            
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
                    // Create a form dynamically to send a POST request for deletion
                    const form = $('<form>', {
                        'action': '<?= site_url('pharmacy/medicines/delete-batch/') ?>' + batchId,
                        'method': 'post',
                        'style': 'display:none;'
                    });
                    // Add CSRF token
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '<?= csrf_token() ?>',
                        'value': '<?= csrf_hash() ?>'
                    }));
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>