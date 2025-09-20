<aside class="app-sidebar side_bg shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url('/') ?>" class="brand-link">
            <img src="<?= base_url('public/assets/img/AdminLTELogo.png') ?>" alt="HMS Logo" class="brand-image" />
            <span class="brand-text">HMS</span> </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <?php
                    $dashboardUrl = base_url('dashboard'); // Default dashboard
                    $currentUri = uri_string();
                    $isActive = $currentUri == 'dashboard';

                    if (session()->get('role_id') == 2) { // Doctor
                        $dashboardUrl = base_url('doctor/dashboard');
                        $isActive = $currentUri == 'doctor/dashboard';
                    } elseif (session()->get('role_id') == 7 || session()->get('role_id') == 8) { // Pharmacy Roles
                        $dashboardUrl = base_url('pharmacy/dashboard');
                        $isActive = url_is('pharmacy*');
                    }
                    ?>
                    <a href="<?= $dashboardUrl ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (session()->get('role_id') == 1): // Admin-only sections 
                ?>
                    <?php $isPatientManagementActive = url_is('patients*') || url_is('opd*') || url_is('ipd*') || url_is('casualty*') || url_is('medical-records*') || url_is('general*') || url_is('discharged-patients*'); ?>
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
                            <li class="nav-item">
                                <a href="<?= base_url('discharged-patients') ?>" class="nav-link <?= uri_string() == 'discharged-patients' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-user-check"></i>
                                    <p>Discharged Patients</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php $isDoctorManagementActive = url_is('doctors*') && !url_is('doctor/appointments*'); // Exclude doctor's own panel from this 
                    ?>
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
                <?php endif; // End Admin-only sections 
                ?>

                <?php if (session()->get('role_id') == 2): // Doctor-specific sections 
                ?>
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
                        </ul>
                    </li>

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

                    <?php $isDoctorDiagnosticsActive = url_is('doctor/diagnostics*'); ?>
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
                <?php endif; // End Doctor-specific sections 
                ?>

                <?php
                $isPharmacyManagementActive = url_is('pharmacy*');
                $roleId = session()->get('role_id');
                ?>

                <?php if ($roleId == 1 || $roleId == 7 || $roleId == 8): // Admin, Pharmacy Manager, or Sales Person 
                ?>
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
                                <a href="<?= base_url('pharmacy/dashboard') ?>" class="nav-link <?= uri_string() == 'pharmacy' || uri_string() == 'pharmacy/dashboard' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            <?php if ($roleId == 1 || $roleId == 7): // Admin or Pharmacy Manager Only 
                            ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/brands') ?>" class="nav-link <?= uri_string() == 'pharmacy/brands' || url_is('pharmacy/brands/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-tags"></i>
                                        <p>Manage Brands</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/generics') ?>" class="nav-link <?= uri_string() == 'pharmacy/generics' || url_is('pharmacy/generics/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-capsules"></i>
                                        <p>Manage Generics</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/medicines') ?>" class="nav-link <?= uri_string() == 'pharmacy/medicines' || url_is('pharmacy/medicines/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-capsules"></i>
                                        <p>Manage Medicines</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/categories') ?>" class="nav-link <?= uri_string() == 'pharmacy/categories' || url_is('pharmacy/categories/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-tags"></i>
                                        <p>Medicine Categories</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/dosage_forms') ?>" class="nav-link <?= uri_string() == 'pharmacy/dosage_forms' || url_is('pharmacy/dosage_forms/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-flask"></i>
                                        <p>Dosage Forms</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/units_of_measure') ?>" class="nav-link <?= uri_string() == 'pharmacy/units_of_measure' || url_is('pharmacy/units_of_measure/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-balance-scale"></i>
                                        <p>Units of Measure</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/suppliers') ?>" class="nav-link <?= uri_string() == 'pharmacy/suppliers' || url_is('pharmacy/suppliers/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-truck"></i>
                                        <p>Suppliers</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/manufacturers') ?>" class="nav-link <?= uri_string() == 'pharmacy/manufacturers' || url_is('pharmacy/manufacturers/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-industry"></i>
                                        <p>Manufacturers</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/purchases') ?>" class="nav-link <?= uri_string() == 'pharmacy/purchases' || url_is('pharmacy/purchases/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-boxes"></i>
                                        <p>Purchases</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/medicines/adjust-stock') ?>" class="nav-link <?= uri_string() == 'pharmacy/medicines/adjust-stock' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-exchange-alt"></i>
                                        <p>Stock Adjustments</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/returns') ?>" class="nav-link <?= uri_string() == 'pharmacy/returns' || url_is('pharmacy/returns/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-undo"></i>
                                        <p>Returns</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/reports') ?>" class="nav-link <?= uri_string() == 'pharmacy/reports' || url_is('pharmacy/reports/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-chart-pie"></i>
                                        <p>Reports</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('pharmacy/salespersons') ?>" class="nav-link <?= uri_string() == 'pharmacy/salespersons' || url_is('pharmacy/salespersons/*') ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-user-tie"></i>
                                        <p>Sales Persons</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a href="<?= base_url('pharmacy/sales') ?>" class="nav-link <?= uri_string() == 'pharmacy/sales' || url_is('pharmacy/sales/*') ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-cash-register"></i>
                                    <p>POS Panel (Sales)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('pharmacy/sales/listBills/all') ?>" class="nav-link <?= (url_is('pharmacy/sales/listBills*')) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sales Bills</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; // End Pharmacy Management section 
                ?>





                <?php if (  $roleId == 8): // Pharmacy Manager or Sales Person Only 
                ?>
                    <li class="nav-item">
                        <a href="<?= site_url('pharmacy/salespersons/profile') ?>" class="nav-link <?= uri_string() == 'pharmacy/salespersons/profile' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>My Sales Report</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?= base_url('profile') ?>" class="nav-link <?= uri_string() == 'profile' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>My Profile</p>
                    </a>
                </li>

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