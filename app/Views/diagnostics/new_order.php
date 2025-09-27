<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= esc($title) ?></h3>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('diagnostics/orders/save') ?>" method="post">
            <!-- Hidden input for the ordered_by field -->
            <input type="hidden" name="ordered_by" value="<?= esc($orderedBy) ?>">

            <div class="form-group">
                <label for="patient_id">Patient</label>
                <select name="patient_id" id="patient_id" class="form-control" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option 
                            value="<?= esc($patient['id']) ?>" 
                            data-phone="<?= esc($patient['phone_number'] ?? '') ?>"
                            data-doc="<?= esc($patient['doctor_name'] ?? '') ?>"
                            <?= (set_value('patient_id') == $patient['id']) ? 'selected' : '' ?>>
                            <?= esc($patient['first_name'] . ' ' . $patient['last_name'] . ' (' . $patient['patient_id_code'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- New fields to display patient details -->
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="doctor_name">Referred Doctor</label>
                <input type="text" id="doctor_name" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="test_ids">Diagnostic Tests</label>
                <select name="test_ids[]" id="test_ids" class="form-control" multiple required>
                    <?php foreach ($diagnosticsTests as $test): ?>
                        <option 
                            value="<?= esc($test['id']) ?>"
                            data-type="<?= esc($test['test_type']) ?>"
                            data-price="<?= esc($test['price']) ?>"
                            <?= in_array($test['id'], set_value('test_ids', [])) ? 'selected' : '' ?>>
                            <?= esc($test['test_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control"><?= esc(set_value('remarks')) ?></textarea>
            </div>

            <!-- Table to show selected test details -->
            <div id="selected_tests_container" class="mt-4" style="display:none;">
                <h4>Selected Test Details</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Test Name</th>
                            <th>Type</th>
                            <th>Price (₹)</th>
                        </tr>
                    </thead>
                    <tbody id="tests_table_body">
                        <!-- Test details will be appended here by JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td id="total_price_cell"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="submit" class="btn btn-success mt-3">Place Order</button>
            <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 on the patient dropdown
        $('#patient_id').select2({
            placeholder: 'Select a patient',
            allowClear: true
        });

        // Initialize Select2 on the diagnostic tests dropdown
        $('#test_ids').select2({
            placeholder: 'Select one or more tests',
            allowClear: true
        });

        // Event listener for patient dropdown change
        $('#patient_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var phoneNumber = selectedOption.data('phone');
            var doctorName = selectedOption.data('doc');

            $('#phone_number').val(phoneNumber);
            $('#doctor_name').val(doctorName);
        });

        // Event listener for diagnostic tests dropdown change
        $('#test_ids').on('change', function() {
            var selectedTests = $(this).find('option:selected');
            var tableBody = $('#tests_table_body');
            var totalPrice = 0;
            var serialNumber = 1;

            tableBody.empty(); // Clear previous data

            if (selectedTests.length > 0) {
                $('#selected_tests_container').show();
            } else {
                $('#selected_tests_container').hide();
            }

            selectedTests.each(function() {
                var testName = $(this).text();
                var testType = $(this).data('type');
                var testPrice = parseFloat($(this).data('price'));
                totalPrice += testPrice;

                var newRow = `
                    <tr>
                        <td>${serialNumber++}</td>
                        <td>${testName}</td>
                        <td>${testType}</td>
                        <td>₹${testPrice.toFixed(2)}</td>
                    </tr>
                `;
                tableBody.append(newRow);
            });

            $('#total_price_cell').text('₹' + totalPrice.toFixed(2));
        });

        // Trigger change on page load if a patient is already selected
        $('#patient_id').trigger('change');
    });
</script>
<?= $this->endSection() ?>
