<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="jumbotron text-center p-5 bg-white rounded-lg shadow-sm">
            <h1 class="display-4 text-primary">Welcome to  HMS, <?= session()->get('first_name') ?>!</h1>
            <p class="lead text-muted mt-3">This is your main dashboard. You are logged in as a <strong><?= session()->get('username') ?></strong>.</p>
            <hr class="my-4">
            <p class="text-secondary">Explore the modules using the sidebar.</p>
            <a class="btn btn-danger btn-lg mt-4" href="<?= base_url('logout') ?>" role="button">Log Out</a>
        </div>
        <!-- Add your dashboard content here -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info rounded-lg shadow-sm">
                    <div class="inner p-3">
                        <h3>150</h3>
                        <p>New Patients (Mock)</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-plus"></i></div>
                    <a href="#" class="small-box-footer rounded-b-lg">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success rounded-lg shadow-sm">
                    <div class="inner p-3">
                        <h3>76 / 200</h3>
                        <p>Beds Available (Mock)</p>
                    </div>
                    <div class="icon"><i class="fas fa-bed"></i></div>
                    <a href="#" class="small-box-footer rounded-b-lg">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning rounded-lg shadow-sm">
                    <div class="inner p-3">
                        <h3>22</h3>
                        <p>Doctors on Duty (Mock)</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-md"></i></div>
                    <a href="#" class="small-box-footer rounded-b-lg">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger rounded-lg shadow-sm">
                    <div class="inner p-3">
                        <h3>3.4 L</h3>
                        <p>Today's Revenue (Mock)</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    <a href="#" class="small-box-footer rounded-b-lg">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
