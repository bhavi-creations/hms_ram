<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?>: <?= esc($order['order_id_code']) ?></h3>
        <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary float-right">Cancel</a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form action points to the update method in your controller -->
        <form action="<?= base_url('diagnostics/orders/update/' . $order['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <input type="hidden" name="_method" value="PUT">

            <!-- Patient Details (Read-Only) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <!-- FIX: We rely on the controller to now provide 'patient_name' -->
                        <p class="form-control-static font-weight-bold"><?= esc($order['patient_name'] ?? 'N/A') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Patient ID</label>
                        <!-- FIX: We rely on the controller to now provide 'patient_id_code' -->
                        <p class="form-control-static font-weight-bold"><?= esc($order['patient_id_code'] ?? 'N/A') ?></p>
                        <!-- Keep patient_id hidden so it's submitted with the form for updates -->
                        <input type="hidden" name="patient_id" value="<?= esc($order['patient_id']) ?>">
                    </div>
                </div>
            </div>
            
            <!-- Diagnostic Tests Multi-Select (Select2 enabled for search) -->
            <div class="form-group">
                <label for="test_ids">Diagnostic Tests (Editable)</label>
                <!-- Added 'select2' class here -->
                <select name="test_ids[]" id="test_ids" class="form-control select2" multiple required>
                    <?php foreach ($diagnosticsTests as $test): ?>
                        <option 
                            value="<?= esc($test['id']) ?>" 
                            <?= in_array($test['id'], set_value('test_ids', $currentTestIds)) ? 'selected' : '' ?>>
                            <!-- FIX: Changed $test['name'] to $test['test_name'] based on diagnostics_tests table -->
                            <?= esc($test['test_name'] ?? 'Name Missing') . ' - (Price: ' . number_format($test['price'] ?? 0, 2) . ')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Use the search bar above to easily find and select tests.</small>
            </div>
            
            <!-- Remarks Textarea -->
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <!-- Pre-fill remarks from the order data -->
                <textarea name="remarks" id="remarks" class="form-control" rows="4"><?= esc(set_value('remarks', $order['remarks'] ?? '')) ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Update Order</button>
            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

 