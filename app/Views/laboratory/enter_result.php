<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-microscope mr-2 text-warning"></i> Lab Results Entry
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('laboratory/results') ?>">Pending Results</a></li>
                    <li class="breadcrumb-item active">Order: <?= esc($order['id']) ?></li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card card-outline card-warning shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Enter Results for Order #<span class="text-primary font-weight-bold"><?= esc($order['order_id_code'] ?? $order['id']) ?></span></h3>
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

                        <p class="text-muted">Fill out the results and update the status for each ordered test below. All test panels are currently open for review.</p>

                        <?php foreach ($orderItems as $item): 
                            $status = esc($item['status'] ?? 'Not Started');
                            $card_color = match(strtolower($status)) {
                                'completed' => 'success',
                                'in progress' => 'warning',
                                default => 'secondary',
                            };
                            // NOTE: Removed $initial_collapsed = 'collapsed-card' logic to keep all cards open
                        ?>
                            <div class="card card-outline card-<?= $card_color ?> mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Test: <span class="font-weight-bold text-dark"><?= esc($item['test_name']) ?></span></h4>
                                    <div class="card-tools">
                                        <?php 
                                            $badge_color = match(strtolower($status)) {
                                                'completed' => 'badge-success',
                                                'in progress' => 'badge-warning',
                                                default => 'badge-secondary',
                                            };
                                        ?>
                                        <span class="badge <?= $badge_color ?> mr-2"><?= ucfirst($status) ?></span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <form action="<?= base_url('laboratory/save_result') ?>" method="post" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="lab_order_item_id" value="<?= esc($item['id']) ?>">

                                        <div class="row">
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="status_<?= esc($item['id']) ?>">Status</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tasks"></i></span></div>
                                                        <select name="status" id="status_<?= esc($item['id']) ?>" class="form-control" required>
                                                            <option value="Not Started" <?= ($status == 'Not Started') ? 'selected' : '' ?>>Not Started</option>
                                                            <option value="In Progress" <?= ($status == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                                            <option value="Completed" <?= ($status == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="result_files_<?= esc($item['id']) ?>">Upload Result Files (PDF, Images etc.)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-upload"></i></span></div>
                                                        <div class="custom-file">
                                                            <input type="file" name="result_files[]" id="result_files_<?= esc($item['id']) ?>" class="custom-file-input" multiple>
                                                            <label class="custom-file-label" for="result_files_<?= esc($item['id']) ?>">Choose file(s)</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="result_<?= esc($item['id']) ?>">Result Notes / Interpretation</label>
                                            <textarea name="result" id="result_<?= esc($item['id']) ?>" class="form-control" rows="5" placeholder="Enter key results, values, or interpretation notes here."><?= esc($item['result'] ?? '') ?></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success mt-2 float-right"><i class="fas fa-save mr-1"></i> Save Result & Upload Files</button>
                                        
                                    </form>

                                    <?php if (!empty($item['files'])): ?>
                                        <h5 class="mt-5 mb-3"><i class="fas fa-file-alt mr-1"></i> Current Uploaded Files (<?= count($item['files']) ?>)</h5>
                                        <ul class="list-group list-group-unbordered">
                                            <?php foreach ($item['files'] as $file): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span class="text-info"><i class="fas fa-file-pdf mr-2"></i> <?= esc($file['file_name']) ?></span>
                                                    <div>
                                                        <a href="<?= base_url('public/uploads/laboratory/' . $file['file_path']) ?>" target="_blank" class="btn btn-sm btn-info mr-2">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="<?= base_url('laboratory/delete_file/' . $file['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the file: <?= esc($file['file_name']) ?>?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                </div>
                            <?php endforeach; ?>

                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('laboratory/results') ?>" class="btn btn-default"><i class="fas fa-arrow-circle-left mr-1"></i> Go Back to Results List</a>
                    </div>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Script to update custom file input label with selected file names
    $(document).ready(function() {
        $('input[type="file"]').on('change', function() {
            let fileNames = [];
            for (let i = 0; i < this.files.length; i++) {
                fileNames.push(this.files[i].name);
            }
            // Update the adjacent custom file label
            $(this).next('.custom-file-label').html(fileNames.join(', ') || 'Choose file(s)');
        });
    });
</script>
<?= $this->endSection() ?>