<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Test Types</h3>
        <a href="<?= base_url('laboratory/types/create') ?>" class="btn btn-primary float-right">Add Test Type</a>
    </div>
    <div class="card-body">
        <table id="typesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>ID</th>
                    <th>Type Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($types)): ?>
                    <?php foreach($types as $type): ?>
                        <tr>
                            <td></td>
                            <td><?= esc($type['id']) ?></td>
                            <td><?= esc($type['name']) ?></td>
                            <td><?= esc($type['description']) ?></td>
                            <td>
                                <a href="<?= base_url('laboratory/types/edit/' . $type['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= base_url('laboratory/types/delete/' . $type['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this test type?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No test types found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        var table = $('#typesTable').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 }
            ],
            order: [[1, 'asc']]
        });

        table.on('order.dt search.dt', function () {
            table.column(0, {search: 'applied', order: 'applied'})
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        }).draw();
    });
</script>
<?= $this->endSection() ?>
