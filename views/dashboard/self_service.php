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
    <title>Self-Service</title>
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
              <h4 class="fw-bold py-3 mb-4">Self-Service</h4>
              <div class="card">
                <div class="card-body">
                  <a href="self-service/profile.php" class="btn btn-primary me-2">My Profile</a>
                  <a href="self-service/payslips.php" class="btn btn-outline-primary me-2">Payslips</a>
                  <a href="self-service/leave-request.php" class="btn btn-outline-primary">Leave Requests</a>
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
$pageTitle = 'Self-Service Portal';
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
                                <h5 class="card-title text-primary">Self-Service Portal</h5>
                                <p class="mb-4">Access your personal information, view payslips, submit leave requests, and manage your profile through our self-service portal.</p>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" onclick="quickActions()">Quick Actions</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Self-Service Portal" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-calendar text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Leave Balance</span>
                        <h3 class="card-title mb-2">30</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-info-circle"></i> Days Available
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-file text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Payslips</span>
                        <h3 class="card-title mb-2">12</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-info-circle"></i> This Year
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-bell text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Notifications</span>
                        <h3 class="card-title mb-2">3</h3>
                        <small class="text-warning fw-semibold">
                            <i class="bx bx-info-circle"></i> Unread
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-user text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Profile</span>
                        <h3 class="card-title mb-2">85%</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-info-circle"></i> Complete
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bx bx-calendar-plus text-primary" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3">Apply Leave</h6>
                                        <p class="text-muted">Submit a new leave request</p>
                                        <button class="btn btn-primary btn-sm" onclick="applyLeave()">Apply Now</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bx bx-file text-info" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3">View Payslips</h6>
                                        <p class="text-muted">Download your payslips</p>
                                        <button class="btn btn-info btn-sm" onclick="viewPayslips()">View All</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bx bx-user text-warning" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3">Update Profile</h6>
                                        <p class="text-muted">Edit your personal information</p>
                                        <button class="btn btn-warning btn-sm" onclick="updateProfile()">Edit Profile</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bx bx-history text-success" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3">Leave History</h6>
                                        <p class="text-muted">View your leave history</p>
                                        <button class="btn btn-success btn-sm" onclick="viewLeaveHistory()">View History</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Leave Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Vacation Leave Request</h6>
                                    <p class="timeline-text">Feb 1-5, 2024 (5 days)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-warning">Pending</span></small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Sick Leave Request</h6>
                                    <p class="timeline-text">Jan 20-22, 2024 (3 days)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-success">Approved</span></small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Personal Leave Request</h6>
                                    <p class="timeline-text">Jan 15, 2024 (1 day)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-danger">Rejected</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Leave Request Approved</h6>
                                    <p class="mb-1">Your sick leave request has been approved.</p>
                                    <small class="text-muted">2 hours ago</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">New</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Payslip Available</h6>
                                    <p class="mb-1">Your January 2024 payslip is now available.</p>
                                    <small class="text-muted">1 day ago</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">New</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Profile Update Required</h6>
                                    <p class="mb-1">Please update your emergency contact information.</p>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                                <span class="badge bg-secondary rounded-pill">Read</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Summary -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Personal Information Summary</h5>
                        <button class="btn btn-primary btn-sm" onclick="updateProfile()">
                            <i class="bx bx-edit me-1"></i>Edit Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-3">
                                        <img src="<?php echo BASE_URL; ?>assets/img/avatars/1.png" alt="Avatar" class="w-px-40 h-auto rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">John Smith</h6>
                                        <small class="text-muted">EMP001</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> john.smith@company.com</p>
                                        <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                                        <p><strong>Department:</strong> IT</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Position:</strong> Senior Developer</p>
                                        <p><strong>Manager:</strong> Sarah Johnson</p>
                                        <p><strong>Hire Date:</strong> 2020-01-15</p>
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

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickActionsModalTitle">Quick Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-primary w-100" onclick="applyLeave()">
                            <i class="bx bx-calendar-plus me-2"></i>Apply Leave
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-info w-100" onclick="viewPayslips()">
                            <i class="bx bx-file me-2"></i>View Payslips
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-warning w-100" onclick="updateProfile()">
                            <i class="bx bx-user me-2"></i>Update Profile
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-success w-100" onclick="viewLeaveHistory()">
                            <i class="bx bx-history me-2"></i>Leave History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
// Self-Service Portal Functions
function quickActions() {
    const quickModal = new bootstrap.Modal(document.getElementById('quickActionsModal'));
    quickModal.show();
}

function applyLeave() {
    window.location.href = 'leave_apply.php';
}

function viewPayslips() {
    window.location.href = 'payslips.php';
}

function updateProfile() {
    window.location.href = 'profile_update.php';
}

function viewLeaveHistory() {
    window.location.href = 'leave_history.php';
}

// Initialize page
$(document).ready(function() {
    // Add any initialization code here
    console.log('Self-Service Portal initialized');
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e7e7e7;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 5px;
}
</style>
