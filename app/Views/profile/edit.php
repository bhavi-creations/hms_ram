<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
            </div>
            <?= form_open('profile/update') ?>
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= old('first_name', $profileData['main']['first_name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= old('last_name', $profileData['main']['last_name']) ?>" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $profileData['main']['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= old('phone_number', $profileData['main']['phone_number']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address"><?= old('address', $profileData['specific']['address'] ?? $profileData['main']['address'] ?? '') ?></textarea>
                </div>
                <hr>
                <p class="text-muted">Leave password blank if you don't want to change it.</p>
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="<?= base_url('profile') ?>" class="btn btn-secondary">Cancel</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>