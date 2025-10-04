<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add New Role</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('roles/save') ?>" method="post">
            <div class="mb-3">
                <label for="name">Role Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter role name">
            </div>

            <!-- Management Level Dropdown (Controls Visibility) -->
            <div class="mb-3">
                <label for="management_level">Management Level</label>
                <select name="management_level" id="management_level" class="form-control" required>
                    <option value="">-- Select Level --</option>
                    <option value="Executive Leader">Executive Leader</option>
                    <option value="Senior Management">Senior Management</option>
                    <option value="Department Head">Department Head</option>
                    <option value="Team Member">Team Member</option>
                </select>
            </div>
            <!-- End Management Level Dropdown -->

            <!-- NEW: Reports To Role Dropdown (Conditional) -->
            <!-- This field is visible ONLY if management_level is 'Team Member' -->
            <div class="mb-3 d-none" id="reports_to_container">
                <label for="reports_to_role_id">Reports To (Department Head)</label>
                <select name="reports_to_role_id" id="reports_to_role_id" class="form-control">
                    <option value="">-- Select Manager Role --</option>
                    <?php 
                    // Assume $departmentHeadRoles is passed from the controller, 
                    // containing roles with management_level = 'Department Head'.
                    if (isset($departmentHeadRoles) && is_array($departmentHeadRoles)):
                        foreach ($departmentHeadRoles as $deptRole): 
                    ?>
                        <option value="<?= esc($deptRole['id']) ?>"><?= esc($deptRole['name']) ?></option>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </select>
                <small class="form-text text-muted">Select the Department Head role this team member reports to. This defines who can view their logs.</small>
            </div>
            <!-- End Reports To Role Dropdown -->

            <div class="mb-3">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Enter role description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="<?= base_url('roles') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('management_level');
    const reportsToContainer = document.getElementById('reports_to_container');
    const reportsToSelect = document.getElementById('reports_to_role_id');

    function toggleReportsToField() {
        if (levelSelect.value === 'Team Member') {
            reportsToContainer.classList.remove('d-none');
            // Make selection mandatory for Team Members
            reportsToSelect.setAttribute('required', 'required'); 
        } else {
            reportsToContainer.classList.add('d-none');
            // Remove requirement for non-Team Members
            reportsToSelect.removeAttribute('required');
            reportsToSelect.value = ''; // Clear selection when hidden
        }
    }

    levelSelect.addEventListener('change', toggleReportsToField);

    // Initial check on page load (in case browser remembered a selection)
    toggleReportsToField();
});
</script>

<?= $this->endSection() ?>
