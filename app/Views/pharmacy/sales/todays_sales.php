<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Today's Sales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                        <li class="breadcrumb-item active">Today's Sales</li>
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
                        <h3 class="card-title">List of Sales for Today</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/sales') ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> New Sale</a>
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
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Invoice/Bill No.</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Phone Number</th>
                                <th>Grand Total</th>
                                <th>Sales Person</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bills) && is_array($bills)) : ?>
                                <?php $s_no = 1; ?>
                                <?php foreach ($bills as $bill) : ?>
                                    <tr>
                                        <td><?= $s_no++ ?></td>
                                        <td><?= esc($bill['bill_id'] ?? 'N/A') ?></td>
                                        <td><?= esc(date('M d, Y, h:i A', strtotime($bill['date'] ?? ''))) ?></td>
                                        <td><?= esc($bill['patient_name'] ?? 'N/A') ?></td>
                                        <td><?= esc($bill['phone_number'] ?? 'N/A') ?></td>
                                        <td>₹ <?= number_format($bill['total_amount'] ?? 0, 2) ?></td>
                                        <td><?= esc($bill['user_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/sales/invoice/' . urlencode($bill['bill_id'])) ?>" class="btn btn-info btn-sm btn_small">
                                                View Bill
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No records found for today.</td>
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
                [2, 'desc']
            ]
        });
    });
</script> -->
<?= $this->endSection() ?>
