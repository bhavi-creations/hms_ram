<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Initiate New Medicine Return</h1>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/returns') ?>">Returns</a></li>
                <li class="breadcrumb-item active">New Return</li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>


            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?= form_open('pharmacy/returns/store', ['id' => 'returnForm']) ?>

            <div class="form-group">
                <label for="invoice_number">Invoice Number <span class="text-danger">*</span></label>
                <input type="text" id="invoice_number" name="invoice_number" class="form-control" value="<?= old('invoice_number') ?>" placeholder="Enter invoice or bill number" required>
                <small class="form-text text-muted">Search to load medicines linked to this invoice.</small>
            </div>

            <div class="form-group">
                <label for="sale_item_id">Medicine <span class="text-danger">*</span></label>
                <select id="sale_item_id" name="sale_item_id" class="form-control" required disabled>
                    <option value="">Select medicine (Enter invoice above)</option>
                </select>
            </div>

            <input type="hidden" id="sale_id" name="sale_id" value="<?= old('sale_id') ?>">
            <input type="hidden" id="billing_id" name="billing_id" value="<?= old('billing_id') ?>">


            <div class="form-group">
                <label for="quantity_returned">Quantity Returned <span class="text-danger">*</span></label>
                <input type="number" id="quantity_returned" name="quantity_returned" class="form-control" min="1" value="<?= old('quantity_returned') ?>" required>
                <small id="available_quantity" class="form-text text-muted"></small>
            </div>

            <div class="form-group">
                <label for="medicine_condition">Medicine Condition <span class="text-danger">*</span></label>
                <select id="medicine_condition" name="medicine_condition" class="form-control" required>
                    <option value="Good" <?= old('medicine_condition') == 'Good' ? 'selected' : '' ?>>Good</option>
                    <option value="Damaged" <?= old('medicine_condition') == 'Damaged' ? 'selected' : '' ?>>Damaged</option>
                    <option value="Expired" <?= old('medicine_condition') == 'Expired' ? 'selected' : '' ?>>Expired</option>
                    <option value="Other" <?= old('medicine_condition') == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="return_reason">Return Reason <span class="text-danger">*</span></label>
                <textarea id="return_reason" name="return_reason" rows="3" class="form-control" placeholder="Provide at least 5 characters reason" required><?= old('return_reason') ?></textarea>
            </div>

            <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="Additional notes"><?= old('notes') ?></textarea>
            </div>



            <button type="submit" class="btn btn-primary">Submit Return Request</button>
            <a href="<?= site_url('pharmacy/returns') ?>" class="btn btn-secondary">Cancel</a>

            <?= form_close() ?>

        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Reset all relevant fields when invoice changes.
        $('#invoice_number').on('change blur', function() {
            var invoiceNumber = $(this).val().trim();
            $('#sale_item_id').html('<option value="">Select medicine (Enter invoice above)</option>').prop('disabled', true);
            $('#available_quantity').text('');
            $('#sale_id').val('');

            if (!invoiceNumber.length) return;

            $('#sale_item_id').prop('disabled', true).html('<option>Loading medicines...</option>');
            $.ajax({
                url: '<?= site_url('pharmacy/returns/getMedicinesByInvoice') ?>/' + encodeURIComponent(invoiceNumber),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.medicines.length > 0) {
                        var options = '<option value="">Select medicine</option>';
                        response.medicines.forEach(function(med) {
                            var sid = med.sale_id ? med.sale_id : '';
                            var bid = med.billing_id ? med.billing_id : '';
                            options += '<option value="' + med.sale_item_id + '" data-available="' + med.quantity + '" data-saleid="' + sid + '" data-billingid="' + bid + '">' +
                                med.medicine_name + ' - Batch: ' + med.batch_number + '</option>';
                        });
                        $('#sale_item_id').html(options).prop('disabled', false);
                    } else {
                        $('#sale_item_id').html('<option value="">No medicines found for this invoice</option>').prop('disabled', true);
                    }
                    $('#available_quantity').text('');
                    $('#sale_id').val('');
                },
                error: function() {
                    $('#sale_item_id').html('<option value="">Error fetching medicines</option>').prop('disabled', true);
                    $('#available_quantity').text('');
                    $('#sale_id').val('');
                }
            });
        });

        // Update hidden sale_id when medicine is selected

        $('#sale_item_id').on('change', function() {
            var $opt = $('option:selected', this);
            var qty = $opt.data('available') || 0;
            var sid = $opt.data('saleid') || '';
            var bid = $opt.data('billingid') || '';

            // Set hidden fields explicitly
            $('#sale_id').val(sid);
            $('#billing_id').val(bid);

            $('#available_quantity').text('Available quantity for return: ' + qty);
            $('#quantity_returned').attr('max', qty).val('').focus();
        });

    });
</script>
<?= $this->endSection() ?>