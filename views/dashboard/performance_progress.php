<?php
$pageTitle = 'Performance Progress';
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
                                <h5 class="card-title text-primary">Performance Progress</h5>
                                <p class="mb-4">Track your performance progress over time, view trends, and monitor your professional development journey.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Performance Progress" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
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
                                <h3 class="card-title mb-0">+15%</h3>
                                <small class="text-muted">vs Last Year</small>
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
                                <i class="bx bx-target-lock text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Goals Achieved</span>
                                <h3 class="card-title mb-0">8/10</h3>
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
                                <i class="bx bx-star text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Average Rating</span>
                                <h3 class="card-title mb-0">4.2</h3>
                                <small class="text-muted">Out of 5.0</small>
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
                                <i class="bx bx-time text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Consistency</span>
                                <h3 class="card-title mb-0">92%</h3>
                                <small class="text-muted">Performance</small>
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
                        <h5 class="card-title mb-0">Performance Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Performance Areas</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceAreasChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goals Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Goals Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Complete Project Alpha</h6>
                                            <span class="badge bg-label-success">90%</span>
                                        </div>
                                        <p class="text-muted">Deliver the new project on time and within budget</p>
                                        <div class="mb-3">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Due: Mar 15, 2024</small>
                                            <small class="text-muted">Priority: High</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm" onclick="updateGoalProgress('project-alpha')">
                                                <i class="bx bx-edit me-1"></i>Update Progress
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Learn New Technology</h6>
                                            <span class="badge bg-label-warning">75%</span>
                                        </div>
                                        <p class="text-muted">Master React and Node.js for full-stack development</p>
                                        <div class="mb-3">
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Due: Apr 30, 2024</small>
                                            <small class="text-muted">Priority: Medium</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm" onclick="updateGoalProgress('new-tech')">
                                                <i class="bx bx-edit me-1"></i>Update Progress
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Team Collaboration</h6>
                                            <span class="badge bg-label-info">60%</span>
                                        </div>
                                        <p class="text-muted">Improve collaboration skills and mentor junior developers</p>
                                        <div class="mb-3">
                                            <div class="progress">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Due: Jun 30, 2024</small>
                                            <small class="text-muted">Priority: Medium</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm" onclick="updateGoalProgress('collaboration')">
                                                <i class="bx bx-edit me-1"></i>Update Progress
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">Certification</h6>
                                            <span class="badge bg-label-secondary">25%</span>
                                        </div>
                                        <p class="text-muted">Obtain AWS Certified Developer certification</p>
                                        <div class="mb-3">
                                            <div class="progress">
                                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 25%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Due: Aug 15, 2024</small>
                                            <small class="text-muted">Priority: Low</small>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm" onclick="updateGoalProgress('certification')">
                                                <i class="bx bx-edit me-1"></i>Update Progress
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

        <!-- Performance History -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Performance History</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="exportProgress()">
                                <i class="bx bx-download me-1"></i>Export
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="viewDetailedProgress()">
                                <i class="bx bx-show me-1"></i>View Details
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Overall Rating</th>
                                        <th>Technical Skills</th>
                                        <th>Communication</th>
                                        <th>Teamwork</th>
                                        <th>Leadership</th>
                                        <th>Trend</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-calendar text-primary me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Q4 2023</h6>
                                                    <small class="text-muted">Jan 15, 2024</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-success">4.2/5</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>4.5</td>
                                        <td>4.0</td>
                                        <td>4.2</td>
                                        <td>3.8</td>
                                        <td>
                                            <i class="bx bx-trending-up text-success"></i>
                                            <span class="text-success">+0.2</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPeriodDetails('q4-2023')">
                                                <i class="bx bx-show me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-calendar text-warning me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Q3 2023</h6>
                                                    <small class="text-muted">Oct 15, 2023</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-success">4.0/5</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>4.3</td>
                                        <td>3.8</td>
                                        <td>4.0</td>
                                        <td>3.5</td>
                                        <td>
                                            <i class="bx bx-trending-up text-success"></i>
                                            <span class="text-success">+0.2</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPeriodDetails('q3-2023')">
                                                <i class="bx bx-show me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-calendar text-info me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Q2 2023</h6>
                                                    <small class="text-muted">Jul 15, 2023</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-warning">3.8/5</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>4.0</td>
                                        <td>3.5</td>
                                        <td>3.8</td>
                                        <td>3.2</td>
                                        <td>
                                            <i class="bx bx-trending-down text-danger"></i>
                                            <span class="text-danger">-0.1</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPeriodDetails('q2-2023')">
                                                <i class="bx bx-show me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-calendar text-success me-2"></i>
                                                <div>
                                                    <h6 class="mb-0">Q1 2023</h6>
                                                    <small class="text-muted">Apr 15, 2023</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold text-warning">3.9/5</span>
                                                <i class="bx bx-star text-warning ms-1"></i>
                                            </div>
                                        </td>
                                        <td>4.1</td>
                                        <td>3.6</td>
                                        <td>3.9</td>
                                        <td>3.3</td>
                                        <td>
                                            <i class="bx bx-trending-up text-success"></i>
                                            <span class="text-success">+0.3</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewPeriodDetails('q1-2023')">
                                                <i class="bx bx-show me-1"></i>View
                                            </button>
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

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Goal Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProgressForm">
                    <div class="mb-3">
                        <label for="progressPercentage" class="form-label">Progress Percentage</label>
                        <input type="number" class="form-control" id="progressPercentage" name="progressPercentage" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="progressNotes" class="form-label">Progress Notes</label>
                        <textarea class="form-control" id="progressNotes" name="progressNotes" rows="3" placeholder="Describe your progress and any challenges"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nextSteps" class="form-label">Next Steps</label>
                        <textarea class="form-control" id="nextSteps" name="nextSteps" rows="3" placeholder="What are your next steps to achieve this goal?"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitProgressUpdate()">Update Progress</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentGoalId = null;

// Performance Progress Functions
function updateGoalProgress(goalId) {
    currentGoalId = goalId;
    const modal = new bootstrap.Modal(document.getElementById('updateProgressModal'));
    modal.show();
}

function submitProgressUpdate() {
    const form = document.getElementById('updateProgressForm');
    const formData = new FormData(form);
    
    const percentage = formData.get('progressPercentage');
    const notes = formData.get('progressNotes');
    const nextSteps = formData.get('nextSteps');
    
    if (percentage && notes && nextSteps) {
        showToast('Goal progress updated successfully!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('updateProgressModal')).hide();
        form.reset();
    } else {
        showToast('Please fill in all required fields', 'error');
    }
}

function viewPeriodDetails(periodId) {
    showToast(`Viewing details for ${periodId}...`, 'info');
}

function exportProgress() {
    showToast('Exporting performance progress...', 'info');
    // Simulate export
    setTimeout(() => {
        showToast('Progress exported successfully!', 'success');
    }, 2000);
}

function viewDetailedProgress() {
    showToast('Viewing detailed progress...', 'info');
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
    // Performance Trend Chart
    const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'],
            datasets: [{
                label: 'Overall Rating',
                data: [3.9, 3.8, 4.0, 4.2, 4.2],
                borderColor: '#696cff',
                backgroundColor: 'rgba(105, 108, 255, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Technical Skills',
                data: [4.1, 4.0, 4.3, 4.5, 4.5],
                borderColor: '#71dd37',
                backgroundColor: 'rgba(113, 221, 55, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Communication',
                data: [3.6, 3.5, 3.8, 4.0, 4.0],
                borderColor: '#ffab00',
                backgroundColor: 'rgba(255, 171, 0, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 0.5
                    }
                }
            }
        }
    });

    // Performance Areas Chart
    const areasCtx = document.getElementById('performanceAreasChart').getContext('2d');
    new Chart(areasCtx, {
        type: 'doughnut',
        data: {
            labels: ['Technical Skills', 'Communication', 'Teamwork', 'Leadership', 'Problem Solving'],
            datasets: [{
                data: [4.5, 4.0, 4.2, 3.8, 4.3],
                backgroundColor: ['#696cff', '#71dd37', '#ffab00', '#ff3e1d', '#03c3ec'],
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
    console.log('Performance Progress page initialized');
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

.trend-icon {
    font-size: 1.2rem;
}
</style>
