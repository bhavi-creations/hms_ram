<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales Bills</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                        <li class="breadcrumb-item active">Sales Bills</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">List of Sales Bills</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/sales') ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> New Sale</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/all') ?>" class="btn btn-sm <?= (!isset($currentType) || $currentType === 'all') ? 'btn-primary' : 'btn-outline-primary' ?>">All Bills</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/outside_sale') ?>" class="btn btn-sm <?= (isset($currentType) && $currentType === 'outside_sale') ? 'btn-primary' : 'btn-outline-primary' ?>">Out-Patients</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/in_hospital') ?>" class="btn btn-sm <?= (isset($currentType) && $currentType === 'in_hospital') ? 'btn-primary' : 'btn-outline-primary' ?>">In-Patients</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/patients') ?>" class="btn btn-sm <?= (isset($currentType) && $currentType === 'patients') ? 'btn-primary' : 'btn-outline-primary' ?>">Patients List</a>
                        </div>
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

                    <table class="table table-bordered table-striped" id="manageReturnsTable">
                        <?php if (isset($currentType) && $currentType === 'patients'): ?>
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>IPD ID</th>
                                    <th>Latest Bill Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone Number</th>
                                    <th>Grand Total</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bills) && is_array($bills)) : ?>
                                    <?php $s_no = 1; ?>
                                    <?php foreach ($bills as $bill) : ?>
                                        <?php
                                        if (($bill['due_amount'] ?? 0) <= 0) {
                                            $status_label = '<span class="badge badge-success">Paid</span>';
                                        } elseif (($bill['due_amount'] ?? 0) < ($bill['total_amount'] ?? 0)) {
                                            $status_label = '<span class="badge badge-warning">Partial</span>';
                                        } else {
                                            $status_label = '<span class="badge badge-danger">Pending</span>';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $s_no++ ?></td>
                                            <td><?= esc($bill['ipd_id_code'] ?? 'N/A') ?></td>
                                            <td data-order="<?= strtotime($bill['latest_bill_date'] ?? '') ?>"><?= esc(date('M d, Y', strtotime($bill['latest_bill_date'] ?? ''))) ?></td>
                                            <td><?= esc($bill['first_name'] . ' ' . $bill['last_name']) ?></td>
                                            <td><?= esc($bill['phone_number'] ?? 'N/A') ?></td>
                                            <td>₹ <?= number_format($bill['total_amount'], 2) ?></td>
                                            <td>₹ <?= number_format($bill['total_paid_amount'], 2) ?></td>
                                            <td>₹ <?= number_format($bill['due_amount'], 2) ?></td>
                                            <td><?= $status_label ?></td>
                                            <td>
                                                <a href="<?= site_url('pharmacy/sales/billsByPatient/' . $bill['id']) ?>" class="btn btn-info btn-sm btn_small">
                                                    View Bills
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No records found for this category.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        <?php elseif (!isset($currentType) || $currentType === 'all'): ?>
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Bill/Invoice No.</th>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone Number</th>
                                    <th>Grand Total</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bills) && is_array($bills)) : ?>
                                    <?php $s_no = 1; ?>
                                    <?php foreach ($bills as $bill) : ?>
                                        <tr>
                                            <td><?= $s_no++ ?></td>
                                            <td><?= esc($bill['invoice_number'] ?? $bill['bill_id'] ?? '') ?></td>
                                            <td data-order="<?= strtotime($bill['sale_date'] ?? $bill['bill_date'] ?? '') ?>"><?= esc(date('M d, Y, h:i A', strtotime($bill['sale_date'] ?? $bill['bill_date'] ?? ''))) ?></td>
                                            <td><?= esc($bill['outside_patient_name'] ?? ($bill['first_name'] . ' ' . $bill['last_name'])) ?></td>
                                            <td><?= esc($bill['outside_patient_phone'] ?? $bill['phone_number'] ?? 'N/A') ?></td>
                                            <td>₹ <?= number_format(($bill['total_amount'] ?? 0), 2) ?></td>
                                            <td><?= esc($bill['sale_type'] ?? 'Out-Patient') ?></td>
                                            <td>
                                                <a href="<?= site_url('pharmacy/sales/invoice/' . urlencode($bill['invoice_number'] ?? $bill['bill_id'])) ?>" class="btn btn-info btn-sm btn_small">
                                                    View Bill
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No records found for this category.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        <?php else: ?>
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Invoice No.</th>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone Number</th>
                                    <th>Grand Total</th>
                                    <?php if (isset($currentType) && $currentType !== 'in_hospital'): ?>
                                        <th>Net Amount</th>
                                    <?php endif; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bills) && is_array($bills)) : ?>
                                    <?php $s_no = 1; ?>
                                    <?php foreach ($bills as $bill) : ?>
                                        <tr>
                                            <td><?= $s_no++ ?></td>
                                            <td>
                                                <?= esc(isset($currentType) && $currentType === 'in_hospital' ? ($bill['bill_id'] ?? '') : ($bill['invoice_number'] ?? '')) ?>
                                            </td>
                                            <td data-order="<?= strtotime(isset($currentType) && $currentType === 'in_hospital' ? $bill['bill_date'] : $bill['sale_date']) ?>">
                                                <?= esc(date('M d, Y, h:i A', strtotime(isset($currentType) && $currentType === 'in_hospital' ? $bill['bill_date'] : $bill['sale_date']))) ?>
                                            </td>
                                            <td>
                                                <?= isset($currentType) && $currentType === 'in_hospital' ? esc($bill['first_name'] . ' ' . $bill['last_name']) : esc($bill['outside_patient_name'] ?? '') ?>
                                            </td>
                                            <td>
                                                <?= isset($currentType) && $currentType === 'in_hospital' ? esc($bill['phone_number'] ?? 'N/A') : esc($bill['outside_patient_phone'] ?? '') ?>
                                            </td>
                                            <td>₹ <?= number_format(($bill['total_amount'] ?? 0), 2) ?></td>
                                            <?php if (isset($currentType) && $currentType !== 'in_hospital'): ?>
                                                <td>₹ <?= number_format(($bill['net_amount'] ?? 0), 2) ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <a href="<?= site_url('pharmacy/sales/invoice/' . urlencode(isset($currentType) && $currentType === 'in_hospital' ? $bill['bill_id'] : $bill['invoice_number'])) ?>" class="btn btn-info btn-sm btn_small">
                                                    View Bill
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No records found for this category.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Only initialize DataTable if the table body contains at least one row.
        // This prevents the "unknown parameter" warning when the table is empty.
        if ($('#manageReturnsTable tbody tr').length > 0) {
            if ($.fn.DataTable.isDataTable('#manageReturnsTable')) {
                $('#manageReturnsTable').DataTable().destroy();
            }
             $('#manageReturnsTable').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: true,
                ordering: true,
                paging: true,
                info: true,
                order: [
                    [2, 'desc'] // Order by the 'Date' column, which is at index 2
                ]
            });
        }
    });
</script>
<?= $this->endSection() ?>
