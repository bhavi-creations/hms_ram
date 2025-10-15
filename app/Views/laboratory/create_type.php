<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-list-alt mr-2 text-primary"></i> Add New Test Type
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/types') ?>">Test Types</a></li>
                    <li class="breadcrumb-item active">Add Type</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card card-outline card-primary shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Define New Category for Lab Tests</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('error') || session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Error!</h5>
                                <?php if (session()->getFlashdata('error')): ?>
                                    <p><?= session()->getFlashdata('error') ?></p>
                                <?php endif; ?>
                                <?php if (session('errors')): ?>
                                    <ul>
                                        <?php foreach (session('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('laboratory/types/save') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <label for="type_name">Type Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tag"></i></span></div>
                                    <input type="text" name="name" id="type_name" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                           required value="<?= old('name') ?>" placeholder="e.g., Blood Chemistry, Hematology, Microbiology">
                                    <?php if (session('errors.name')): ?><div class="invalid-feedback"><?= session('errors.name') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="type_description">Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-info-circle"></i></span></div>
                                    <textarea name="description" id="type_description" class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                              rows="3" placeholder="A brief explanation of tests covered by this type."><?= old('description') ?></textarea>
                                    <?php if (session('errors.description')): ?><div class="invalid-feedback"><?= session('errors.description') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <a href="<?= base_url('laboratory/types') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Type</button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>