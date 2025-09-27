<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?></h3>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('diagnostics/tests/update/' . $test['id']) ?>" method="post">
            <input type="hidden" name="id" value="<?= esc($test['id']) ?>">
            <div class="form-group">
                <label for="test_name">Test Name</label>
                <input type="text" name="test_name" id="test_name" class="form-control" value="<?= esc(set_value('test_name', $test['test_name'])) ?>" required>
            </div>
            <div class="form-group">
                <label for="test_type">Test Type</label>
                <input type="text" name="test_type" id="test_type" class="form-control" value="<?= esc(set_value('test_type', $test['test_type'])) ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?= esc(set_value('price', $test['price'])) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Test</button>
            <a href="<?= base_url('diagnostics/tests') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
