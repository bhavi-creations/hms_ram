<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= esc($page_title ?? 'Add New Department') ?></h3>
            </div>
            <?= form_open(base_url('departments/store')) ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Department Name*</label>
                        <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>"
                               id="name" name="name" placeholder="Enter department name"
                               value="<?= old('name') ?>">
                        <?php if ($validation->hasError('name')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('name') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                  id="description" name="description" rows="3"
                                  placeholder="Enter department description"><?= old('description') ?></textarea>
                        <?php if ($validation->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('description') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Department</button>
                    <a href="<?= base_url('departments') ?>" class="btn btn-default float-right">Cancel</a>
                </div>
            <?= form_close() ?>
            </div>
        </div>
</div>
<?= $this->endSection() ?>