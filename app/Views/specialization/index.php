<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><?= esc($page_title ?? 'Specializations List') ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('specializations/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Specialization
                    </a>
                </div>
            </div>
            
            <!-- Display Flash Messages (Success/Error) -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <table id="specializationTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php if (!empty($specializations)): ?>
                            <?php foreach ($specializations as $spec): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($spec['name']) ?></td>
                                    <td><?= esc($spec['description'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= base_url('specializations/edit/' . $spec['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>  
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?= $spec['id'] ?>" data-name="<?= esc($spec['name']) ?>">
                                            <i class="fas fa-trash"></i>  
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No specializations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the specialization: <strong><span id="specName"></span></strong>?
                <p class="text-danger mt-2"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include CSRF Token as meta tag for easy JS access -->
<meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" id="csrf_token_meta">

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Get CSRF details from the meta tag
    const csrfTokenMeta = document.getElementById('csrf_token_meta');
    const csrfName = csrfTokenMeta.getAttribute('name');
    const csrfHash = csrfTokenMeta.getAttribute('content');

    // Update the hash after every request for security (important for CI AJAX)
    let currentCsrfHash = csrfHash;

    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = $('#deleteModal'); // Assuming jQuery/Bootstrap 4+ modal handling
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const specNameSpan = document.getElementById('specName');
        let specializationIdToDelete = null;

        // 1. Setup Click Handlers for Delete Buttons
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                specializationIdToDelete = this.dataset.id;
                const specializationName = this.dataset.name;
                specNameSpan.textContent = specializationName;
                deleteModal.modal('show'); // Show the Bootstrap modal
            });
        });

        // 2. Setup Confirmation Button Handler
        confirmDeleteBtn.addEventListener('click', async function() {
            if (specializationIdToDelete) {
                // Temporarily disable the button to prevent double-click
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';

                try {
                    const response = await fetch('<?= base_url('specializations/delete') ?>/' + specializationIdToDelete, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            // Add the current CSRF token to the header
                            [csrfName]: currentCsrfHash
                        },
                    });

                    const result = await response.json();

                    // Update CSRF hash from the response header if available (CI provides a new one)
                    const newCsrfHash = response.headers.get(csrfName) || result.token || null;
                    if (newCsrfHash) {
                        currentCsrfHash = newCsrfHash;
                        csrfTokenMeta.setAttribute('content', newCsrfHash);
                    }

                    if (response.ok && result.status === 'success') {
                        // Redirect to list page with success message
                        window.location.href = '<?= base_url('specializations') ?>?success=' + encodeURIComponent(result.message);
                    } else {
                        // Redirect with an error message in the query string (non-alert error handling)
                        const errorMessage = result.message || 'The specialization could not be deleted.';
                        window.location.href = '<?= base_url('specializations') ?>?error=' + encodeURIComponent(errorMessage);
                    }

                } catch (error) {
                    console.error('Fetch error during deletion:', error);
                    // Redirect with a generic network error message
                    window.location.href = '<?= base_url('specializations') ?>?error=' + encodeURIComponent('A network error occurred. Please try again.');
                }
            }
        });

        // 3. Handle Error/Success messages from query parameters (fallback for redirect)
        const urlParams = new URLSearchParams(window.location.search);
        const successMsg = urlParams.get('success');
        const errorMsg = urlParams.get('error');

        // Function to display the message in an alert box
        const displayAlert = (message, type) => {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = decodeURIComponent(message) + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            const cardHeader = document.querySelector('.card-header');
            if (cardHeader) {
                // Insert the alert after the card header
                cardHeader.parentNode.insertBefore(alertDiv, cardHeader.nextSibling);
            }
        };

        if (successMsg) {
            displayAlert(successMsg, 'success');
        } else if (errorMsg) {
            displayAlert(errorMsg, 'danger');
        }

        // Clean up the URL to prevent re-displaying on refresh
        if (successMsg || errorMsg) {
             // Use history.replaceState to clean the URL without refreshing
            history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
<?= $this->endSection() ?>
