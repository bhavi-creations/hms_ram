<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lab Reports</h3>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="<?= base_url('laboratory/reports') ?>" method="get" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Patient ID, Name, or Phone" value="<?= esc($searchTerm ?? '') ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <?php if (isset($searchTerm) && $searchTerm): ?>
                        <a href="<?= base_url('laboratory/reports') ?>" class="btn btn-secondary ms-2">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Order ID</th>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Phone Number</th>
                        <th>Doctor Name</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($labOrders)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No lab reports found.</td>
                        </tr>
                    <?php else: ?>
                        <?php $sno = 1; ?>
                        <?php foreach ($labOrders as $order): ?>
                            <tr>
                                <td><?= $sno++ ?></td>
                                <td><?= esc($order['order_id_code']) ?></td>
                                <td><?= esc($order['patient_id']) ?></td>
                                <td><?= esc($order['patient_name']) ?></td>
                                <td><?= esc($order['phone_number']) ?></td>
                                <td><?= esc($order['doctor_name']) ?></td>
                                <td><?= esc(date('Y-m-d', strtotime($order['order_date']))) ?></td>
                                <td><?= esc($order['status']) ?></td>
                                <td class="action-btns">
                                    <a href="<?= base_url('laboratory/report/' . $order['order_id']) ?>" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this lab report and all its associated files? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Delete Button Handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                confirmDeleteBtn.href = '<?= base_url('laboratory/delete_report/') ?>' + orderId;
                deleteModal.show();
            });
        });
    });
</script>
<?= $this->endSection() ?>