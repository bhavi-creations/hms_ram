<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <?= esc($title) ?> 
                    <?php if (isset($asset['id'])): ?>
                        <small class="text-muted">#<?= esc($asset['id']) ?></small>
                    <?php endif; ?>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assets') ?>">Assets & Equipment</a></li>
                    <li class="breadcrumb-item active"><?= isset($asset['id']) ? 'Edit' : 'Create' ?></li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card card-outline card-<?= isset($asset['id']) ? 'info' : 'primary' ?> shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools mr-1"></i>
                            <?= isset($asset['id']) ? 'Update Asset Details: ' . esc($asset['name']) : 'Enter New Asset Details' ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Error!</h5>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Errors!</h5>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?= form_open(isset($asset['id']) ? 'assets/update/' . esc($asset['id']) : 'assets/store') ?>
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary"><i class="fas fa-file-alt"></i> General Information</legend>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Asset Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-box"></i></span></div>
                                                <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $asset['name'] ?? '') ?>" placeholder="e.g., X-ray Machine, Patient Monitor" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category">Category <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-list-alt"></i></span></div>
                                                <input type="text" class="form-control" id="category" name="category" value="<?= old('category', $asset['category'] ?? '') ?>" placeholder="e.g., Medical Equipment, IT Hardware" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detailed description of the asset, including model/make..."><?= old('description', $asset['description'] ?? '') ?></textarea>
                                </div>

                            </fieldset>
                            
                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-info"><i class="fas fa-clipboard-list"></i> Tracking & Status</legend>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="asset_tag">Asset Tag (Unique Identifier)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-barcode"></i></span></div>
                                                <input type="text" class="form-control" id="asset_tag" name="asset_tag" value="<?= old('asset_tag', $asset['asset_tag'] ?? '') ?>" placeholder="e.g., EQ-001, IT-LAP-005">
                                            </div>
                                            <small class="form-text text-muted">Leave blank if no specific tag is assigned yet.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="location">Current Location</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                                                <input type="text" class="form-control" id="location" name="location" value="<?= old('location', $asset['location'] ?? '') ?>" placeholder="e.g., ICU Ward, Lab 1, Storage Room">
                                            </div>
                                            <small class="form-text text-muted">Where is this asset currently located?</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-check-circle"></i></span></div>
                                        <select class="form-control select2" id="status" name="status" required>
                                            <option value="Operational" <?= old('status', $asset['status'] ?? '') == 'Operational' ? 'selected' : '' ?>>Operational (Ready to use)</option>
                                            <option value="Under Maintenance" <?= old('status', $asset['status'] ?? '') == 'Under Maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                                            <option value="Out of Service" <?= old('status', $asset['status'] ?? '') == 'Out of Service' ? 'selected' : '' ?>>Out of Service</option>
                                            <option value="Disposed" <?= old('status', $asset['status'] ?? '') == 'Disposed' ? 'selected' : '' ?>>Disposed (Removed from inventory)</option>
                                        </select>
                                    </div>
                                </div>

                            </fieldset>
                            
                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-warning"><i class="fas fa-calendar-alt"></i> Date & Warranty</legend>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purchase_date">Purchase Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div>
                                                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= old('purchase_date', $asset['purchase_date'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="warranty_expiry_date">Warranty Expiry Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-times"></i></span></div>
                                                <input type="date" class="form-control" id="warranty_expiry_date" name="warranty_expiry_date" value="<?= old('warranty_expiry_date', $asset['warranty_expiry_date'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about the asset's condition, maintenance, history, etc."><?= old('notes', $asset['notes'] ?? '') ?></textarea>
                            </div>

                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                <a href="<?= base_url('assets') ?>" class="btn btn-default mr-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-<?= isset($asset['id']) ? 'info' : 'primary' ?>">
                                    <i class="fas fa-save mr-1"></i> <?= isset($asset['id']) ? 'Update Asset' : 'Add Asset' ?>
                                </button>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
                </div>
            </div>
        </div></section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for a premium-looking dropdown
        $('.select2').select2({
            theme: 'bootstrap4', // Assuming AdminLTE/Bootstrap theme for Select2
            minimumResultsForSearch: 10,
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>