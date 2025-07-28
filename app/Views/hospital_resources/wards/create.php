<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($title) ?></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?= form_open('wards/store') ?>
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="name">Ward Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name') ?>" placeholder="Enter ward name" required>
                        <?php if (session('errors.name')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.name') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter ward description"><?= old('description') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Bed Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= (session('errors.capacity')) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" value="<?= old('capacity', 0) ?>" min="1" placeholder="Enter bed capacity" required>
                        <?php if (session('errors.capacity')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.capacity') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="bed_prefix">Bed Prefix <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (session('errors.bed_prefix')) ? 'is-invalid' : '' ?>" id="bed_prefix" name="bed_prefix" value="<?= old('bed_prefix', 'GEN') ?>" placeholder="e.g., GEN, ICU, PED" required>
                        <small class="form-text text-muted">This prefix will be used for naming beds (e.g., GEN-1, GEN-2).</small>
                        <?php if (session('errors.bed_prefix')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.bed_prefix') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control <?= (session('errors.status')) ? 'is-invalid' : '' ?>" id="status" name="status" required>
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Ward</button>
                        <a href="<?= base_url('wards') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                    <?= form_close() ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.container-fluid -->
<?= $this->endSection() ?>
