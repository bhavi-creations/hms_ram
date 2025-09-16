<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Generic</h1>
        <a href="<?= site_url('pharmacy/generics') ?>" class="btn btn-secondary mb-3">Back to List</a>
    </section>

    <section class="content">
        <?= \Config\Services::validation()->listErrors(); ?>
        <form action="<?= site_url('pharmacy/generics/update/' . $generic['id']) ?>" method="post" autocomplete="off" novalidate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="generic_name">Generic Name</label>
                <input 
                    type="text" 
                    id="generic_name" 
                    name="generic_name" 
                    class="form-control <?= session('errors.generic_name') ? 'is-invalid' : '' ?>" 
                    value="<?= set_value('generic_name', $generic['generic_name']) ?>" 
                    required 
                    autofocus 
                    autocomplete="off"
                >
                <?php if(session('errors.generic_name')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.generic_name') ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Generic</button>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
