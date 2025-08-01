<?= $this->extend('layouts/main') ?> // Ensure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Purchases Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Purchases</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Order Transactions</h3>
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

                    <form class="form-inline mb-3" action="<?= site_url('pharmacy/reports/purchases') ?>" method="get">
                        <label for="start_date" class="mr-2">Start Date:</label>
                        <input type="date" class="form-control mr-2" id="start_date" name="start_date" value="<?= esc($startDate ?? date('Y-m-01')) ?>">

                        <label for="end_date" class="mr-2">End Date:</label>
                        <input type="date" class="form-control mr-2" id="end_date" name="end_date" value="<?= esc($endDate ?? date('Y-m-d')) ?>">

                        <label for="supplier_id" class="ml-3 mr-2">Supplier:</label>
                        <select class="form-control mr-2" id="supplier_id" name="supplier_id">
                            <option value="">All Suppliers</option>
                            <?php if (!empty($suppliers) && is_array($suppliers)) : ?>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option value="<?= esc($supplier['id']) ?>" <?= set_select('supplier_id', $supplier['id'], ($selectedSupplierId == $supplier['id'])) ?>><?= esc($supplier['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="purchasesReportTable">
                        <thead>
                            <tr>
                                <th>Purchase ID</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th>Total Amount</th>
                                <th>Ordered By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchases) && is_array($purchases)) : ?>
                                <?php
                                $totalPurchaseAmount = 0;
                                foreach ($purchases as $purchase) :
                                    $totalPurchaseAmount += $purchase['total_amount'];
                                ?>
                                    <tr>
                                        <td><?= esc($purchase['id']) ?></td>
                                        <td><?= esc($purchase['supplier_name']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($purchase['purchase_date']))) ?></td>
                                        <td><?= esc(number_format($purchase['total_amount'], 2)) ?></td>
                                        <td><?= esc($purchase['ordered_by_first_name'] . ' ' . $purchase['ordered_by_last_name']) ?></td>
                                        <td><?= esc($purchase['status']) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/purchases/view/' . $purchase['id']) ?>" class="btn btn-sm btn-info">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No purchase records found for the selected criteria.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total Purchases:</th>
                                <th><?= esc(number_format($totalPurchaseAmount ?? 0, 2)) ?></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        // Initialize DataTables if you're using it (recommended for lists)
        if ($.fn.DataTable) {
            $('#purchasesReportTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[2, 'desc']] // Order by Purchase Date descending
            });
        }
        // Initialize Select2 for the supplier dropdown
        if ($.fn.select2) {
            $('#supplier_id').select2({
                placeholder: 'Select Supplier',
                allowClear: true
            });
        }
    });
</script>
<?= $this->endSection() ?>