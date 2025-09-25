<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Enter Results for Order #<?= esc($order['id']) ?></h3>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php foreach ($orderItems as $item): ?>
            <div class="border p-4 mb-4">
                <h4>Test: <?= esc($item['test_name']) ?></h4> <!-- Updated to show test name -->
                <form action="<?= base_url('laboratory/save_result') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="lab_order_item_id" value="<?= esc($item['id']) ?>">

                    <div class="form-group">
                        <label for="result_<?= esc($item['id']) ?>">Result</label>
                        <textarea name="result" id="result_<?= esc($item['id']) ?>" class="form-control" rows="5"><?= esc($item['result'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status_<?= esc($item['id']) ?>">Status</label>
                        <select name="status" id="status_<?= esc($item['id']) ?>" class="form-control">
                            <option value="Not Started" <?= ($item['status'] == 'Not Started') ? 'selected' : '' ?>>Not Started</option>
                            <option value="In Progress" <?= ($item['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= ($item['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="result_files_<?= esc($item['id']) ?>">Upload Result Files (PDF, Images etc.)</label>
                        <input type="file" name="result_files[]" id="result_files_<?= esc($item['id']) ?>" class="form-control" multiple>
                    </div>

                    <button type="submit" class="btn btn-success mt-2">Save Result & Upload Files</button>
                </form>

                <?php if (!empty($item['files'])): ?>
                    <h5 class="mt-4">Uploaded Files:</h5>
                    <ul class="list-group">
                        <?php foreach ($item['files'] as $file): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= esc($file['file_name']) ?>
                                <a href="<?= base_url('public/uploads/laboratory/' . $file['file_path']) ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                                <a href="<?= base_url('laboratory/delete_file/' . $file['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file?')">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('laboratory/results') ?>" class="btn btn-secondary">Go Back</a>
    </div>
</div>
<?= $this->endSection() ?>
