<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Enter Results for Order #<?= esc($order['order_id_code']) ?></h3>
        <p class="mb-0">Patient: <?= esc($order['patient_name']) ?> | Doctor: <?= esc($order['doctor_name']) ?></p>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <!-- Global Remarks Area (Optional if using the item-by-item model) -->
        <div class="mb-4">
            <label for="order_remarks" class="form-label">Order Remarks (Global)</label>
            <textarea id="order_remarks" class="form-control" rows="2" readonly><?= esc($order['remarks'] ?? 'No global remarks.') ?></textarea>
        </div>

        <?php foreach ($orderItems as $item): ?>
            <div class="border p-4 mb-4 rounded shadow-sm">
                <h5 class="text-info">Test: <?= esc($item['test_name']) ?> <span class="badge bg-secondary">(ID: <?= esc($item['id']) ?>)</span></h5>
                
                <!-- START OF INDIVIDUAL ITEM FORM -->
                <form action="<?= base_url('diagnostics/results/save') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <!-- HIDDEN FIELD: Sends the Item ID to the saveResult controller method -->
                    <input type="hidden" name="diagnostics_order_item_id" value="<?= esc($item['id']) ?>"> 

                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="result_<?= esc($item['id']) ?>">Result Value / Notes</label>
                                <!-- FIX: Changed $item['result_value'] to the correct $item['result'] -->
                                <textarea name="result" id="result_<?= esc($item['id']) ?>" class="form-control" rows="3"><?= esc(old('result', $item['result'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_<?= esc($item['id']) ?>">Status</label>
                                <!-- Field name is 'status' (singular) -->
                                <select name="status" id="status_<?= esc($item['id']) ?>" class="form-control">
                                    <option value="Pending" <?= ($item['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= ($item['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= ($item['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="result_files_<?= esc($item['id']) ?>">Upload Result Files (PDF, Images) - Multiple allowed</label>
                        <!-- Field name MUST be 'result_files[]' to match Lab module -->
                        <input type="file" name="result_files[]" id="result_files_<?= esc($item['id']) ?>" class="form-control" multiple>
                    </div>

                    <button type="submit" class="btn btn-success mt-3 w-100">
                        <i class="fas fa-save me-2"></i>Save Result & Upload Files
                    </button>
                </form>
                <!-- END OF INDIVIDUAL ITEM FORM -->

                <?php if (!empty($filesByItem[$item['id']])): ?>
                    <h6 class="mt-4">Uploaded Files:</h6>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($filesByItem[$item['id']] as $file): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                <span class="text-truncate" style="max-width: 70%;"><?= esc($file['file_name']) ?></span>
                                <div>
                                    <a href="<?= base_url('diagnostics/reports/file/' . $file['id']) ?>" target="_blank" class="btn btn-sm btn-info me-2">View</a> 
                                    <a href="<?= base_url('diagnostics/delete_file/' . $file['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file?')">Delete</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('diagnostics/reports/view/' . $order['id']) ?>" class="btn btn-primary me-2">View Final Report</a>
        <a href="<?= base_url('diagnostics/results') ?>" class="btn btn-secondary">Back to Results List</a>
    </div>
</div>
<?= $this->endSection() ?>
