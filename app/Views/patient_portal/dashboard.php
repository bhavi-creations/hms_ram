<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Patient Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Home</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Welcome Alert & Quick Stats Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible">
                    <h5><i class="icon fas fa-info"></i> Welcome Back!</h5>
                    Hello, **<?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?>**. Your Patient ID is **<?= esc($patient['patient_id_code']) ?>**.
                    This portal allows you to manage your health records and view results.
                </div>
            </div>

            <!-- Quick Info Boxes (using the count data) -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= count($appointments) ?></h3>
                        <p>Total Appointments</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <a href="<?= base_url('patient-portal/appointments') ?>" class="small-box-footer">More Info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= count($labs) ?></h3>
                        <p>Lab Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <a href="<?= base_url('patient-portal/labs') ?>" class="small-box-footer">View Reports <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= count($diagnostics) ?></h3>
                        <p>Diagnostic Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-x-ray"></i>
                    </div>
                    <a href="<?= base_url('patient-portal/diagnostics') ?>" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <!-- START: UPDATED BOX - Replaced 'Patient Status' with 'Total Invoices' count -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <!-- Assumes $invoices is passed from the controller, representing all patient invoices -->
                        <h3><?= count($invoices ?? []) ?></h3> 
                        <p>Total Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <a href="<?= base_url('patient-portal/invoices') ?>" class="small-box-footer">View Bills <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- END: UPDATED BOX -->
        </div>

        <!-- Detailed Data Cards Row -->
        <div class="row">

            <!-- My Profile Card (More comprehensive details) -->
            <div class="col-lg-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-circle mr-1"></i> Your Profile Information</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Patient ID</dt>
                            <dd class="col-sm-7"><strong><?= esc($patient['patient_id_code']) ?></strong></dd>

                            <dt class="col-sm-5">Date of Birth</dt>
                            <dd class="col-sm-7"><?= date('M d, Y', strtotime($patient['date_of_birth'])) ?></dd>

                            <dt class="col-sm-5">Gender</dt>
                            <dd class="col-sm-7"><?= esc($patient['gender']) ?></dd>

                            <dt class="col-sm-5">Blood Group</dt>
                            <dd class="col-sm-7"><?= esc($patient['blood_group'] ?? 'N/A') ?></dd>

                            <dt class="col-sm-5">Phone</dt>
                            <dd class="col-sm-7"><?= esc($patient['phone_number'] ?? 'N/A') ?></dd>
                            
                            <dt class="col-sm-5">Email</dt>
                            <dd class="col-sm-7"><?= esc($patient['email'] ?? 'N/A') ?></dd>
                            
                            <dt class="col-sm-5">Address</dt>
                            <dd class="col-sm-7 text-truncate"><?= esc($patient['address'] ?? 'N/A') ?></dd>
                            
                            <!-- Displaying Patient Status in the Profile Card now that the small box is gone -->
                            <dt class="col-sm-5">Status</dt>
                            <dd class="col-sm-7"><strong><?= esc($patient['patient_type']) ?></strong></dd>
                            
                        </dl>
                        <hr class="mt-2 mb-2">
                        <p class="text-muted text-sm mb-1">Emergency Contact: <?= esc($patient['emergency_contact_name'] ?? 'N/A') ?></p>
                        <p class="text-muted text-sm">Contact Phone: <?= esc($patient['emergency_contact_phone'] ?? 'N/A') ?></p>

                        <a href="<?= base_url('profile') ?>" class="btn btn-sm btn-outline-info btn-block mt-3">Manage Profile</a>
                    </div>
                </div>
            </div>

            <!-- Latest Appointments Card (Table View) -->
            <div class="col-lg-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Upcoming Appointments (Latest 5)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($appointments)): ?>
                                        <?php $latestAppointments = array_slice($appointments, 0, 5); ?>
                                        <?php foreach ($latestAppointments as $appointment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></strong><br>
                                                    <small class="text-muted"><?= esc($appointment['appointment_time'] ?? 'N/A') ?></small>
                                                </td>
                                                <td>
                                                    <?= esc($appointment['doctor_name'] ?? 'ID: ' . ($appointment['doctor_id'] ?? 'N/A')) ?>
                                                </td>
                                                <td><?= esc($appointment['purpose'] ?? 'General Check-up') ?></td>
                                                <td>
                                                    <?php 
                                                         $status = esc($appointment['status']);
                                                         $badgeClass = match($status) {
                                                             'Scheduled' => 'badge-info',
                                                             'Confirmed' => 'badge-primary',
                                                             'Completed' => 'badge-success',
                                                             default => 'badge-secondary',
                                                         };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No appointments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('patient-portal/appointments') ?>" class="small-box-footer text-primary">View All Appointments</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lab & Diagnostic Orders Row -->
        <div class="row">
            <!-- Lab Orders Card -->
            <div class="col-lg-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-flask mr-1"></i> Latest Lab Orders</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                           <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($labs)): ?>
                                        <?php $latestLabs = array_slice($labs, 0, 5); ?>
                                        <?php foreach ($latestLabs as $lab): ?>
                                            <tr>
                                                <td><strong><?= esc($lab['order_id_code']) ?></strong></td>
                                                <td><?= date('M d, Y', strtotime($lab['order_date'])) ?></td>
                                                <td>
                                                    <?php 
                                                         $status = esc($lab['status']);
                                                         $badgeClass = match($status) {
                                                             'Pending' => 'badge-warning',
                                                             'Processing' => 'badge-info',
                                                             'Completed' => 'badge-success',
                                                             default => 'badge-secondary',
                                                         };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No lab orders found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('patient-portal/labs') ?>" class="small-box-footer text-success">View All Lab Reports</a>
                    </div>
                </div>
            </div>

            <!-- Diagnostics Orders Card -->
            <div class="col-lg-6">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-notes-medical mr-1"></i> Latest Diagnostic Orders</h3>
                         <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                           <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($diagnostics)): ?>
                                        <?php $latestDiagnostics = array_slice($diagnostics, 0, 5); ?>
                                        <?php foreach ($latestDiagnostics as $diag): ?>
                                            <tr>
                                                <td><strong><?= esc($diag['order_id_code']) ?></strong></td>
                                                <td><?= date('M d, Y', strtotime($diag['order_date'])) ?></td>
                                                <td>
                                                    <?php 
                                                         $status = esc($diag['status']);
                                                         $badgeClass = match($status) {
                                                             'Pending' => 'badge-warning',
                                                             'Scheduled' => 'badge-info',
                                                             'Completed' => 'badge-danger',
                                                             default => 'badge-secondary',
                                                         };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No diagnostic orders found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('patient-portal/diagnostics') ?>" class="small-box-footer text-danger">View All Diagnostics</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- START: NEW INVOICES/BILLING ROW -->
        <div class="row">
             <!-- Latest Invoices Card -->
            <div class="col-lg-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-receipt mr-1"></i> Latest Invoices / Bills (Latest 5)</h3>
                         <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                           <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Invoice Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($invoices)): ?>
                                        <?php $latestInvoices = array_slice($invoices, 0, 5); ?>
                                        <?php foreach ($latestInvoices as $invoice): ?>
                                            <tr>
                                                <td><strong><?= esc($invoice['invoice_id_code'] ?? 'N/A') ?></strong></td>
                                                <td><?= date('M d, Y', strtotime($invoice['invoice_date'] ?? date('Y-m-d'))) ?></td>
                                                <!-- Assuming 'total_amount' is available and needs formatting -->
                                                <td><strong>$<?= number_format($invoice['total_amount'] ?? 0, 2) ?></strong></td>
                                                <td>
                                                    <?php 
                                                         $status = esc($invoice['status'] ?? 'Draft');
                                                         $badgeClass = match($status) {
                                                             'Draft' => 'badge-secondary',
                                                             'Pending' => 'badge-warning',
                                                             'Paid' => 'badge-success',
                                                             'Overdue' => 'badge-danger',
                                                             default => 'badge-secondary',
                                                         };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                </td>
                                                <td>
                                                     <a href="<?= base_url('patient-portal/invoices/' . ($invoice['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No invoices found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('patient-portal/invoices') ?>" class="small-box-footer text-info">View All Bills & Payment History</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: NEW INVOICES/BILLING ROW -->

    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<?= $this->endSection() ?>
