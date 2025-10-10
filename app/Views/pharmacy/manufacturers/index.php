<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Manufacturers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Manufacturers</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Manufacturers</h3>
                            <div class="card-tools">
                                <a href="<?= site_url('pharmacy/manufacturers/create') ?>" class="btn btn-primary">Add New Manufacturer</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <?php if (session()->getFlashdata('success')) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= session()->getFlashdata('success') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                     

                            <table id="manufacturersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Name</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $serial_number = 1; ?> <?php foreach ($manufacturers as $manufacturer) : ?>
                                        <tr>
                                            <td><?= $serial_number++ ?></td>
                                            <td><?= esc($manufacturer['name']) ?></td>
                                            <td><?= esc($manufacturer['contact_person']) ?></td>
                                            <td><?= esc($manufacturer['phone']) ?></td>
                                            <td><?= esc($manufacturer['email']) ?></td>
                                            <td><?= esc($manufacturer['address']) ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url('pharmacy/manufacturers/edit/' . $manufacturer['id']) ?>" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="<?= site_url('pharmacy/manufacturers/delete/' . $manufacturer['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this manufacturer?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function() {
        $("#manufacturersTable").DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
<?= $this->endSection() ?>