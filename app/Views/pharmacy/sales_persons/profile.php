<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="<?= base_url('public/assets/img/default-profile.png') ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= esc($salesPerson['first_name'] . ' ' . $salesPerson['last_name']) ?></h3>

                <p class="text-muted text-center"><?= esc($salesPerson['salesperson_id']) ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right"><?= esc($salesPerson['email']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right"><?= esc($salesPerson['phone']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Address</b> <a class="float-right"><?= esc($salesPerson['address']) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="float-right"><?= $salesPerson['status'] ? 'Active' : 'Inactive' ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Joined On</b> <a class="float-right"><?= esc($salesPerson['created_at']) ?></a>
                    </li>
                </ul>

                <a href="#" class="btn btn-primary btn-block"><b>Edit Profile</b></a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>