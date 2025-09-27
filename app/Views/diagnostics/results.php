    <?= $this->extend('layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= esc($title) ?></h3>
            <!-- Link changed to view the main orders list instead of placing a new order -->
            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary float-right">View All Orders</a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (empty($orders)) : ?>
                <p>No diagnostic orders are currently ready for results entry or reporting.</p>
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
                                <td>
                                    <?php
                                    $badge = 'badge-secondary';
                                    if ($order['status'] === 'Completed') $badge = 'badge-success'; 
                                    if ($order['status'] === 'Processing') $badge = 'badge-warning';
                                    if ($order['status'] === 'Sample Collected') $badge = 'badge-info';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= esc($order['status']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-wrap">

                                        <!-- Enter Results Button (Now ALWAYS available for editing/review) -->
                                        <a href="<?= base_url('diagnostics/results/enter/' . $order['id']) ?>" class="btn btn-sm btn-success" title="Enter/Edit Results">
                                            <i class="fas fa-file-signature"></i> Enter Results
                                        </a>

                                        <!-- View Report Button (Available if Completed or Processing) -->
                                        <?php if ($order['status'] === 'Completed' || $order['status'] === 'Processing') : ?>
                                            <a href="<?= base_url('diagnostics/reports/view/' . $order['id']) ?>" class="btn btn-sm btn-info ml-1" title="View Report">
                                                <i class="fas fa-file-pdf"></i> View Report
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
    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
        // DataTables Initialization (Kept for responsive table features)
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
        });
    </script>
    <?= $this->endSection() ?>