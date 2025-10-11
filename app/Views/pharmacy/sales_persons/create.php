<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create New Salesperson</h1>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <?= implode('<br>', session()->getFlashdata('errors')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <!-- IMPORTANT: Using form_open_multipart() to enable file uploads -->
                <?= form_open_multipart('pharmacy/salespersons/store') ?>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?= old('address') ?></textarea>
                </div>
                
                <!-- New field for Profile Picture upload -->
                <div class="form-group">
                    <label for="profile_picture">Profile Picture (JPG, JPEG, PNG | Max 1MB)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture">
                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                        </div>
                    </div>
                    <?php if (session()->getFlashdata('errors')['profile_picture'] ?? false): ?>
                        <small class="text-danger"><?= session()->getFlashdata('errors')['profile_picture'] ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save Salesperson</button>
                <?= form_close() ?>
            </div>
        </div>
    </section>
</div>

<!-- Custom script for displaying the selected file name in the file input -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
<?= $this->endSection() ?>
