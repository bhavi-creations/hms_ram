<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Details</h3>
        <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-warning float-right">Edit User</a>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>ID</th>
                <td><?= esc($user['id']) ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= esc($user['email']) ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?= esc($user['role_id']) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= esc($user['status']) ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?= esc($user['phone_number']) ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?= esc($user['address']) ?></td>
            </tr>
            <tr>
                <th>Last Login</th>
                <td><?= esc($user['last_login']) ?></td>
            </tr>
        </table>
        <a href="<?= base_url('users') ?>" class="btn btn-secondary mt-3">Back to Users List</a>
    </div>
</div>
<?= $this->endSection() ?>
