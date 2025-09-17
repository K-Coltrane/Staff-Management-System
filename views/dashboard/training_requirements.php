<?php
$pageTitle = 'Training Requirements';
$pageCss = ['assets/vendor/css/pages/page-auth.css'];
include '../../../includes/header.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Training Requirements</h5>
                                <p class="mb-4">View and manage your mandatory and recommended training requirements based on your role and department.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Training Requirements" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-error text-danger" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Mandatory</span>
                                <h3 class="card-title mb-0">5</h3>
                                <small class="text-muted">Required Courses</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Completed</span>
                                <h3 class="card-title mb-0">3</h3>
                                <small class="text-muted">Mandatory Done</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Pending</span>
                                <h3 class="card-title mb-0">2</h3>
                                <small class="text-muted">Not Started</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-calendar text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Due Soon</span>
                                <h3 class="card-title mb-0">1</h3>
                                <small class="text-muted">Within 30 days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filter Requirements</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="typeFilter" class="form-label">Type</label>
                                <select class="form-select" id="typeFilter" onchange="filterRequirements()">
                                    <option value="all">All Types</option>
                                    <option value="mandatory">Mandatory</option>
                                    <option value="recommended">Recommended</option>
                                    <option value="optional">Optional</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" onchange="filterRequirements()">
                                    <option value="all">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="not-started">Not Started</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="departmentFilter" class="form-label">Department</label>
                                <select class="form-select" id="departmentFilter" onchange="filterRequirements()">
                                    <option value="all">All Departments</option>
                                    <option value="it">IT</option>
                                    <option value="hr">HR</option>
                                    <option value="finance">Finance</option>
                                    <option value="marketing">Marketing</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-primary" onclick="exportRequirements()">
                                        <i class="bx bx-download me-1"></i>Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Training Requirements</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshRequirements()">
                                <i class="bx bx-refresh me-1"></i>Refresh
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="requestNewRequirement()">
                                <i class="bx bx-plus me-1"></i>Request Training
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="requirementsTable">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Type</th>
                                        <th>Department</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-primary me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">JavaScript Advanced</h6>
                                                    <small class="text-muted">Advanced programming concepts</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-danger">Mandatory</span></td>
                                        <td>IT</td>
                                        <td>40 hours</td>
                                        <td><span class="badge bg-label-warning">In Progress</span></td>
                                        <td>Feb 15, 2024</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                                                </div>
                                                <small>75%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewRequirement('js-advanced')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="continueRequirement('js-advanced')"><i class="bx bx-play me-2"></i>Continue</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="requestExtension('js-advanced')"><i class="bx bx-time me-2"></i>Request Extension</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-success me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Project Management</h6>
                                                    <small class="text-muted">PMI methodologies</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-success">Recommended</span></td>
                                        <td>IT</td>
                                        <td>60 hours</td>
                                        <td><span class="badge bg-label-info">Enrolled</span></td>
                                        <td>Mar 1, 2024</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                                                </div>
                                                <small>45%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewRequirement('pm')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="startRequirement('pm')"><i class="bx bx-play me-2"></i>Start</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="unenrollRequirement('pm')"><i class="bx bx-x me-2"></i>Unenroll</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-warning me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Leadership Skills</h6>
                                                    <small class="text-muted">Management and leadership</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-warning">Recommended</span></td>
                                        <td>IT</td>
                                        <td>30 hours</td>
                                        <td><span class="badge bg-label-secondary">Not Started</span></td>
                                        <td>Mar 15, 2024</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 20%"></div>
                                                </div>
                                                <small>20%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewRequirement('leadership')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="enrollRequirement('leadership')"><i class="bx bx-plus me-2"></i>Enroll</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="requestExtension('leadership')"><i class="bx bx-time me-2"></i>Request Extension</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-danger me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Security Awareness</h6>
                                                    <small class="text-muted">Cybersecurity best practices</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-danger">Mandatory</span></td>
                                        <td>IT</td>
                                        <td>20 hours</td>
                                        <td><span class="badge bg-label-success">Completed</span></td>
                                        <td>Jan 31, 2024</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                                </div>
                                                <small>100%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewRequirement('security')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="viewCertificate('security')"><i class="bx bx-certificate me-2"></i>View Certificate</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="retakeRequirement('security')"><i class="bx bx-refresh me-2"></i>Retake</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>

<!-- Request Training Modal -->
<div class="modal fade" id="requestTrainingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request New Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="requestTrainingForm">
                    <div class="mb-3">
                        <label for="trainingTitle" class="form-label">Training Title</label>
                        <input type="text" class="form-control" id="trainingTitle" name="trainingTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="trainingType" class="form-label">Training Type</label>
                        <select class="form-select" id="trainingType" name="trainingType" required>
                            <option value="">Select Type</option>
                            <option value="mandatory">Mandatory</option>
                            <option value="recommended">Recommended</option>
                            <option value="optional">Optional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="trainingDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="trainingDescription" name="trainingDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="trainingDuration" class="form-label">Duration (hours)</label>
                        <input type="number" class="form-control" id="trainingDuration" name="trainingDuration" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="trainingReason" class="form-label">Reason for Request</label>
                        <textarea class="form-control" id="trainingReason" name="trainingReason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTrainingRequest()">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
// Training Requirements Functions
function filterRequirements() {
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const department = document.getElementById('departmentFilter').value;
    
    // Simulate filtering
    showToast('Requirements filtered successfully!', 'success');
}

function refreshRequirements() {
    showToast('Requirements refreshed successfully!', 'success');
}

function exportRequirements() {
    showToast('Exporting requirements...', 'info');
    // Simulate export
    setTimeout(() => {
        showToast('Requirements exported successfully!', 'success');
    }, 2000);
}

function requestNewRequirement() {
    const modal = new bootstrap.Modal(document.getElementById('requestTrainingModal'));
    modal.show();
}

function submitTrainingRequest() {
    const form = document.getElementById('requestTrainingForm');
    const formData = new FormData(form);
    
    const title = formData.get('trainingTitle');
    const type = formData.get('trainingType');
    const description = formData.get('trainingDescription');
    const duration = formData.get('trainingDuration');
    const reason = formData.get('trainingReason');
    
    if (title && type && description && duration && reason) {
        showToast('Training request submitted successfully!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('requestTrainingModal')).hide();
        form.reset();
    } else {
        showToast('Please fill in all required fields', 'error');
    }
}

function viewRequirement(requirementId) {
    showToast(`Viewing details for ${requirementId}...`, 'info');
}

function continueRequirement(requirementId) {
    showToast(`Continuing ${requirementId}...`, 'info');
}

function startRequirement(requirementId) {
    showToast(`Starting ${requirementId}...`, 'info');
}

function enrollRequirement(requirementId) {
    showToast(`Enrolling in ${requirementId}...`, 'info');
}

function unenrollRequirement(requirementId) {
    if (confirm('Are you sure you want to unenroll from this training?')) {
        showToast(`Unenrolled from ${requirementId}`, 'success');
    }
}

function requestExtension(requirementId) {
    showToast(`Requesting extension for ${requirementId}...`, 'info');
}

function viewCertificate(requirementId) {
    showToast(`Opening certificate for ${requirementId}...`, 'info');
}

function retakeRequirement(requirementId) {
    if (confirm('Are you sure you want to retake this training?')) {
        showToast(`Retaking ${requirementId}...`, 'info');
    }
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to toast container
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Initialize page
$(document).ready(function() {
    console.log('Training Requirements page initialized');
});
</script>

<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.dropdown-toggle::after {
    margin-left: 0.5em;
}

.badge {
    font-size: 0.75em;
}

.progress {
    height: 8px;
}

.card-body h6 {
    font-weight: 600;
}

.table-active {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>
