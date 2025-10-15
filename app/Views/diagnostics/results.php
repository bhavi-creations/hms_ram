<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
// Helper function to render status badges
$renderStatusBadge = function ($status) {
    $status = strtolower($status);
    $class = 'badge bg-secondary';
    switch ($status) {
        case 'sample collected':
            $class = 'badge bg-info';
            break;
        case 'processing':
            $class = 'badge bg-warning';
            break;
        case 'completed':
        case 'finalized':
            $class = 'badge bg-success';
            break;
        case 'cancelled':
            $class = 'badge bg-danger';
            break;
        default:
            $class = 'badge bg-secondary';
            break;
    }
    return '<span class="badge ' . $class . '">' . esc(ucwords($status)) . '</span>';
};
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title ?? 'Diagnostic Results Entry') ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Results Entry</li> 
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
                        <h3 class="card-title"><i class="fas fa-flask mr-2"></i> Orders for Results Entry & Reporting</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-list mr-1"></i> View All Orders
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($orders)) : ?>
                            <div class="alert alert-info text-center">
                                No diagnostic orders are currently ready for results entry or reporting.
                            </div>
                        <?php else : ?>
                            <table class="table table-bordered table-striped table-hover" id="diagnosticsResultsTable">
                                <thead>
                                    <tr>    
                                        <th style="width: 5%;">S.No.</th>
                                        <th>Order ID</th>
                                        <th>Patient ID</th>
                                        <th style="width: 25%;">Patient Name</th>
                                        <th>Doctor</th>
                                        <th style="width: 12%;">Order Date</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($orders as $order) : ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= esc($order['order_id_code']) ?></td>
                                            <td><?= esc($order['patient_id_code']) ?></td>
                                            <td><?= esc($order['patient_name']) ?></td>
                                            <td><?= esc($order['doctor_name']) ?></td>
                                            <td><?= date('d M Y', strtotime(esc($order['order_date']))) ?></td>
                                            <td>
                                                <?= $renderStatusBadge($order['status']) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <a href="<?= base_url('diagnostics/results/enter/' . $order['id']) ?>" class="btn btn-sm btn-success mr-1 mb-1" title="Enter/Edit Results">
                                                        <i class="fas fa-file-signature"></i> Results
                                                    </a>

                                                    <?php 
                                                        $statusLower = strtolower($order['status']);
                                                        if ($statusLower === 'completed' || $statusLower === 'processing') : 
                                                    ?>
                                                        <a href="<?= base_url('diagnostics/reports/view/' . $order['id']) ?>" class="btn btn-sm btn-info mb-1" title="View Generated Report">
                                                            <i class="fas fa-file-pdf"></i> Report
                                                        </a>
                                                    <?php endif; ?>
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
        // Initialize DataTables with customized, icon-based buttons
        var table = $('#diagnosticsResultsTable').DataTable({
            "responsive": true,
            "lengthChange": false,
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
            // Default sort by Order Date column (index 5) in descending order
            "order": [
                [5, 'desc']
            ],
            "columnDefs": [
                { 
                    "orderable": false, 
                    "searchable": false, 
                    "targets": [0, 7] // Disable sorting/searching on S.No. (0) and Actions (7)
                }
            ]
        }).buttons().container().appendTo('#diagnosticsResultsTable_wrapper .col-md-6:eq(0)');

        // Custom function to re-number the S.No column after sort/search/paging
        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
<?= $this->endSection() ?>