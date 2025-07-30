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
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Edit Medicine Details</h3>
                    </div>
                    <form action="<?= site_url('pharmacy/medicines/update/' . esc($medicine['id'])) ?>" method="post">
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

                            <?php if (isset($validation)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach ($validation->getErrors() as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="generic_name">Generic Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="generic_name" name="generic_name" 
                                    value="<?= old('generic_name', $medicine['generic_name']) ?>" placeholder="Enter generic name">
                            </div>
                            <div class="form-group">
                                <label for="brand_name">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brand_name" name="brand_name" 
                                    value="<?= old('brand_name', $medicine['brand_name']) ?>" placeholder="Enter brand name">
                            </div>
                            <div class="form-group">
                                <label for="strength">Strength <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="strength" name="strength" 
                                    value="<?= old('strength', $medicine['strength']) ?>" placeholder="e.g., 500mg, 10mg/5ml">
                            </div>
                            <div class="form-group">
                                <label for="dosage_form">Dosage Form <span class="text-danger">*</span></label>
                                <select class="form-control" id="dosage_form" name="dosage_form">
                                    <option value="">Select Dosage Form</option>
                                    <?php 
                                    $dosageForms = [
                                        'Tablet', 'Capsule', 'Syrup', 'Suspension', 'Injection', 'Cream', 'Ointment',
                                        'Solution', 'Drops', 'Suppository', 'Inhaler', 'Powder', 'Gel', 'Lotion', 'Spray'
                                    ];
                                    ?>
                                    <?php foreach ($dosageForms as $form): ?>
                                        <option value="<?= esc($form) ?>" 
                                            <?= (old('dosage_form', $medicine['dosage_form']) == $form) ? 'selected' : '' ?>>
                                            <?= esc($form) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= esc($category['id']) ?>" 
                                                <?= (old('category_id', $medicine['category_id']) == $category['id']) ? 'selected' : '' ?>>
                                                <?= esc($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="manufacturer_id">Manufacturer <span class="text-danger">*</span></label>
                                <select class="form-control" id="manufacturer_id" name="manufacturer_id">
                                    <option value="">Select Manufacturer</option>
                                    <?php if (!empty($manufacturers)): ?>
                                        <?php foreach ($manufacturers as $manufacturer): ?>
                                            <option value="<?= esc($manufacturer['id']) ?>" 
                                                <?= (old('manufacturer_id', $medicine['manufacturer_id']) == $manufacturer['id']) ? 'selected' : '' ?>>
                                                <?= esc($manufacturer['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="reorder_level" name="reorder_level" 
                                    value="<?= old('reorder_level', $medicine['reorder_level']) ?>" placeholder="Enter reorder level">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                    placeholder="Enter description"><?= old('description', $medicine['description']) ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= site_url('pharmacy/medicines') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>