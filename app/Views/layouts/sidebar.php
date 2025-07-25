<aside class="app-sidebar side_bg shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url('/') ?>" class="brand-link">
            <img src="<?= base_url('public/assets/img/AdminLTELogo.png') ?>" alt="HMS Logo" class="brand-image" />
            <span class="brand-text">HMS</span> <!-- Changed to HMS -->
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- Dashboard (Visible to all authenticated users) -->
                <li class="nav-item">
                    <?php if (session()->get('role_id') == 1): ?>
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                    <?php elseif (session()->get('role_id') == 2): ?>
                        <a href="<?= base_url('doctor/dashboard') ?>" class="nav-link <?= uri_string() == 'doctor/dashboard' ? 'active' : '' ?>">
                    <?php else: ?>
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                    <?php endif; ?>
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (session()->get('role_id') == 1): // Admin-only sections ?>
                    <!-- Patient Management -->
                    <?php $isPatientManagementActive = url_is('patients*') || url_is('opd*') || url_is('ipd*') || url_is('casualty*') || url_is('medical-records*') || url_is('general*'); ?>
                    <li class="nav-item <?= $isPatientManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isPatientManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-hospital-user"></i>
                            <p>
                                Patient Management
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('patients/register') ?>" class="nav-link <?= uri_string() == 'patients/register' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Register Patient</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('patients') ?>" class="nav-link <?= uri_string() == 'patients' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-injured"></i>
                                    <p>Patient List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('general') ?>" class="nav-link <?= uri_string() == 'general' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-list-alt"></i>
                                    <p>General Patients List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('opd') ?>" class="nav-link <?= uri_string() == 'opd' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-person-walking"></i>
                                    <p>OPD Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('ipd') ?>" class="nav-link <?= uri_string() == 'ipd' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-bed-pulse"></i>
                                    <p>IPD Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('casualty') ?>" class="nav-link <?= uri_string() == 'casualty' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-truck-medical"></i>
                                    <p>Casualty / ER</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('medical-records') ?>" class="nav-link <?= uri_string() == 'medical-records' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-file-medical"></i>
                                    <p>Medical Records</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Doctor Management (Admin only) -->
                    <?php $isDoctorManagementActive = url_is('doctors*') && !url_is('doctor/appointments*'); // Exclude doctor's own panel from this ?>
                    <li class="nav-item <?= $isDoctorManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDoctorManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>
                                Doctor Management
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('doctors/new') ?>" class="nav-link <?= uri_string() == 'doctors/new' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Register Doctor</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('doctors') ?>" class="nav-link <?= uri_string() == 'doctors' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-stethoscope"></i>
                                    <p>Doctor List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('doctors/schedule') ?>" class="nav-link <?= uri_string() == 'doctors/schedule' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Doctor Schedule</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Appointment Management (Admin view) -->
                    <?php $isAppointmentManagementActive = url_is('appointments*') && !url_is('doctor/appointments*'); ?>
                    <li class="nav-item <?= $isAppointmentManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isAppointmentManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>
                                Appointment Management (Admin)
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('appointments/create') ?>" class="nav-link <?= uri_string() == 'appointments/create' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-calendar-plus"></i>
                                    <p>Schedule Appointment</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('appointments') ?>" class="nav-link <?= uri_string() == 'appointments' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-calendar-day"></i>
                                    <p>View All Appointments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('appointments/history') ?>" class="nav-link <?= uri_string() == 'appointments/history' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>Appointment History</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Pharmacy Management (Admin only) -->
                    <?php $isPharmacyManagementActive = url_is('pharmacy*'); ?>
                    <li class="nav-item <?= $isPharmacyManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isPharmacyManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-pills"></i>
                            <p>
                                Pharmacy Management
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/drugs') ?>" class="nav-link <?= uri_string() == 'pharmacy/drugs' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-capsules"></i>
                                    <p>Manage Drugs</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/categories') ?>" class="nav-link <?= uri_string() == 'pharmacy/categories' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-tags"></i>
                                    <p>Drug Categories</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/suppliers') ?>" class="nav-link <?= uri_string() == 'pharmacy/suppliers' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-truck"></i>
                                    <p>Suppliers</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/stock-in') ?>" class="nav-link <?= uri_string() == 'pharmacy/stock-in' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-arrow-down"></i>
                                    <p>Stock In</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/dispense') ?>" class="nav-link <?= uri_string() == 'pharmacy/dispense' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                                    <p>Stock Out / Dispense</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/alerts') ?>" class="nav-link <?= uri_string() == 'pharmacy/alerts' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-bell"></i>
                                    <p>Expiry Alerts</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Laboratory Management (Admin only) -->
                    <?php $isLaboratoryManagementActive = url_is('laboratory*'); ?>
                    <li class="nav-item <?= $isLaboratoryManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isLaboratoryManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-flask"></i>
                            <p>
                                Laboratory Management
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('laboratory/orders') ?>" class="nav-link <?= uri_string() == 'laboratory/orders' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-vials"></i>
                                    <p>Order Tests</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laboratory/results') ?>" class="nav-link <?= uri_string() == 'laboratory/results' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-microscope"></i>
                                    <p>Enter Results</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laboratory/reports') ?>" class="nav-link <?= uri_string() == 'laboratory/reports' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-file-alt"></i>
                                    <p>View Lab Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laboratory/types') ?>" class="nav-link <?= uri_string() == 'laboratory/types' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-tags"></i>
                                    <p>Test Types</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Billing & Accounts (Admin only) -->
                    <?php $isBillingAccountsActive = url_is('billing*') || url_is('invoices*'); ?>
                    <li class="nav-item <?= $isBillingAccountsActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isBillingAccountsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>
                                Billing & Accounts
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('billing/create') ?>" class="nav-link <?= uri_string() == 'billing/create' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-plus-circle"></i>
                                    <p>Create Invoice</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('invoices') ?>" class="nav-link <?= uri_string() == 'invoices' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-list-alt"></i>
                                    <p>View Invoices</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('billing/payments') ?>" class="nav-link <?= uri_string() == 'billing/payments' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-money-check-alt"></i>
                                    <p>Payment History</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('billing/services') ?>" class="nav-link <?= uri_string() == 'billing/services' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-hand-holding-medical"></i>
                                    <p>Manage Services</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Hospital Resources (Admin only) -->
                    <?php $isHospitalResourcesActive = url_is('wards*') || url_is('beds*') || url_is('assets*'); ?>
                    <li class="nav-item <?= $isHospitalResourcesActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isHospitalResourcesActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-hospital"></i>
                            <p>
                                Hospital Resources
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('wards') ?>" class="nav-link <?= uri_string() == 'wards' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-building"></i>
                                    <p>Wards</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('beds') ?>" class="nav-link <?= uri_string() == 'beds' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-bed"></i>
                                    <p>Beds</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('assets') ?>" class="nav-link <?= uri_string() == 'assets' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-laptop-medical"></i>
                                    <p>Assets & Equipment</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Staff Management (Admin only) -->
                    <?php $isStaffManagementActive = url_is('staff*') || url_is('users*') || url_is('roles*'); ?>
                    <li class="nav-item <?= $isStaffManagementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isStaffManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Staff Management
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('staff/register') ?>" class="nav-link <?= uri_string() == 'staff/register' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Add Staff</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('staff') ?>" class="nav-link <?= uri_string() == 'staff' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Staff List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('users') ?>" class="nav-link <?= uri_string() == 'users' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-users-gear"></i>
                                    <p>User Accounts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('roles') ?>" class="nav-link <?= uri_string() == 'roles' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>Roles & Permissions</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('staff/attendance') ?>" class="nav-link <?= uri_string() == 'staff/attendance' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-clipboard-user"></i>
                                    <p>Attendance</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Diagnostics & Imaging (Admin only) -->
                    <?php $isDiagnosticsActive = url_is('diagnostics*') || url_is('imaging*'); ?>
                    <li class="nav-item <?= $isDiagnosticsActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDiagnosticsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-x-ray"></i>
                            <p>
                                Diagnostics & Imaging
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('diagnostics/orders') ?>" class="nav-link <?= uri_string() == 'diagnostics/orders' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-notes-medical"></i>
                                    <p>Order Imaging</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('diagnostics/results') ?>" class="nav-link <?= uri_string() == 'diagnostics/results' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-file-image"></i>
                                    <p>View Results</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Reporting & Analytics (Admin only) -->
                    <?php $isReportingActive = url_is('reports*'); ?>
                    <li class="nav-item <?= $isReportingActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isReportingActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Reporting & Analytics
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('reports/patients') ?>" class="nav-link <?= uri_string() == 'reports/patients' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Patient Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('reports/financial') ?>" class="nav-link <?= uri_string() == 'reports/financial' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-chart-pie"></i>
                                    <p>Financial Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('reports/pharmacy') ?>" class="nav-link <?= uri_string() == 'reports/pharmacy' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-chart-area"></i>
                                    <p>Pharmacy Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('reports/lab') ?>" class="nav-link <?= uri_string() == 'reports/lab' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Lab Reports</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- System Configuration & Audit (Admin only) -->
                    <?php $isSystemConfigActive = url_is('settings*') || url_is('master-data*') || url_is('audit*'); ?>
                    <li class="nav-item <?= $isSystemConfigActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isSystemConfigActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                System Config & Audit
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('settings') ?>" class="nav-link <?= uri_string() == 'settings' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-sliders-h"></i>
                                    <p>General Settings</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master-data') ?>" class="nav-link <?= uri_string() == 'master-data' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-database"></i>
                                    <p>Master Data</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('audit-logs') ?>" class="nav-link <?= uri_string() == 'audit-logs' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>Audit Logs</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; // End Admin-only sections ?>

                <?php if (session()->get('role_id') == 2): // Doctor-specific sections ?>
                    <!-- Doctor's Appointment Management -->
                    <?php $isDoctorAppointmentActive = url_is('doctor/appointments*'); ?>
                    <li class="nav-item <?= $isDoctorAppointmentActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDoctorAppointmentActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>
                                My Appointments
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/appointments') ?>" class="nav-link <?= uri_string() == 'doctor/appointments' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Current Appointments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/appointments/history') ?>" class="nav-link <?= uri_string() == 'doctor/appointments/history' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>Appointment History</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Doctor's Patient Management (if doctors can view all patients or specific ones) -->
                    <?php $isDoctorPatientActive = url_is('doctor/patients*'); ?>
                    <li class="nav-item <?= $isDoctorPatientActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDoctorPatientActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-injured"></i>
                            <p>
                                My Patients
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/patients') ?>" class="nav-link <?= uri_string() == 'doctor/patients' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>View My Patients</p>
                                </a>
                            </li>
                            <!-- Add other doctor-specific patient links here -->
                        </ul>
                    </li>

                    <!-- Doctor's Medical Records (if they can access) -->
                    <?php $isDoctorMedicalRecordsActive = url_is('doctor/medical-records*'); ?>
                    <li class="nav-item <?= $isDoctorMedicalRecordsActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDoctorMedicalRecordsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>
                                Medical Records
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/medical-records') ?>" class="nav-link <?= uri_string() == 'doctor/medical-records' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-notes-medical"></i>
                                    <p>View Records</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Doctor's Diagnostics & Imaging (if they can order/view results) -->
                    <?php $isDoctorDiagnosticsActive = url_is('doctor/diagnostics*'); ?>
                    <li class="nav-item <?= $isDoctorDiagnosticsActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDoctorDiagnosticsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-x-ray"></i>
                            <p>
                                Diagnostics & Imaging
                                <i class="nav-arrow fas fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/diagnostics/orders') ?>" class="nav-link <?= uri_string() == 'doctor/diagnostics/orders' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-notes-medical"></i>
                                    <p>Order Imaging</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('doctor/diagnostics/results') ?>" class="nav-link <?= uri_string() == 'doctor/diagnostics/results' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-file-image"></i>
                                    <p>View Results</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php endif; // End Doctor-specific sections ?>

                <!-- Logout Link (Visible to all authenticated users) -->
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
