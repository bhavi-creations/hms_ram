<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= esc($title) ?></h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?= form_open(url_to('pharmacy.medicines.store')) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generic_name">Generic Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= (service('validation')->hasError('generic_name')) ? 'is-invalid' : '' ?>" id="generic_name" name="generic_name" value="<?= old('generic_name') ?>" required>
                                <?php if (service('validation')->hasError('generic_name')): ?>
                                    <div class="invalid-feedback">
                                        <?= service('validation')->getError('generic_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand_name">Brand Name</label>
                                <input type="text" class="form-control <?= (service('validation')->hasError('brand_name')) ? 'is-invalid' : '' ?>" id="brand_name" name="brand_name" value="<?= old('brand_name') ?>">
                                <?php if (service('validation')->hasError('brand_name')): ?>
                                    <div class="invalid-feedback">
                                        <?= service('validation')->getError('brand_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dosage_form_id">Dosage Form <span class="text-danger">*</span></label>
                                <select class="form-control <?= (service('validation')->hasError('dosage_form_id')) ? 'is-invalid' : '' ?>" id="dosage_form_id" name="dosage_form_id" required>
                                    <option value="">Select Dosage Form</option>
                                    <?php foreach ($dosageForms as $form) : ?>
                                        <option value="<?= esc($form['id']) ?>" <?= set_select('dosage_form_id', $form['id']) ?>>
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
                                <input type="text" placeholder="Ex: 5 / 5mg  (enter number only)" class="form-control <?= (service('validation')->hasError('strength')) ? 'is-invalid' : '' ?>" id="strength" name="strength" value="<?= old('strength') ?>" required>
                                <?php if (service('validation')->hasError('strength')): ?>
                                    <div class="invalid-feedback">
                                        <?= service('validation')->getError('strength') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_of_measure_id" class="form-label">Unit of Measure</label>
                                <select name="unit_of_measure_id" id="unit_of_measure_id" class="form-control <?= (service('validation')->hasError('unit_of_measure_id')) ? 'is-invalid' : '' ?>">
                                    <option value="">-- Select Unit --</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= esc($unit['id']) ?>" <?= set_select('unit_of_measure_id', $unit['id']) ?>>
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
                                <select class="form-control <?= (service('validation')->hasError('manufacturer_id')) ? 'is-invalid' : '' ?>" id="manufacturer_id" name="manufacturer_id" required>
                                    <option value="">Select Manufacturer</option>
                                    <?php foreach ($manufacturers as $manufacturer) : ?>
                                        <option value="<?= esc($manufacturer['id']) ?>" <?= set_select('manufacturer_id', $manufacturer['id']) ?>>
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
                                <select class="form-control <?= (service('validation')->hasError('category_id')) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= esc($category['id']) ?>" <?= set_select('category_id', $category['id']) ?>>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= (service('validation')->hasError('reorder_level')) ? 'is-invalid' : '' ?>" id="reorder_level" name="reorder_level" value="<?= old('reorder_level') ?>" required>
                                <?php if (service('validation')->hasError('reorder_level')): ?>
                                    <div class="invalid-feedback">
                                        <?= service('validation')->getError('reorder_level') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input <?= (service('validation')->hasError('is_active')) ? 'is-invalid' : '' ?>" type="checkbox" id="is_active" name="is_active" value="1" <?= set_checkbox('is_active', '1', false) ?>>
                                <label class="form-check-label" for="is_active">
                                    Is Active
                                </label>
                                <?php if (service('validation')->hasError('is_active')): ?>
                                    <div class="invalid-feedback">
                                        <?= service('validation')->getError('is_active') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control <?= (service('validation')->hasError('description')) ? 'is-invalid' : '' ?>" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                        <?php if (service('validation')->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= service('validation')->getError('description') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Add Medicine</button>
                    <a href="<?= url_to('pharmacy.medicines.index') ?>" class="btn btn-secondary mt-3">Cancel</a>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
