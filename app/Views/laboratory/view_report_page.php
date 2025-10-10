<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Lab Report Details for Order #<?= esc($order['id']) ?></h3>
                </div>
                <div class="card-body">
                    <!-- Patient and Doctor Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Patient Details</h4>
                            <hr>
                            <p><strong>Patient Name:</strong> <?= esc($order['patient_first_name'] . ' ' . $order['patient_last_name']) ?></p>
                            <p><strong>Patient ID:</strong> <?= esc($order['patient_id_code']) ?></p>
                            <p><strong>Phone Number:</strong> <?= esc($order['phone_number']) ?></p>
                            <p><strong>Order Date:</strong> <?= esc(date('Y-m-d H:i', strtotime($order['order_date']))) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Doctor Details</h4>
                            <hr>
                            <p><strong>Doctor Name:</strong> <?= esc($order['doctor_first_name'] . ' ' . $order['doctor_last_name']) ?></p>
                            <p><strong>Order Status:</strong>
                                <span class="badge 
                                    <?php if ($order['status'] == 'Completed') echo 'bg-success';
                                    elseif ($order['status'] == 'In Progress') echo 'bg-warning text-dark';
                                    else echo 'bg-secondary'; ?>">
                                    <?= esc($order['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Lab Test Results -->
                    <div class="mt-4">
                        <h4>Lab Tests</h4>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Test Type</th>
                                        <th>Result</th>
                                        <th>Status</th>
                                        <th>Files</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orderItems)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No tests found for this order.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td><?= esc($item['test_name']) ?></td>
                                                <td><?= esc($item['test_type_name']) ?></td>
                                                <td><?= esc($item['result'] ?? 'N/A') ?></td>
                                                <td><?= esc($item['status']) ?></td>
                                                <td>
                                                    <?php if (!empty($item['files'])): ?>
                                                        <ul class="list-unstyled">
                                                            <?php foreach ($item['files'] as $file): ?>
                                                                <li>
                                                                    <a href="<?= base_url('public/uploads/laboratory/' . $file['file_path']) ?>" target="_blank">
                                                                        <i class="fas fa-file-alt"></i> <?= esc($file['file_name']) ?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mt-4">
                        <h4>Remarks</h4>
                        <hr>
                        <p><?= esc($order['remarks']) ?? 'N/A' ?></p>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="#" onclick="window.history.back();" class="btn btn-secondary">Back to Results</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
