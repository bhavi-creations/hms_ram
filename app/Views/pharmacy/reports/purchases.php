<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Purchases</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Data Overview</h3>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('pharmacy/reports/purchases') ?>" method="get" class="form-inline mb-4">
                        <div class="form-group mr-2">
                            <label for="start_date" class="mr-1">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="form-group mr-2">
                            <label for="end_date" class="mr-1">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="form-group mr-2">
                            <label for="supplier_id" class="mr-1">Supplier:</label>
                            <select class="form-control" id="supplier_id" name="supplier_id">
                                <option value="">All Suppliers</option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option value="<?= esc($supplier['id']) ?>" <?= ($supplierId == $supplier['id']) ? 'selected' : '' ?>>
                                        <?= esc($supplier['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="purchaseReportTable">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Invoice No.</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th>Ordered By</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchases) && is_array($purchases)) : ?>
                                <?php $s_no = 1; ?>
                                <?php foreach ($purchases as $purchase) : ?>
                                    <tr>
                                        <td><?= $s_no++ ?></td>
                                        <td><?= esc($purchase['invoice_number']) ?></td>
                                        <td><?= esc($purchase['supplier_name']) ?></td>
                                        <td><?= esc(date('Y-m-d', strtotime($purchase['purchase_date']))) ?></td>
                                        <td><?= esc($purchase['ordered_by_first_name']) . ' ' . esc($purchase['ordered_by_last_name']) ?></td>
                                        <td><?= esc(number_format($purchase['total_amount'], 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No purchase data found for the selected period.</td>
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
    $(function () {
        if ($.fn.DataTable) {
            $('#purchaseReportTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[3, 'desc']] // Order by Purchase Date descending
            });
        }
    });
</script>
<?= $this->endSection() ?>