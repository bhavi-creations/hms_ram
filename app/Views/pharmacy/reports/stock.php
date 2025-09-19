<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Current Stock Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicine Stock Levels</h3>
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

                    <form class="form-inline mb-3" action="<?= site_url('pharmacy/reports/stock') ?>" method="get">
                        <label for="low_stock_threshold" class="mr-2">Low Stock Threshold:</label>
                        <input type="number" class="form-control mr-2" id="low_stock_threshold" name="low_stock_threshold" value="<?= esc($lowStockThreshold ?? 10) ?>" min="0">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>

                    <table class="table table-bordered table-striped" id="stockReportTable">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Medicine Name</th>
                                <th>Generic Name</th>
                                <th>Strength</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Total Stock</th>
                                <th>No. of Batches</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stockData) && is_array($stockData)) : ?>
                                <?php $s_no = 1; ?>
                                <?php foreach ($stockData as $item) :
                                    $statusClass = '';
                                    $statusText = 'In Stock';
                                    if ($item['total_stock'] === null || $item['total_stock'] == 0) {
                                        $statusClass = 'badge-danger';
                                        $statusText = 'Out of Stock';
                                    } elseif ($item['total_stock'] < $lowStockThreshold) {
                                        $statusClass = 'badge-warning';
                                        $statusText = 'Low Stock';
                                    } else {
                                        $statusClass = 'badge-success';
                                        $statusText = 'In Stock';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $s_no++ ?></td>
                                        <td><?= esc($item['brand_name']) ?></td>
                                        <td><?= esc($item['generic_name']) ?></td>
                                        <td><?= esc($item['strength']) ?></td>
                                        <td><?= esc($item['category_name']) ?></td>
                                        <td><?= esc($item['manufacturer_name']) ?></td>
                                        <td><?= esc($item['total_stock'] ?? 0) ?></td>
                                        <td><?= esc($item['num_batches'] ?? 0) ?></td>
                                        <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">No medicine stock data found.</td>
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
        // Initialize DataTables if you're using it
        if ($.fn.DataTable) {
            $('#stockReportTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [[5, 'asc']] // Order by Total Stock ascending
            });
        }
    });
</script>
<?= $this->endSection() ?>
