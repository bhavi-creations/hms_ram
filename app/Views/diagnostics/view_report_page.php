<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Diagnostic Report Details for Order #<?= esc($order['order_id_code']) ?></h3>
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
                            <p><strong>Order Date:</strong> <?= esc(date('Y-m-d H:i', strtotime($order['created_at']))) ?></p>
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

                    <!-- Diagnostic Test Results -->
                    <div class="mt-4">
                        <h4>Diagnostic Tests</h4>
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
                                                <!-- Assuming test_type is available directly from the join -->
                                                <td><?= esc($item['test_type']) ?></td> 
                                                <td><?= esc($item['result'] ?? 'N/A') ?></td>
                                                <td><?= esc($item['status']) ?></td>
                                                <td>
                                                    <?php if (!empty($item['files'])): ?>
                                                        <ul class="list-unstyled">
                                                            <?php foreach ($item['files'] as $file): ?>
                                                                <li>
                                                                    <!-- Link uses the established diagnostics/reports/file route -->
                                                                    <a href="<?= base_url('diagnostics/reports/file/' . $file['id']) ?>" target="_blank">
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
                        <h4>Order Remarks (Global)</h4>
                        <hr>
                        <p><?= esc($order['remarks']) ?? 'N/A' ?></p>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="#" onclick="window.history.back();" class="btn btn-secondary me-2">Back</a>
                    <a href="<?= base_url('diagnostics/results/enter/' . $order['id']) ?>" class="btn btn-warning">Edit Results</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
