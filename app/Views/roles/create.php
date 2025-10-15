<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-tag mr-2"></i> Add New Role
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('roles') ?>">Roles</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card card-outline card-primary shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">Define Role Properties</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Errors!</h5>
                                <ul>
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('roles/save') ?>" method="post">
                            <?= csrf_field() ?>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-primary"><i class="fas fa-info-circle"></i> Basic Role Information</legend>
                                
                                <div class="form-group">
                                    <label for="name">Role Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tag"></i></span></div>
                                        <input type="text" name="name" id="name" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" value="<?= old('name') ?>" required placeholder="e.g., Administrator, Senior Nurse, Radiologist">
                                        <?php if (session('errors.name')): ?><div class="invalid-feedback"><?= session('errors.name') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the role's responsibilities and privileges"><?= old('description') ?></textarea>
                                </div>
                            </fieldset>

                            <fieldset class="p-3 border border-secondary rounded-lg mb-4">
                                <legend class="w-auto px-2 h5 text-info"><i class="fas fa-sitemap"></i> Hierarchy</legend>
                                
                                <div class="form-group">
                                    <label for="management_level">Management Level <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-level-up-alt"></i></span></div>
                                        <select name="management_level" id="management_level" class="form-control select2-level <?= session('errors.management_level') ? 'is-invalid' : '' ?>" required>
                                            <option value="">-- Select Level --</option>
                                            <option value="Executive Leader" <?= old('management_level') == 'Executive Leader' ? 'selected' : '' ?>>Executive Leader</option>
                                            <option value="Senior Management" <?= old('management_level') == 'Senior Management' ? 'selected' : '' ?>>Senior Management</option>
                                            <option value="Department Head" <?= old('management_level') == 'Department Head' ? 'selected' : '' ?>>Department Head</option>
                                            <option value="Team Member" <?= old('management_level') == 'Team Member' ? 'selected' : '' ?>>Team Member</option>
                                        </select>
                                        <?php if (session('errors.management_level')): ?><div class="invalid-feedback"><?= session('errors.management_level') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="reports_to_container">
                                    <label for="reports_to_role_id">Reports To Role (Direct Supervisor) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-shield"></i></span></div>
                                        <select name="reports_to_role_id" id="reports_to_role_id" class="form-control select2-reports-to">
                                            <option value="">-- Select Manager Role --</option>
                                            <?php 
                                            // Assume $departmentHeadRoles contains roles eligible to be managers
                                            if (isset($departmentHeadRoles) && is_array($departmentHeadRoles)):
                                                $oldReportsToId = old('reports_to_role_id');
                                                foreach ($departmentHeadRoles as $deptRole): 
                                            ?>
                                                <option value="<?= esc($deptRole['id']) ?>" <?= $oldReportsToId == $deptRole['id'] ? 'selected' : '' ?>><?= esc($deptRole['name']) ?></option>
                                            <?php 
                                                endforeach;
                                            endif; 
                                            ?>
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">Mandatory for 'Team Member'. Select the manager's role they report to.</small>
                                </div>
                                </fieldset>
                            
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                <a href="<?= base_url('roles') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Role</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for Management Level
        const $levelSelect = $('#management_level').select2({
            theme: 'bootstrap4',
            placeholder: '-- Select Level --',
            minimumResultsForSearch: Infinity,
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for Reports To Role
        const $reportsToSelect = $('#reports_to_role_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Select Manager Role --',
            allowClear: true,
            width: '100%'
        });

        const $reportsToContainer = $('#reports_to_container');
        
        function toggleReportsToField() {
            if ($levelSelect.val() === 'Team Member') {
                $reportsToContainer.removeClass('d-none');
                // Set the 'required' attribute on the original select element
                $reportsToSelect.attr('required', 'required'); 
            } else {
                $reportsToContainer.addClass('d-none');
                // Remove the 'required' attribute and clear the selection when hiding
                $reportsToSelect.removeAttr('required');
                $reportsToSelect.val(null).trigger('change'); // Clear Select2 selection
            }
        }

        // Attach event listener to the Select2 change event
        $levelSelect.on('change', toggleReportsToField);

        // Initial check on page load (important for preserving old values on validation failure)
        toggleReportsToField();
    });
</script>
<?= $this->endSection() ?>