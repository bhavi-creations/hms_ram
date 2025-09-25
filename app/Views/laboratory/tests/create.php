<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Lab Test</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('laboratory') ?>">Laboratory</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('laboratory/tests') ?>">Tests</a></li>
                        <li class="breadcrumb-item active">Add Test</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Add Lab Test Details</h3>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('errors')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <form action="<?= base_url('laboratory/tests/save') ?>" method="post" novalidate>
                                <?= csrf_field() ?>
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Test Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" required placeholder="Enter test name">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter test description"><?= old('description') ?></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="test_type_id" class="form-label">Test Type <span class="text-danger">*</span></label>
                                    <select name="test_type_id" id="test_type_id" class="form-select form-control" required>
                                        <option value="">Select Test Type</option>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?= esc($type['id']) ?>" <?= old('test_type_id') == $type['id'] ? 'selected' : '' ?>><?= esc($type['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="price" class="form-label">Price (in ₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" value="<?= old('price') ?>" class="form-control" required step="0.01" min="0" placeholder="Enter price">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success px-4 me-2">Save Test</button>
                                    <a href="<?= base_url('laboratory/tests') ?>" class="btn btn-secondary px-4">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
