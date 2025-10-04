<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Role: <?= esc($role['name']) ?></h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('roles/update/' . $role['id']) ?>" method="post">
            <input type="hidden" name="id" value="<?= esc($role['id']) ?>">
            
            <div class="mb-3">
                <label for="name">Role Name</label>
                <input type="text" name="name" id="name" value="<?= esc($role['name']) ?>" class="form-control" required>
            </div>
            
            <!-- Management Level Dropdown (Controls Visibility) -->
            <div class="mb-3">
                <label for="management_level">Management Level</label>
                <select name="management_level" id="management_level" class="form-control" required>
                    <?php 
                    // Safely retrieve the current level for pre-selection
                    $currentLevel = esc($role['management_level'] ?? 'Team Member'); 
                    $levels = ['Executive Leader', 'Senior Management', 'Department Head', 'Team Member'];
                    ?>
                    <option value="">-- Select Level --</option>
                    <?php foreach ($levels as $level) : ?>
                        <option value="<?= $level ?>" <?= ($currentLevel === $level) ? 'selected' : '' ?>>
                            <?= $level ?>
                        </option>
                    <?php endforeach; ?>
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
                    // Assume $departmentHeadRoles is passed from the controller
                    $currentManagerId = esc($role['reports_to_role_id'] ?? null);
                    if (isset($departmentHeadRoles) && is_array($departmentHeadRoles)):
                        foreach ($departmentHeadRoles as $deptRole): 
                    ?>
                        <option value="<?= esc($deptRole['id']) ?>" <?= ($currentManagerId == $deptRole['id']) ? 'selected' : '' ?>>
                            <?= esc($deptRole['name']) ?>
                        </option>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </select>
                <small class="form-text text-muted">Select the Department Head role this team member reports to.</small>
            </div>
            <!-- End Reports To Role Dropdown -->
            
            <div class="mb-3">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= esc($role['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
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
        // If "Team Member" is selected, show the Reports To field
        if (levelSelect.value === 'Team Member') {
            reportsToContainer.classList.remove('d-none');
            reportsToSelect.setAttribute('required', 'required'); 
        } else {
            reportsToContainer.classList.add('d-none');
            reportsToSelect.removeAttribute('required');
            // Do not clear value here on edit, as it might be needed by PHP if the user cancels the change
            // reportsToSelect.value = ''; 
        }
    }

    levelSelect.addEventListener('change', toggleReportsToField);

    // Initial check on page load to set the initial state based on current role data
    toggleReportsToField();
});
</script>
<?= $this->endSection() ?>
