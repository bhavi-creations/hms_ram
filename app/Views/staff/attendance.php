<?php
// Extends the main layout defined in C:\xampp\htdocs\hms_ram\app\Views\layouts\main.php
$this->extend('layouts/main');
$this->section('content');
?>

<div class="row pt-4">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800">Staff Attendance Overview</h1>
        <!-- Display a placeholder/warning if the list of authorized staff IDs is missing -->
        <?php if (!isset($authorizedStaffIdsJson) && in_array($userRoleName ?? '', ['Team Leader', 'Admin'])): ?>
            <div class="alert alert-warning">
                <strong>Configuration Warning:</strong> The list of staff to display is missing from the server. Showing all available records.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-1"></i> All Staff Records</h3>
            </div>
            <div class="card-body">
                
                <!-- Initial state: Only loading spinner is visible -->
                <div id="loading-spinner" class="text-center py-5">
                    <i class="fas fa-sync fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Loading all staff attendance records...</p>
                </div>

                <!-- Filter container starts hidden (d-none) -->
                <div class="row mb-3 d-none" id="filter-container">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date-filter">Filter by Date:</label>
                            <input type="date" id="date-filter" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Data container starts hidden (d-none) -->
                <div class="table-responsive d-none" id="attendance-data-container">
                    <table class="table table-bordered table-striped" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Staff Name</th>
                                <th>User ID</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="staff-attendance-log">
                            <!-- Data will be inserted here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<!-- Firebase and Staff Attendance Script -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
    import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
    // NOTE: Changed import from collectionGroup to collection since we are querying a single public collection now
    import { getFirestore, onSnapshot, query, collection, where } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
    // Using SweetAlert2 which is loaded in the main layout

    // --- Global Variables (Mandatory Canvas Environment) ---
    const appId = typeof __app_id !== 'undefined' ? __app_id : 'local-dev-app-id';
    
    // --- Define the new Public Path for Attendance Overview Data ---
    const ATTENDANCE_OVERVIEW_PATH = `artifacts/${appId}/public/data/attendance_overview`;
    
    const MOCK_FIREBASE_CONFIG = {
        apiKey: "AIzaSy_MOCK_API_KEY",
        authDomain: "mock-project.firebaseapp.com",
        projectId: "mock-project-id",
        storageBucket: "mock-project.appspot.com",
        messagingSenderId: "1234567890",
        appId: "1:1234567890:web:mockeddeviceid",
    };

    const firebaseConfig = typeof __firebase_config !== 'undefined' 
        ? JSON.parse(__firebase_config) 
        : MOCK_FIREBASE_CONFIG; 

    const initialAuthToken = typeof __initial_auth_token !== 'undefined' ? __initial_auth_token : null;

    let app, db, auth, userId = null;
    let allAttendanceData = [];
    let isAuthReady = false;
    let isLocalDev = typeof __firebase_config === 'undefined';
    
    // PHP variables injected from the controller (assumed)
    const authorizedUserIds = JSON.parse('<?= $authorizedStaffIdsJson ?? '[]' ?>');
    
    // --- Utility Functions ---

    /**
     * Formats a timestamp into a human-readable time string, forcing the time zone to IST.
     * @param {firebase.firestore.Timestamp|Date|string} timestamp - The time to format.
     * @returns {string} The formatted time string (e.g., "02:30 PM IST").
     */
    function formatTime(timestamp) {
        if (!timestamp) return '---';
        const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
        
        // --- CRITICAL UPDATE: Force time zone to Asia/Kolkata (IST) ---
        return date.toLocaleTimeString('en-IN', { 
            hour: '2-digit', 
            minute: '2-digit', 
            hour12: true,
            timeZone: 'Asia/Kolkata' // <-- Enforces IST
        });
    }

    function calculateDuration(startTimestamp, endTimestamp) {
        if (!startTimestamp || !endTimestamp) return 'N/A';
        const start = startTimestamp.toDate ? startTimestamp.toDate() : new Date(startTimestamp);
        const end = endTimestamp.toDate ? endTimestamp.toDate() : new Date(endTimestamp);
        const diffMs = Math.abs(end.getTime() - start.getTime());
        
        const hours = Math.floor(diffMs / (1000 * 60 * 60));
        const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

        return `${hours}h ${minutes}m`;
    }
    
    // --- UI Update Functions ---

    const logBody = document.getElementById('staff-attendance-log');
    const loadingSpinner = document.getElementById('loading-spinner');
    const dataContainer = document.getElementById('attendance-data-container');
    const filterContainer = document.getElementById('filter-container');
    const dateFilterInput = document.getElementById('date-filter');

    /**
     * Renders the staff attendance log based on the current filter.
     * @param {string | null} filterDate - YYYY-MM-DD date string to filter by, or null for all.
     */
    function renderAttendanceLog(filterDate = null) {
        // Hide spinner and show content containers
        loadingSpinner.classList.add('d-none');
        dataContainer.classList.remove('d-none');
        filterContainer.classList.remove('d-none');
        
        logBody.innerHTML = '';
        
        let filteredData = allAttendanceData;

        if (filterDate) {
            filteredData = allAttendanceData.filter(log => log.date === filterDate);
        }

        if (filteredData.length === 0) {
            logBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        ${filterDate ? `No records found for <strong>${filterDate}</strong>.` : 'No staff attendance records found.'}
                    </td>
                </tr>
            `;
            return;
        }

        // Sort by date descending (most recent first) and then by name
        filteredData.sort((a, b) => {
            if (a.date !== b.date) {
                // Assuming date is in YYYY-MM-DD format for string comparison
                return (b.date > a.date) ? 1 : -1;
            }
            return a.userName.localeCompare(b.userName);
        });

        filteredData.forEach(log => {
            const duration = calculateDuration(log.clockIn, log.clockOut);
            const statusText = log.clockOut ? 'Completed' : 'Clocked In';
            const statusBadgeClass = log.clockOut ? 'badge-success' : 'badge-primary';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${log.date}</td>
                <td><strong class="text-primary">${log.userName}</strong></td>
                <td><small class="text-muted">${log.recordUserId}</small></td>
                <td><span class="badge bg-success">${formatTime(log.clockIn)}</span></td>
                <td>${log.clockOut ? `<span class="badge bg-danger">${formatTime(log.clockOut)}</span>` : '---'}</td>
                <td>${log.clockOut ? `<strong class="text-info">${duration}</strong>` : '<span class="text-warning">In Progress</span>'}</td>
                <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
            `;
            logBody.appendChild(row);
        });
    }

    // --- Core Firebase Logic ---

    async function initializeFirebase() {
        try {
            if (isLocalDev) {
                const message = "Running in Local Development Mode: Database functionality is disabled. Displaying mock data structure.";
                console.warn(message);
                
                loadingSpinner.innerHTML = `<i class="fas fa-desktop mr-2 text-warning"></i> ${message}`;
                
                setTimeout(() => {
                    loadingSpinner.classList.add('d-none');
                    dataContainer.classList.remove('d-none');
                    filterContainer.classList.remove('d-none');
                    logBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3">Database connection bypassed. This view will only show real-time data when running in a configured Canvas/Live environment.</td></tr>`;
                }, 500);
                
                return; // Stop initialization for local dev
            }

            // --- Normal initialization for live/configured environment ---
            app = initializeApp(firebaseConfig);
            db = getFirestore(app);
            auth = getAuth(app);

            if (initialAuthToken) {
                await signInWithCustomToken(auth, initialAuthToken);
            } else {
                await signInAnonymously(auth);
            }

            onAuthStateChanged(auth, (user) => {
                if (user) {
                    userId = user.uid;
                    isAuthReady = true;
                    startStaffAttendanceListener();
                } else {
                    console.error("User is not authenticated for staff attendance view.");
                    loadingSpinner.innerHTML = `<i class="fas fa-lock mr-2 text-danger"></i> Authentication failed. Cannot load data.`;
                    isAuthReady = true; 
                }
            });

        } catch (error) {
            console.error("Error initializing Firebase:", error);
            Swal.fire('Error', 'Failed to initialize application services.', 'error');
        }
    }

    /**
     * Starts the realtime listener for all staff attendance logs from the public overview collection.
     */
    function startStaffAttendanceListener() {
        if (!isAuthReady || !db) return;

        // Query the dedicated public overview collection
        // Path: /artifacts/{appId}/public/data/attendance_overview
        const attendanceCollection = collection(db, ATTENDANCE_OVERVIEW_PATH);
        
        let q = query(attendanceCollection);

        // --- IMPORTANT: Filtering Logic for Team Leaders ---
        // If the CodeIgniter controller successfully provided a list of authorized IDs,
        // we use a 'where in' clause to restrict the view.
        if (authorizedUserIds.length > 0) {
            // NOTE: Firestore 'in' query supports up to 10 items. If more than 10 staff members,
            // we'd need to fetch all and filter client-side, or use multiple queries. 
            // Assuming few enough staff for a single query.
            if (authorizedUserIds.length <= 10 && authorizedUserIds.every(id => id !== 'ALL_STAFF_VIEW')) {
                q = query(q, where('recordUserId', 'in', authorizedUserIds));
            } else if (authorizedUserIds.length > 10 && authorizedUserIds.every(id => id !== 'ALL_STAFF_VIEW')) {
                // If more than 10 but not ALL_STAFF_VIEW, we must filter client-side after fetching everything allowed by security rules
                // For simplicity here, we'll proceed without 'where in' and rely on security rules to restrict the query.
                // A better implementation might require multiple queries or client-side filtering after a basic fetch.
                console.warn("Too many staff members for a single 'where in' query. Relying on security rules.");
            }
            // If authorizedUserIds contains 'ALL_STAFF_VIEW', we query everything (no 'where in' needed).
        }
        
        onSnapshot(q, (snapshot) => {
            allAttendanceData = [];

            snapshot.forEach((doc) => {
                const data = doc.data();
                if (data.clockIn && data.userName && data.recordUserId) {
                    
                    // Client-side filter fallback for managers with > 10 staff or if ALL_STAFF_VIEW is not set
                    const isAuthorized = authorizedUserIds.includes('ALL_STAFF_VIEW') || authorizedUserIds.includes(data.recordUserId.toString());
                    
                    if (isAuthorized) {
                         allAttendanceData.push({
                            ...data,
                            date: data.date, 
                            recordUserId: data.recordUserId,
                        });
                    }
                }
            });

            // Re-render the log with the current filter applied (if any)
            renderAttendanceLog(dateFilterInput.value);

        }, (error) => {
            console.error("Error listening to staff attendance log:", error);
            loadingSpinner.innerHTML = `<i class="fas fa-exclamation-triangle mr-2 text-danger"></i> Failed to load attendance data.`;
            dataContainer.classList.add('d-none');
            filterContainer.classList.add('d-none');
            Swal.fire('Error', 'Failed to load staff attendance data in real-time. Check permissions and data path.', 'error');
        });
    }


    // --- Event Listeners and Initialization ---

    // Use jQuery ready function for initialization
    $(function() {
        initializeFirebase();
        
        // Add event listener for date filter
        dateFilterInput.addEventListener('change', (e) => {
            renderAttendanceLog(e.target.value);
        });
    });
</script>
<?php $this->endSection() ?>
