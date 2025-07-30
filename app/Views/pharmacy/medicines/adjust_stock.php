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
                        <h3 class="card-title">Stock Adjustment Details</h3>
                    </div>
                    <form action="<?= site_url('pharmacy/medicines/store-adjustment') ?>" method="post">
                        <?= csrf_field() ?>
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
                                <label for="medicine_id">Medicine <span class="text-danger">*</span></label>
                                <select class="form-control" id="medicine_id" name="medicine_id">
                                    <option value="">Select Medicine</option>
                                    <?php if (!empty($medicines)): ?>
                                        <?php foreach ($medicines as $med): ?>
                                            <option value="<?= esc($med['id']) ?>" <?= (old('medicine_id') == $med['id']) ? 'selected' : '' ?>>
                                                <?= esc($med['brand_name']) ?> (<?= esc($med['generic_name']) ?> - <?= esc($med['strength']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group" id="batch_selection_group" style="display: <?= old('medicine_id') ? 'block' : 'none' ?>;">
                                <label for="batch_id">Batch <span class="text-danger">*</span></label>
                                <select class="form-control" id="batch_id" name="batch_id">
                                    <option value="">Select Batch</option>
                                    <?php if (old('medicine_id') && !empty($batches_for_old_medicine)): ?>
                                        <?php foreach ($batches_for_old_medicine as $batch): ?>
                                            <option value="<?= esc($batch['id']) ?>" <?= (old('batch_id') == $batch['id']) ? 'selected' : '' ?>>
                                                <?= esc($batch['batch_number']) ?> (Stock: <?= esc($batch['current_stock']) ?> | Exp: <?= esc(date('M Y', strtotime($batch['expiry_date']))) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Please select a medicine first to load batches.</small>
                            </div>

                            <div class="form-group">
                                <label for="adjustment_type">Adjustment Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="adjustment_type" name="adjustment_type">
                                    <option value="">Select Type</option>
                                    <option value="increase" <?= (old('adjustment_type') == 'increase') ? 'selected' : '' ?>>Increase Stock</option>
                                    <option value="decrease" <?= (old('adjustment_type') == 'decrease') ? 'selected' : '' ?>>Decrease Stock</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                    value="<?= old('quantity') ?>" placeholder="Enter quantity to adjust">
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason for Adjustment <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" 
                                    placeholder="e.g., Damaged goods, Stock count discrepancy, Return from patient"><?= old('reason') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit Adjustment</button>
                            <a href="<?= site_url('pharmacy/medicines') ?>" class="btn btn-secondary">Cancel</a>
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
        // Function to load batches based on selected medicine
        function loadBatches(medicineId, selectedBatchId = null) {
            if (medicineId) {
                $('#batch_selection_group').show();
                $.ajax({
                    url: '<?= site_url('pharmacy/medicines/get-batches-by-medicine/') ?>' + medicineId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let batchSelect = $('#batch_id');
                        batchSelect.empty();
                        batchSelect.append($('<option>', {
                            value: '',
                            text : 'Select Batch'
                        }));
                        if (response.batches.length > 0) {
                            $.each(response.batches, function(index, batch) {
                                let optionText = `${batch.batch_number} (Stock: ${batch.current_stock} | Exp: ${moment(batch.expiry_date).format('MMM YYYY')})`;
                                batchSelect.append($('<option>', {
                                    value: batch.id,
                                    text : optionText,
                                    selected: (selectedBatchId == batch.id) // Pre-select if matches old input
                                }));
                            });
                        } else {
                            batchSelect.append($('<option>', {
                                value: '',
                                text: 'No active batches found'
                            }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading batches:", error);
                        $('#batch_id').empty().append($('<option>', { value: '', text: 'Error loading batches' }));
                    }
                });
            } else {
                $('#batch_selection_group').hide();
                $('#batch_id').empty().append($('<option>', { value: '', text: 'Select Batch' }));
            }
        }

        // Load batches if an old medicine_id is present (after validation error)
        const oldMedicineId = '<?= old('medicine_id') ?>';
        if (oldMedicineId) {
            loadBatches(oldMedicineId, '<?= old('batch_id') ?>');
        }

        // Event listener for medicine selection change
        $('#medicine_id').on('change', function() {
            loadBatches($(this).val());
        });
    });
</script>
<?= $this->endSection() ?>