<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Lab Orders</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Lab Orders</li> 
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
                        <h3 class="card-title"><i class="fas fa-flask mr-2"></i> All Lab Orders</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('laboratory/orders/new') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Place New Order
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <table id="ordersTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No.</th>
                                    <th>Order ID</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th style="width: 10%;">Patient Phone</th>
                                    <th>Doctor</th>
                                    <th>Ordered By</th>
                                    <th style="width: 12%;">Order Date</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1; ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $sno++ ?></td>
                                        <td><?= esc($order['order_id_code']) ?></td>
                                        <td><?= esc($order['patient_id_code']) ?></td>
                                        <td><?= esc($order['patient_name']) ?></td>
                                        <td><?= esc($order['phone_number']) ?></td>
                                        <td><?= esc($order['doctor_name']) ?></td>
                                        <td><?= esc($order['ordered_by_name']) ?></td>
                                        <td><?= date('d M Y', strtotime(esc($order['order_date']))) ?></td>
                                        <td>
                                            <?php
                                                $statusClass = '';
                                                switch (strtolower($order['status'])) {
                                                    case 'pending':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    case 'processing':
                                                        $statusClass = 'badge bg-info';
                                                        break;
                                                    case 'completed':
                                                    case 'delivered':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'badge bg-danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                        break;
                                                }
                                            ?>
                                            <span class="<?= $statusClass ?>"><?= esc($order['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('laboratory/view_order_page/' . $order['id']) ?>" class="btn btn-sm btn-info" title="View Order Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('laboratory/report/edit/' . $order['id']) ?>" class="btn btn-sm btn-primary" title="Edit Report">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-danger delete-order-btn" data-id="<?= $order['id'] ?>" title="Delete Order">
                                                <i class="fas fa-trash"></i>
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
        // Initialize DataTables with customized, icon-based buttons
        var table = $('#ordersTable').DataTable({
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
            // Default sort by Order Date column (index 7) in descending order
            "order": [
                [7, 'desc']
            ],
            "columnDefs": [
                { 
                    "orderable": false, 
                    "targets": [0, 9] // Disable sorting on S.No. (0) and Actions (9)
                },
                { 
                    "searchable": false, 
                    "targets": [0, 9] // Disable searching on S.No. (0) and Actions (9)
                }
            ]
        }).buttons().container().appendTo('#ordersTable_wrapper .col-md-6:eq(0)');

        // Custom function to re-number the S.No column after sort/search/paging
        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
        
        // SweetAlert2 for delete confirmation
        $('.delete-order-btn').on('click', function(e) {
            e.preventDefault();
            const orderId = $(this).data('id');
            const deleteUrl = '<?= base_url('laboratory/orders/delete/') ?>' + orderId;

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! All associated lab items and results will be lost.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                } else {
                     Toast.fire({
                        icon: 'info',
                        title: 'Deletion cancelled.'
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>