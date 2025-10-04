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
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Management Level</th>
                    <th>Reports To Role</th> <!-- NEW COLUMN: Shows the Department Head this role reports to -->
                    <th>Description</th>
                    <th>Actions</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($roles as $role) : ?>
                    <tr>
                        <td><?= esc($role['id']) ?></td>
                        <td><?= esc($role['name']) ?></td>
                        <!-- Display Management Level -->
                        <td><span class="badge badge-info"><?= esc($role['management_level']) ?></span></td>
                        <!-- Display Reports To Role Name -->
                        <td>
                            <?php 
                            // Check if the role is a Team Member and if the associated manager role name exists
                            // Note: 'reports_to_role_name' must be fetched via a JOIN in your controller
                            if ($role['management_level'] === 'Team Member' && !empty($role['reports_to_role_name'])): 
                            ?>
                                <span class="badge badge-secondary"><?= esc($role['reports_to_role_name']) ?></span>
                            <?php elseif ($role['management_level'] === 'Team Member'): ?>
                                <span class="badge badge-danger">Not Assigned</span>
                            <?php else: ?>
                                ---
                            <?php endif; ?>
                        </td>
                        <td><?= esc($role['description']) ?></td>
                        <td>
                            <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <!-- Using a standard link for delete, using a custom confirmation instead of alert() -->
                            <a href="<?= base_url('roles/delete/' . $role['id']) ?>" class="btn btn-sm btn-danger delete-confirm">Delete</a>
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
    $(document).ready(function() {
        // Initialize DataTable
        $("#rolesTable").DataTable({
            responsive: true,
            autoWidth: false,
        });

        // Custom modal confirmation for delete action
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            const deleteUrl = $(this).attr('href');

            // Using window.confirm as a simple placeholder for a custom modal
            if (window.confirm("Are you sure you want to delete this role? This action cannot be undone.")) {
                window.location.href = deleteUrl;
            }
        });
    });
</script>
<?= $this->endSection() ?>
