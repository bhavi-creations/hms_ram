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
                <li class="breadcrumb-item active"><?= esc($title) ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">New Sales Transaction</h3>
                    </div>
                    <form action="<?= site_url('pharmacy/sales/process-sale') ?>" method="post" id="posForm">
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

                            <?php if (isset($errors)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Validation Error!</h5>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prescription_type">Prescription Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="prescription_type" name="prescription_type">
                                            <option value="">Select Type</option>
                                            <option value="in_hospital" <?= (old('prescription_type') == 'in_hospital') ? 'selected' : '' ?>>In-Hospital Prescription</option>
                                            <option value="outside_sale" <?= (old('prescription_type') == 'outside_sale') ? 'selected' : '' ?>>Outside Sale</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="in_hospital_fields" style="display: <?= (old('prescription_type') == 'in_hospital') ? 'block' : 'none' ?>;">
                                        <div class="form-group">
                                            <label for="patient_id_code">Patient ID Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="patient_id_code" name="patient_id_code" 
                                                value="<?= old('patient_id_code') ?>" placeholder="Enter Patient ID Code (e.g., PNT-001)">
                                        </div>
                                        <div class="form-group">
                                            <label for="doctor_id">Doctor (Optional)</label>
                                            <select class="form-control select2" id="doctor_id" name="doctor_id" style="width: 100%;">
                                                <option value="">Select Doctor</option>
                                                </select>
                                        </div>
                                    </div>
                                    <div id="outside_sale_fields" style="display: <?= (old('prescription_type') == 'outside_sale') ? 'block' : 'none' ?>;">
                                        <div class="form-group">
                                            <label for="outside_patient_name">Patient Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="outside_patient_name" name="outside_patient_name" 
                                                value="<?= old('outside_patient_name') ?>" placeholder="Enter patient name">
                                        </div>
                                        <div class="form-group">
                                            <label for="outside_patient_phone">Patient Phone (Optional)</label>
                                            <input type="text" class="form-control" id="outside_patient_phone" name="outside_patient_phone" 
                                                value="<?= old('outside_patient_phone') ?>" placeholder="Enter patient phone">
                                        </div>
                                        <div class="form-group">
                                            <label for="outside_patient_address">Patient Address (Optional)</label>
                                            <textarea class="form-control" id="outside_patient_address" name="outside_patient_address" 
                                                rows="2" placeholder="Enter patient address"><?= old('outside_patient_address') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <h4>Medicine Items</h4>
                            <div id="medicine-items-container">
                                <?php 
                                // Re-populate items if there are validation errors
                                $old_items = old('items');
                                if (!empty($old_items)): 
                                    foreach ($old_items as $key => $item): 
                                        $item_id = $key; // Use key as a temporary item ID
                                ?>
                                    <div class="row medicine-item border border-secondary rounded p-3 mb-2" data-item-id="<?= $item_id ?>">
                                        <div class="col-md-11 row">
                                            <div class="form-group col-md-4">
                                                <label>Medicine <span class="text-danger">*</span></label>
                                                <select class="form-control medicine-select" name="items[<?= $item_id ?>][medicine_id]">
                                                    <option value="">Select Medicine</option>
                                                    <?php foreach ($medicines as $med): ?>
                                                        <option value="<?= esc($med['id']) ?>" 
                                                            <?= (old("items.{$item_id}.medicine_id") == $med['id']) ? 'selected' : '' ?>
                                                            data-unit-price="<?= esc($med['selling_price_per_unit']) ?>">
                                                            <?= esc($med['brand_name']) ?> (<?= esc($med['generic_name']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Batch <span class="text-danger">*</span></label>
                                                <select class="form-control batch-select" name="items[<?= $item_id ?>][batch_id]">
                                                    <option value="">Select Batch</option>
                                                    <?php 
                                                    // This will be tricky to pre-populate correctly with old input
                                                    // A separate AJAX call for each old item might be needed or pass all batches
                                                    // For now, it will be empty and rely on user re-selection or JS to fetch.
                                                    // Consider an AJAX endpoint for specific medicine's batches
                                                    ?>
                                                </select>
                                                <small class="form-text text-muted batch-stock-info"></small>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control item-quantity" name="items[<?= $item_id ?>][quantity]" 
                                                    value="<?= old("items.{$item_id}.quantity") ?>" min="1">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Unit Price</label>
                                                <input type="number" class="form-control item-unit-price" name="items[<?= $item_id ?>][unit_selling_price]" 
                                                    value="<?= old("items.{$item_id}.unit_selling_price") ?>" step="0.01" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Discount/Item</label>
                                                <input type="number" class="form-control item-discount-per-item" name="items[<?= $item_id ?>][discount_per_item]" 
                                                    value="<?= old("items.{$item_id}.discount_per_item", 0) ?>" step="0.01">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Sub Total</label>
                                                <input type="number" class="form-control item-sub-total" value="0.00" step="0.01" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                                        </div>
                                    </div>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm mb-3" id="add-medicine-item">Add Medicine Item</button>
                            <hr>

                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="form-group row">
                                        <label for="total_amount_display" class="col-sm-6 col-form-label">Total Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="total_amount_display" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="total_discount" class="col-sm-6 col-form-label">Total Sale Discount (Optional):</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" id="total_discount" name="total_discount" 
                                                value="<?= old('total_discount', 0) ?>" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="net_amount_display" class="col-sm-6 col-form-label">Net Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="net_amount_display" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="">Select Method</option>
                                            <option value="Cash" <?= (old('payment_method') == 'Cash') ? 'selected' : '' ?>>Cash</option>
                                            <option value="Card" <?= (old('payment_method') == 'Card') ? 'selected' : '' ?>>Card</option>
                                            <option value="UPI" <?= (old('payment_method') == 'UPI') ? 'selected' : '' ?>>UPI</option>
                                            <option value="Insurance" <?= (old('payment_method') == 'Insurance') ? 'selected' : '' ?>>Insurance</option>
                                            <option value="Other" <?= (old('payment_method') == 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes">Notes (Optional)</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                            placeholder="Any specific notes for this sale"><?= old('notes') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Process Sale</button>
                            <a href="<?= site_url('pharmacy/dashboard') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/adminlte/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/moment/moment.min.js') ?>"></script>

<script>
    var itemCounter = <?= !empty($old_items) ? count($old_items) : 0 ?>; // Keep track of item count for unique names

    $(function () {
        // Initialize Select2 for doctor selection
        $('#doctor_id').select2({
            placeholder: 'Search for a Doctor',
            allowClear: true,
            ajax: {
                url: '<?= site_url('api/get-doctors') ?>', // You'll need to create this API endpoint
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data.doctors, function (doctor) {
                            return {
                                id: doctor.id,
                                text: doctor.first_name + ' ' + doctor.last_name + ' (' + doctor.specialization + ')'
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // Initialize Select2 for all medicine selects
        $('.medicine-select').select2({
            placeholder: 'Select Medicine',
            allowClear: true,
            // You can add AJAX here if medicine list is huge, but for now assuming it's passed from controller
        });

        // Toggle patient/doctor fields based on prescription type
        $('#prescription_type').on('change', function() {
            var type = $(this).val();
            if (type === 'in_hospital') {
                $('#in_hospital_fields').show();
                $('#outside_sale_fields').hide();
            } else if (type === 'outside_sale') {
                $('#in_hospital_fields').hide();
                $('#outside_sale_fields').show();
            } else {
                $('#in_hospital_fields').hide();
                $('#outside_sale_fields').hide();
            }
        }).trigger('change'); // Trigger on load to apply initial old('prescription_type') state

        // Function to calculate and update totals
        function updateTotals() {
            let totalAmount = 0;
            let netAmount = 0;

            $('.medicine-item').each(function() {
                let quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.item-unit-price').val()) || 0;
                let discountPerItem = parseFloat($(this).find('.item-discount-per-item').val()) || 0;

                let itemSubTotal = (quantity * unitPrice) - discountPerItem;
                if (itemSubTotal < 0) itemSubTotal = 0; // Prevent negative sub-total

                $(this).find('.item-sub-total').val(itemSubTotal.toFixed(2));

                totalAmount += (quantity * unitPrice);
                netAmount += itemSubTotal;
            });

            let totalSaleDiscount = parseFloat($('#total_discount').val()) || 0;
            let finalNetAmount = netAmount - totalSaleDiscount;
            if (finalNetAmount < 0) finalNetAmount = 0; // Prevent negative final net amount

            $('#total_amount_display').val(totalAmount.toFixed(2));
            $('#net_amount_display').val(finalNetAmount.toFixed(2));
        }

        // Add Medicine Item button click
        $('#add-medicine-item').on('click', function() {
            itemCounter++;
            var newItemHtml = `
                <div class="row medicine-item border border-secondary rounded p-3 mb-2" data-item-id="${itemCounter}">
                    <div class="col-md-11 row">
                        <div class="form-group col-md-4">
                            <label>Medicine <span class="text-danger">*</span></label>
                            <select class="form-control medicine-select" name="items[${itemCounter}][medicine_id]">
                                <option value="">Select Medicine</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?= esc($med['id']) ?>" data-unit-price="<?= esc($med['selling_price_per_unit']) ?>">
                                        <?= esc($med['brand_name']) ?> (<?= esc($med['generic_name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Batch <span class="text-danger">*</span></label>
                            <select class="form-control batch-select" name="items[${itemCounter}][batch_id]">
                                <option value="">Select Batch</option>
                            </select>
                            <small class="form-text text-muted batch-stock-info"></small>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control item-quantity" name="items[${itemCounter}][quantity]" value="1" min="1">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Unit Price</label>
                            <input type="number" class="form-control item-unit-price" name="items[${itemCounter}][unit_selling_price]" value="0.00" step="0.01" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Discount/Item</label>
                            <input type="number" class="form-control item-discount-per-item" name="items[${itemCounter}][discount_per_item]" value="0.00" step="0.01">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sub Total</label>
                            <input type="number" class="form-control item-sub-total" value="0.00" step="0.01" readonly>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                    </div>
                </div>
            `;
            $('#medicine-items-container').append(newItemHtml);
            // Re-initialize Select2 for the newly added medicine select
            $(`select[name="items[${itemCounter}][medicine_id]"]`).select2({
                placeholder: 'Select Medicine',
                allowClear: true,
            });
            updateTotals(); // Recalculate totals after adding new item
        });

        // Remove Medicine Item button click (delegated event)
        $('#medicine-items-container').on('click', '.remove-item', function() {
            $(this).closest('.medicine-item').remove();
            updateTotals(); // Recalculate totals after removing item
        });

        // Handle medicine selection change (delegated event)
        $('#medicine-items-container').on('change', '.medicine-select', function() {
            const medicineId = $(this).val();
            const $itemRow = $(this).closest('.medicine-item');
            const $batchSelect = $itemRow.find('.batch-select');
            const $unitPriceInput = $itemRow.find('.item-unit-price');
            const $stockInfo = $itemRow.find('.batch-stock-info');
            const selectedMedicineOption = $(this).find('option:selected');
            const defaultUnitPrice = selectedMedicineOption.data('unit-price') || '0.00';

            $unitPriceInput.val(defaultUnitPrice); // Set default selling price

            $batchSelect.empty().append($('<option>', { value: '', text: 'Select Batch' }));
            $stockInfo.text(''); // Clear previous stock info

            if (medicineId) {
                $.ajax({
                    url: '<?= site_url('pharmacy/medicines/get-batches-by-medicine/') ?>' + medicineId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.batches.length > 0) {
                            $.each(response.batches, function(index, batch) {
                                let optionText = `${batch.batch_number} (Stock: ${batch.current_stock} | Exp: ${moment(batch.expiry_date).format('MMM YYYY')})`;
                                $batchSelect.append($('<option>', {
                                    value: batch.id,
                                    text : optionText,
                                    'data-current-stock': batch.current_stock
                                }));
                            });
                        } else {
                            $batchSelect.append($('<option>', {
                                value: '',
                                text: 'No active batches found'
                            }));
                        }
                        updateTotals(); // Recalculate totals after batch update
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading batches:", error);
                        $batchSelect.empty().append($('<option>', { value: '', text: 'Error loading batches' }));
                        updateTotals();
                    }
                });
            } else {
                updateTotals();
            }
        });

        // Handle batch selection change (delegated event) to show stock
        $('#medicine-items-container').on('change', '.batch-select', function() {
            const $selectedOption = $(this).find('option:selected');
            const currentStock = $selectedOption.data('current-stock');
            const $stockInfo = $(this).closest('.medicine-item').find('.batch-stock-info');
            
            if (currentStock !== undefined) {
                $stockInfo.text(`Available Stock: ${currentStock}`);
            } else {
                $stockInfo.text('');
            }
        });

        // Recalculate totals on quantity, unit price, item discount, or total discount change
        $('#medicine-items-container').on('input', '.item-quantity, .item-unit-price, .item-discount-per-item', updateTotals);
        $('#total_discount').on('input', updateTotals);

        // Initial calculation on page load (important for old input)
        updateTotals();
    });
</script>
<?= $this->endSection() ?>