<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-microscope mr-2 text-success"></i> Enter Results for Order: <span class="text-primary">#<?= esc($order['order_id_code']) ?></span>
                </h1>
                <p class="mb-0 text-muted">
                    <i class="fas fa-user-injured"></i> Patient: **<?= esc($order['patient_name']) ?>**  
                    <!-- <i class="fas fa-user-md"></i> Doctor: **<?= esc($order['doctor_name']) ?>** -->
                </p>
            </div><div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/results') ?>">Results Entry</a></li>
                    <li class="breadcrumb-item active">Order #<?= esc($order['order_id_code']) ?></li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-success shadow-lg">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Result Entry - Item by Item</h3>
                <div class="card-tools">
                    <a href="<?= base_url('diagnostics/reports/view/' . $order['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-file-pdf mr-1"></i> View Final Report
                    </a>
                </div>
            </div>
            <div class="card-body">
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show"><i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                <?php endif; ?>

                <div class="callout callout-info mb-4">
                    <h5 class="text-info"><i class="fas fa-info-circle"></i> Global Order Remarks</h5>
                    <p class="mb-0 text-muted"><?= esc($order['remarks'] ?? 'No global remarks.') ?></p>
                </div>

                <?php foreach ($orderItems as $item): ?>
                    <div class="card card-outline card-secondary mb-4 shadow-sm">
                        <div class="card-header bg-gray-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-flask mr-1 text-info"></i> **<?= esc($item['test_name']) ?>**
                                <span class="badge bg-dark ml-2">Item ID: <?= esc($item['id']) ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            <form action="<?= base_url('diagnostics/results/save') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="diagnostics_order_item_id" value="<?= esc($item['id']) ?>"> 

                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="result_<?= esc($item['id']) ?>"><i class="fas fa-pen-nib"></i> Result Value / Notes</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-align-left"></i></span></div>
                                                <textarea name="result" id="result_<?= esc($item['id']) ?>" class="form-control" rows="3" placeholder="Enter numerical result or detailed observations..."><?= esc(old('result', $item['result'] ?? '')) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status_<?= esc($item['id']) ?>"><i class="fas fa-clock"></i> Status</label>
                                            <select name="status" id="status_<?= esc($item['id']) ?>" class="form-control">
                                                <option value="Pending" <?= ($item['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="In Progress" <?= ($item['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Completed" <?= ($item['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="result_files_<?= esc($item['id']) ?>"><i class="fas fa-upload"></i> Upload Result Files (PDF, Images)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="result_files[]" id="result_files_<?= esc($item['id']) ?>" class="custom-file-input" multiple>
                                            <label class="custom-file-label" for="result_files_<?= esc($item['id']) ?>">Choose file(s)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success mt-3 w-100">
                                    <i class="fas fa-save mr-2"></i> Save Result & Upload Files for this Test
                                </button>
                            </form>

                            <?php if (!empty($filesByItem[$item['id']])): ?>
                                <h6 class="mt-4 text-info"><i class="fas fa-paperclip mr-1"></i> Existing Uploads:</h6>
                                <ul class="list-group list-group-unbordered">
                                    <?php foreach ($filesByItem[$item['id']] as $file): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                            <span class="text-truncate" style="max-width: 65%;"><i class="fas fa-file-alt mr-1"></i> <?= esc($file['file_name']) ?></span>
                                            <div>
                                                <a href="<?= base_url('diagnostics/reports/file/' . $file['id']) ?>" target="_blank" class="btn btn-xs btn-info mr-2" title="View File"><i class="fas fa-eye"></i> View</a> 
                                                <a href="<?= base_url('diagnostics/delete_file/' . $file['id']) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this file?')" title="Delete File"><i class="fas fa-trash"></i> Delete</a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= base_url('diagnostics/results') ?>" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Results List
                </a>
                <a href="<?= base_url('diagnostics/reports/view/' . $order['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-file-pdf mr-1"></i> View Final Report
                </a>
            </div>
        </div>
        </div></section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Required to show the selected file name in the custom-file-input
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            // Handle multiple file selection for display
            if(this.files.length > 1) {
                $(this).next('.custom-file-label').html(this.files.length + ' files selected');
            } else {
                $(this).next('.custom-file-label').html(fileName);
            }
        });
    });
</script>
<?= $this->endSection() ?>