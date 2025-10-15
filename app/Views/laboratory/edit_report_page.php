<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-edit mr-2 text-info"></i> Edit Lab Order: <span class="text-primary"><?= esc($order['order_id_code']) ?></span>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/orders') ?>">Lab Orders</a></li>
                    <li class="breadcrumb-item active">Edit Order</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card card-outline card-info shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">Update Details for Order #<?= esc($order['order_id_code']) ?></h3>
                        <div class="card-tools">
                             <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-sm btn-default"><i class="fas fa-list-alt mr-1"></i> Back to Orders</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Errors!</h5>
                                <ul>
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('laboratory/update_order/' . $order['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary"><i class="fas fa-user-injured"></i> Patient Details (Read-only)</legend>
                                
                                <div class="form-group row">
                                    <div class="col-md-6 mb-3">
                                        <label for="patient_name">Patient Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                            <input type="text" class="form-control" id="patient_name" value="<?= esc($order['patient_name']) ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="patient_id">Patient ID</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                                            <input type="text" class="form-control" id="patient_id" value="<?= esc($order['patient_id_code']) ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-info"><i class="fas fa-vials"></i> Test Selection</legend>
                                
                                <div class="form-group">
                                    <label for="lab_test_ids">Select Lab Tests <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-flask"></i></span></div>
                                        <select name="lab_test_ids[]" id="lab_test_ids" class="form-control select2-tests <?= session('errors.lab_test_ids') ? 'is-invalid' : '' ?>" multiple="multiple" required>
                                            <?php foreach ($labTests as $test): 
                                                // Check if test ID was in the old input (for validation errors) or is currently selected
                                                $oldTests = old('lab_test_ids') ?? $currentTests;
                                                $selected = in_array($test['id'], $oldTests) ? 'selected' : '';
                                            ?>
                                                <option value="<?= esc($test['id']) ?>" <?= $selected ?>>
                                                    <?= esc($test['name']) ?> (₹<?= number_format(esc($test['price']), 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (session('errors.lab_test_ids')): ?><div class="invalid-feedback d-block"><?= session('errors.lab_test_ids') ?></div><?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">Use the dropdown to add or remove tests from the order.</small>
                                </div>
                            </fieldset>

                            <div class="form-group">
                                <label for="remarks">Remarks/Clinical Notes</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-notes-medical"></i></span></div>
                                    <textarea name="remarks" id="remarks" rows="3" class="form-control" placeholder="Enter any specific clinical notes or instructions..."><?= old('remarks', $order['remarks']) ?></textarea>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Update Order</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    // Note: If you've already loaded select2 in your main layout, you don't need the external script tag here.
    // Assuming Select2 is loaded globally via layouts/main.

    $(document).ready(function() {
        // Initialize Select2 for the multiselect dropdown with bootstrap4 theme
        $('#lab_test_ids').select2({
            theme: 'bootstrap4',
            placeholder: "Select one or more tests",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>