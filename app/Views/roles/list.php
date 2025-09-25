<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles List</h3>
        <a href="<?= base_url('roles/create') ?>" class="btn btn-primary float-right">Add Role</a>
    </div>
    <div class="card-body">
        <table id="rolesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <!-- <th>S.No.</th> -->
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>   
            </thead>
            <tbody>
                <?php foreach ($roles as $role) : ?>
                    <tr>
                          <!-- <td></td> -->
                        <td><?= esc($role['id']) ?></td>
                        <td><?= esc($role['name']) ?></td>
                        <td><?= esc($role['description']) ?></td>
                        <td>
                            <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= base_url('roles/delete/' . $role['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $("#rolesTable").DataTable({
        responsive: true,
        autoWidth: false,
    });
 
    // var t = $("#rolesTable").DataTable({
    //     responsive: true,
    //     autoWidth: false,
    //     columnDefs: [{
    //         searchable: false,
    //         orderable: false,
    //         targets: 0   
    //     }],
    //     order: [[1, 'asc']]  
    // });

    // t.on('order.dt search.dt', function () {
    //     t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
    //         cell.innerHTML = i + 1;
    //     });
    // }).draw();
</script>



<?= $this->endSection() ?>