<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><?= esc($page_title ?? 'Add New Specialization') ?></h3>
            </div>
            
            <?= form_open(base_url('specializations/store')) ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Specialization Name*</label>
                        <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>"
                                id="name" name="name" placeholder="Enter specialization name" 
                                value="<?= old('name') ?>" required>
                        <?php if ($validation->hasError('name')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('name') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                id="description" name="description" rows="3" 
                                placeholder="Briefly describe the specialization"><?= old('description') ?></textarea>
                        <?php if ($validation->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('description') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Save Specialization</button>
                    <a href="<?= base_url('specializations') ?>" class="btn btn-default float-right">Cancel</a>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
