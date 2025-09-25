<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lab Order Details: <?= esc($order['order_id_code']) ?></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>**Patient Information**</h5>
                <p><strong>Patient ID:</strong> <?= esc($order['patient_id_code']) ?></p>
                <p><strong>Patient Name:</strong> <?= esc($order['patient_name']) ?></p>
                <p><strong>Phone Number:</strong> <?= esc($order['phone_number']) ?></p>
            </div>
            <div class="col-md-6">
                <h5>**Order Information**</h5>
                <p><strong>Order ID:</strong> <?= esc($order['order_id_code']) ?></p>
                <p><strong>Ordered By:</strong> <?= esc($order['ordered_by_name']) ?></p>
                <p><strong>Doctor:</strong> <?= esc($order['doctor_name']) ?></p>
                <p><strong>Order Date:</strong> <?= esc($order['order_date']) ?></p>
                <p><strong>Status:</strong> <?= esc($order['status']) ?></p>
            </div>
        </div>

        <hr>

        <h5>**Ordered Tests**</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalPrice = 0; ?>
                <?php foreach ($testItems as $item): ?>
                    <tr>
                        <td><?= esc($item['test_name']) ?></td>
                        <td><?= esc($item['price']) ?></td>
                    </tr>
                    <?php $totalPrice += $item['price']; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total Price</strong></td>
                    <td><strong><?= esc($totalPrice) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <?php if (!empty($order['remarks'])): ?>
            <h5>**Remarks**</h5>
            <p><?= esc($order['remarks']) ?></p>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>
<?= $this->endSection() ?>
            