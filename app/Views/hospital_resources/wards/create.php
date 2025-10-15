<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= esc($title) ?> <i class="fas fa-hospital-alt text-primary"></i></h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('wards') ?>">Wards</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
                <div class="card card-outline card-primary shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle mr-1"></i>
                            New Ward Registration
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Error!</h5>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?= form_open('wards/store') ?>
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary">Ward Details</legend>
                                
                                <div class="form-group">
                                    <label for="name">Ward Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name') ?>" placeholder="Enter ward name (e.g., General Ward 1)" required>
                                        <?php if (session('errors.name')) : ?>
                                            <div class="invalid-feedback">
                                                <?= session('errors.name') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the ward..."><?= old('description') ?></textarea>
                                </div>
                            </fieldset>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary">Capacity & Identification</legend>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="capacity">Bed Capacity <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                                </div>
                                                <input type="number" class="form-control <?= (session('errors.capacity')) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" value="<?= old('capacity', 0) ?>" min="1" placeholder="Enter total number of beds" required>
                                                <?php if (session('errors.capacity')) : ?>
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.capacity') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bed_prefix">Bed Prefix <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                </div>
                                                <input type="text" class="form-control <?= (session('errors.bed_prefix')) ? 'is-invalid' : '' ?>" id="bed_prefix" name="bed_prefix" value="<?= old('bed_prefix', 'GEN') ?>" placeholder="e.g., GEN, ICU, PED" required>
                                                <?php if (session('errors.bed_prefix')) : ?>
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.bed_prefix') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <small class="form-text text-muted">This prefix will be used for naming beds (e.g., GEN-1, GEN-2).</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-circle-notch"></i></span>
                                        </div>
                                        <select class="form-control select2 <?= (session('errors.status')) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                            <option value="Active" <?= old('status') == 'Active' ? 'selected' : '' ?>>Active</option>
                                            <option value="Inactive" <?= old('status') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                            <option value="Under Maintenance" <?= old('status') == 'Under Maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                                        </select>
                                        <?php if (session('errors.status')) : ?>
                                            <div class="invalid-feedback">
                                                <?= session('errors.status') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </fieldset>
                            
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                <a href="<?= base_url('wards') ?>" class="btn btn-default mr-2"><i class="fas fa-times"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Ward</button>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
                </div>
            </div>
        </div></section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for a premium-looking dropdown
        $('#status').select2({
            theme: 'bootstrap4', // Assuming AdminLTE/Bootstrap theme for Select2
            minimumResultsForSearch: Infinity, // Hides the search box
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>