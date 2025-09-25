<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add New Role</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('roles/save') ?>" method="post">
            <div class="mb-3">
                <label for="name">Role Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Enter role name">
            </div>
            <div class="mb-3">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Enter role description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="<?= base_url('roles') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
