<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Enter Test Results</h3>
    </div>
    <div class="card-body">
        <table id="resultsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Order ID</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Phone Number</th>
                    <th>Doctor</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td></td>
                        <td><?= esc($order['order_id_code']) ?></td>
                        <td><?= esc($order['patient_id']) ?></td>
                        <td><?= esc($order['patient_name']) ?></td>
                        <td><?= esc($order['phone_number']) ?></td>
                        <td><?= esc($order['doctor_name']) ?></td>
                        <td><?= esc($order['order_date']) ?></td>
                        <td><?= esc($order['status']) ?></td>
                        <td>
                            <a href="<?= base_url('laboratory/results/enter/' . $order['id']) ?>" class="btn btn-sm btn-primary">Enter Result</a>
                            <a href="<?= base_url('laboratory/reports/view/' . $order['id']) ?>" class="btn btn-sm btn-info">View Report</a>
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
        var table = $('#resultsTable').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 }
            ],
            order: [[1, 'asc']]
        });

        table.on('order.dt search.dt', function() {
            table.column(0, { order: 'applied', search: 'applied' }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
<?= $this->endSection() ?>
