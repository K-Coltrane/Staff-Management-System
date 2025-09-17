<?php
include_once '../../config/database.php';
include_once '../../includes/functions.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit; }
?>
<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Training & Development</title>
    <link rel="stylesheet" href="../../assets/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include '../../includes/sidebar.php'; ?>
        <div class="layout-page">
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Training & Development</h4>
              <div class="card">
                <div class="card-body">
                  <a href="training_requirements.php" class="btn btn-primary me-2">Training Requirements</a>
                  <a href="training_progress.php" class="btn btn-outline-primary">Training Progress</a>
                </div>
              </div>
            </div>
            <footer class="content-footer footer bg-footer-theme"><div class="container-xxl"><div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column"><div class="mb-2 mb-md-0">© <script>document.write(new Date().getFullYear())</script>, Staff Management System</div></div></div></footer>
          </div>
        </div>
      </div>
    </div>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/js/main.js"></script>
  </body>
</html>

<?php
$pageTitle = 'Training & Development';
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
                                <h5 class="card-title text-primary">Training & Professional Development</h5>
                                <p class="mb-4">Track your training progress, view requirements, and manage your professional development journey.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Training & Development" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Overview Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-book text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Completed Courses</span>
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
                                <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">In Progress</span>
                                <h3 class="card-title mb-0">3</h3>
                                <small class="text-muted">Active Courses</small>
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
                                <i class="bx bx-target-lock text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Required</span>
                                <h3 class="card-title mb-0">5</h3>
                                <small class="text-muted">Mandatory Courses</small>
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
                                <i class="bx bx-certificate text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Certifications</span>
                                <h3 class="card-title mb-0">12</h3>
                                <small class="text-muted">Total Earned</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="trainingTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                    <i class="bx bx-home me-1"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="requirements-tab" data-bs-toggle="tab" data-bs-target="#requirements" type="button" role="tab">
                                    <i class="bx bx-list-check me-1"></i>Requirements
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">
                                    <i class="bx bx-trending-up me-1"></i>Progress
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="certifications-tab" data-bs-toggle="tab" data-bs-target="#certifications" type="button" role="tab">
                                    <i class="bx bx-certificate me-1"></i>Certifications
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="trainingTabsContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Training Progress Overview</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Current Training</h6>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>JavaScript Advanced</span>
                                                                <span>75%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>Project Management</span>
                                                                <span>45%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>Leadership Skills</span>
                                                                <span>20%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: 20%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Upcoming Deadlines</h6>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">JavaScript Advanced</h6>
                                                                    <small class="text-muted">Due: Feb 15, 2024</small>
                                                                </div>
                                                                <span class="badge bg-warning">5 days</span>
                                                            </div>
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">Project Management</h6>
                                                                    <small class="text-muted">Due: Mar 1, 2024</small>
                                                                </div>
                                                                <span class="badge bg-info">19 days</span>
                                                            </div>
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">Leadership Skills</h6>
                                                                    <small class="text-muted">Due: Mar 15, 2024</small>
                                                                </div>
                                                                <span class="badge bg-success">33 days</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Quick Actions</h5>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" onclick="enrollInCourse()">
                                                <i class="bx bx-plus me-2"></i>Enroll in Course
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="viewAllCourses()">
                                                <i class="bx bx-list-ul me-2"></i>View All Courses
                                            </button>
                                            <button class="btn btn-outline-success" onclick="requestTraining()">
                                                <i class="bx bx-bookmark me-2"></i>Request Training
                                            </button>
                                            <button class="btn btn-outline-info" onclick="viewCertifications()">
                                                <i class="bx bx-certificate me-2"></i>View Certifications
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements Tab -->
                            <div class="tab-pane fade" id="requirements" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Training Requirements</h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Course Name</th>
                                                        <th>Type</th>
                                                        <th>Duration</th>
                                                        <th>Status</th>
                                                        <th>Due Date</th>
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
                                                        <td><span class="badge bg-label-primary">Mandatory</span></td>
                                                        <td>40 hours</td>
                                                        <td><span class="badge bg-label-warning">In Progress</span></td>
                                                        <td>Feb 15, 2024</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="continueCourse('js-advanced')">
                                                                <i class="bx bx-play me-1"></i>Continue
                                                            </button>
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
                                                        <td><span class="badge bg-label-success">Optional</span></td>
                                                        <td>60 hours</td>
                                                        <td><span class="badge bg-label-info">Enrolled</span></td>
                                                        <td>Mar 1, 2024</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="startCourse('pm')">
                                                                <i class="bx bx-play me-1"></i>Start
                                                            </button>
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
                                                        <td>30 hours</td>
                                                        <td><span class="badge bg-label-secondary">Not Started</span></td>
                                                        <td>Mar 15, 2024</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary" onclick="enrollCourse('leadership')">
                                                                <i class="bx bx-plus me-1"></i>Enroll
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Tab -->
                            <div class="tab-pane fade" id="progress" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Training Progress</h5>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h6>JavaScript Advanced</h6>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">30/40 hours completed</small>
                                                        <small class="text-muted">75%</small>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <h6>Project Management</h6>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">27/60 hours completed</small>
                                                        <small class="text-muted">45%</small>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <h6>Leadership Skills</h6>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: 20%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">6/30 hours completed</small>
                                                        <small class="text-muted">20%</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Achievements</h5>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bx bx-trophy text-warning me-2" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h6 class="mb-0">Course Completion</h6>
                                                        <small class="text-muted">8 courses completed</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bx bx-star text-success me-2" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h6 class="mb-0">Perfect Score</h6>
                                                        <small class="text-muted">5 perfect scores</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bx bx-time text-info me-2" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h6 class="mb-0">Early Completion</h6>
                                                        <small class="text-muted">3 early finishes</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certifications Tab -->
                            <div class="tab-pane fade" id="certifications" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Certifications</h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <i class="bx bx-certificate text-primary" style="font-size: 3rem;"></i>
                                                        <h6 class="mt-3">AWS Certified Developer</h6>
                                                        <p class="text-muted">Amazon Web Services</p>
                                                        <span class="badge bg-label-success">Valid</span>
                                                        <p class="mt-2"><small>Expires: Dec 15, 2024</small></p>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="viewCertificate('aws-dev')">
                                                            <i class="bx bx-show me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <i class="bx bx-certificate text-success" style="font-size: 3rem;"></i>
                                                        <h6 class="mt-3">PMP Certification</h6>
                                                        <p class="text-muted">Project Management Institute</p>
                                                        <span class="badge bg-label-success">Valid</span>
                                                        <p class="mt-2"><small>Expires: Mar 20, 2025</small></p>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="viewCertificate('pmp')">
                                                            <i class="bx bx-show me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <i class="bx bx-certificate text-warning" style="font-size: 3rem;"></i>
                                                        <h6 class="mt-3">Scrum Master</h6>
                                                        <p class="text-muted">Scrum Alliance</p>
                                                        <span class="badge bg-label-warning">Expiring Soon</span>
                                                        <p class="mt-2"><small>Expires: Feb 28, 2024</small></p>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="viewCertificate('scrum')">
                                                            <i class="bx bx-show me-1"></i>View
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
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>

<!-- Enroll Course Modal -->
<div class="modal fade" id="enrollCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enroll in Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="enrollCourseForm">
                    <div class="mb-3">
                        <label for="courseSelect" class="form-label">Select Course</label>
                        <select class="form-select" id="courseSelect" name="courseSelect" required>
                            <option value="">Choose a course...</option>
                            <option value="js-advanced">JavaScript Advanced</option>
                            <option value="pm">Project Management</option>
                            <option value="leadership">Leadership Skills</option>
                            <option value="react">React Development</option>
                            <option value="nodejs">Node.js Backend</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="enrollmentReason" class="form-label">Reason for Enrollment</label>
                        <textarea class="form-control" id="enrollmentReason" name="enrollmentReason" rows="3" placeholder="Why do you want to enroll in this course?"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEnrollment()">Enroll</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
// Training & Development Functions
function enrollInCourse() {
    const modal = new bootstrap.Modal(document.getElementById('enrollCourseModal'));
    modal.show();
}

function submitEnrollment() {
    const form = document.getElementById('enrollCourseForm');
    const formData = new FormData(form);
    
    const course = formData.get('courseSelect');
    const reason = formData.get('enrollmentReason');
    
    if (course && reason) {
        showToast('Successfully enrolled in course!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('enrollCourseModal')).hide();
        form.reset();
    } else {
        showToast('Please fill in all required fields', 'error');
    }
}

function viewAllCourses() {
    showToast('Redirecting to all courses...', 'info');
}

function requestTraining() {
    showToast('Training request form opened...', 'info');
}

function viewCertifications() {
    // Switch to certifications tab
    const certificationsTab = document.getElementById('certifications-tab');
    const tab = new bootstrap.Tab(certificationsTab);
    tab.show();
}

function continueCourse(courseId) {
    showToast(`Continuing ${courseId} course...`, 'info');
}

function startCourse(courseId) {
    showToast(`Starting ${courseId} course...`, 'info');
}

function enrollCourse(courseId) {
    showToast(`Enrolling in ${courseId} course...`, 'info');
}

function viewCertificate(certId) {
    showToast(`Opening ${certId} certificate...`, 'info');
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
    console.log('Training & Development page initialized');
});
</script>

<style>
.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #696cff;
    color: #696cff;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #696cff;
    color: #696cff;
}

.progress {
    height: 8px;
}

.card-body h6 {
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}
</style>
