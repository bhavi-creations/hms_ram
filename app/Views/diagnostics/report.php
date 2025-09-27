<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?>: <?= esc($order['order_id_code']) ?></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>**Patient Information**</h5>
                <p><strong>Patient ID:</strong> <?= esc($order['patient_id_code']) ?></p>
                <p><strong>Patient Name:</strong> <?= esc($order['patient_name']) ?></p>
                <p><strong>Phone Number:</strong> <?= esc($order['patient_phone']) ?></p>
            </div>
            <div class="col-md-6">
                <h5>**Order Information**</h5>
                <p><strong>Order ID:</strong> <?= esc($order['order_id_code']) ?></p>
                <p><strong>Ordered By:</strong> <?= esc($order['doctor_name']) ?></p>
                <p><strong>Order Date:</strong> <?= esc($order['order_date']) ?></p>
                <p><strong>Status:</strong> <?= esc($order['status']) ?></p>
            </div>
        </div>

        <hr>

        <h5>**Test Results**</h5>
        <?php foreach ($orderItems as $item): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Test: <?= esc($item['test_name']) ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Result:</strong> <?= esc($item['result']) ?></p>
                    <p><strong>Status:</strong> <?= esc($item['status']) ?></p>
                    
                    <?php 
                    $itemFiles = array_filter($orderFiles, function($file) use ($item) {
                        return $file['diagnostics_order_item_id'] == $item['id'];
                    });
                    ?>
                    <?php if (!empty($itemFiles)): ?>
                        <h6>Files:</h6>
                        <div class="row">
                            <?php foreach ($itemFiles as $file): ?>
                                <div class="col-md-4 mb-3">
                                    <a href="<?= base_url($file['file_path']) ?>" target="_blank">
                                        <?php if (strpos($file['file_type'], 'image') !== false): ?>
                                            <img src="<?= base_url($file['file_path']) ?>" class="img-fluid img-thumbnail" alt="Test Image">
                                        <?php else: ?>
                                            <i class="fas fa-file-alt fa-5x"></i>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (!empty($order['remarks'])): ?>
            <h5>**Remarks**</h5>
            <p><?= esc($order['remarks']) ?></p>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>
<?= $this->endSection() ?>
