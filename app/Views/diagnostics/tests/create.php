<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-flask mr-2 text-success"></i> <?= esc($title) ?>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/tests') ?>">Tests</a></li>
                    <li class="breadcrumb-item active">New Test</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-outline card-success shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Enter New Diagnostic Test Information</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Error!</h5>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('diagnostics/tests/save') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <label for="test_name">Test Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tag"></i></span></div>
                                    <input type="text" name="test_name" id="test_name" 
                                           class="form-control <?= session('errors.test_name') ? 'is-invalid' : '' ?>" 
                                           value="<?= esc(set_value('test_name')) ?>" required placeholder="e.g., Complete Blood Count (CBC)">
                                    <?php if (session('errors.test_name')): ?><div class="invalid-feedback"><?= session('errors.test_name') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="test_type">Test Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-list-alt"></i></span></div>
                                    <input type="text" name="test_type" id="test_type" 
                                           class="form-control <?= session('errors.test_type') ? 'is-invalid' : '' ?>" 
                                           value="<?= esc(set_value('test_type')) ?>" required placeholder="e.g., Hematology, Biochemistry">
                                    <?php if (session('errors.test_type')): ?><div class="invalid-feedback"><?= session('errors.test_type') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-rupee-sign"></i></span></div>
                                    <input type="number" name="price" id="price" 
                                           class="form-control <?= session('errors.price') ? 'is-invalid' : '' ?>" 
                                           step="0.01" min="0" value="<?= esc(set_value('price')) ?>" required placeholder="0.00">
                                    <?php if (session('errors.price')): ?><div class="invalid-feedback"><?= session('errors.price') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                                <a href="<?= base_url('diagnostics/tests') ?>" class="btn btn-default mr-2 px-4"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i> Save Test</button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>