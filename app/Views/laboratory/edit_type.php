<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header"><h3 class="card-title">Edit Test Type</h3></div>
    <div class="card-body">
        <form action="<?= base_url('laboratory/types/update/' . $type['id']) ?>" method="post">
            <div class="form-group">
                <label>Type Name</label>
                <input type="text" name="name" class="form-control" required value="<?= old('name', $type['name']) ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?= old('description', $type['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Type</button>
            <a href="<?= base_url('laboratory/types') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
