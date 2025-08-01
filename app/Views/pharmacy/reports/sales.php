<?= $this->extend('layouts/main') ?> // Ensure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Sales</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Transactions Overview</h3>
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

                    <form class="form-inline mb-3" action="<?= site_url('pharmacy/reports/sales') ?>" method="get">
                        <label for="start_date" class="mr-2">Start Date:</label>
                        <input type="date" class="form-control mr-2" id="start_date" name="start_date" value="<?= esc($startDate ?? date('Y-m-01')) ?>">

                        <label for="end_date" class="mr-2">End Date:</label>
                        <input type="date" class="form-control mr-2" id="end_date" name="end_date" value="<?= esc($endDate ?? date('Y-m-d')) ?>">

                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="salesReportTable">
                        <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Sale Date</th>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Sales Person</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sales) && is_array($sales)) : ?>
                                <?php
                                $totalSalesAmount = 0;
                                foreach ($sales as $sale) :
                                    $totalSalesAmount += $sale['total_amount'];
                                ?>
                                    <tr>
                                        <td><?= esc($sale['invoice_number']) ?></td>
                                        <td><?= esc(date('Y-m-d H:i', strtotime($sale['sale_date']))) ?></td>
                                        <td><?= esc($sale['customer_name'] ?? 'N/A') ?></td>
                                        <td><?= esc(number_format($sale['total_amount'], 2)) ?></td>
                                        <td><?= esc($sale['payment_method']) ?></td>
                                        <td><?= esc($sale['sales_person_first_name'] . ' ' . $sale['sales_person_last_name']) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/sales/invoice/' . $sale['id']) ?>" class="btn btn-sm btn-info">View Invoice</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No sales records found for the selected period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total Sales:</th>
                                <th><?= esc(number_format($totalSalesAmount ?? 0, 2)) ?></th>
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
            $('#salesReportTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[1, 'desc']] // Order by Sale Date descending
            });
        }
    });
</script>
<?= $this->endSection() ?>