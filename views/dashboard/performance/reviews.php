<?php
// Performance Management - Performance Reviews
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Performance Reviews';
$error = '';
$success = '';

// Handle form submission for new performance review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $employee_id = $_POST['employee_id'];
    $review_period_start = $_POST['review_period_start'];
    $review_period_end = $_POST['review_period_end'];
    $overall_rating = $_POST['overall_rating'];
    $goals_achieved = sanitize($_POST['goals_achieved']);
    $areas_for_improvement = sanitize($_POST['areas_for_improvement']);
    $strengths = sanitize($_POST['strengths']);
    $recommendations = sanitize($_POST['recommendations']);
    
    if (empty($employee_id) || empty($review_period_start) || empty($review_period_end) || empty($overall_rating)) {
        $error = "Please fill in all required fields.";
    } else {
        $query = "INSERT INTO performance_reviews (employee_id, reviewer_id, review_period_start, review_period_end, overall_rating, goals_achieved, areas_for_improvement, strengths, recommendations, status, review_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Draft', CURDATE())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iississss", $employee_id, $_SESSION['user_id'], $review_period_start, $review_period_end, $overall_rating, $goals_achieved, $areas_for_improvement, $strengths, $recommendations);
        
        if ($stmt->execute()) {
            $success = "Performance review created successfully!";
        } else {
            $error = "Error creating performance review: " . $stmt->error;
        }
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE performance_reviews SET status = ? WHERE review_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $review_id);
    
    if ($stmt->execute()) {
        $success = "Review status updated successfully!";
    } else {
        $error = "Error updating review status.";
    }
}

// Get employees for dropdown (only for managers and admins)
$employees = [];
if (isAdmin() || $_SESSION['role'] == 'manager') {
    $empQuery = "SELECT ep.employee_id, ep.first_name, ep.last_name, ep.employee_number 
                 FROM employee_profiles ep 
                 WHERE ep.employment_status = 'Active' 
                 ORDER BY ep.first_name, ep.last_name";
    $empResult = mysqli_query($conn, $empQuery);
    if ($empResult) {
        while ($row = mysqli_fetch_assoc($empResult)) {
            $employees[] = $row;
        }
    }
}

// Get performance reviews
$reviewsQuery = "SELECT pr.*, ep.first_name, ep.last_name, ep.employee_number, u.username as reviewer_name 
                 FROM performance_reviews pr 
                 LEFT JOIN employee_profiles ep ON pr.employee_id = ep.employee_id 
                 LEFT JOIN users u ON pr.reviewer_id = u.id 
                 ORDER BY pr.review_date DESC";
$reviewsResult = mysqli_query($conn, $reviewsQuery);

// Get my performance reviews (for regular staff)
$myReviewsQuery = "SELECT pr.*, ep.first_name, ep.last_name, ep.employee_number, u.username as reviewer_name 
                   FROM performance_reviews pr 
                   LEFT JOIN employee_profiles ep ON pr.employee_id = ep.employee_id 
                   LEFT JOIN users u ON pr.reviewer_id = u.id 
                   WHERE ep.user_id = ? 
                   ORDER BY pr.review_date DESC";
$myReviewsStmt = $conn->prepare($myReviewsQuery);
$myReviewsStmt->bind_param("i", $_SESSION['user_id']);
$myReviewsStmt->execute();
$myReviews = $myReviewsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>Staff Management System - <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../../assets/css/core.css" />
    <link rel="stylesheet" href="../../../assets/css/demo.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <div class="app-brand demo">
                    <a href="../index.php" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bold ms-2">Staff MS</span>
                    </a>
                </div>
                <ul class="menu-inner py-1">
                    <li class="menu-item">
                        <a href="../index.php" class="menu-link">
                            <i class="menu-icon icon-base bx bx-home-smile"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base bx bx-line-chart"></i>
                            <div>Performance Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="reviews.php" class="menu-link">
                                    <div>Performance Reviews</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="progress.php" class="menu-link">
                                    <div>Performance Progress</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </aside>

            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached">
                    <div class="navbar-nav-right d-flex align-items-center">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-primary"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?php echo $_SESSION['username']; ?></h6>
                                                    <small class="text-body-secondary"><?php echo $_SESSION['role']; ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider my-1"></div></li>
                                    <li><a class="dropdown-item" href="../../../logout.php"><i class="bx bx-power-off me-2"></i>Log Out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Performance Reviews</h5>
                                        <?php if (isAdmin() || $_SESSION['role'] == 'manager'): ?>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReviewModal">
                                                <i class="bx bx-plus"></i> Create Review
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Employee</th>
                                                        <th>Review Period</th>
                                                        <th>Rating</th>
                                                        <th>Status</th>
                                                        <th>Reviewer</th>
                                                        <th>Review Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($reviewsResult && mysqli_num_rows($reviewsResult) > 0): ?>
                                                        <?php while ($review = mysqli_fetch_assoc($reviewsResult)): ?>
                                                            <tr>
                                                                <td>
                                                                    <h6 class="mb-0"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></h6>
                                                                    <small class="text-muted"><?php echo $review['employee_number']; ?></small>
                                                                </td>
                                                                <td>
                                                                    <?php echo date('M d, Y', strtotime($review['review_period_start'])); ?><br>
                                                                    <small class="text-muted">to <?php echo date('M d, Y', strtotime($review['review_period_end'])); ?></small>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                            <i class="bx bx-star<?php echo $i <= $review['overall_rating'] ? '' : '-o'; ?> text-warning"></i>
                                                                        <?php endfor; ?>
                                                                        <span class="ms-1"><?php echo $review['overall_rating']; ?>/5</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $review['status'] == 'Completed' ? 'success' : ($review['status'] == 'Draft' ? 'warning' : 'info'); ?>">
                                                                        <?php echo $review['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($review['reviewer_name']); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($review['review_date'])); ?></td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="view-review.php?id=<?php echo $review['review_id']; ?>">View</a></li>
                                                                            <?php if (isAdmin() || $_SESSION['role'] == 'manager'): ?>
                                                                                <li><a class="dropdown-item" href="edit-review.php?id=<?php echo $review['review_id']; ?>">Edit</a></li>
                                                                                <?php if ($review['status'] == 'Draft'): ?>
                                                                                    <li>
                                                                                        <form method="POST" style="display: inline;">
                                                                                            <input type="hidden" name="action" value="update_status">
                                                                                            <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                                                            <input type="hidden" name="status" value="Completed">
                                                                                            <button type="submit" class="dropdown-item">Mark as Completed</button>
                                                                                        </form>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No performance reviews found</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- My Performance Reviews Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">My Performance Reviews</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($myReviews)): ?>
                                            <?php foreach ($myReviews as $review): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-0">Review Period</h6>
                                                        <small class="text-muted">
                                                            <?php echo date('M d, Y', strtotime($review['review_period_start'])); ?> - 
                                                            <?php echo date('M d, Y', strtotime($review['review_period_end'])); ?>
                                                        </small>
                                                        <br>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="bx bx-star<?php echo $i <= $review['overall_rating'] ? '' : '-o'; ?> text-warning" style="font-size: 12px;"></i>
                                                            <?php endfor; ?>
                                                            <span class="ms-1 small"><?php echo $review['overall_rating']; ?>/5</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-<?php echo $review['status'] == 'Completed' ? 'success' : ($review['status'] == 'Draft' ? 'warning' : 'info'); ?>">
                                                            <?php echo $review['status']; ?>
                                                        </span>
                                                        <br>
                                                        <small class="text-muted"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No performance reviews found</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Review Modal -->
    <?php if (isAdmin() || $_SESSION['role'] == 'manager'): ?>
    <div class="modal fade" id="createReviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Performance Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee *</label>
                                    <select class="form-select" id="employee_id" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?php echo $emp['employee_id']; ?>">
                                                <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_number'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="overall_rating" class="form-label">Overall Rating *</label>
                                    <select class="form-select" id="overall_rating" name="overall_rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Below Average</option>
                                        <option value="3">3 - Average</option>
                                        <option value="4">4 - Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="review_period_start" class="form-label">Review Period Start *</label>
                                    <input type="date" class="form-control" id="review_period_start" name="review_period_start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="review_period_end" class="form-label">Review Period End *</label>
                                    <input type="date" class="form-control" id="review_period_end" name="review_period_end" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="goals_achieved" class="form-label">Goals Achieved</label>
                            <textarea class="form-control" id="goals_achieved" name="goals_achieved" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="strengths" class="form-label">Strengths</label>
                            <textarea class="form-control" id="strengths" name="strengths" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="areas_for_improvement" class="form-label">Areas for Improvement</label>
                            <textarea class="form-control" id="areas_for_improvement" name="areas_for_improvement" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommendations" class="form-label">Recommendations</label>
                            <textarea class="form-control" id="recommendations" name="recommendations" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




