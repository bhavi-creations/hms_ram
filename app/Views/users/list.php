<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Users List</h3>
        <a href="<?= base_url('users/register') ?>" class="btn btn-primary float-right">Add User</a>
    </div>
    <div class="card-body">
        <table id="usersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= esc($user['id']) ?></td>
                        <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['role_id']) // You can join with Role name in controller ?></td>
                        <td><?= esc($user['status']) ?></td>
                        <td>
                            <a href="<?= base_url('users/view/' . $user['id']) ?>" class="btn btn-sm btn-info">View</a>
                            <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= base_url('users/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
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
    $("#usersTable").DataTable({
        responsive: true,
        autoWidth: false,
    });
</script>
<?= $this->endSection() ?>
