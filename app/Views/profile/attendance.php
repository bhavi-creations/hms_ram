<?php
// Extends the main layout defined in C:\xampp\htdocs\hms_ram\app\Views\layouts\main.php
$this->extend('layouts/main');
$this->section('content');

// Helper function to calculate duration (defined locally for simplicity, ideally in a Helper file)
function calculate_duration($check_in_time, $check_out_time) {
    if (!$check_in_time || !$check_out_time) return 'N/A';
    
    $start = strtotime($check_in_time);
    $end = strtotime($check_out_time);
    $diff = abs($end - $start);

    $hours = floor($diff / 3600);
    $minutes = floor(($diff % 3600) / 60);

    return "{$hours}h {$minutes}m";
}

// Determine button state based on controller data
$button_text = 'Clock In';
$button_class = 'btn-success';
$action_url = base_url('attendance/checkIn');
$status_message = '<i class="fas fa-question-circle mr-2"></i> Ready to <strong>Clock In</strong> for the day.';

if (isset($isClockedIn) && $isClockedIn) {
    $button_text = 'Clock Out';
    $button_class = 'btn-danger';
    $action_url = base_url('attendance/checkOut');
    $status_message = '<i class="fas fa-sign-in-alt mr-2"></i> Clocked In since <strong>' . date('h:i A', strtotime($todayRecord['check_in_time'])) . '</strong>.';
} elseif (isset($isShiftComplete) && $isShiftComplete) {
    $button_text = 'Shift Complete';
    $button_class = 'btn-secondary';
    $action_url = '#'; // No action
    $status_message = '<i class="fas fa-sign-out-alt mr-2"></i> Day <strong>Completed</strong>. Total: <strong>' . calculate_duration($todayRecord['check_in_time'], $todayRecord['check_out_time']) . '</strong>';
}
?>

<!-- Include necessary CSS/JS assets for Date Range Picker (Assuming these are loaded globally or in the main layout) -->
<!-- For single date pickers, we'll rely on the standard AdminLTE/Bootstrap datepicker if available, 
     but keeping moment.js for general date manipulation. -->

<div class="row pt-4">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800"><?= esc($title) ?></h1>
    </div>
</div>

<div class="row">
    <!-- Filter and Summary Row -->
    <div class="col-12 mb-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter History</h3>
            </div>
            <div class="card-body">
                <form id="attendance-filter-form" method="GET" action="<?= base_url('attendance') ?>">
                    <div class="row">
                        <!-- From Date Column -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from_input">From Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <!-- Changed to a standard text input for datepicker -->
                                    <input type="text" class="form-control datepicker" id="date_from_input" name="date_from" value="<?= esc($dateFrom ?? '') ?>" placeholder="YYYY-MM-DD" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        
                        <!-- To Date Column -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to_input">To Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <!-- Changed to a standard text input for datepicker -->
                                    <input type="text" class="form-control datepicker" id="date_to_input" name="date_to" value="<?= esc($dateTo ?? '') ?>" placeholder="YYYY-MM-DD" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Apply Filter Button -->
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <!-- Clocking Card (Now PHP-Driven) -->
    <div class="col-lg-4">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Live Status</h3>
            </div>
            <div class="card-body text-center">
                <!-- Live Clock (Still handled by simple JS) -->
                <div id="current-time" class="display-4 font-weight-bold mb-4 text-dark">Loading...</div>

                <div id="status-message" class="alert alert-secondary mb-4" role="alert">
                    <?= $status_message ?>
                </div>

                <!-- Clock Button (Now a Form Submission) -->
                <?php if (!isset($isShiftComplete) || !$isShiftComplete): ?>
                <form action="<?= $action_url ?>" method="post">
                    <!-- Ensure the form uses the correct action -->
                    <button id="clock-button" class="btn btn-lg btn-block <?= $button_class ?>">
                        <?= $button_text ?>
                    </button>
                </form>
                <?php else: ?>
                <!-- Disabled button when shift is complete -->
                <button id="clock-button" class="btn btn-lg btn-block <?= $button_class ?>" disabled>
                    <?= $button_text ?>
                </button>
                <?php endif; ?>
                
                <small class="text-muted mt-2 d-block">Click to record your attendance for today.</small>
            </div>
        </div>
        
        <!-- Total Working Days Summary Card -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= esc($totalWorkingDays ?? 0) ?></h3>
                <p>Total Days Logged (in Selected Range)</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <a href="<?= base_url('attendance') ?>" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Attendance Log Table (Now PHP-Driven) -->
    <div class="col-lg-8">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> My Log History</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th style="width: 20%">Date</th>
                                <th style="width: 20%">Clock In</th>
                                <th style="width: 20%">Clock Out</th>
                                <th style="width: 40%">Duration</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-log">
                            <!-- Looping over $logHistory which is passed by the controller -->
                            <?php if (empty($logHistory)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No attendance records found for the selected date range.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($logHistory as $log): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($log['date'])) ?></td>
                                    <td><span class="badge bg-success"><?= date('h:i A', strtotime($log['check_in_time'])) ?></span></td>
                                    <td>
                                        <?php if ($log['check_out_time']): ?>
                                            <span class="badge bg-danger"><?= date('h:i A', strtotime($log['check_out_time'])) ?></span>
                                        <?php else: ?>
                                            ---
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($log['check_out_time']): ?>
                                            <strong class="text-primary"><?= calculate_duration($log['check_in_time'], $log['check_out_time']) ?></strong>
                                        <?php else: ?>
                                            <span class="text-warning">In Progress</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<!-- Script Dependencies: Assumes Bootstrap Datepicker CSS/JS are loaded globally (e.g., via AdminLTE) -->
<!-- If not already loaded, you might need to manually include: 
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
-->
<script>
    function startRealtimeClock() {
        const timeDisplay = document.getElementById('current-time');
        setInterval(() => {
            const now = new Date();
            timeDisplay.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }, 1000);
    }

    $(function() {
        startRealtimeClock();

        // --- Separate Date Picker Initialization ---
        // Initialize Datepicker on both inputs
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd', // Standard format for database/PHP processing
            autoclose: true,
            todayHighlight: true,
            // Restrict dates to today or the past
            endDate: '0d' 
        });

        // Set the initial values from PHP variables
        $('#date_from_input').val('<?= esc($dateFrom ?? '') ?>');
        $('#date_to_input').val('<?= esc($dateTo ?? '') ?>');

        // Note: The form submission is now handled by the user clicking the "Apply Filter" button,
        // which submits the form via the standard GET method, passing 'date_from' and 'date_to'.
        
    });
</script>
<?php $this->endSection() ?>
