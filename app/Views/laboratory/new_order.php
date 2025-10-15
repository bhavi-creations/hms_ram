<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-microscope mr-2 text-success"></i> New Lab Order
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/orders') ?>">Lab Orders</a></li>
                    <li class="breadcrumb-item active">New Order</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card card-outline card-success shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">Order Details</h3>
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

                        <form action="<?= base_url('laboratory/orders/save') ?>" method="post">
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary"><i class="fas fa-user-injured"></i> Patient Information</legend>
                                
                                <div class="form-group">
                                    <label for="patient_id">Select Patient <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                                        <select name="patient_id" id="patient_id" class="form-control select2-ajax <?= session('errors.patient_id') ? 'is-invalid' : '' ?>" required>
                                            <option value="">Search by Patient Name or ID...</option>
                                            <?php if (old('patient_id') && old('patient_name')): // Retain old value on error ?>
                                                <option value="<?= old('patient_id') ?>" selected><?= old('patient_name') ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <?php if (session('errors.patient_id')): ?><div class="invalid-feedback"><?= session('errors.patient_id') ?></div><?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Phone Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                            <input type="text" id="patient_phone" class="form-control" readonly placeholder="Phone number (auto-filled)" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Referred Doctor</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-md"></i></span></div>
                                            <input type="text" id="patient_doctor" class="form-control" readonly placeholder="Doctor name (auto-filled)" />
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-success"><i class="fas fa-flask"></i> Test Selection</legend>

                                <div class="form-group">
                                    <label for="test_ids">Select Tests <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-vials"></i></span></div>
                                        <select name="test_ids[]" id="test_ids" class="form-control select2-tests <?= session('errors.test_ids') ? 'is-invalid' : '' ?>" multiple required>
                                            <?php foreach ($labTests as $test): 
                                                // Check if test ID was in the old input (for validation errors)
                                                $selected = in_array($test['id'], old('test_ids', [])) ? 'selected' : '';
                                            ?>
                                                <option value="<?= esc($test['id']) ?>" <?= $selected ?>>
                                                    <?= esc($test['name']) ?> (<?= number_format($test['price'], 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (session('errors.test_ids')): ?><div class="invalid-feedback d-block"><?= session('errors.test_ids') ?></div><?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">Select one or more lab tests for this order.</small>
                                </div>
                            </fieldset>

                            <div class="form-group">
                                <label for="remarks">Remarks/Clinical Notes</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-notes-medical"></i></span></div>
                                    <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Enter any specific clinical notes or instructions..."><?= old('remarks') ?></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Place Order</button>
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
    $(document).ready(function() {
        // Initialize Select2 for Patient Search (AJAX)
        $('#patient_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Search by Patient Name or ID...',
            allowClear: true,
            ajax: {
                url: '<?= base_url('patients/search') ?>', // Ensure this URL is correct
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            // item structure expected from backend: {id: 1, text: 'John Doe (#123)', phone: '...', doctor: '...'}
                            return {
                                id: item.id,
                                text: item.text,
                                phone: item.phone,
                                doctor: item.doctor
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Event handler to populate phone and doctor fields on patient selection
        $('#patient_id').on('select2:select', function(e) {
            var data = e.params.data;
            $('#patient_phone').val(data.phone || 'N/A');
            $('#patient_doctor').val(data.doctor || 'N/A');
        });
        
        // Handle deselection (clearing the fields if the patient selection is cleared)
        $('#patient_id').on('select2:clear', function(e) {
            $('#patient_phone').val('');
            $('#patient_doctor').val('');
        });

        // Initialize Select2 for Lab Tests (Multiple Select)
        $('.select2-tests').select2({
            theme: 'bootstrap4',
            placeholder: 'Select tests...',
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for the Level dropdown to match theme
        $('.select2-level').select2({
            theme: 'bootstrap4',
            placeholder: '-- Select Level --',
            minimumResultsForSearch: Infinity,
            allowClear: true,
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>