<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/medicines') ?>">Medicines</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/medicines/batches/' . esc($medicine['id'])) ?>"><?= esc($medicine['brand_name']) ?> Batches</a></li>
                <li class="breadcrumb-item active"><?= esc($title) ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Add New Batch for: <strong><?= esc($medicine['brand_name']) ?> (<?= esc($medicine['generic_name']) ?>)</strong></h3>
                    </div>
                    <form action="<?= site_url('pharmacy/medicines/store-batch') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="medicine_id" value="<?= esc($medicine['id']) ?>">
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= session()->getFlashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($validation)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach ($validation->getErrors() as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="batch_number">Batch Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="batch_number" name="batch_number" 
                                    value="<?= old('batch_number') ?>" placeholder="Enter batch number">
                            </div>
                            <div class="form-group">
                                <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                <select class="form-control" id="supplier_id" name="supplier_id">
                                    <option value="">Select Supplier</option>
                                    <?php if (!empty($suppliers)): ?>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= esc($supplier['id']) ?>" <?= (old('supplier_id') == $supplier['id']) ? 'selected' : '' ?>>
                                                <?= esc($supplier['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="initial_stock">Initial Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="initial_stock" name="initial_stock" 
                                    value="<?= old('initial_stock') ?>" placeholder="Enter initial stock quantity">
                            </div>
                            <div class="form-group">
                                <label for="cost_price_per_unit">Cost Price Per Unit <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="cost_price_per_unit" name="cost_price_per_unit" 
                                    value="<?= old('cost_price_per_unit') ?>" step="0.01" placeholder="Enter cost price">
                            </div>
                            <div class="form-group">
                                <label for="selling_price_per_unit">Selling Price Per Unit <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="selling_price_per_unit" name="selling_price_per_unit" 
                                    value="<?= old('selling_price_per_unit') ?>" step="0.01" placeholder="Enter selling price">
                            </div>
                            <div class="form-group">
                                <label for="manufacture_date">Manufacture Date <span class="text-danger">*</span></label>
                                <div class="input-group date" id="manufacture_date_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="manufacture_date" name="manufacture_date" 
                                        data-target="#manufacture_date_picker" value="<?= old('manufacture_date') ?>" />
                                    <div class="input-group-append" data-target="#manufacture_date_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                <div class="input-group date" id="expiry_date_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="expiry_date" name="expiry_date" 
                                        data-target="#expiry_date_picker" value="<?= old('expiry_date') ?>" />
                                    <div class="input-group-append" data-target="#expiry_date_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Add Batch</button>
                            <a href="<?= site_url('pharmacy/medicines/batches/' . esc($medicine['id'])) ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function () {
        // Init Tempus Dominus for Manufacture Date
        $('#manufacture_date_picker').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            }
        });

        // Init Tempus Dominus for Expiry Date
        $('#expiry_date_picker').datetimepicker({
            format: 'YYYY-MM-DD',
            minDate: moment().add(1, 'days'), // Expiry date must be in the future
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            }
        });

        // Ensure manufacture date is not after expiry date
        $("#manufacture_date_picker").on("change.datetimepicker", function (e) {
            $('#expiry_date_picker').datetimepicker('minDate', e.date);
        });

        // Ensure expiry date is not before manufacture date
        $("#expiry_date_picker").on("change.datetimepicker", function (e) {
            if (e.date) {
                 $('#manufacture_date_picker').datetimepicker('maxDate', e.date);
            } else {
                $('#manufacture_date_picker').datetimepicker('maxDate', false); // Clear maxDate if expiry is cleared
            }
        });
    });
</script>
<?= $this->endSection() ?>