<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" id="patientTable">
        <thead>
            <tr>
                <th>S.No.</th> <!-- Changed from ID -->
                <th>Primary ID</th>
                <th>Type</th>
                <th>OPD ID</th>
                <th>IPD ID</th>
                <th>General ID</th>
                <th>Casualty ID</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Phone</th>
                <th>Registered On</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="13" class="text-center">No patients found.</td>
                </tr>
            <?php else: ?>
                <?php $serial = 1; ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= $serial++ ?></td> <!-- Auto S.No. -->
                        <td><strong><?= esc($patient['patient_id_code']) ?></strong></td>
                        <td><?= esc($patient['patient_type']) ?></td>
                        <td><?= esc($patient['opd_id_code'] ?? 'N/A') ?></td>
                        <td><?= esc($patient['ipd_id_code'] ?? 'N/A') ?></td>
                        <td><?= esc($patient['gen_id_code'] ?? 'N/A') ?></td>
                        <td><?= esc($patient['cus_id_code'] ?? 'N/A') ?></td>
                        <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                        <td><?= esc($patient['gender']) ?></td>
                        <td><?= esc(date('M d, Y', strtotime($patient['date_of_birth']))) ?></td>
                        <td><?= esc($patient['phone_number']) ?></td>
                        <td><?= esc(date('M d, Y', strtotime($patient['created_at']))) ?></td>
                        <td>
                            <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="btn btn-info btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="<?= base_url('patients/delete/' . $patient['id']) ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this patient?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>

    </table>
</div>