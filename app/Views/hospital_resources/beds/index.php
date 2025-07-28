<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Beds List</h3>
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
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <h4>Filter by Ward:</h4>
                        <a href="<?= base_url('beds') ?>" class="btn btn-<?= is_null($selectedWard) ? 'primary' : 'outline-primary' ?> btn-sm mb-2">All Wards</a>
                        <?php foreach ($wards as $ward) : ?>
                            <a href="<?= base_url('beds/filter/' . $ward['id']) ?>" class="btn btn-<?= ($selectedWard && $selectedWard['id'] == $ward['id']) ? 'primary' : 'outline-primary' ?> btn-sm mb-2">
                                <?= esc($ward['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <h4>Filter by Status:</h4>
                        <?php
                        $statuses = ['Available', 'Occupied', 'Under Maintenance', 'Dirty'];
                        $currentWardId = $selectedWard ? $selectedWard['id'] : '';
                        ?>
                        <a href="<?= base_url('beds/' . ($currentWardId ? 'filter/' . $currentWardId : '') ) ?>" class="btn btn-<?= empty($currentStatusFilter) ? 'info' : 'outline-info' ?> btn-sm mb-2">All Statuses</a>
                        <?php foreach ($statuses as $status) : ?>
                            <a href="<?= base_url('beds/' . ($currentWardId ? 'filter/' . $currentWardId : '') . '?status=' . urlencode($status)) ?>" class="btn btn-<?= ($currentStatusFilter == $status) ? 'info' : 'outline-info' ?> btn-sm mb-2">
                                <?= esc($status) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <table id="bedsTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Ward</th>
                                <th>Bed Number</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $filteredBeds = $beds;
                            if (!empty($currentStatusFilter)) {
                                $filteredBeds = array_filter($beds, function($bed) use ($currentStatusFilter) {
                                    return $bed['status'] === $currentStatusFilter;
                                });
                            }
                            ?>
                            <?php if (!empty($filteredBeds)) : ?>
                                <?php $sno = 1; ?>
                                <?php foreach ($filteredBeds as $bed) : ?>
                                    <tr>
                                        <td><?= $sno++ ?></td>
                                        <td><?= esc($wardModel->find($bed['ward_id'])['name'] ?? 'N/A') ?></td>
                                        <td><?= esc($bed['bed_number']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $textColorStyle = ''; 
                                            switch ($bed['status']) {
                                                case 'Available':
                                                    $statusClass = 'badge badge-success';
                                                    $textColorStyle = 'color: #000 !important;'; // Black text for green background
                                                    break;
                                                case 'Occupied':
                                                    $statusClass = 'badge badge-danger';
                                                    $textColorStyle = 'color:  #000 !important;'; // White text for red background
                                                    break;
                                                case 'Under Maintenance':
                                                    $statusClass = 'badge badge-warning';
                                                    $textColorStyle = 'color: #000 !important;'; // Black text for yellow/orange background
                                                    break;
                                                case 'Dirty':
                                                    $statusClass = 'badge badge-info';
                                                    $textColorStyle = 'color: #000 !important;'; // Black text for light blue background
                                                    break;
                                                default:
                                                    $statusClass = 'badge badge-secondary';
                                                    $textColorStyle = 'color: #000 !important;'; // White text for grey background
                                                    break;
                                            }
                                            ?>
                                            <span class="<?= $statusClass ?>" style="<?= $textColorStyle ?>"><?= esc($bed['status']) ?></span>
                                        </td>
                                        <td><?= esc($bed['notes']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-bed-status-btn" data-id="<?= $bed['id'] ?>" data-current-status="<?= esc($bed['status']) ?>" title="Change Bed Status">
                                                <i class="fas fa-sync-alt"></i> Change Status
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No beds found for the selected filters.</td>
                                </tr>
                            <?php endif; ?>
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
</div><!-- /.container-fluid -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables & Plugins -->
<script src="<?= base_url('public/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/datatables/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>

<script>
    $(function() {
        // Initialize DataTables
        $("#bedsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        }).buttons().container().appendTo('#bedsTable_wrapper .col-md-6:eq(0)');

        // SweetAlert2 for changing bed status
        $('.edit-bed-status-btn').on('click', function() {
            const bedId = $(this).data('id');
            const currentStatus = $(this).data('current-status');
            
            // Get fresh CSRF token and hash right before the Swal.fire call
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            let csrfHash = $('meta[name="csrf-hash"]').attr('content');

            Swal.fire({
                title: 'Change Bed Status',
                input: 'select',
                inputOptions: {
                    'Available': 'Available',
                    'Occupied': 'Occupied',
                    'Under Maintenance': 'Under Maintenance',
                    'Dirty': 'Dirty'
                },
                inputValue: currentStatus,
                showCancelButton: true,
                confirmButtonText: 'Update',
                showLoaderOnConfirm: true,
                preConfirm: (newStatus) => {
                    if (!newStatus) {
                        Swal.showValidationMessage('Please select a status.');
                        return false;
                    }
                    
                    // Ensure the latest CSRF hash is used for the request
                    csrfHash = $('meta[name="csrf-hash"]').attr('content');

                    // Log the data being sent for debugging
                    console.log("Sending AJAX request for Bed ID:", bedId);
                    console.log("New Status:", newStatus);
                    console.log("CSRF Token Name:", csrfToken);
                    console.log("CSRF Hash Value:", csrfHash);

                    return $.ajax({
                        url: '<?= base_url('beds/updateStatus/') ?>' + bedId,
                        type: 'POST', // Explicitly set type to POST
                        data: {
                            status: newStatus,
                            [csrfToken]: csrfHash // Include CSRF token
                        },
                        dataType: 'json',
                        // Add a beforeSend to set the X-Requested-With header, though not strictly needed now
                        // It's good practice for AJAX requests
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        }
                    })
                    .done(function(response) { // Use .done for success
                        console.log("AJAX Success Response:", response); // Log success response
                        if (response.success) {
                            // Update the CSRF hash in the meta tag for subsequent requests
                            $('meta[name="csrf-hash"]').attr('content', response.csrfHash);
                            return response;
                        } else {
                            Swal.showValidationMessage(`Error: ${response.message}`);
                            return false;
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) { // Use .fail for errors
                        console.error("AJAX Error:", jqXHR.status, textStatus, errorThrown); // Log error details
                        console.error("AJAX Error Response Text:", jqXHR.responseText); // Log the full response for debugging

                        let errorMessage = `Request failed: ${textStatus}`;
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = `Error: ${jqXHR.responseJSON.message}`;
                        } else if (errorThrown) {
                            errorMessage = `Error: ${errorThrown}`;
                        }
                        Swal.showValidationMessage(errorMessage);
                        return false;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Status Updated!',
                        text: result.value.message,
                        icon: 'success'
                    }).then(() => {
                        // Reload the page to reflect the changes
                        location.reload();
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
