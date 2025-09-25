<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lab Orders</h3>
        <a href="<?= base_url('laboratory/orders/new') ?>" class="btn btn-primary float-right">Place New Order</a>
    </div>
    <div class="card-body">
        <table id="ordersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Order ID</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Patient Phone</th>
                    <th>Doctor</th>
                    <th>Ordered By</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                        <td><?= esc($order['order_date']) ?></td>
                        <td><?= esc($order['status']) ?></td>
                        <td>
                            <a href="<?= base_url('laboratory/view_order_page/' . $order['id']) ?>" class="btn btn-sm btn-info">View</a>
                            <a href="<?= base_url('laboratory/report/edit/' . $order['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= base_url('laboratory/orders/delete/' . $order['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var table = $('#ordersTable').DataTable({
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
    });
</script>
<?= $this->endSection() ?>
