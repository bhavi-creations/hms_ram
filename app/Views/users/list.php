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
                    <!-- NEW: S.no column added first -->
                    <th style="width: 5%;">S.no</th> 
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; // Initialize the serial number counter ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <!-- NEW: Display and increment S.no -->
                        <td class="text-center"><?= $sn++ ?></td> 
                        <td><?= esc($user['id']) ?></td>
                        <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['role_name']) // You can join with Role name in controller ?></td>
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
        // Since we added S.no as the first column, we tell DataTables not to order by it initially.
        // It will usually default to the ID column, which is still displayed second.
        "order": [[ 1, "asc" ]] 
    });
</script>
<?= $this->endSection() ?>
