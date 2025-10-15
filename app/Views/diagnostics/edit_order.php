<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-edit mr-2 text-info"></i> <?= esc($title) ?>: <span class="text-primary"><?= esc($order['order_id_code']) ?></span>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/orders') ?>">Orders</a></li>
                    <li class="breadcrumb-item active">Edit Order</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-info shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Update Order Details</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-sm btn-default"><i class="fas fa-times-circle mr-1"></i> Cancel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Error!</h5>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <h4 class="text-secondary mb-3"><i class="fas fa-user-injured mr-1"></i> Patient Information (Read-Only)</h4>
                        <div class="p-3 mb-4 border rounded-lg bg-light">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Patient Name:</dt>
                                <dd class="col-sm-9 font-weight-bold text-info"><?= esc($order['patient_name'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-3">Patient ID:</dt>
                                <dd class="col-sm-9"><?= esc($order['patient_id_code'] ?? 'N/A') ?></dd>
                            </dl>
                        </div>
                        
                        <form action="<?= base_url('diagnostics/orders/update/' . $order['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="patient_id" value="<?= esc($order['patient_id']) ?>">
                            
                            <div class="form-group mb-4">
                                <label for="test_ids">Diagnostic Tests (Editable) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-vials"></i></span></div>
                                    <select name="test_ids[]" id="test_ids" class="form-control select2bs4 <?= session('errors.test_ids') ? 'is-invalid' : '' ?>" multiple required style="width: 100%;">
                                        <?php foreach ($diagnosticsTests as $test): ?>
                                            <option 
                                                value="<?= esc($test['id']) ?>" 
                                                <?= in_array($test['id'], set_value('test_ids', $currentTestIds)) ? 'selected' : '' ?>>
                                                <?= esc($test['test_name'] ?? 'Name Missing') . ' - (Price: ₹' . number_format($test['price'] ?? 0, 2) . ')' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.test_ids')): ?><div class="invalid-feedback d-block"><?= session('errors.test_ids') ?></div><?php endif; ?>
                                </div>
                                <small class="form-text text-muted mt-2">Add or remove tests from this order. Use the search bar to find tests.</small>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="remarks">Remarks / Instructions</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clipboard"></i></span></div>
                                    <textarea name="remarks" id="remarks" class="form-control <?= session('errors.remarks') ? 'is-invalid' : '' ?>" 
                                              rows="4" placeholder="Enter any specific instructions or remarks for this order."><?= esc(set_value('remarks', $order['remarks'] ?? '')) ?></textarea>
                                    <?php if (session('errors.remarks')): ?><div class="invalid-feedback"><?= session('errors.remarks') ?></div><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end pt-3">
                                <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-default mr-2 px-4"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-info px-4"><i class="fas fa-sync-alt"></i> Update Order</button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 4 theme
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: 'Select one or more tests',
            allowClear: true
        });
    });
</script>
<?= $this->endSection() ?>