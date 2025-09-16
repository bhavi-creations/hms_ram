<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Brand</h1>
        <a href="<?= site_url('pharmacy/brands') ?>" class="btn btn-secondary mb-3">Back to List</a>
    </section>

    <section class="content">
        <?= \Config\Services::validation()->listErrors(); ?>
        <form action="<?= site_url('pharmacy/brands/update/' . $brand['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="brand_name">Brand Name</label>
                <input type="text" id="brand_name" name="brand_name" class="form-control" 
                    value="<?= set_value('brand_name', $brand['brand_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Brand</button>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
