<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
// Helper function to render status badges
$renderStatusBadge = function ($status) {
    $status = strtolower($status);
    $class = 'badge bg-secondary';
    switch ($status) {
        case 'pending':
        case 'not started':
            $class = 'badge bg-warning';
            break;
        case 'in progress':
        case 'processing':
            $class = 'badge bg-info';
            break;
        case 'completed':
        case 'delivered':
            $class = 'badge bg-success';
            break;
        case 'cancelled':
            $class = 'badge bg-danger';
            break;
        default:
            $class = 'badge bg-secondary';
            break;
    }
    return '<span class="' . $class . '">' . esc($status) . '</span>';
};
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Test Results Entry</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Lab Results</li> 
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
                        <h3 class="card-title"><i class="fas fa-microscope mr-2"></i> Orders Awaiting Results</h3>
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

                        <table id="resultsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No.</th>
                                    <th>Order ID</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th style="width: 10%;">Phone Number</th>
                                    <th>Doctor</th>
                                    <th style="width: 12%;">Order Date</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; // Initialize the serial number counter ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td> 
                                        <td><?= esc($order['order_id_code']) ?></td>
                                        <td><?= esc($order['patient_id']) ?></td>
                                        <td><?= esc($order['patient_name']) ?></td>
                                        <td><?= esc($order['phone_number']) ?></td>
                                        <td><?= esc($order['doctor_name']) ?></td>
                                        <td><?= date('d M Y', strtotime(esc($order['order_date']))) ?></td>
                                        <td><?= $renderStatusBadge($order['status']) ?></td>
                                        <td>
                                            <a href="<?= base_url('laboratory/results/enter/' . $order['id']) ?>" class="btn btn-sm btn-primary" title="Enter Lab Results">
                                                <i class="fas fa-edit"></i> Result
                                            </a>
                                            <a href="<?= base_url('laboratory/reports/view/' . $order['id']) ?>" class="btn btn-sm btn-info" title="View Generated Report">
                                                <i class="fas fa-file-alt"></i> Report
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
        var table = $('#resultsTable').DataTable({
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
            // Default sort by Order Date column (index 6) in descending order
            "order": [
                [6, 'desc']
            ],
            "columnDefs": [
                { 
                    "orderable": false, 
                    "searchable": false, 
                    "targets": [0, 8] // Disable sorting/searching on S.No. (0) and Actions (8)
                }
            ]
        }).buttons().container().appendTo('#resultsTable_wrapper .col-md-6:eq(0)');

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