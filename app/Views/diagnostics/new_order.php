<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-cart-plus mr-2 text-success"></i> <?= esc($title) ?>
                </h1>
            </div><div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('diagnostics/orders') ?>">Orders</a></li>
                    <li class="breadcrumb-item active">New Order</li>
                </ol>
            </div></div></div></div>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Select Patient and Tests for New Order</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h5 class="alert-heading"><i class="icon fas fa-exclamation-triangle"></i> Validation Error!</h5>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('diagnostics/orders/save') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="ordered_by" value="<?= esc($orderedBy) ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-injured"></i></span></div>
                                            <select name="patient_id" id="patient_id" class="form-control select2bs4 <?= session('errors.patient_id') ? 'is-invalid' : '' ?>" required style="width: 100%;">
                                                <option value="">Select Patient</option>
                                                <?php foreach ($patients as $patient): ?>
                                                    <option 
                                                        value="<?= esc($patient['id']) ?>" 
                                                        data-phone="<?= esc($patient['phone_number'] ?? 'N/A') ?>"
                                                        data-doc="<?= esc($patient['doctor_name'] ?? 'N/A') ?>"
                                                        <?= (set_value('patient_id') == $patient['id']) ? 'selected' : '' ?>>
                                                        <?= esc($patient['first_name'] . ' ' . $patient['last_name'] . ' (' . $patient['patient_id_code'] . ')') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (session('errors.patient_id')): ?><div class="invalid-feedback d-block"><?= session('errors.patient_id') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="test_ids">Diagnostic Tests <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-vials"></i></span></div>
                                            <select name="test_ids[]" id="test_ids" class="form-control select2bs4 <?= session('errors.test_ids') ? 'is-invalid' : '' ?>" multiple required style="width: 100%;">
                                                <?php foreach ($diagnosticsTests as $test): ?>
                                                    <option 
                                                        value="<?= esc($test['id']) ?>"
                                                        data-type="<?= esc($test['test_type']) ?>"
                                                        data-price="<?= esc($test['price']) ?>"
                                                        <?= in_array($test['id'], set_value('test_ids', [])) ? 'selected' : '' ?>>
                                                        <?= esc($test['test_name'] . ' (₹' . number_format($test['price'], 2) . ')') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (session('errors.test_ids')): ?><div class="invalid-feedback d-block"><?= session('errors.test_ids') ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                            <input type="text" id="phone_number" class="form-control bg-white" readonly placeholder="Will be filled automatically">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_name">Referred Doctor</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-md"></i></span></div>
                                            <input type="text" id="doctor_name" class="form-control bg-white" readonly placeholder="Will be filled automatically">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="remarks">Remarks / Instructions</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clipboard"></i></span></div>
                                    <textarea name="remarks" id="remarks" class="form-control <?= session('errors.remarks') ? 'is-invalid' : '' ?>" rows="3" placeholder="Enter any specific instructions or remarks for this order."><?= esc(set_value('remarks')) ?></textarea>
                                    <?php if (session('errors.remarks')): ?><div class="invalid-feedback"><?= session('errors.remarks') ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div id="selected_tests_container" class="mt-4" style="display:<?= (count(set_value('test_ids', [])) > 0) ? 'block' : 'none' ?>;">
                                <h4 class="text-success mb-3"><i class="fas fa-list-alt mr-1"></i> Order Summary</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">S.No</th>
                                                <th style="width: 45%;">Test Name</th>
                                                <th style="width: 25%;">Type</th>
                                                <th style="width: 25%;" class="text-right">Price (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tests_table_body">
                                            </tbody>
                                        <tfoot>
                                            <tr class="bg-light font-weight-bold">
                                                <td colspan="3" class="text-right"><strong>Total Order Price:</strong></td>
                                                <td id="total_price_cell" class="text-right text-lg text-success">₹0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-3">
                                <a href="<?= base_url('diagnostics/orders') ?>" class="btn btn-default mr-2 px-4"><i class="fas fa-times-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-success px-4"><i class="fas fa-paper-plane"></i> Place Order</button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
        </div>
    </div></section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 4 theme
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: $(this).attr('placeholder') || 'Select an option', // Use placeholder from HTML
            allowClear: true
        });

        // Function to update patient details fields
        function updatePatientDetails() {
            var selectedOption = $('#patient_id').find('option:selected');
            var phoneNumber = selectedOption.data('phone') || '';
            var doctorName = selectedOption.data('doc') || '';

            $('#phone_number').val(phoneNumber);
            $('#doctor_name').val(doctorName);
        }

        // Function to render selected tests table
        function updateSelectedTestsTable() {
            var selectedTests = $('#test_ids').find('option:selected');
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
                var testName = $(this).text().split(' (₹')[0].trim(); // Extract name before price in text
                var testType = $(this).data('type');
                var testPrice = parseFloat($(this).data('price')) || 0;
                totalPrice += testPrice;

                var newRow = `
                    <tr>
                        <td>${serialNumber++}</td>
                        <td>${testName}</td>
                        <td>${testType}</td>
                        <td class="text-right">₹${testPrice.toFixed(2)}</td>
                    </tr>
                `;
                tableBody.append(newRow);
            });

            $('#total_price_cell').text('₹' + totalPrice.toFixed(2));
        }

        // Event listener for patient dropdown change
        $('#patient_id').on('change', updatePatientDetails);

        // Event listener for diagnostic tests dropdown change
        $('#test_ids').on('change', updateSelectedTestsTable);

        // Trigger change on page load to populate patient details (if selected) and tests table (if pre-selected by old input)
        updatePatientDetails();
        updateSelectedTestsTable();
    });
</script>
<?= $this->endSection() ?>