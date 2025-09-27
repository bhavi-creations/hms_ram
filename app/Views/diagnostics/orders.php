<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?></h3>
        <a href="<?= base_url('diagnostics/orders/new') ?>" class="btn btn-primary float-right">Place New Order</a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (empty($orders)) : ?>
            <p>No diagnostic orders found.</p>
        <?php else : ?>
            <table class="table table-bordered table-striped" id="diagnosticsOrdersTable">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Order ID</th>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                            <td><?= esc($order['order_date']) ?></td>
                            <td><?= esc($order['status']) ?></td>
                            <td>
                                <div class="d-flex align-items-center flex-wrap">
                                    <a href="<?= base_url('diagnostics/orders/view/' . $order['id']) ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?= base_url('diagnostics/orders/edit/' . $order['id']) ?>" class="btn btn-sm btn-primary ml-1" title="Edit Order">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('diagnostics/orders/delete/' . $order['id']) ?>" class="btn btn-sm btn-danger ml-1 delete-btn" data-order-id="<?= esc($order['order_id_code']) ?>" title="Delete Order">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var table = $('#diagnosticsOrdersTable').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                searchable: false,
                targets: 0
            }],
            order: [
                [1, 'asc']
            ]
        });

        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // NEW: Delete Confirmation Logic
        $('#diagnosticsOrdersTable').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var deleteUrl = $(this).attr('href');
            var orderIdCode = $(this).data('order-id');

            if (confirm('Are you sure you want to delete the order ' + orderIdCode + '? This action cannot be undone.')) {
                window.location.href = deleteUrl;
            }
        });
    });
</script>
<?= $this->endSection() ?>
