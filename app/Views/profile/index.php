<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <!-- Profile Card Column (Displays Picture and Summary) -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    
                    <!-- FIX 1: Use the calculated $profileImageUrl from the controller -->
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="<?= esc($profileImageUrl) ?>"
                             alt="User Profile Picture"
                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ced4da;">
                    </div>
                    
                    <!-- FIX 2: Use the universal $profile array for names -->
                    <h3 class="profile-username text-center mt-3"><?= esc($profile['first_name'] . ' ' . $profile['last_name']) ?></h3>
                    
                    <!-- Display Role Name from Controller -->
                    <p class="text-muted text-center"><?= esc($profile['specialization'] ?? $roleName ?? 'User') ?></p>
                    
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <!-- Display specific ID if available, otherwise default to generic ID -->
                            <b>User ID</b> 
                            <a class="float-right">
                                <?php
                                // Attempt to find a specific ID code based on known roles, or fall back to user table ID
                                if ($userRoleId == 2 && isset($profile['doctor_id_code'])) {
                                    echo esc($profile['doctor_id_code']);
                                } elseif ($userRoleId == 8 && isset($profile['salesperson_id'])) {
                                    echo esc($profile['salesperson_id']);
                                } else {
                                    echo esc($profile['id'] ?? 'N/A');
                                }
                                ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>Role</b> <a class="float-right"><?= esc($roleName) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right"><?= esc($profile['email'] ?? 'N/A') ?></a>
                        </li>
                    </ul>
                    
                    <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-block"><b>Edit Profile</b></a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Bio & Contact</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Bio Summary</strong>
                    <p class="text-muted">
                        <?= esc($profile['bio'] ?? 'No bio provided.') ?>
                    </p>
                    <hr>
                    <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                    <!-- Phone number check is simple due to merged $profile array -->
                    <p class="text-muted"><?= esc($profile['phone'] ?? $profile['phone_number'] ?? 'N/A') ?></p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                    <p class="text-muted"><?= esc($profile['address'] ?? 'N/A') ?></p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-4 -->

        <!-- Main Profile Content Column (Tabs for details/settings) -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Full Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab">Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#signature" data-toggle="tab">Signature</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Full Details Tab -->
                        <div class="active tab-pane" id="details">
                            <h5 class="mb-3">Personal and Professional Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <!-- FIX 3: Use $profile array -->
                                        <li class="list-group-item"><b>Date of Birth :</b> <span class="float-right"><?= esc($profile['date_of_birth'] ?? 'N/A') ?></span></li>
                                        <li class="list-group-item"><b>Gender :</b> <span class="float-right"><?= esc($profile['gender'] ?? 'N/A') ?></span></li>
                                        <li class="list-group-item"><b>Qualification :</b> <span class="float-right"><?= esc($profile['qualification'] ?? 'N/A') ?></span></li>
                                        <li class="list-group-item"><b>Joined On :</b> <span class="float-right"><?= esc($profile['created_at'] ?? 'N/A') ?></span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <!-- FIX 3: Use $profile array -->
                                        <li class="list-group-item"><b>Medical License No. :</b> <span class="float-right"><?= esc($profile['medical_license_no'] ?? 'N/A') ?></span></li>
                                        <li class="list-group-item"><b>Registration Number :</b> <span class="float-right"><?= esc($profile['registration_number'] ?? 'N/A') ?></span></li>
                                        <li class="list-group-item"><b>OPD Fee :</b> <span class="float-right">₹<?= esc(number_format($profile['opd_fee'] ?? 0, 2)) ?></span></li>
                                        <li class="list-group-item"><b>Status :</b> <span class="float-right"><?= esc($profile['status'] ?? 'N/A') ?></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /#details -->

                        <!-- Documents Tab Placeholder -->
                        <div class="tab-pane" id="documents">
                            <p class="text-muted">This section is for displaying user-specific documents (e.g., certificates, licenses). The logic to fetch and display files for all user types would need to be centralized here.</p>
                        </div>
                        <!-- /#documents -->
                        
                        <!-- Signature Tab -->
                        <div class="tab-pane text-center" id="signature">
                            <?php if (!empty($profile['signature_image'])): ?>
                                <h5 class="mt-3 mb-2">Saved Digital Signature</h5>
                                <?php 
                                // Replicate path logic from controller for signature display
                                // This block determines the correct folder based on the role ID
                                $roleFolder = 'users';
                                if ($userRoleId == 2) {
                                    $roleFolder = 'doctors';
                                } elseif ($userRoleId == 8) {
                                    $roleFolder = 'pharmacy_sales_persons';
                                }
                                $signatureImageUrl = base_url('public/uploads/' . $roleFolder . '/' . urlencode($profile['signature_image'])); 
                                ?>
                                <img src="<?= esc($signatureImageUrl) ?>" class="img-fluid" style="max-width: 250px; border: 1px solid #ddd; padding: 5px;" alt="Digital Signature">
                            <?php else: ?>
                                <p class="text-muted mt-3">No digital signature uploaded for this profile type.</p>
                                <a href="<?= base_url('profile/edit') ?>" class="btn btn-sm btn-outline-primary mt-2">Upload Signature</a>
                            <?php endif; ?>
                        </div>
                        <!-- /#signature -->

                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-8 -->
    </div>
</div>
<?= $this->endSection() ?>
