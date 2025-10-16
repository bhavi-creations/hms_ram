<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-warning">
            <div class="card-header">
                <!-- Title adjusted to reflect editing and include the specialization name -->
                <h3 class="card-title"><?= esc($page_title ?? 'Edit Specialization') ?>: *<?= esc($specialization['name'] ?? 'New Record') ?>*</h3>
            </div>
            
            <!--
                Form is opened to the 'update' route, passing the record ID.
                Note: CodeIgniter 4's form_open usually defaults to POST, but for RESTful updates,
                the controller will likely need to cast the POST request to a PUT/PATCH method
                using a hidden field or middleware, as CI4 supports method spoofing.
            -->
            <?= form_open(base_url('specializations/update/' . $specialization['id'])) ?>
                
                <!-- Hidden field for Method Spoofing (Standard CI4 practice for PUT/PATCH requests) -->
                <?= form_hidden('_method', 'PUT') ?>
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Specialization Name*</label>
                        <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>"
                                id="name" name="name" placeholder="Enter specialization name" 
                                value="<?= old('name', $specialization['name'] ?? '') ?>" required>
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
                                placeholder="Briefly describe the specialization"><?= old('description', $specialization['description'] ?? '') ?></textarea>
                        <?php if ($validation->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('description') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Specialization</button>
                    <a href="<?= base_url('specializations') ?>" class="btn btn-default float-right">Cancel</a>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
