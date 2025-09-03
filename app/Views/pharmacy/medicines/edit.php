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
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Edit Medicine: <?= esc($medicine['generic_name']) ?></h3>
                        <div class="card-tools">
                            <a href="<?= site_url('pharmacy/medicines') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Display Validation Errors from the session -->
                        <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">Validation Errors:</h4>
                                <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?= form_open(url_to('pharmacy.medicines.update', $medicine['id'])) ?>
                        <?= form_hidden('_method', 'PUT') ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="generic_name">Generic Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="generic_name" name="generic_name" value="<?= old('generic_name', $medicine['generic_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_name">Brand Name</label>
                                    <input type="text" class="form-control" id="brand_name" name="brand_name" value="<?= old('brand_name', $medicine['brand_name'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dosage_form_id">Dosage Form <span class="text-danger">*</span></label>
                                    <select class="form-control" id="dosage_form_id" name="dosage_form_id" required>
                                        <option value="">Select Dosage Form</option>
                                        <?php foreach ($dosageForms as $form) : ?>
                                            <option value="<?= esc($form['id']) ?>" <?= (old('dosage_form_id', $medicine['dosage_form_id'] ?? '') == $form['id']) ? 'selected' : '' ?>>
                                                <?= esc($form['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="strength">Strength <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="strength" name="strength" value="<?= old('strength', $medicine['strength'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_of_measure_id">Unit of Measure</label>
                                    <select name="unit_of_measure_id" id="unit_of_measure_id" class="form-control">
                                        <option value="">-- Select Unit --</option>
                                        <?php foreach ($units as $unit) : ?>
                                            <option value="<?= esc($unit['id']) ?>" <?= (old('unit_of_measure_id', $medicine['unit_of_measure_id'] ?? '') == $unit['id']) ? 'selected' : '' ?>>
                                                <?= esc($unit['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturer_id">Manufacturer <span class="text-danger">*</span></label>
                                    <select class="form-control" id="manufacturer_id" name="manufacturer_id" required>
                                        <option value="">Select Manufacturer</option>
                                        <?php foreach ($manufacturers as $manufacturer) : ?>
                                            <option value="<?= esc($manufacturer['id']) ?>" <?= (old('manufacturer_id', $medicine['manufacturer_id'] ?? '') == $manufacturer['id']) ? 'selected' : '' ?>>
                                                <?= esc($manufacturer['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Category <span class="text-danger">*</span></label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category) : ?>
                                            <option value="<?= esc($category['id']) ?>" <?= (old('category_id', $medicine['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                                <?= esc($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- New Row for HSN Code and GST Rate -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hsn_code">HSN Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="hsn_code" name="hsn_code" value="<?= old('hsn_code', $medicine['hsn_code'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gst_rate">GST Rate (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="gst_rate" name="gst_rate" value="<?= old('gst_rate', $medicine['gst_rate'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="<?= old('reorder_level', $medicine['reorder_level'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $medicine['is_active'] ?? '') == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Is Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $medicine['description'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Update Medicine</button>
                        <a href="<?= url_to('pharmacy.medicines.index') ?>" class="btn btn-secondary mt-3">Cancel</a>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
