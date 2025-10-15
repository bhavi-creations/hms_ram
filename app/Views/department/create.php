<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-sitemap mr-2 text-primary"></i> <?= esc($page_title ?? 'Add New Department') ?>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('departments') ?>">Departments</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-outline card-success shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Enter Department Details</h3>
                    </div>
                    <?= form_open(base_url('departments/store')) ?>
                        <?= csrf_field() ?>
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="name">Department Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-building"></i></span></div>
                                    <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>"
                                           id="name" name="name" placeholder="Enter department name (e.g., Administration, Lab)"
                                           value="<?= old('name') ?>" required>
                                    <?php if ($validation->hasError('name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-info-circle"></i></span></div>
                                    <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                             id="description" name="description" rows="3"
                                             placeholder="Enter department description or role"><?= old('description') ?></textarea>
                                    <?php if ($validation->hasError('description')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('description') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= base_url('departments') ?>" class="btn btn-default"><i class="fas fa-times-circle mr-1"></i> Cancel</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Save Department</button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div></section>
<?= $this->endSection() ?>