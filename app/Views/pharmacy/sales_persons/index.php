<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Manage Salespersons</h1>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="<?= site_url('pharmacy/salespersons/create') ?>" class="btn btn-primary">Add Salesperson</a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <table class="table table-bordered table-striped" id="salespersonTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Salesperson Code</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sales_persons)): ?>
                            <?php foreach ($sales_persons as $key => $person): ?>
                                <tr>
                                    <td><?= esc($key + 1) ?></td>
                                    <td><?= esc($person['salesperson_id']) ?></td>
                                    <td><?= esc($person['first_name']) ?></td>
                                    <td><?= esc($person['last_name']) ?></td>
                                    <td><?= esc($person['phone']) ?></td>
                                    <td><?= esc($person['email']) ?></td>
                                    <td>
                                        <?php if ($person['status'] == 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('pharmacy/salespersons/edit/' . $person['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="<?= site_url('pharmacy/salespersons/toggle-status/' . $person['id']) ?>" class="btn btn-sm <?= ($person['status'] == 1) ? 'btn-danger' : 'btn-success' ?>" onclick="return confirm('Are you sure you want to change the status of this salesperson?');">
                                            <?= ($person['status'] == 1) ? 'Deactivate' : 'Activate' ?>
                                        </a>
                                        <a href="<?= site_url('pharmacy/salespersons/delete/' . $person['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to permanently delete this salesperson?');">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No salespersons found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#salespersonTable').DataTable({
            "order": [[ 1, "desc" ]]
        });
    });
</script>
<?= $this->endSection() ?>