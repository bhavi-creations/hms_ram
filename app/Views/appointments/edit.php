<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Edit Appointment #<?= esc($appointment['id']) ?></h2>
<hr>

<div class="row">
    <div class="col-md-6">

        <!-- Display validation errors -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Appointment update form -->
        <form action="<?= base_url('appointments/update/' . $appointment['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group mb-3">
                <label for="doctor_id" class="form-label">Doctor</label>
                <select name="doctor_id" id="doctor_id" class="form-control" required>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= esc($doctor['id']) ?>" <?= ($doctor['id'] == $appointment['doctor_id']) ? 'selected' : '' ?>>
                            Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="<?= esc($appointment['appointment_date']) ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="appointment_time" class="form-label">Appointment Time</label>
                <input type="time" name="appointment_time" id="appointment_time" class="form-control" value="<?= esc($appointment['appointment_time']) ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <?php $statuses = ['Pending', 'Confirmed', 'Cancelled', 'Completed']; ?>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>" <?= ($status == $appointment['status']) ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="reason_for_visit" class="form-label">Reason for Visit</label>
                <textarea name="reason_for_visit" id="reason_for_visit" rows="4" class="form-control"><?= esc($appointment['reason_for_visit']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Appointment</button>
            <a href="<?= base_url('appointments') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>