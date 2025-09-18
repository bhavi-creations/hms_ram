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
                        <h3 class="card-title">Filter Sales Data</h3>
                    </div>
                    <?= form_open('pharmacy/reports/sales/' . esc($currentType), ['method' => 'get']) ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate) ?>">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="<?= site_url('pharmacy/reports/sales/' . esc($currentType)) ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">List of Sales Bills</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/sales') ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> New Sale</a>
                            <a href="<?= site_url('pharmacy/reports/sales/outside_sale') ?>" class="btn btn-sm <?= ($currentType === 'outside_sale') ? 'btn-primary' : 'btn-outline-primary' ?>">Out-Patients</a>
                            <a href="<?= site_url('pharmacy/reports/sales/in_hospital') ?>" class="btn btn-sm <?= ($currentType === 'in_hospital') ? 'btn-primary' : 'btn-outline-primary' ?>">In-Patients</a>
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

                    <table class="table table-bordered table-striped" id="manageSalesTable">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Invoice No.</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Phone Number</th>
                                <th>Sales Person</th>
                                <th>Total Amount</th>
                                <?php if ($currentType === 'outside_sale') : ?>
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
                                        <td><?= esc($bill['invoice_number'] ?? $bill['bill_id']) ?></td>
                                        <td><?= esc(date('M d, Y, h:i A', strtotime($bill['sale_date'] ?? $bill['bill_date']))) ?></td>
                                        <td>
                                            <?php 
                                                // Display either the registered patient name or the outside patient name
                                                if ($currentType === 'in_hospital') {
                                                    echo esc($bill['first_name'] . ' ' . $bill['last_name']);
                                                } else {
                                                    echo esc($bill['outside_patient_name']);
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                if ($currentType === 'in_hospital') {
                                                    echo esc($bill['phone_number'] ?? 'N/A');
                                                } else {
                                                    echo esc($bill['outside_patient_phone'] ?? 'N/A');
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?= esc($bill['sales_person_first_name'] . ' ' . $bill['sales_person_last_name'] ?? 'N/A') ?>
                                        </td>
                                        <td><?= number_format(($bill['total_amount'] ?? 0), 2) ?></td>
                                        <?php if ($currentType === 'outside_sale') : ?>
                                            <td><?= number_format(($bill['net_amount'] ?? 0), 2) ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="<?= site_url('pharmacy/reports/viewInvoice/' . urlencode($bill['invoice_number'] ?? $bill['bill_id'])) ?>" class="btn btn-info btn-sm btn_small">
                                                View Bill
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">No records found for this category.</td>
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
<script>
    $(document).ready(function() {
        $('#manageSalesTable').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            order: [
                [1, 'desc']
            ] // Sort by date column
        });
    });
</script>
<?= $this->endSection() ?>