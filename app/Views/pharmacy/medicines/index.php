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
                <li class="breadcrumb-item active"><?= esc($title) ?></li>
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
                        <h3 class="card-title">List of All Medicines</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/medicines/create') ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle mr-1"></i> Add New Medicine
                            </a>
                            <a href="<?= site_url('pharmacy/medicines/adjust-stock') ?>" class="btn btn-sm btn-info ml-2">
                                <i class="fas fa-boxes mr-1"></i> Adjust Stock
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

                        <table id="medicinesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Generic Name</th>
                                    <th>Brand Name</th>
                                    <th>Strength</th>
                                    <th>Form</th>
                                    <th>Category</th>
                                    <th>Manufacturer</th>
                                    <th>Total Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($medicines) && is_array($medicines)): ?>
                                    <?php foreach ($medicines as $medicine): ?>
                                        <tr>
                                            <td><?= esc($medicine['id']) ?></td>
                                            <td><?= esc($medicine['generic_name']) ?></td>
                                            <td><?= esc($medicine['brand_name']) ?></td>
                                            <td><?= esc($medicine['strength']) ?></td>
                                            <td><?= esc($medicine['dosage_form']) ?></td>
                                            <td><?= esc($medicine['category_name']) ?></td>
                                            <td><?= esc($medicine['manufacturer_name']) ?></td>
                                            <td>
                                                <span class="badge <?= ($medicine['total_stock'] <= $medicine['reorder_level']) ? 'bg-danger' : 'bg-success' ?>">
                                                    <?= esc($medicine['total_stock']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($medicine['reorder_level']) ?></td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Medicine Actions">
                                                    <a href="<?= site_url('pharmacy/medicines/batches/' . $medicine['id']) ?>" class="btn btn-info btn-sm" title="View Batches">
                                                        <i class="fas fa-boxes"></i>
                                                    </a>
                                                    <a href="<?= site_url('pharmacy/medicines/edit/' . $medicine['id']) ?>" class="btn btn-primary btn-sm" title="Edit Medicine">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-medicine" data-id="<?= esc($medicine['id']) ?>" title="Delete Medicine">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No medicines found.</td>
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
        $("#medicinesTable").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#medicinesTable_wrapper .col-md-6:eq(0)');

        // SweetAlert for delete confirmation
        $(document).on('click', '.delete-medicine', function (e) {
            e.preventDefault();
            const medicineId = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! Deleting a medicine will also remove all its associated batches.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically to send a POST request for deletion
                    const form = $('<form>', {
                        'action': '<?= site_url('pharmacy/medicines/delete/') ?>' + medicineId,
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