<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= esc($title) ?></li> 
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline rounded-lg shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock mr-2"></i> Attendance Log History</h3>
                    </div>
                    
                    <div class="card-body">
                        
                        <form action="<?= current_url() ?>" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_from">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?= esc($dateFrom) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="date_to">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?= esc($dateTo) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <?php if (empty($logHistory)): ?>
                            <div class="alert alert-info text-center">
                                No attendance records found for the selected period.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table id="attendanceTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Staff Member</th>
                                            <th style="width: 15%;">Role</th>
                                            <th style="width: 10%;">Date</th>
                                            <th style="width: 20%;">Check-In</th>
                                            <th style="width: 20%;">Check-Out</th>
                                            <th style="width: 15%;">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logHistory as $log): ?>
                                        <tr>
                                            <td><?= esc($log['first_name'] . ' ' . $log['last_name']) ?></td>
                                            <td><?= esc($log['role_name']) ?></td>
                                            <td><?= date('D, d M Y', strtotime(esc($log['date']))) ?></td>
                                            <td>
                                                <?php if (!empty($log['check_in_time'])): ?>
                                                    <span class="badge badge-success"><?= date('h:i:s A', strtotime(esc($log['check_in_time']))) ?></span>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['check_out_time'])): ?>
                                                    <span class="badge badge-danger"><?= date('h:i:s A', strtotime(esc($log['check_out_time']))) ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">IN PROGRESS</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                // --- Duration Calculation Logic ---
                                                $inTimeStr = trim($log['check_in_time'] ?? '');
                                                $outTimeStr = trim($log['check_out_time'] ?? '');
                                                $durationOutput = '--';

                                                if (!empty($inTimeStr) && !empty($outTimeStr)) {
                                                    try {
                                                        // Use native DateTime objects for calculation
                                                        $in = new \DateTime($inTimeStr);
                                                        $out = new \DateTime($outTimeStr);

                                                        if ($in && $out) {
                                                            $interval = $in->diff($out);
                                                            
                                                            // Format the difference as Hh Mm
                                                            $durationOutput = $interval->format('%h hours %i minutes');
                                                        } else {
                                                            $durationOutput = '-- Parsing Failed --'; 
                                                        }
                                                    } catch (\Exception $e) {
                                                        $durationOutput = '-- Error --'; 
                                                    }
                                                }
                                                echo esc($durationOutput);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Initialize DataTables with customized, icon-based buttons
        // Only initialize if the table exists (i.e., logHistory is not empty)
        if ($('#attendanceTable').length) {
            $("#attendanceTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "dom": 'Bfrtip', // Enable buttons
                "buttons": [
                    { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                    { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                    { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
                ],
                // Default sort by Date column (index 2) in descending order
                "order": [[ 2, "desc" ]],
                "columnDefs": [ 
                    // Disable sorting on Staff Member (0), Role (1), Check-In (3), Check-Out (4), Duration (5)
                    { "orderable": false, "targets": [0, 1, 3, 4, 5] } 
                ]
            }).buttons().container().appendTo('#attendanceTable_wrapper .col-md-6:eq(0)');
        }
    });
</script>
<?= $this->endSection() ?>