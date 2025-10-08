<!--
    File: app/Views/patient_portal/appointments_list.php
    Description: Displays a list of the patient's upcoming (Scheduled/Confirmed) appointments,
    using AdminLTE styling.
-->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Invoices</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('patient-portal/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Invoices</h3>
            </div>
            <h1>UNDER DEVELOPMENT</h1>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
