<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> <?= esc($title) ?></h3>
            </div>
            
            <div class="card-body">
                
                <!-- Filter Form -->
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
                
                <!-- Attendance Table -->
                <?php if (empty($logHistory)): ?>
                    <div class="alert alert-info text-center">
                        No attendance records found for the selected period.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <?php if ($currentRole == 1 || $currentRole != 1): ?>
                                        <th style="width: 20%;">Staff Member</th>
                                        <th style="width: 15%;">Role</th> <!-- Added Role Header -->
                                    <?php endif; ?>
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
                                    <td><?= esc($log['role_name']) ?></td> <!-- Display Role Data -->
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
                                        // --- FIX: Using native PHP DateTime to bypass CodeIgniter Time library issues ---
                                        $inTimeStr = trim($log['check_in_time'] ?? '');
                                        $outTimeStr = trim($log['check_out_time'] ?? '');
                                        $durationOutput = '--';

                                        if (!empty($inTimeStr) && !empty($outTimeStr)) {
                                            try {
                                                // Create native DateTime objects from the string data
                                                $in = new \DateTime($inTimeStr);
                                                $out = new \DateTime($outTimeStr);

                                                if ($in && $out) {
                                                    $interval = $in->diff($out);
                                                    
                                                    // Format the difference as Hh Mm
                                                    $durationOutput = $interval->format('%h hours %i minutes');
                                                } else {
                                                    $durationOutput = '-- Parsing Failed (Native) --'; 
                                                }
                                            
                                            } catch (\Exception $e) {
                                                // Fallback for any native DateTime creation errors
                                                $durationOutput = '-- Error Processing Time (Native) --'; 
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

<?= $this->endSection() ?>
