<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Lab Order #<?= esc($order['order_id_code']) ?></h3>
        <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-sm btn-primary float-right">Back to Orders</a>
    </div>
    <div class="card-body">
        <form action="<?= base_url('laboratory/update_order/' . $order['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="patient_name">Patient Name</label>
                    <input type="text" class="form-control" id="patient_name" value="<?= esc($order['patient_name']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label for="patient_id">Patient ID</label>
                    <input type="text" class="form-control" id="patient_id" value="<?= esc($order['patient_id_code']) ?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label for="lab_test_ids">Select Lab Tests</label>
                <select name="lab_test_ids[]" id="lab_test_ids" class="form-control" multiple="multiple" required>
                    <?php foreach ($labTests as $test): ?>
                        <option value="<?= esc($test['id']) ?>" <?= in_array($test['id'], $currentTests) ? 'selected' : '' ?>>
                            <?= esc($test['name']) ?> (₹<?= esc($test['price']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">You can select multiple tests.</small>
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="form-control"><?= esc($order['remarks']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success float-right">Update Order</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for the multiselect dropdown
        $('#lab_test_ids').select2({
            placeholder: "Select one or more tests",
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>
