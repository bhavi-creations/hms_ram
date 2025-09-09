<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Approve/Reject Return Request</h1>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pharmacy/returns') ?>">Returns</a></li>
                <li class="breadcrumb-item active">Approval</li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <table class="table table-bordered">
                <tr>
                    <th>Invoice Number</th>
                    <td><?= esc($returnRequest['sale_id'] ? $returnRequest['sale_id'] : ($returnRequest['billing_id'] ?? 'N/A')) ?></td>
                </tr>
                <tr>
                    <th>Medicine</th>
                    <td><?= esc($returnRequest['medicine_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Batch Number</th>
                    <td><?= esc($returnRequest['batch_number'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Quantity Returned</th>
                    <td><?= esc($returnRequest['quantity_returned']) ?></td>
                </tr>
                <tr>
                    <th>Return Reason</th>
                    <td><?= nl2br(esc($returnRequest['return_reason'])) ?></td>
                </tr>
                <tr>
                    <th>Requested By</th>
                    <td><?= esc($returnRequest['requested_by_user_id']) ?></td>
                </tr>
                <tr>
                    <th>Return Date</th>
                    <td><?= esc(date('Y-m-d H:i', strtotime($returnRequest['return_date']))) ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= esc(ucfirst($returnRequest['approval_status'])) ?></td>
                </tr>
            </table>

            <?= form_open('pharmacy/returns/processApproval/' . $returnRequest['id']) ?>
            <div class="form-group">
                <label for="approval_status">Approval Status <span class="text-danger">*</span></label>
                <select name="approval_status" id="approval_status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="approved">Approve</option>
                    <option value="rejected">Reject</option>
                </select>
            </div>

            <div class="form-group">
                <label for="approval_notes">Approval/Rejection Notes</label>
                <textarea name="approval_notes" id="approval_notes" rows="4" class="form-control" placeholder="Add any notes here"><?= old('approval_notes') ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Submit</button>
            <a href="<?= site_url('pharmacy/returns') ?>" class="btn btn-secondary">Cancel</a>
            <?= form_close() ?>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
