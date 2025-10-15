<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-plus mr-2 text-primary"></i> Add New Referred Person
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('referred-persons') ?>">Referred Persons</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Fill Referred Person Details</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('referred-persons') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left mr-1"></i> Back to List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show"><i class="icon fas fa-ban"></i> <?= esc(session()->getFlashdata('error')) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show"><i class="icon fas fa-check"></i> <?= esc(session()->getFlashdata('success')) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <?php endif; ?>
                        
                        <?= form_open('referred-persons/store') ?>
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tag"></i></span></div>
                                    <input type="text" name="name" id="name" 
                                           class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                           value="<?= old('name') ?>" required placeholder="Enter name of doctor or institution">
                                    <?php if (isset($validation) && $validation->hasError('name')): ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('name')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-briefcase"></i></span></div>
                                    <input type="text" name="type" id="type" 
                                           class="form-control <?= isset($validation) && $validation->hasError('type') ? 'is-invalid' : '' ?>" 
                                           value="<?= old('type') ?>" required placeholder="e.g., Doctor, Clinic, Hospital">
                                    <?php if (isset($validation) && $validation->hasError('type')): ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('type')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_info">Contact Info (Phone/Email/Address)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                    <input type="text" name="contact_info" id="contact_info" 
                                           class="form-control <?= isset($validation) && $validation->hasError('contact_info') ? 'is-invalid' : '' ?>" 
                                           value="<?= old('contact_info') ?>" placeholder="e.g., +1234567890 or street address">
                                    <?php if (isset($validation) && $validation->hasError('contact_info')): ?>
                                        <div class="invalid-feedback"><?= esc($validation->getError('contact_info')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                                <a href="<?= base_url('referred-persons') ?>" class="btn btn-default mr-2 px-4"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i> Save Referred Person</button>
                            </div>
                        <?= form_close() ?>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>