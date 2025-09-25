<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Lab Test</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('laboratory/tests/update/' . $test['id']) ?>" method="post">
            <div class="form-group">
                <label for="name">Test Name</label>
                <input type="text" name="name" value="<?= old('name', $test['name']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control"><?= old('description', $test['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="test_type_id">Test Type</label>
                <select name="test_type_id" class="form-control" required>
                    <option value="">Select Test Type</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= esc($type['id']) ?>" <?= old('test_type_id', $test['test_type_id']) == $type['id'] ? 'selected' : '' ?>><?= esc($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" value="<?= old('price', $test['price']) ?>" class="form-control" required step="0.01" min="0">
            </div>
            <button type="submit" class="btn btn-primary">Update Test</button>
            <a href="<?= base_url('laboratory/tests') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
