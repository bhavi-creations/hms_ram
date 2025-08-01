<?= $this->extend('layouts/main') ?> // Ensure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create New Purchase</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/purchases') ?>">Purchases</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div></section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Purchase Order Details</h3>
                </div>
                <form action="<?= site_url('pharmacy/purchases/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control select2" id="supplier_id" name="supplier_id" style="width: 100%;">
                                <option value="">Select Supplier</option>
                                <?php if (!empty($suppliers) && is_array($suppliers)) : ?>
                                    <?php foreach ($suppliers as $supplier) : ?>
                                        <option value="<?= esc($supplier['id']) ?>" <?= set_select('supplier_id', $supplier['id']) ?>><?= esc($supplier['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= set_value('purchase_date', date('Y-m-d')) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= set_value('remarks') ?></textarea>
                        </div>

                        <h4 class="mt-4">Purchase Items</h4>
                        <table class="table table-bordered" id="purchase_items_table">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Batch</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Expiry Date</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select class="form-control medicine-select select2" name="items[0][medicine_id]" style="width: 100%;" required>
                                            <option value="">Select Medicine</option>
                                            <?php if (!empty($medicines) && is_array($medicines)) : ?>
                                                <?php foreach ($medicines as $medicine) : ?>
                                                    <option value="<?= esc($medicine['id']) ?>"><?= esc($medicine['name']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control batch-input" name="items[0][batch_number]" placeholder="Batch No." required></td>
                                    <td><input type="number" class="form-control quantity-input" name="items[0][quantity]" min="1" value="1" required></td>
                                    <td><input type="number" class="form-control unit-cost-input" name="items[0][unit_cost]" step="0.01" min="0" value="0.00" required></td>
                                    <td><input type="date" class="form-control expiry-date-input" name="items[0][expiry_date]" required></td>
                                    <td><input type="text" class="form-control subtotal-output" readonly value="0.00"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-item-row"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                                    <td><input type="text" class="form-control" id="total_amount" name="total_amount" readonly value="0.00"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <button type="button" class="btn btn-success" id="add_item_row"><i class="fas fa-plus"></i> Add Item</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit Purchase</button>
                        <a href="<?= site_url('pharmacy/purchases') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            </div>
    </section>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select an option',
            allowClear: true
        });

        let itemIndex = 1; // Start index for new items

        function calculateRowSubtotal(row) {
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const unitCost = parseFloat(row.find('.unit-cost-input').val()) || 0;
            const subtotal = quantity * unitCost;
            row.find('.subtotal-output').val(subtotal.toFixed(2));
            calculateTotalAmount();
        }

        function calculateTotalAmount() {
            let total = 0;
            $('#purchase_items_table .item-row').each(function() {
                const subtotal = parseFloat($(this).find('.subtotal-output').val()) || 0;
                total += subtotal;
            });
            $('#total_amount').val(total.toFixed(2));
        }

        // Initial calculation for existing rows (if any, though none on create page normally)
        $('#purchase_items_table .item-row').each(function() {
            calculateRowSubtotal($(this));
        });

        // Add new item row
        $('#add_item_row').on('click', function() {
            const newRow = `
                <tr class="item-row">
                    <td>
                        <select class="form-control medicine-select select2" name="items[${itemIndex}][medicine_id]" style="width: 100%;" required>
                            <option value="">Select Medicine</option>
                            <?php if (!empty($medicines) && is_array($medicines)) : ?>
                                <?php foreach ($medicines as $medicine) : ?>
                                    <option value="<?= esc($medicine['id']) ?>"><?= esc($medicine['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td><input type="text" class="form-control batch-input" name="items[${itemIndex}][batch_number]" placeholder="Batch No." required></td>
                    <td><input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" min="1" value="1" required></td>
                    <td><input type="number" class="form-control unit-cost-input" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" value="0.00" required></td>
                    <td><input type="date" class="form-control expiry-date-input" name="items[${itemIndex}][expiry_date]" required></td>
                    <td><input type="text" class="form-control subtotal-output" readonly value="0.00"></td>
                    <td>
                        <button type="button" class="btn btn-danger remove-item-row"><i class="fas fa-times"></i></button>
                    </td>
                </tr>
            `;
            $('#purchase_items_table tbody').append(newRow);
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true
            }); // Re-initialize Select2 for new elements
            itemIndex++;
        });

        // Remove item row
        $('#purchase_items_table').on('click', '.remove-item-row', function() {
            if ($('#purchase_items_table tbody .item-row').length > 1) { // Don't remove the last row
                $(this).closest('.item-row').remove();
                calculateTotalAmount();
            } else {
                alert('You must have at least one item.');
            }
        });

        // Live calculation on quantity and unit cost change
        $('#purchase_items_table').on('input', '.quantity-input, .unit-cost-input', function() {
            calculateRowSubtotal($(this).closest('.item-row'));
        });
    });
</script>
<?= $this->endSection() ?>