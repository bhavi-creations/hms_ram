<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-bed mr-2"></i>Beds List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Beds List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary card-outline rounded-lg shadow-sm">
                    
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

                        <div class="mb-4">
                            <h4><i class="fas fa-tags mr-1"></i> Filter by Ward:</h4>
                            <a href="<?= base_url('beds') ?>" class="btn btn-<?= is_null($selectedWard) ? 'primary' : 'outline-primary' ?> btn-sm mb-2">All Wards</a>
                            <?php foreach ($wards as $ward) : ?>
                                <a href="<?= base_url('beds/filter/' . $ward['id']) ?>" class="btn btn-<?= ($selectedWard && $selectedWard['id'] == $ward['id']) ? 'primary' : 'outline-primary' ?> btn-sm mb-2">
                                    <?= esc($ward['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-4">
                            <h4><i class="fas fa-filter mr-1"></i> Filter by Status:</h4>
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

                        <table id="bedsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">S.No</th>
                                    <th style="width: 15%;">Ward</th>
                                    <th style="width: 15%;">Bed Number</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 35%;">Notes</th>
                                    <th style="width: 15%;">Actions</th>
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
                                                $statusClass = 'badge bg-secondary'; // Default
                                                $textColorStyle = ''; 
                                                switch ($bed['status']) {
                                                    case 'Available':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'Occupied':
                                                        $statusClass = 'badge bg-danger';
                                                        break;
                                                    case 'Under Maintenance':
                                                        $statusClass = 'badge bg-warning';
                                                        $textColorStyle = 'color: #000 !important;'; // Ensure dark text on warning
                                                        break;
                                                    case 'Dirty':
                                                        $statusClass = 'badge bg-info';
                                                        $textColorStyle = 'color: #000 !important;'; // Ensure dark text on info
                                                        break;
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>" style="<?= $textColorStyle ?>"><?= esc($bed['status']) ?></span>
                                            </td>
                                            <td><?= esc($bed['notes']) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-bed-status-btn" data-id="<?= $bed['id'] ?>" data-current-status="<?= esc($bed['status']) ?>" title="Change Bed Status">
                                                    <i class="fas fa-sync-alt mr-1"></i> Status
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
        $("#bedsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-info' },
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-secondary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger' },
                { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-warning' }
            ],
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] } // Disable sorting on S.No (0) and Actions (5)
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

                    return $.ajax({
                        url: '<?= base_url('beds/updateStatus/') ?>' + bedId,
                        type: 'POST', // Explicitly set type to POST
                        data: {
                            status: newStatus,
                            [csrfToken]: csrfHash // Include CSRF token
                        },
                        dataType: 'json',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        }
                    })
                    .done(function(response) { 
                        if (response.success) {
                            // Update the CSRF hash in the meta tag for subsequent requests
                            $('meta[name="csrf-hash"]').attr('content', response.csrfHash);
                            return response;
                        } else {
                            // If the response indicates failure but still a 200 status
                            Swal.showValidationMessage(`Error: ${response.message || 'Unknown server error.'}`);
                            return false;
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) { 
                        let errorMessage = `Request failed: ${textStatus}`;
                        // Try to get a more specific error message from the server response
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
                        text: result.value.message || 'Bed status has been successfully updated.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload the page to reflect the changes (new status badge, filters)
                        location.reload();
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>