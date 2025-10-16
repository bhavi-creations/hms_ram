<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-valign-middle" id="patientTable">
        <thead class="bg-light text-dark">
            <tr>
                <th style="width: 50px;">S.No.</th> 
                <th>Primary ID</th>
                <th>Type</th>
                <!-- <th>OPD ID</th>
                <th>IPD ID</th>
                <th>General ID</th>
                <th>Casualty ID</th> -->
                <th>Full Name</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Phone</th>
                <th>Registered On</th>
                <th style="width: 150px;" class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="13" class="text-center py-4 text-muted"> <i class="fas fa-user-slash fa-2x d-block mb-2"></i>
                        No patients found in the database.
                    </td>
                </tr>
            <?php else: ?>
                <?php $serial = 1; ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= $serial++ ?>.</td> 
                        <td><span class="badge bg-primary p-2"><strong><?= esc($patient['patient_id_code']) ?></strong></span></td>
                        <td><span class="badge bg-success"><?= esc($patient['patient_type']) ?></span></td>
                        <!-- <td><span class="badge bg-info"><?= esc($patient['opd_id_code'] ?? 'N/A') ?></span></td>
                        <td><span class="badge bg-info"><?= esc($patient['ipd_id_code'] ?? 'N/A') ?></span></td>
                        <td><span class="badge bg-secondary"><?= esc($patient['gen_id_code'] ?? 'N/A') ?></span></td>
                        <td><span class="badge bg-secondary"><?= esc($patient['cus_id_code'] ?? 'N/A') ?></span></td> -->
                        <td><h5 class="font-weight-bold mb-0"><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></h5></td>
                        <td><?= esc($patient['gender']) ?></td>
                        <td><?= esc(date('M d, Y', strtotime($patient['date_of_birth']))) ?></td>
                        <td><a href="tel:<?= esc($patient['phone_number']) ?>"><?= esc($patient['phone_number']) ?></a></td>
                        <td><?= esc(date('M d, Y', strtotime($patient['created_at']))) ?></td>
                        <td class="text-center">
                            <div class="btn-group" role="group" aria-label="Patient Actions">
                                <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-outline-info btn-sm" title="View Patient Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-outline-warning btn-sm" title="Edit Patient Record">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button" class="btn btn-danger btn-sm delete-patient-btn" data-patient-id="<?= esc($patient['id']) ?>" title="Delete Patient Record">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Example for handling the new delete button class with SweetAlert2 (recommended over inline confirm)
    $(document).on('click', '.delete-patient-btn', function(e) {
        e.preventDefault();
        const patientId = $(this).data('patient-id');
        const $row = $(this).closest('tr');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, redirect to the delete URL (or use AJAX)
                window.location.href = '<?= base_url('patients/delete/') ?>' + patientId;
            }
        });
    });
</script>