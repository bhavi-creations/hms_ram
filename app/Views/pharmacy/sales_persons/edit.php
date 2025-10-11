<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Salesperson: <?= esc($salesperson['first_name'] . ' ' . $salesperson['last_name']) ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <!-- Display validation errors -->
                        <?= implode('<br>', session()->getFlashdata('errors')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <!-- Shows generic errors, crucial for file handling exceptions -->
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?= form_open_multipart('pharmacy/salespersons/update/' . $salesperson['id']) ?>
                
                <div class="row">
                    <!-- Left Column: Image and Image Upload Field (4/12) -->
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <label>Current Profile Picture</label>
                            <?php 
                                $profilePicture = $salesperson['profile_picture'] ?? null;
                                
                                // FIX: Using the user-specified path including 'public/'
                                $imagePath = !empty($profilePicture) 
                                    ? base_url('public/uploads/sales_persons/' . $profilePicture) 
                                    : base_url('dist/img/default-user.png');
                            ?>
                            <img src="<?= esc($imagePath) ?>" 
                                 alt="Current profile picture"
                                 class="img-fluid img-circle mb-3"
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='<?= base_url('dist/img/default-user.png') ?>';">
                            
                            <input type="hidden" name="old_profile_picture" value="<?= esc($profilePicture) ?>">
                        </div>

                        <!-- New File Upload Field -->
                        <div class="form-group">
                            <label for="profile_picture">Change Profile Picture</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture">
                                <label class="custom-file-label" for="profile_picture">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Max size 2MB. Allowed: JPG, JPEG, PNG.</small>
                        </div>
                    </div>

                    <!-- Right Column: Personal Details (8/12) -->
                    <div class="col-md-8">
                        <!-- Salesperson Code (Readonly for reference) -->
                        <div class="form-group">
                            <label>Salesperson Code</label>
                            <input type="text" class="form-control" value="<?= esc($salesperson['salesperson_id']) ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $salesperson['first_name']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $salesperson['last_name']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="<?= old('phone', $salesperson['phone']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email', $salesperson['email']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"><?= old('address', $salesperson['address']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option value="1" <?= (old('status', $salesperson['status']) == 1) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= (old('status', $salesperson['status']) == 0) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div> <!-- End of row -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Salesperson</button>
                    <a href="<?= site_url('pharmacy/salespersons/show/' . $salesperson['id']) ?>" class="btn btn-secondary float-right"><i class="fas fa-eye"></i> View Profile</a>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Script for Custom File Input to display the selected file name -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_picture');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                // Get the name of the file selected
                let fileName = e.target.files[0].name;
                // Find the next sibling (the label) and set its text to the file name
                let nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        }
    });
</script>
<?= $this->endSection() ?>
