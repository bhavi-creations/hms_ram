<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lab Tests</h3>
        <a href="<?= base_url('laboratory/tests/create') ?>" class="btn btn-primary float-right">Add Lab Test</a>
    </div>
    <div class="card-body">
        <table id="testsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>ID</th>
                    <th>Test Name</th>
                    <th>Description</th>
                    <th>Test Type</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tests)): ?>
                    <?php foreach($tests as $test): ?>
                        <tr>
                            <td></td> <!-- Will be populated dynamically -->
                            <td><?= esc($test['id']) ?></td>
                            <td><?= esc($test['name']) ?></td>
                            <td><?= esc($test['description']) ?></td>
                            <td><?= esc($test['test_type_name'] ?? $test['test_type_id']) ?></td>
                            <td><?= esc($test['price']) ?></td>
                            <td>
                                <a href="<?= base_url('laboratory/tests/edit/' . $test['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= base_url('laboratory/tests/delete/' . $test['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this test?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No lab tests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var table = $('#testsTable').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 } // S.No column not searchable or orderable
            ],
            order: [[1, 'asc']] // Initial sort by ID
        });

        // Auto number for S.No column on order and search
        table.on('order.dt search.dt', function() {
            table.column(0, { order: 'applied', search: 'applied'}).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
<?= $this->endSection() ?>
