<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?></h3>
        <a href="<?= base_url('diagnostics/tests/create') ?>" class="btn btn-primary float-right">Add New Test</a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (empty($tests)) : ?>
            <p>No diagnostic tests found.</p>
        <?php else : ?>
            <table id="testsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Test Name</th>
                        <th>Test Type</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sno = 1; ?>
                    <?php foreach ($tests as $test) : ?>
                        <tr>
                            <td><?= $sno++ ?></td>
                            <td><?= esc($test['test_name']) ?></td>
                            <td><?= esc($test['test_type']) ?></td>
                            <td><?= esc(number_format($test['price'], 2)) ?></td>
                            <td>
                                <a href="<?= base_url('diagnostics/tests/edit/' . $test['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                <a href="<?= base_url('diagnostics/tests/delete/' . $test['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this test?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var table = $('#testsTable').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                searchable: false,
                targets: 0
            }],
            order: [
                [1, 'asc']
            ]
        });

        table.on('order.dt search.dt', function() {
            table.column(0, {
                order: 'applied',
                search: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
<?= $this->endSection() ?>
