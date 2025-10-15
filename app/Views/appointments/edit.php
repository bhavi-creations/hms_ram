<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Edit Appointment <small class="text-muted">#<?= esc($appointment['id']) ?></small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('appointments') ?>">Appointments</a></li>
                    <li class="breadcrumb-item active">Edit #<?= esc($appointment['id']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline card-info shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-1"></i>
                            Update Appointment Details
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h4 class="alert-heading"><i class="icon fas fa-ban"></i> Validation Error!</h4>
                                <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('appointments/update/' . $appointment['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <fieldset class="mb-4 p-3 border border-info rounded-lg">
                                 

                                 

                                <div class="form-group mb-3">
                                    <label for="doctor_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                                        </div>
                                        <select name="doctor_id" id="doctor_id" class="form-control select2" required>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <option value="<?= esc($doctor['id']) ?>" <?= ($doctor['id'] == $appointment['doctor_id']) ? 'selected' : '' ?>>
                                                    Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> (<?= esc($doctor['specialization'] ?? 'General') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="<?= esc(date('Y-m-d', strtotime($appointment['appointment_date']))) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                <input type="time" name="appointment_time" id="appointment_time" class="form-control" value="<?= esc($appointment['appointment_time']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="mb-4 p-3 border border-secondary rounded-lg">
                                <legend class="w-auto px-2 h5 text-info">Status & Notes</legend>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                        <select name="status" id="status" class="form-control select2" required>
                                            <?php $statuses = ['Pending', 'Confirmed', 'Cancelled', 'Completed']; ?>
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?= $status ?>" <?= ($status == $appointment['status']) ? 'selected' : '' ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="reason_for_visit" class="form-label">Reason for Visit/Notes</label>
                                    <textarea name="reason_for_visit" id="reason_for_visit" rows="4" class="form-control" placeholder="Update the reason for the visit or add internal notes..."><?= esc($appointment['reason_for_visit']) ?></textarea>
                                </div>
                            </fieldset>

                            <div class="card-footer clearfix">
                                <div class="float-right">
                                    <a href="<?= base_url('appointments') ?>" class="btn btn-default mr-2"><i class="fas fa-times-circle"></i> Cancel</a>
                                    <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

---

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 on the doctor and status dropdowns for a premium look
        $('.select2').select2({
            theme: 'bootstrap4', // Assuming AdminLTE/Bootstrap theme for Select2
            minimumResultsForSearch: 10,
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>