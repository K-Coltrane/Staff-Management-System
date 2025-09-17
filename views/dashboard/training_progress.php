<?php
$pageTitle = 'Training Progress';
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
                                <h5 class="card-title text-primary">Training Progress</h5>
                                <p class="mb-4">Track your training progress, view completion rates, and monitor your professional development journey.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Training Progress" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-trending-up text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Overall Progress</span>
                                <h3 class="card-title mb-0">68%</h3>
                                <small class="text-muted">This Year</small>
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
                                <span class="fw-semibold d-block mb-1">Hours Completed</span>
                                <h3 class="card-title mb-0">204</h3>
                                <small class="text-muted">Out of 300</small>
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
                                <i class="bx bx-check-circle text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Courses Done</span>
                                <h3 class="card-title mb-0">8</h3>
                                <small class="text-muted">This Year</small>
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
                                <i class="bx bx-star text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Average Score</span>
                                <h3 class="card-title mb-0">92%</h3>
                                <small class="text-muted">All Courses</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Charts -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Training Progress Over Time</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="progressChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Progress by Category</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Training -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Training</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">JavaScript Advanced</h6>
                                            <span class="badge bg-label-warning">In Progress</span>
                                        </div>
                                        <p class="text-muted">Advanced programming concepts</p>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Progress</span>
                                                <span>75%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">30/40 hours</small>
                                            <small class="text-muted">Due: Feb 15</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm w-100" onclick="continueTraining('js-advanced')">
                                                <i class="bx bx-play me-1"></i>Continue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Project Management</h6>
                                            <span class="badge bg-label-info">Enrolled</span>
                                        </div>
                                        <p class="text-muted">PMI methodologies</p>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Progress</span>
                                                <span>45%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">27/60 hours</small>
                                            <small class="text-muted">Due: Mar 1</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm w-100" onclick="startTraining('pm')">
                                                <i class="bx bx-play me-1"></i>Start
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Leadership Skills</h6>
                                            <span class="badge bg-label-secondary">Not Started</span>
                                        </div>
                                        <p class="text-muted">Management and leadership</p>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Progress</span>
                                                <span>20%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 20%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">6/30 hours</small>
                                            <small class="text-muted">Due: Mar 15</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-outline-primary btn-sm w-100" onclick="enrollTraining('leadership')">
                                                <i class="bx bx-plus me-1"></i>Enroll
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Training -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Completed Training</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="exportProgress()">
                                <i class="bx bx-download me-1"></i>Export
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="viewAllCompleted()">
                                <i class="bx bx-list-ul me-1"></i>View All
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Score</th>
                                        <th>Completed Date</th>
                                        <th>Certificate</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-success me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Security Awareness</h6>
                                                    <small class="text-muted">Cybersecurity best practices</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-primary">Security</span></td>
                                        <td>20 hours</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-success">95%</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>Jan 31, 2024</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success" onclick="viewCertificate('security')">
                                                <i class="bx bx-certificate me-1"></i>View
                                            </button>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewDetails('security')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="retakeCourse('security')"><i class="bx bx-refresh me-2"></i>Retake</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="shareCertificate('security')"><i class="bx bx-share me-2"></i>Share</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-info me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">React Development</h6>
                                                    <small class="text-muted">Frontend development</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-info">Development</span></td>
                                        <td>50 hours</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-success">88%</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>Jan 15, 2024</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success" onclick="viewCertificate('react')">
                                                <i class="bx bx-certificate me-1"></i>View
                                            </button>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewDetails('react')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="retakeCourse('react')"><i class="bx bx-refresh me-2"></i>Retake</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="shareCertificate('react')"><i class="bx bx-share me-2"></i>Share</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-book text-warning me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Node.js Backend</h6>
                                                    <small class="text-muted">Server-side development</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-warning">Backend</span></td>
                                        <td>45 hours</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-success">92%</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>Dec 20, 2023</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success" onclick="viewCertificate('nodejs')">
                                                <i class="bx bx-certificate me-1"></i>View
                                            </button>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewDetails('nodejs')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="retakeCourse('nodejs')"><i class="bx bx-refresh me-2"></i>Retake</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="shareCertificate('nodejs')"><i class="bx bx-share me-2"></i>Share</a></li>
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

<?php include '../../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Training Progress Functions
function continueTraining(courseId) {
    showToast(`Continuing ${courseId} training...`, 'info');
}

function startTraining(courseId) {
    showToast(`Starting ${courseId} training...`, 'info');
}

function enrollTraining(courseId) {
    showToast(`Enrolling in ${courseId} training...`, 'info');
}

function viewCertificate(certId) {
    showToast(`Opening ${certId} certificate...`, 'info');
}

function viewDetails(courseId) {
    showToast(`Viewing details for ${courseId}...`, 'info');
}

function retakeCourse(courseId) {
    if (confirm('Are you sure you want to retake this course?')) {
        showToast(`Retaking ${courseId}...`, 'info');
    }
}

function shareCertificate(certId) {
    showToast(`Sharing ${certId} certificate...`, 'info');
}

function exportProgress() {
    showToast('Exporting training progress...', 'info');
    // Simulate export
    setTimeout(() => {
        showToast('Progress exported successfully!', 'success');
    }, 2000);
}

function viewAllCompleted() {
    showToast('Viewing all completed training...', 'info');
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

// Initialize charts
function initializeCharts() {
    // Progress Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Training Hours',
                data: [20, 35, 45, 60, 75, 90, 110, 130, 150, 170, 190, 204],
                borderColor: '#696cff',
                backgroundColor: 'rgba(105, 108, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 250
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Development', 'Security', 'Management', 'Other'],
            datasets: [{
                data: [45, 25, 20, 10],
                backgroundColor: ['#696cff', '#71dd37', '#ffab00', '#ff3e1d'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Initialize page
$(document).ready(function() {
    initializeCharts();
    console.log('Training Progress page initialized');
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

.chart-container {
    position: relative;
    height: 300px;
}
</style>
