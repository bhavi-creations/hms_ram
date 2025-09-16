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
                            <a href="<?= site_url('pharmacy/sales/listBills/outside_sale') ?>" class="btn btn-sm <?= ($currentType === 'outside_sale') ? 'btn-primary' : 'btn-outline-primary' ?>">Out-Patients</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/in_hospital') ?>" class="btn btn-sm <?= ($currentType === 'in_hospital') ? 'btn-primary' : 'btn-outline-primary' ?>">In-Patients</a>
                            <a href="<?= site_url('pharmacy/sales/listBills/patients') ?>" class="btn btn-sm <?= ($currentType === 'patients') ? 'btn-primary' : 'btn-outline-primary' ?>">Patients List</a>
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

                    <table class="table table-bordered table-striped" id="manageReturnsTable" >
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <?php if ($currentType === 'patients'): ?>
                                    <th>IPD ID</th>
                                    <th>Latest Bill Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone Number</th>
                                    <th>Grand Total</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                <?php else: ?>
                                    <th>Invoice No.</th>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone Number</th>
                                    <th>Grand Total</th>
                                    <?php if ($currentType !== 'in_hospital'): ?>
                                        <th>Net Amount</th>
                                    <?php endif; ?>

                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bills) && is_array($bills)) : ?>
                                <?php $s_no = 1; ?>
                                <?php foreach ($bills as $bill) : ?>
                                    <?php if ($currentType === 'patients'): ?>
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
                                            <td><?= esc(date('M d, Y', strtotime($bill['latest_bill_date'] ?? ''))) ?></td>
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
                                    <?php else: ?>
                                        <tr>
                                            <td><?= $s_no++ ?></td>
                                            <td>
                                                <?= esc($currentType === 'in_hospital' ? $bill['bill_id'] : ($bill['invoice_number'] ?? '')) ?>
                                            </td>
                                            <td data-order="<?= strtotime($currentType === 'in_hospital' ? $bill['bill_date'] : $bill['sale_date']) ?>">
                                                <?= esc(date('M d, Y, h:i A', strtotime($currentType === 'in_hospital' ? $bill['bill_date'] : $bill['sale_date']))) ?>
                                            </td>
                                            <td>
                                                <?= $currentType === 'in_hospital' ? esc($bill['first_name'] . ' ' . $bill['last_name']) : esc($bill['outside_patient_name']) ?>
                                            </td>
                                            <td>
                                                <?= $currentType === 'in_hospital' ? esc($bill['phone_number'] ?? 'N/A') : esc($bill['outside_patient_phone'] ?? '') ?>
                                            </td>
                                            <td><?= number_format(($bill['total_amount'] ?? 0), 2) ?></td>
                                            <?php if ($currentType !== 'in_hospital'): ?>
                                                <td><?= number_format(($bill['net_amount'] ?? 0), 2) ?></td>
                                            <?php endif; ?>

                                            <td>
                                                <a href="<?= site_url('pharmacy/sales/invoice/' . urlencode($currentType === 'in_hospital' ? $bill['bill_id'] : $bill['invoice_number'])) ?>" class="btn btn-info btn-sm btn_small">
                                                    View Bill
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="<?= $currentType === 'patients' ? 10 : 8 ?>" class="text-center">No records found for this category.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- <script>
    $(function() {
       
        if ($.fn.DataTable) {
            $('#salesBillsTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "order": [
                    [$currentType === 'patients' ? 2 : 2, "desc"]  
                ],
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        }
    });
</script> -->



<script>
    $(document).ready(function() {
        $('#manageReturnsTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            order: [
                [1, 'asc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>