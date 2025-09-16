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
                                    <label for="brand_id">Brand Name <span class="text-danger">*</span></label>
                                    <select id="brand_id" name="brand_id" class="form-control select2 <?= (service('validation')->hasError('brand_id')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand) : ?>
                                            <option value="<?= esc($brand['id']) ?>" <?= (old('brand_id', $medicine['brand_id'] ?? '') == $brand['id']) ? 'selected' : '' ?>>
                                                <?= esc($brand['brand_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('brand_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('brand_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="generic_id">Generic Name <span class="text-danger">*</span></label>
                                    <select id="generic_id" name="generic_id" class="form-control select2 <?= (service('validation')->hasError('generic_id')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Generic</option>
                                        <?php foreach ($generics as $generic) : ?>
                                            <option value="<?= esc($generic['id']) ?>" <?= (old('generic_id', $medicine['generic_id'] ?? '') == $generic['id']) ? 'selected' : '' ?>>
                                                <?= esc($generic['generic_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('generic_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('generic_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dosage_form_id">Dosage Form <span class="text-danger">*</span></label>
                                    <select id="dosage_form_id" name="dosage_form_id" class="form-control select2 <?= (service('validation')->hasError('dosage_form_id')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Dosage Form</option>
                                        <?php foreach ($dosageForms as $form) : ?>
                                            <option value="<?= esc($form['id']) ?>" <?= (old('dosage_form_id', $medicine['dosage_form_id'] ?? '') == $form['id']) ? 'selected' : '' ?>>
                                                <?= esc($form['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('dosage_form_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('dosage_form_id') ?>
                                        </div>
                                    <?php endif; ?>
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
                                    <select id="unit_of_measure_id" name="unit_of_measure_id" class="form-control select2 <?= (service('validation')->hasError('unit_of_measure_id')) ? 'is-invalid' : '' ?>">
                                        <option value="">-- Select Unit --</option>
                                        <?php foreach ($units as $unit) : ?>
                                            <option value="<?= esc($unit['id']) ?>" <?= (old('unit_of_measure_id', $medicine['unit_of_measure_id'] ?? '') == $unit['id']) ? 'selected' : '' ?>>
                                                <?= esc($unit['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('unit_of_measure_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('unit_of_measure_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturer_id">Manufacturer <span class="text-danger">*</span></label>
                                    <select id="manufacturer_id" name="manufacturer_id" class="form-control select2 <?= (service('validation')->hasError('manufacturer_id')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Manufacturer</option>
                                        <?php foreach ($manufacturers as $manufacturer) : ?>
                                            <option value="<?= esc($manufacturer['id']) ?>" <?= (old('manufacturer_id', $medicine['manufacturer_id'] ?? '') == $manufacturer['id']) ? 'selected' : '' ?>>
                                                <?= esc($manufacturer['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('manufacturer_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('manufacturer_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Category <span class="text-danger">*</span></label>
                                    <select id="category_id" name="category_id" class="form-control select2 <?= (service('validation')->hasError('category_id')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category) : ?>
                                            <option value="<?= esc($category['id']) ?>" <?= (old('category_id', $medicine['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                                <?= esc($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (service('validation')->hasError('category_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= service('validation')->getError('category_id') ?>
                                        </div>
                                    <?php endif; ?>
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