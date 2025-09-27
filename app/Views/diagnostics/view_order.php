<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?></h3>
        <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary float-right">Back to Orders</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Order ID</dt>
                    <dd class="col-sm-8"><?= esc($order['order_id_code']) ?></dd>

                    <dt class="col-sm-4">Patient ID</dt>
                    <dd class="col-sm-8"><?= esc($order['patient_id_code']) ?></dd>

                    <dt class="col-sm-4">Patient Name</dt>
                    <dd class="col-sm-8"><?= esc($order['patient_name']) ?></dd>

                    <dt class="col-sm-4">Doctor</dt>
                    <dd class="col-sm-8"><?= esc($order['doctor_name']) ?></dd>

                    <dt class="col-sm-4">Order Date</dt>
                    <dd class="col-sm-8"><?= esc($order['order_date']) ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><?= esc($order['status']) ?></dd>

                    <dt class="col-sm-4">Ordered By</dt>
                    <dd class="col-sm-8"><?= esc($order['created_by_name']) ?></dd>

                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8"><?= esc($order['created_at']) ?></dd>

                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8"><?= esc($order['updated_at']) ?></dd>
                </dl>
            </div>
        </div>

        <hr>

        <h4>Ordered Tests</h4>
        <?php if (empty($orderItems)) : ?>
            <p>No tests found for this order.</p>
        <?php else : ?>
            <?php $totalPrice = 0; ?>
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Test Name</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item) : ?>
                        <?php $totalPrice += (float)$item['price']; ?>
                        <tr>
                            <td><?= esc($item['test_name']) ?></td>
                            <td class="text-right"><?= esc(number_format($item['price'] ?? 0, 2)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row">
                <!-- Adjusted to use col-md-4 offset-md-8 for cleaner right alignment of the total -->
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-right">Total Charges:</th>
                            <td class="text-right font-weight-bold">
                                <?= esc(number_format($totalPrice, 2)) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($order['remarks'])): ?>
            <hr>
            <h4>Remarks</h4>
            <p class="border p-3 bg-light rounded"><?= nl2br(esc($order['remarks'])) ?></p>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
