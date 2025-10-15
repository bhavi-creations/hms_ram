<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-flask mr-2 text-info"></i> Edit Lab Test: <span class="text-primary"><?= esc($test['name']) ?></span>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/tests') ?>">Lab Tests</a></li>
                    <li class="breadcrumb-item active">Edit Test</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card card-outline card-info shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Update Test Specifications</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session('errors')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Error!</h5>
                                <ul class="mb-0">
                                    <?php foreach (session('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('laboratory/tests/update/' . $test['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT"> <div class="form-group">
                                <label for="name">Test Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-text-width"></i></span></div>
                                    <input type="text" name="name" id="name" value="<?= old('name', $test['name']) ?>" 
                                           class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" required>
                                    <?php if (session('errors.name')): ?><div class="invalid-feedback"><?= session('errors.name') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="test_type_id">Test Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tags"></i></span></div>
                                    <select name="test_type_id" id="test_type_id" class="form-control <?= session('errors.test_type_id') ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Test Type</option>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?= esc($type['id']) ?>" 
                                                    <?= old('test_type_id', $test['test_type_id']) == $type['id'] ? 'selected' : '' ?>>
                                                <?= esc($type['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.test_type_id')): ?><div class="invalid-feedback"><?= session('errors.test_type_id') ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="price">Price (in ₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-rupee-sign"></i></span></div>
                                    <input type="number" name="price" id="price" value="<?= old('price', $test['price']) ?>" 
                                           class="form-control <?= session('errors.price') ? 'is-invalid' : '' ?>" 
                                           required step="0.01" min="0">
                                    <?php if (session('errors.price')): ?><div class="invalid-feedback"><?= session('errors.price') ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description / Notes</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-info-circle"></i></span></div>
                                    <textarea name="description" id="description" class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                              rows="4" placeholder="Briefly describe the test purpose or procedure."><?= old('description', $test['description']) ?></textarea>
                                    <?php if (session('errors.description')): ?><div class="invalid-feedback"><?= session('errors.description') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end pt-3">
                                <a href="<?= base_url('laboratory/tests') ?>" class="btn btn-default px-4 mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-info px-4"><i class="fas fa-sync-alt"></i> Update Test</button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>