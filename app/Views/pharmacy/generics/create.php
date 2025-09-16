<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add New Generic</h1>
        <a href="<?= site_url('pharmacy/generics') ?>" class="btn btn-secondary mb-3">Back to List</a>
    </section>

    <section class="content">
        <?= \Config\Services::validation()->listErrors(); ?>
        <form action="<?= site_url('pharmacy/generics/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="generic_name">Generic Name</label>
                <input type="text" id="generic_name" name="generic_name" class="form-control" value="<?= set_value('generic_name') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Generic</button>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
