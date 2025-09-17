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
    <title>Performance Management</title>
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
              <h4 class="fw-bold py-3 mb-4">Performance Management</h4>
              <div class="card">
                <div class="card-body">
                  <a href="performance_reviews.php" class="btn btn-primary me-2">Performance Reviews</a>
                  <a href="performance_progress.php" class="btn btn-outline-primary">Performance Progress</a>
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
$pageTitle = 'Performance Management';
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
                                <h5 class="card-title text-primary">Performance Management</h5>
                                <p class="mb-4">Track performance reviews, set goals, and monitor your professional development progress.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Performance Management" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-target-lock text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Current Rating</span>
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
                                <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Goals Achieved</span>
                                <h3 class="card-title mb-0">8</h3>
                                <small class="text-muted">Out of 10</small>
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
                                <span class="fw-semibold d-block mb-1">Pending Reviews</span>
                                <h3 class="card-title mb-0">2</h3>
                                <small class="text-muted">This Quarter</small>
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
                                <i class="bx bx-trending-up text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Improvement</span>
                                <h3 class="card-title mb-0">+15%</h3>
                                <small class="text-muted">vs Last Quarter</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="performanceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                    <i class="bx bx-home me-1"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                                    <i class="bx bx-clipboard me-1"></i>Reviews
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="goals-tab" data-bs-toggle="tab" data-bs-target="#goals" type="button" role="tab">
                                    <i class="bx bx-target-lock me-1"></i>Goals
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">
                                    <i class="bx bx-trending-up me-1"></i>Progress
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="performanceTabsContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Performance Summary</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Current Goals Progress</h6>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>Complete Project Alpha</span>
                                                                <span>90%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>Learn New Technology</span>
                                                                <span>75%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>Team Collaboration</span>
                                                                <span>60%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Recent Reviews</h6>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">Q4 2023 Review</h6>
                                                                    <small class="text-muted">Completed: Jan 15, 2024</small>
                                                                </div>
                                                                <span class="badge bg-success">4.2/5</span>
                                                            </div>
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">Q3 2023 Review</h6>
                                                                    <small class="text-muted">Completed: Oct 15, 2023</small>
                                                                </div>
                                                                <span class="badge bg-success">4.0/5</span>
                                                            </div>
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1">Q2 2023 Review</h6>
                                                                    <small class="text-muted">Completed: Jul 15, 2023</small>
                                                                </div>
                                                                <span class="badge bg-warning">3.8/5</span>
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
                                            <button class="btn btn-primary" onclick="startSelfReview()">
                                                <i class="bx bx-edit me-2"></i>Start Self Review
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="setNewGoal()">
                                                <i class="bx bx-plus me-2"></i>Set New Goal
                                            </button>
                                            <button class="btn btn-outline-success" onclick="viewAllReviews()">
                                                <i class="bx bx-clipboard me-2"></i>View All Reviews
                                            </button>
                                            <button class="btn btn-outline-info" onclick="viewProgress()">
                                                <i class="bx bx-trending-up me-2"></i>View Progress
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Performance Reviews</h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Review Period</th>
                                                        <th>Reviewer</th>
                                                        <th>Rating</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-clipboard text-primary me-2"></i>
                                                                <div>
                                                                    <h6 class="mb-0">Q4 2023 Review</h6>
                                                                    <small class="text-muted">Annual Performance Review</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>Sarah Johnson</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="fw-semibold text-success">4.2/5</span>
                                                                <i class="bx bx-star text-warning ms-1"></i>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-label-success">Completed</span></td>
                                                        <td>Jan 15, 2024</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#" onclick="viewReview('q4-2023')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="downloadReview('q4-2023')"><i class="bx bx-download me-2"></i>Download</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="shareReview('q4-2023')"><i class="bx bx-share me-2"></i>Share</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-clipboard text-warning me-2"></i>
                                                                <div>
                                                                    <h6 class="mb-0">Q1 2024 Review</h6>
                                                                    <small class="text-muted">Quarterly Performance Review</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>Sarah Johnson</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="fw-semibold text-warning">Pending</span>
                                                                <i class="bx bx-time text-warning ms-1"></i>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-label-warning">In Progress</span></td>
                                                        <td>Apr 15, 2024</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#" onclick="continueReview('q1-2024')"><i class="bx bx-edit me-2"></i>Continue</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="viewReview('q1-2024')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bx bx-clipboard text-info me-2"></i>
                                                                <div>
                                                                    <h6 class="mb-0">Q3 2023 Review</h6>
                                                                    <small class="text-muted">Quarterly Performance Review</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>Sarah Johnson</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="fw-semibold text-success">4.0/5</span>
                                                                <i class="bx bx-star text-warning ms-1"></i>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-label-success">Completed</span></td>
                                                        <td>Oct 15, 2023</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#" onclick="viewReview('q3-2023')"><i class="bx bx-show me-2"></i>View Details</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="downloadReview('q3-2023')"><i class="bx bx-download me-2"></i>Download</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="shareReview('q3-2023')"><i class="bx bx-share me-2"></i>Share</a></li>
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

                            <!-- Goals Tab -->
                            <div class="tab-pane fade" id="goals" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">Performance Goals</h5>
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
                                                            <button class="btn btn-primary btn-sm" onclick="updateGoal('project-alpha')">
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
                                                            <button class="btn btn-primary btn-sm" onclick="updateGoal('new-tech')">
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
                                                            <button class="btn btn-primary btn-sm" onclick="updateGoal('collaboration')">
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
                                                            <button class="btn btn-primary btn-sm" onclick="updateGoal('certification')">
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

                            <!-- Progress Tab -->
                            <div class="tab-pane fade" id="progress" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Performance Progress</h5>
                                        <div class="card">
                                            <div class="card-body">
                                                <canvas id="performanceChart" height="100"></canvas>
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
                                                        <h6 class="mb-0">Top Performer</h6>
                                                        <small class="text-muted">Q4 2023</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bx bx-star text-success me-2" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h6 class="mb-0">Goal Achiever</h6>
                                                        <small class="text-muted">8 goals completed</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bx bx-time text-info me-2" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h6 class="mb-0">Early Completion</h6>
                                                        <small class="text-muted">3 projects early</small>
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

<!-- Set Goal Modal -->
<div class="modal fade" id="setGoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set New Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="setGoalForm">
                    <div class="mb-3">
                        <label for="goalTitle" class="form-label">Goal Title</label>
                        <input type="text" class="form-control" id="goalTitle" name="goalTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="goalDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="goalDescription" name="goalDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="goalPriority" class="form-label">Priority</label>
                        <select class="form-select" id="goalPriority" name="goalPriority" required>
                            <option value="">Select Priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="goalDueDate" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="goalDueDate" name="goalDueDate" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitGoal()">Set Goal</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Performance Management Functions
function startSelfReview() {
    showToast('Starting self review...', 'info');
}

function setNewGoal() {
    const modal = new bootstrap.Modal(document.getElementById('setGoalModal'));
    modal.show();
}

function submitGoal() {
    const form = document.getElementById('setGoalForm');
    const formData = new FormData(form);
    
    const title = formData.get('goalTitle');
    const description = formData.get('goalDescription');
    const priority = formData.get('goalPriority');
    const dueDate = formData.get('goalDueDate');
    
    if (title && description && priority && dueDate) {
        showToast('Goal set successfully!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('setGoalModal')).hide();
        form.reset();
    } else {
        showToast('Please fill in all required fields', 'error');
    }
}

function viewAllReviews() {
    // Switch to reviews tab
    const reviewsTab = document.getElementById('reviews-tab');
    const tab = new bootstrap.Tab(reviewsTab);
    tab.show();
}

function viewProgress() {
    // Switch to progress tab
    const progressTab = document.getElementById('progress-tab');
    const tab = new bootstrap.Tab(progressTab);
    tab.show();
}

function viewReview(reviewId) {
    showToast(`Viewing ${reviewId} review...`, 'info');
}

function downloadReview(reviewId) {
    showToast(`Downloading ${reviewId} review...`, 'info');
}

function shareReview(reviewId) {
    showToast(`Sharing ${reviewId} review...`, 'info');
}

function continueReview(reviewId) {
    showToast(`Continuing ${reviewId} review...`, 'info');
}

function updateGoal(goalId) {
    showToast(`Updating ${goalId} goal...`, 'info');
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

// Initialize performance chart
function initializePerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'],
            datasets: [{
                label: 'Performance Rating',
                data: [3.5, 3.8, 4.0, 4.2, 4.2],
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
                    max: 5,
                    ticks: {
                        stepSize: 0.5
                    }
                }
            }
        }
    });
}

// Initialize page
$(document).ready(function() {
    initializePerformanceChart();
    console.log('Performance Management page initialized');
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

.dropdown-toggle::after {
    margin-left: 0.5em;
}

.table-active {
    background-color: rgba(0, 0, 0, 0.05);
}

.chart-container {
    position: relative;
    height: 300px;
}
</style>
