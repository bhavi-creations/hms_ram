<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Register New Staff Member</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('staff') ?>">Staff List</a></li>
                    <li class="breadcrumb-item active">Register</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Staff Details</h5>
                    </div>
                    <?= form_open('staff/save') ?>
                    <div class="card-body">
                        <!-- Display Validation Errors -->
                        <?php if (session()->get('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session()->get('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="role_id">Staff Role <span class="text-danger">*</span></label>
                                <!-- Added ID to target with JS -->
                                <select name="role_id" id="role_id" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <?php 
                                            // Determine if this role was selected previously (on validation error)
                                            $selected = old('role_id') == $role['id'] ? 'selected' : ''; 
                                        ?>
                                        <!-- Store management level in a data attribute -->
                                        <option value="<?= $role['id'] ?>" data-level="<?= esc($role['management_level']) ?>" <?= $selected ?>>
                                            <?= esc($role['name']) ?> (<?= esc($role['management_level']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- START NEW CONDITIONAL FIELD: Department Head Assignment -->
                            <div class="form-group col-md-6" id="manager_assignment_block" style="display: none;">
                                <label for="manager_id">Assign Department Head (Manager)</label>
                                <select name="manager_id" class="form-control">
                                    <option value="">-- No Manager Assigned --</option>
                                    <?php foreach ($departmentHeads as $head): ?>
                                        <?php 
                                            // Check if this head was selected previously
                                            $selected = old('manager_id') == $head['id'] ? 'selected' : ''; 
                                        ?>
                                        <option value="<?= $head['id'] ?>" <?= $selected ?>>
                                            <?= esc($head['first_name'] . ' ' . $head['last_name']) ?> (ID: <?= esc($head['id']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Only required for 'Team Member' roles.</small>
                            </div>
                            <!-- END NEW CONDITIONAL FIELD -->
                            
                            <div class="form-group col-md-6" id="username_block">
                                <label for="username">Username (for login) <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="<?= old('phone_number') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control"><?= old('address') ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Register Staff
                        </button>
                        <a href="<?= base_url('staff') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const $roleSelect = $('#role_id');
        const $managerBlock = $('#manager_assignment_block');
        
        // This array maps role IDs to their management levels. 
        // We generate it here so we don't have to look up the data-attribute every time, 
        // though using the data-attribute is simpler for this single file view.
        // We'll stick to the simpler data-attribute check:

        function toggleManagerField() {
            // Get the selected option element
            const selectedOption = $roleSelect.find('option:selected');
            
            // Get the management level from the data attribute
            const managementLevel = selectedOption.data('level');

            // Check if the role is 'Team Member'
            if (managementLevel === 'Team Member') {
                $managerBlock.show();
                // Optionally, you might want to require this field if it's visible, 
                // but we will keep it optional for now to allow unassigned team members.
            } else {
                $managerBlock.hide();
                // Clear the value when hidden to prevent saving an incorrect manager_id
                $managerBlock.find('select').val('');
            }
        }

        // 1. Attach event listener to the role dropdown
        $roleSelect.on('change', toggleManagerField);

        // 2. Run the function once on load to handle validation errors (if old('role_id') was set)
        toggleManagerField();
    });
</script>
<?= $this->endSection() ?>
