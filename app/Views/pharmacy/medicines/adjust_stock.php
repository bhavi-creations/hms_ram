<?= $this->extend('layouts/main') ?>

<?= $this->section('content_header') ?>
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= esc($title) ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/dashboard') ?>">Pharmacy</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/medicines') ?>">Medicines</a></li>
                <li class="breadcrumb-item active"><?= esc($title) ?></li>
            </ol>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Adjust Medicine Stock</h3>
                    </div>
                    <?php if (empty($batch) || empty($medicine)): ?>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                Please select a medicine and batch to adjust stock from the <a href="<?= site_url('pharmacy/medicines') ?>">Medicines list</a>.
                            </div>
                        </div>
                    <?php else: ?>
                    <!-- Form for stock adjustment -->
                    <form action="<?= site_url('pharmacy/medicines/adjust-stock') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="batch_id" value="<?= esc($batch['id']) ?>">
                        
                        <div class="card-body">
                            <!-- This block displays validation errors after a form submission -->
                            <?php if (session('validation')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <h5><i class="icon fas fa-ban"></i> Validation Failed!</h5>
                                    <ul>
                                        <?php foreach (session('validation')->getErrors() as $field => $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="medicine_name">Medicine</label>
                                <input type="text" class="form-control" id="medicine_name" value="<?= esc($medicine['brand_name']) ?> (<?= esc($medicine['generic_name']) ?>)" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="batch_number">Batch Number</label>
                                <input type="text" class="form-control" id="batch_number" value="<?= esc($batch['batch_number']) ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="current_stock">Current Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="current_stock" name="current_stock" value="<?= old('current_stock', $batch['current_stock']) ?>" placeholder="Enter the new stock quantity" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter notes about the stock adjustment"><?= old('notes') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit Adjustment</button>
                            <a href="<?= site_url('pharmacy/medicines') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script>
     $(document).ready(function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php elseif (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>'
            });
        <?php endif; ?>
    });
</script> -->
<?= $this->endSection() ?>
