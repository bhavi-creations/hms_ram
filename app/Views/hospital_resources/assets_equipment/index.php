<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools mr-2"></i>All Assets & Equipment</h3>
                <div class="card-tools">
                    <a href="<?= base_url('assets/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add New Asset
                    </a>
                </div>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <table id="assetsTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">S.No</th>
                            <th style="width: 20%;">Name</th>
                            <th style="width: 15%;">Asset Tag</th>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 15%;">Location</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Purchase Date</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($assets)): ?>
                            <?php foreach ($assets as $index => $asset): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($asset['name']) ?></td>
                                    <td><?= esc($asset['asset_tag'] ?? 'N/A') ?></td>
                                    <td><?= esc($asset['category']) ?></td>
                                    <td><?= esc($asset['location'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge
                                            <?php
                                                // Using AdminLTE 3 utility classes (bg-*) for badge colors
                                                if ($asset['status'] == 'Operational') echo 'bg-success';
                                                else if ($asset['status'] == 'Under Maintenance') echo 'bg-warning text-dark'; // Use text-dark for visibility on light bg
                                                else if ($asset['status'] == 'Out of Service') echo 'bg-danger';
                                                else if ($asset['status'] == 'Disposed') echo 'bg-secondary';
                                                else echo 'bg-info text-dark'; // Use text-dark for visibility on light bg
                                            ?>
                                        "><?= esc($asset['status']) ?></span>
                                    </td>
                                    <td><?= esc($asset['purchase_date'] ? date('M d, Y', strtotime($asset['purchase_date'])) : 'N/A') ?></td>
                                    <td>
                                        <a href="<?= base_url('assets/edit/' . $asset['id']) ?>" class="btn btn-sm btn-info" title="Edit Asset">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('assets/delete/' . $asset['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this asset?')" title="Delete Asset">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">No assets found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        $("#assetsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // Custom buttons with icons and AdminLTE styling
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] } // Disable sorting on S.No (0) and Actions (7)
            ]
        }).buttons().container().appendTo('#assetsTable_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>