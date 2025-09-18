<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('public/assets/img/default-profile.png') ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= esc($profileData['main']['first_name'] . ' ' . $profileData['main']['last_name']) ?></h3>

                <p class="text-muted text-center">
                    <?php
                    // Display role-specific ID if available
                    if ($role_id == 8 && isset($profileData['specific']['salesperson_id'])) {
                        echo 'Sales Person ID: ' . esc($profileData['specific']['salesperson_id']);
                    } elseif ($role_id == 2 && isset($profileData['specific']['doctor_id'])) {
                        echo 'Doctor ID: ' . esc($profileData['specific']['doctor_id']);
                    } else {
                        // Or just display the username for other roles
                        echo 'Username: ' . esc($profileData['main']['username']);
                    }
                    ?>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Role</b> <a class="float-right"><?= esc($roleName) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right"><?= esc($profileData['main']['email']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right"><?= esc($profileData['main']['phone_number']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Address</b> <a class="float-right">
                        <?php
                            // Check for the address in the role-specific data first
                            if (isset($profileData['specific']) && isset($profileData['specific']['address'])) {
                                echo esc($profileData['specific']['address']);
                            } else {
                                // Fall back to the main user data
                                echo esc($profileData['main']['address'] ?? 'N/A');
                            }
                        ?>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="float-right"><?= esc($profileData['main']['status']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Joined On</b> <a class="float-right"><?= esc($profileData['main']['created_at']) ?></a>
                    </li>
                </ul>

                <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-block"><b>Edit Profile</b></a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>