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
                                        <!-- Patient Search with Button -->
                                        <div class="form-group">
                                            <label for="patient_id_code">Patient IPD-ID <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <!-- CRITICAL FIX: REMOVED name="patient_id_code" FROM THIS FIELD. It will no longer be sent to the server directly. -->
                                                <input type="text" class="form-control" id="patient_id_code"
                                                    value="<?= old('patient_id_code') ?>" placeholder="Enter Patient IPD-ID (e.g., 2023-0001)">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" id="search-patient-btn">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Patient Details Card (Hidden by default) -->
                                        <div id="patient-details-card" class="card card-secondary mt-3 d-none">
                                            <div class="card-header">
                                                <h5 class="card-title">Patient Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Name:</strong> <span id="patient-name"></span></p>
                                                <p><strong>Phone:</strong> <span id="patient-phone"></span></p>
                                                <p><strong>Referring Doctor:</strong> <span id="patient-doctor"></span></p>

                                            </div>
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

                                                <div class="form-group col-md-3">
                                                    <label>Category <span class="text-danger">*</span></label>
                                                    <select class="form-control category-select" name="items[1][category_id]">
                                                        <option value="">Select Category</option>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <!-- Adjusted column size to fit the new field -->
                                                <div class="form-group col-md-3">
                                                    <label>Medicine <span class="text-danger">*</span></label>
                                                    <select class="form-control medicine-select" name="items[1][medicine_id]">
                                                        <option value="">Select Medicine</option>
                                                        <!-- Medicines will be populated dynamically -->
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Batch <span class="text-danger">*</span></label>
                                                    <select class="form-control batch-select" name="items[<?= $item_id ?>][batch_id]">
                                                        <option value="">Select Batch</option>
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

                            <!-- Detailed Summary Table -->
                            <div id="detailed-summary-container">
                                <h5>Detailed Summary</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Medicine</th>
                                                <th>Batch</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Discount/item</th>
                                                <th>HSN Code</th>
                                                <th>GST %</th>
                                                <th>Expiry Date</th>
                                                <th>Gross Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailed-summary-table-body">
                                            <!-- Rows will be dynamically added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <!-- Final Totals Section -->
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="form-group row">
                                        <label for="total_items" class="col-sm-6 col-form-label">Total Items:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="total_items" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="total_units" class="col-sm-6 col-form-label">Total Units:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="total_units" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="total_gst_amount" class="col-sm-6 col-form-label">Total GST Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="total_gst_amount" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="sub_total_without_gst" class="col-sm-6 col-form-label">Sub Total (without GST):</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="sub_total_without_gst" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="total_discount" class="col-sm-6 col-form-label">Total Discount:</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" id="total_discount" name="total_discount"
                                                value="<?= old('total_discount', 0) ?>" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="total_final_amount" class="col-sm-6 col-form-label">Total Final Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="total_final_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method" required>
                                            <option value="">Select</option>
                                            <option value="Cash" <?= (old('payment_method') == 'Cash') ? 'selected' : '' ?>>Cash</option>
                                            <option value="Card" <?= (old('payment_method') == 'Card') ? 'selected' : '' ?>>Card</option>
                                            <option value="UPI" <?= (old('payment_method') == 'UPI') ? 'selected' : '' ?>>UPI</option>
                                            <?php if ((old('prescription_type') ?? '') === 'in_hospital'): ?>
                                                <option value="Insurance" <?= (old('payment_method') == 'Insurance') ? 'selected' : '' ?>>Insurance</option>
                                            <?php endif; ?>
                                            <option value="Credit" <?= (old('payment_method') == 'Credit') ? 'selected' : '' ?>>Credit</option>
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

                            <!-- A hidden input field to send the patient_id_code -->
                            <input type="hidden" name="patient_id_code" id="patient_id_code" />



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
<script src="<?= base_url('public/assets/adminlte/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('public/assets/adminlte/plugins/moment/moment.js') ?>"></script>


<script>
    // Global counter to ensure unique names for dynamically added items
    var itemCounter = <?= !empty($old_items) ? count($old_items) : 0 ?>;

    $(function() {
        // A function to display user-friendly messages
        function showMessage(type, message) {
            const alertDiv = $('#alert-message');
            alertDiv.removeClass().addClass(`alert alert-${type} alert-dismissible fade show`).html(`
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `).show();
        }



        function togglePaymentOptions() {
            var presType = $('#prescription_type').val();
            var creditOption = $('#payment_method option[value="Credit"]');
            var insuranceOption = $('#payment_method option[value="Insurance"]');

            if (presType === 'in_hospital') {
                creditOption.show();
                insuranceOption.show();
            } else {
                if ($('#payment_method').val() === 'Credit' || $('#payment_method').val() === 'Insurance') {
                    $('#payment_method').val('');
                }
                creditOption.hide();
                insuranceOption.hide();
            }
        }

        // Run on page load
        togglePaymentOptions();

        // Run when prescription type changes
        $('#prescription_type').on('change', function() {
            togglePaymentOptions();
        });



        // Initialize Select2 for doctor selection
        $('#doctor_id').select2({
            placeholder: 'Search for a Doctor',
            allowClear: true,
            ajax: {
                url: '<?= site_url('api/get-doctors') ?>',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data.doctors, function(doctor) {
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

        function initializeSelect2(selector) {
            $(selector).select2({
                placeholder: 'Select an option',
                allowClear: true,
            });
        }
        initializeSelect2('.category-select');
        initializeSelect2('.medicine-select');
        initializeSelect2('.batch-select');



        // Patient Search Button Click
        $('#search-patient-btn').on('click', function() {
            // var ipdId = $('#patient_id').val();  
            var ipdId = $('#patient_id_code').val();

            // Disable submit button initially to block premature submission
            $('#posForm button[type="submit"]').prop('disabled', true);

            if (ipdId && ipdId.trim().length > 0) {
                $.ajax({
                    url: '<?= site_url('pharmacy/medicines/getPatientDetailsAndBills') ?>/' + encodeURIComponent(ipdId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Set hidden input with the exact IPD code from response
                            $('input[name="patient_id_code"]').val(response.patient.ipd_id_code);

                            // Fill patient details in UI
                            $('#patient-name').text(response.patient.name);
                            $('#patient-phone').text(response.patient.phone);
                            $('#patient-doctor').text(response.patient.doctor);

                            // Show patient details card
                            $('#patient-details-card').removeClass('d-none');

                            // Enable submit button now that we have valid patient info
                            $('#posForm button[type="submit"]').prop('disabled', false);

                            // Load billing information into table
                            var bills = response.patient.bills;
                            $('#patient-bills-table-body').empty();

                            if (bills && bills.length > 0) {
                                bills.forEach(function(bill) {
                                    var dueAmount = bill.totalGrossAmount - bill.totalPaidAmount;
                                    var rowHtml = '<tr>' +
                                        '<td>' + bill.billId + '</td>' +
                                        '<td>' + bill.date + '</td>' +
                                        '<td>' + bill.totalGrossAmount.toFixed(2) + '</td>' +
                                        '<td>' + bill.totalPaidAmount.toFixed(2) + '</td>' +
                                        '<td>' + dueAmount.toFixed(2) + '</td>' +
                                        '</tr>';
                                    $('#patient-bills-table-body').append(rowHtml);
                                });
                            } else {
                                $('#patient-bills-table-body').html('<tr><td colspan="5">No bills found for this patient.</td></tr>');
                            }
                        } else {
                            // Patient not found or error in response
                            showMessage('danger', 'Patient not found: ' + (response.message ? response.message : 'Unknown error'));
                            // Clear fields
                            $('#patient-details-card').addClass('d-none');
                            $('input[name="patient_id_code"]').val('');
                            $('#posForm button[type="submit"]').prop('disabled', true);
                        }
                    },
                    error: function() {
                        showMessage('danger', 'An error occurred while searching for the patient. Please try again.');
                        $('#patient-details-card').addClass('d-none');
                        $('input[name="patient_id_code"]').val('');
                        $('#posForm button[type="submit"]').prop('disabled', true);
                    }
                });
            } else {
                showMessage('warning', 'Please enter a valid Patient IPD-ID.');
                // Clear patient details and disable submit
                $('#patient-details-card').addClass('d-none');
                $('input[name="patient_id_code"]').val('');
                $('#posForm button[type="submit"]').prop('disabled', true);
            }
        });


        // Form submission validation
        $('#posForm').on('submit', function(e) {
            if ($('#prescription_type').val() === 'in_hospital') {
                var val = $('input[name="patient_id_code"]').val();
                if (!val || val.trim() === '') {
                    alert('Please search and select a patient first.');
                    e.preventDefault();
                    return false;
                }
            }
        });







        // Toggle patient/doctor fields and detailed table based on prescription type
        $('#prescription_type').on('change', function() {
            var type = $(this).val();

            // This is a CRITICAL addition: if type is not in-hospital, we clear the hidden patient IDs.
            if (type !== 'in_hospital') {
                $('#patient_id_code').val('');
            }


            if (type === 'in_hospital') {
                $('#in_hospital_fields').show();
                $('#outside_sale_fields').hide();
                $('#patient-details-card').removeClass('d-none');
            } else if (type === 'outside_sale') {
                $('#in_hospital_fields').hide();
                $('#outside_sale_fields').show();
                $('#patient-details-card').addClass('d-none');
            } else {
                $('#in_hospital_fields').hide();
                $('#outside_sale_fields').hide();
                $('#patient-details-card').addClass('d-none');
            }
            updateTotals();
        }).trigger('change');

        function updateTotals() {
            const prescriptionType = $('#prescription_type').val();
            const detailedTableBody = $('#detailed-summary-table-body');
            detailedTableBody.empty();

            let totalItems = 0;
            let totalUnits = 0;
            let totalGST = 0;
            let totalSubTotal = 0;
            let totalDiscount = 0;

            let sno = 1;

            $('.medicine-item').each(function() {
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const unitPrice = parseFloat($(this).find('.item-unit-price').val()) || 0;
                const discountPerItem = parseFloat($(this).find('.item-discount-per-item').val()) || 0;

                const medicineSelect = $(this).find('.medicine-select option:selected');
                const medicineName = medicineSelect.data('brand-name') || '';
                const hsnCode = medicineSelect.data('hsn-code') || '';
                const gstRate = parseFloat(medicineSelect.data('gst-rate')) || 0;

                const batchSelect = $(this).find('.batch-select option:selected');
                const batchNumber = batchSelect.text().split(' ')[0] || '';
                const expiryDate = batchSelect.data('expiry-date') || '';

                const grossAmount = (quantity * unitPrice);
                const itemDiscount = (quantity * discountPerItem);
                const itemSubTotal = grossAmount - itemDiscount;
                const itemGSTAmount = (prescriptionType === 'outside_sale') ? itemSubTotal * (gstRate / 100) : 0;

                $(this).find('.item-sub-total').val(itemSubTotal.toFixed(2));

                const newRow = `
                <tr>
                    <td>${sno++}</td>
                    <td>${medicineName}</td>
                    <td>${batchNumber}</td>
                    <td>${quantity}</td>
                    <td>₹ ${unitPrice.toFixed(2)}</td>
                    <td>₹ ${discountPerItem.toFixed(2)}</td>
                    <td>${hsnCode}</td>
                    <td>${(prescriptionType === 'outside_sale') ? gstRate : 0}%</td>
                    <td>${expiryDate ? moment(expiryDate).format('MMM YYYY') : 'N/A'}</td>
                    <td>₹ ${(itemSubTotal + itemGSTAmount).toFixed(2)}</td>
                </tr>
            `;
                detailedTableBody.append(newRow);

                totalItems++;
                totalUnits += quantity;
                totalGST += itemGSTAmount;
                totalSubTotal += itemSubTotal;
                totalDiscount += itemDiscount;
            });

            const totalSaleDiscount = parseFloat($('#total_discount').val()) || 0;
            totalDiscount += totalSaleDiscount;

            const finalAmount = totalSubTotal + totalGST - totalSaleDiscount;

            $('#total_items').val(totalItems);
            $('#total_units').val(totalUnits);
            $('#total_gst_amount').val(totalGST.toFixed(2));
            $('#sub_total_without_gst').val(totalSubTotal.toFixed(2));
            $('#total_discount').val(totalSaleDiscount.toFixed(2));
            $('#total_final_amount').val(finalAmount.toFixed(2));
        }

        // Add Medicine Item button click
        $('#add-medicine-item').on('click', function() {
            itemCounter++;
            var newItemHtml = `
            <div class="row medicine-item border border-secondary rounded p-3 mb-2" data-item-id="${itemCounter}">
                <div class="col-md-11 row">
                    <div class="form-group col-md-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select class="form-control category-select" name="items[${itemCounter}][category_id]">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Medicine <span class="text-danger">*</span></label>
                        <select class="form-control medicine-select" name="items[${itemCounter}][medicine_id]">
                            <option value="">Select Medicine</option>
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
            const newRow = $(`div[data-item-id="${itemCounter}"]`);
            initializeSelect2(newRow.find('.category-select'));
            initializeSelect2(newRow.find('.medicine-select'));
            initializeSelect2(newRow.find('.batch-select'));
            updateTotals();
        });

        $('#medicine-items-container').on('click', '.remove-item', function() {
            $(this).closest('.medicine-item').remove();
            updateTotals();
        });

        $('#medicine-items-container').on('change', '.category-select', function() {
            const categoryId = $(this).val();
            const $itemRow = $(this).closest('.medicine-item');
            const $medicineSelect = $itemRow.find('.medicine-select');

            $medicineSelect.empty().append('<option value="">Select Medicine</option>');
            $itemRow.find('.batch-select').empty().append('<option value="">Select Batch</option>').trigger('change');
            $itemRow.find('.batch-stock-info').text('');

            if (categoryId) {
                $.ajax({
                    url: '<?= site_url('pharmacy/sales/getMedicinesByCategory/') ?>' + categoryId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.medicines.length > 0) {
                          

                            $.each(response.medicines, function(index, med) {
                                $medicineSelect.append($('<option>', {
                                    value: med.id,
                                    text: med.brand_name + ' (' + med.generic_name + ', ' + med.strength + ')',
                                    'data-unit-price': med.selling_price,
                                    'data-gst-rate': med.gst_rate,
                                    'data-brand-name': med.brand_name,
                                    'data-hsn-code': med.hsn_code || '',
                                }));
                            });

                        } else {
                            $medicineSelect.append($('<option>', {
                                value: '',
                                text: 'No medicines found for this category'
                            }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading medicines by category:", error);
                        $medicineSelect.empty().append('<option value="">Error loading medicines</option>');
                    }
                });
            }
        });

        $('#medicine-items-container').on('change', '.medicine-select', function() {
            const medicineId = $(this).val();
            const $itemRow = $(this).closest('.medicine-item');
            const $batchSelect = $itemRow.find('.batch-select');
            const $unitPriceInput = $itemRow.find('.item-unit-price');
            const $stockInfo = $itemRow.find('.batch-stock-info');

            $batchSelect.empty().append($('<option>', {
                value: '',
                text: 'Select Batch'
            }));
            $stockInfo.text('');
            $unitPriceInput.val('0.00');

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
                                    text: optionText,
                                    'data-current-stock': batch.current_stock,
                                    'data-selling-price': batch.selling_price,
                                    'data-expiry-date': batch.expiry_date,
                                }));
                            });
                            initializeSelect2($batchSelect);
                        } else {
                            $batchSelect.append($('<option>', {
                                value: '',
                                text: 'No active batches found'
                            }));
                        }
                        updateTotals();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading batches:", error);
                        $batchSelect.empty().append($('<option>', {
                            value: '',
                            text: 'Error loading batches'
                        }));
                        updateTotals();
                    }
                });
            } else {
                updateTotals();
            }
        });

        $('#medicine-items-container').on('change', '.batch-select', function() {
            const $selectedOption = $(this).find('option:selected');
            const currentStock = $selectedOption.data('current-stock');
            const sellingPrice = $selectedOption.data('selling-price');
            const $stockInfo = $(this).closest('.medicine-item').find('.batch-stock-info');
            const $unitPriceInput = $(this).closest('.medicine-item').find('.item-unit-price');
            const $itemQuantity = $(this).closest('.medicine-item').find('.item-quantity');

            if (currentStock !== undefined) {
                $stockInfo.text(`Available Stock: ${currentStock}`);
                $itemQuantity.attr('max', currentStock);
            } else {
                $stockInfo.text('');
                $itemQuantity.removeAttr('max');
            }

            if (sellingPrice !== undefined) {
                $unitPriceInput.val(parseFloat(sellingPrice).toFixed(2));
            } else {
                $unitPriceInput.val('0.00');
            }
            updateTotals();
        });



        $('#medicine-items-container').on('input', '.item-quantity, .item-unit-price, .item-discount-per-item', updateTotals);
        $('#total_discount').on('input', updateTotals);

        updateTotals();
    });
</script>
<?= $this->endSection() ?>