<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Add New Referred Person</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="<?= base_url('referred-persons') ?>" class="btn btn-secondary btn-flat">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Fill Details</h3>
        </div>
        <div class="card-body">
            <?= session()->getFlashdata('error') ? '<div class="alert alert-danger">'.esc(session()->getFlashdata('error')).'</div>' : '' ?>
            <?= session()->getFlashdata('success') ? '<div class="alert alert-success">'.esc(session()->getFlashdata('success')).'</div>' : '' ?>
            
            <?= form_open('referred-persons/store') ?>
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" value="<?= old('name') ?>" required>
                <?php if (isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('name')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="type">Type <span class="text-danger">*</span></label>
                <input type="text" name="type" id="type" class="form-control <?= isset($validation) && $validation->hasError('type') ? 'is-invalid' : '' ?>" value="<?= old('type') ?>" required>
                <?php if (isset($validation) && $validation->hasError('type')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('type')) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="contact_info">Contact Info</label>
                <input type="text" name="contact_info" id="contact_info" class="form-control <?= isset($validation) && $validation->hasError('contact_info') ? 'is-invalid' : '' ?>" value="<?= old('contact_info') ?>">
                <?php if (isset($validation) && $validation->hasError('contact_info')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('contact_info')) ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary">Save</button>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
