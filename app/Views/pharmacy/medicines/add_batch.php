<?= $this->extend('layouts/main') ?>

<?= $this->section('content_header') ?>
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
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Add New Batch for: <strong><?= esc($medicine['brand_name']) ?> (<?= esc($medicine['generic_name']) ?>)</strong></h3>
                    </div>
                    <form action="<?= site_url('pharmacy/medicines/store-batch') ?>" method="post" id="addBatchForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="medicine_id" value="<?= esc($medicine['id']) ?>">
                        <div class="card-body">
                            <!-- This block displays validation errors after a form submission -->
                            <?php if (session('validation')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <h5><i class="icon fas fa-ban"></i> Validation Failed!</h5>
                                    <ul>
                                        <?php foreach (session('validation')->getErrors() as $field => $error): ?>
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
                                <select class="form-control select2" id="supplier_id" name="supplier_id" style="width: 100%;">
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


                            <!-- Dynamic Calculation Section -->
                            <div class="form-group">
                                <label>Stock Quantity Calculation</label>
                                <div id="packaging-levels-container" class="mb-3">
                                    <?php
                                    $oldPackagingQuantities = old('packaging_unit_quantity');
                                    $oldPackagingNames = old('packaging_unit_name');

                                    if (!empty($oldPackagingQuantities)):
                                        foreach ($oldPackagingQuantities as $key => $quantity): ?>
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="packaging_unit_name[]" placeholder="e.g., 'Boxes per Carton'" list="unit-suggestions" value="<?= esc($oldPackagingNames[$key]) ?>">
                                                <input type="number" class="form-control packaging-input" name="packaging_unit_quantity[]" value="<?= esc($quantity) ?>" min="1" placeholder="Quantity">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-danger remove-level-btn"><i class="fas fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach;
                                    else: ?>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="packaging_unit_name[]" placeholder="e.g., 'Carton Boxes'" list="unit-suggestions">
                                            <input type="number" class="form-control packaging-input" name="packaging_unit_quantity[]" value="1" min="1" placeholder="Quantity">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger remove-level-btn"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="add-level-btn" class="btn btn-info btn-sm">
                                    <i class="fas fa-plus"></i> Add Packaging Level
                                </button>
                                <hr class="my-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">Total Stock Quantity</span>
                                    </div>
                                    <input type="hidden" id="initial_quantity" name="initial_quantity" value="<?= old('initial_quantity', 1) ?>">
                                    <input type="text" id="calculated-stock-display" class="form-control font-weight-bold" value="<?= old('initial_quantity', 1) ?>" readonly>
                                </div>
                            </div>
                            <datalist id="unit-suggestions">
                                <option value="Carton Boxes">
                                <option value="Boxes">
                                <option value="Sheets">
                                <option value="Tablets">
                                <option value="Bottles">
                                <option value="Vials">
                                <option value="Syringes">
                            </datalist>
                            <!-- End of Dynamic Calculation Section -->

                            <div class="form-group">
                                <label for="purchase_price">Purchase Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="purchase_price" name="purchase_price"
                                    value="<?= old('purchase_price') ?>" step="0.01" placeholder="Enter purchase price">
                            </div>
                            <div class="form-group">
                                <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="selling_price" name="selling_price"
                                    value="<?= old('selling_price') ?>" step="0.01" placeholder="Enter selling price">
                            </div>

                            <div class="form-group">
                                <label for="manufacturing_date">Manufacture Date <span class="text-danger">*</span></label>
                                <div class="input-group date" id="manufacturing_date_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="manufacturing_date" name="manufacturing_date"
                                        data-target="#manufacturing_date_picker" value="<?= old('manufacturing_date') ?>" required />
                                    <div class="input-group-append" data-target="#manufacturing_date_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                <div class="input-group date" id="expiry_date_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="expiry_date" name="expiry_date"
                                        data-target="#expiry_date_picker" value="<?= old('expiry_date') ?>" required />
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        console.log('add_batch.php script loaded. jQuery is ready.');

        // --- Date Picker Initialization ---
        // Initialize Tempus Dominus Datepickers
        $('#manufacturing_date_picker').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: moment() // Manufacture date cannot be in the future
        });

        $('#expiry_date_picker').datetimepicker({
            format: 'YYYY-MM-DD',
            minDate: moment().add(1, 'days') // Expiry date must be after today
        });

        // Use event listeners to ensure manufacture date is before expiry date
        $('#manufacturing_date_picker').on("change.datetimepicker", function(e) {
            $('#expiry_date_picker').datetimepicker('minDate', e.date);
        });

        $('#expiry_date_picker').on("change.datetimepicker", function(e) {
            $('#manufacturing_date_picker').datetimepicker('maxDate', e.date);
        });

        // Repopulate date pickers if old data exists
        const oldManufactureDate = '<?= old('manufacturing_date') ?>';
        const oldExpiryDate = '<?= old('expiry_date') ?>';

        if (oldManufactureDate) {
            $('#manufacturing_date_picker').datetimepicker('date', moment(oldManufactureDate));
        }

        if (oldExpiryDate) {
            $('#expiry_date_picker').datetimepicker('date', moment(oldExpiryDate));
        }

        // Init Select2 for the supplier dropdown
        $('#supplier_id').select2();

        // --- Dynamic Calculation Script ---
        const container = $('#packaging-levels-container');
        const addLevelBtn = $('#add-level-btn');
        const initialQuantityHiddenInput = $('#initial_quantity');
        const calculatedDisplay = $('#calculated-stock-display');

        function updateCalculation() {
            let total = 1;
            container.find('.packaging-input').each(function() {
                const value = parseInt($(this).val()) || 1;
                total *= value;
            });
            initialQuantityHiddenInput.val(total);
            calculatedDisplay.val(total);
        }

        function createNewLevel() {
            const newDiv = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="packaging_unit_name[]" placeholder="e.g., 'Boxes per Carton'" list="unit-suggestions">
                    <input type="number" class="form-control packaging-input" name="packaging_unit_quantity[]" value="1" min="1" placeholder="Quantity">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-level-btn"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `;
            container.append(newDiv);
            updateCalculation();
        }

        addLevelBtn.on('click', createNewLevel);

        container.on('click', '.remove-level-btn', function() {
            if (container.find('.input-group').length > 1) {
                $(this).closest('.input-group').remove();
                updateCalculation();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot remove last row',
                    text: 'You must have at least one packaging level.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

        container.on('input', '.packaging-input', updateCalculation);

        updateCalculation();

        // CRUCIAL FIX: Force a final calculation before form submission
        $('#addBatchForm').on('submit', function(event) {
            updateCalculation();
        });
    });
</script>
<?= $this->endSection() ?>