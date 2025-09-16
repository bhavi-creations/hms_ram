<?= $this->extend('layouts/main') ?>


<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Purchases by Supplier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
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
                    <h3 class="card-title">Suppliers Purchase Summary</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($suppliers)): ?>
                        <table id="purchaseSuppliersTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Supplier</th>
                                    <th>Total Amount (₹)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1;
                                foreach ($suppliers as $supplier): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td>
                                        <td><?= esc($supplier['supplier_name']) ?></td>
                                        <td><?= number_format($supplier['total_amount'], 2) ?></td>
                                        <td>
                                            <a href="<?= site_url('pharmacy/purchases/bySupplier/' . $supplier['supplier_id']) ?>" class="btn btn-sm btn-info">
                                                View Purchases
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">No purchase records found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
 
<script>
    $(document).ready(function() {
        $('#purchaseSuppliersTable').DataTable({
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