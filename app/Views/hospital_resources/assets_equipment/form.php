<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assets') ?>">Assets & Equipment</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline rounded-lg shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><?= isset($asset['id']) ? 'Edit Asset Details' : 'Enter New Asset Details' ?></h3>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                    <div class="form-group">
                        <label for="name">Asset Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $asset['name'] ?? '') ?>" placeholder="e.g., X-ray Machine, Patient Monitor" required>
                    </div>

                    <div class="form-group">
                        <label for="asset_tag">Asset Tag (Unique Identifier)</label>
                        <input type="text" class="form-control" id="asset_tag" name="asset_tag" value="<?= old('asset_tag', $asset['asset_tag'] ?? '') ?>" placeholder="e.g., EQ-001, IT-LAP-005">
                        <small class="form-text text-muted">Leave blank if no specific tag.</small>
                    </div>

                    <div class="form-group">
                        <label for="category">Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category" name="category" value="<?= old('category', $asset['category'] ?? '') ?>" placeholder="e.g., Medical Equipment, IT Hardware, Furniture" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detailed description of the asset"><?= old('description', $asset['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_date">Purchase Date</label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= old('purchase_date', $asset['purchase_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warranty_expiry_date">Warranty Expiry Date</label>
                                <input type="date" class="form-control" id="warranty_expiry_date" name="warranty_expiry_date" value="<?= old('warranty_expiry_date', $asset['warranty_expiry_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?= old('location', $asset['location'] ?? '') ?>" placeholder="e.g., ICU Ward, Lab 1, Doctor's Office 3">
                        <small class="form-text text-muted">Where is this asset currently located?</small>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Operational" <?= old('status', $asset['status'] ?? '') == 'Operational' ? 'selected' : '' ?>>Operational</option>
                            <option value="Under Maintenance" <?= old('status', $asset['status'] ?? '') == 'Under Maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                            <option value="Out of Service" <?= old('status', $asset['status'] ?? '') == 'Out of Service' ? 'selected' : '' ?>>Out of Service</option>
                            <option value="Disposed" <?= old('status', $asset['status'] ?? '') == 'Disposed' ? 'selected' : '' ?>>Disposed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about the asset's condition, history, etc."><?= old('notes', $asset['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> <?= isset($asset['id']) ? 'Update Asset' : 'Add Asset' ?>
                        </button>
                        <a href="<?= base_url('assets') ?>" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
