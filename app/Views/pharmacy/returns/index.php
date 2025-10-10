<?= $this->extend('layouts/main') ?> // Make sure this points to your main layout file

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Returns</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy</li>
                        <li class="breadcrumb-item active">Returns</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Medicine Returns List</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('pharmacy/returns/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Initiate New Return
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                     

                    <table id="manageReturnsTable"   class="table table-bordered table-striped " >
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Sale ID</th>
                                <th>Medicine Name</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Notes</th>

                                <th>Return Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($returns) && is_array($returns)) : ?>
                                <?php $serial = 1; ?>
                                <?php foreach ($returns as $return) : ?>
                                    <tr>
                                        <td><?= esc($serial++) ?></td>
                                        <td><?= esc($return['invoice_number'] ?? 'N/A') ?></td>
                                        <td><?= esc($return['medicine_name']) ?></td>
                                        <td><?= esc($return['quantity']) ?></td>
                                        <td><?= esc($return['reason']) ?></td>
                                        <td><?= esc($return['status']) ?></td>
                                        <td>
                                            <?php
                                            $createNote = '';
                                            $approvalNote = '';

                                            if (!empty($return['notes'])) {
                                                $parts = explode("\nApproval/Rejection Notes:", $return['notes']);
                                                $createNote = trim($parts[0]);
                                                $approvalNote = isset($parts[1]) ? trim($parts[1]) : '';
                                            }
                                            ?>

                                            <?php if ($createNote !== ''): ?>
                                                <p><strong>Return Rqst :</strong> <?= esc($createNote) ?></p>
                                            <?php endif; ?>

                                            <?php if ($approvalNote !== ''): ?>
                                                <p><strong>Approval Note :</strong> <?= esc($approvalNote) ?></p>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= esc(date('Y-m-d', strtotime($return['return_date']))) ?></td>
                                        <td>
                                            <?php
                                            $status = strtolower($return['status']);
                                            $btnClass = 'btn-warning';
                                            if ($status === 'approved') {
                                                $btnClass = 'btn-success';
                                            } elseif ($status === 'rejected') {
                                                $btnClass = 'btn-danger';
                                            }
                                            ?>
                                            <a href="<?= site_url('pharmacy/returns/approve/' . $return['id']) ?>" class="btn btn-sm <?= $btnClass ?>">
                                                <?= ucfirst($status ?: 'Pending') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No returns found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
 
<script>
    $(document).ready(function() {
        $('#manageReturnsTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            order: [
                [1, 'asc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>
