<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Salesperson Profile: <?= esc($person['first_name'] . ' ' . $person['last_name']) ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Image Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <?php 
                                // Safely determine the profile picture path.
                                $profilePicture = $person['profile_picture'] ?? null;
                                // FIX: Changed 'uploads/salespersons/' to 'uploads/sales_persons/' 
                                // to match the file system path provided by the user.
                                $imagePath = !empty($profilePicture) 
                                    ? base_url('public/uploads/sales_persons/' . $profilePicture) 
                                    : base_url('dist/img/default-user.png');
                                    
                                // DEBUG TIP: Uncomment the line below, save the file, then view 
                                // the page source in your browser to check the generated URL.
                                // echo "<!-- Debug URL: " . esc($imagePath) . " -->";
                            ?>
                            <img class="profile-user-img img-fluid img-circle"
                                 src="<?= esc($imagePath) ?>"
                                 alt="Salesperson profile picture"
                                 onerror="this.onerror=null; this.src='<?= base_url('dist/img/default-user.png') ?>';"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>

                        <h3 class="profile-username text-center mt-3"><?= esc($person['first_name'] . ' ' . $person['last_name']) ?></h3>
                        <p class="text-muted text-center"><?= esc($person['salesperson_id']) ?></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="float-right">
                                    <?php if (($person['status'] ?? 0) == 1): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <!-- Safely handle missing created_at -->
                                <b>Joined</b> <a class="float-right"><?= isset($person['created_at']) ? date('M d, Y', strtotime($person['created_at'])) : 'N/A' ?></a>
                            </li>
                        </ul>

                        <a href="<?= site_url('pharmacy/salespersons') ?>" class="btn btn-primary btn-block"><b><i class="fas fa-arrow-left"></i> Back to List</b></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Details Card -->
                <div class="card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Detailed Information</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9"><?= esc($person['email']) ?></dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9"><?= esc($person['phone']) ?></dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9"><?= nl2br(esc($person['address'])) ?></dd>
                            
                            <dt class="col-sm-3">User ID (System)</dt>
                            <!-- Use null coalescing operator to avoid "Undefined array key" error -->
                            <dd class="col-sm-9"><?= esc($person['user_id'] ?? 'N/A (Not Linked)') ?></dd>
                            
                            <dt class="col-sm-3">Last Updated</dt>
                            <!-- Safely handle missing updated_at -->
                            <dd class="col-sm-9"><?= isset($person['updated_at']) ? date('Y-m-d H:i:s', strtotime($person['updated_at'])) : 'N/A' ?></dd>
                        </dl>
                    </div>
                    <div class="card-footer">
                        <a href="<?= site_url('pharmacy/salespersons/edit/' . $person['id']) ?>" class="btn btn-info"><i class="fas fa-edit"></i> Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
