<?php
// Performance Management - Performance Progress
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Performance Progress';

// Get employee profile
$employee = getEmployeeByUserId($_SESSION['user_id']);
if (!$employee) {
    header("Location: ../../../index.php");
    exit();
}

// Get performance reviews for the employee
$reviewsQuery = "SELECT pr.*, u.username as reviewed_by_name
                 FROM performance_reviews pr 
                 LEFT JOIN users u ON pr.reviewed_by = u.id
                 WHERE pr.employee_id = ? 
                 ORDER BY pr.review_period_start DESC";
$reviewsStmt = $conn->prepare($reviewsQuery);
$reviewsStmt->bind_param("i", $employee['employee_id']);
$reviewsStmt->execute();
$reviews = $reviewsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get performance goals
$goalsQuery = "SELECT * FROM performance_goals WHERE employee_id = ? AND status = 'Active' ORDER BY created_at DESC";
$goalsStmt = $conn->prepare($goalsQuery);
$goalsStmt->bind_param("i", $employee['employee_id']);
$goalsStmt->execute();
$goals = $goalsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                            <li class="menu-item">
                                <a href="reviews.php" class="menu-link">
                                    <div>Performance Reviews</div>
                                </a>
                            </li>
                            <li class="menu-item active">
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
                        <div class="row">
                            <!-- Performance Reviews -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">My Performance Reviews</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($reviews)): ?>
                                            <div class="row">
                                                <?php foreach ($reviews as $review): ?>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card border">
                                                            <div class="card-header">
                                                                <h6 class="card-title mb-0">
                                                                    Review Period: <?php echo formatDate($review['review_period_start']); ?> - <?php echo formatDate($review['review_period_end']); ?>
                                                                </h6>
                                                                <small class="text-muted">
                                                                    Reviewed by: <?php echo $review['reviewed_by_name']; ?>
                                                                </small>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <strong>Overall Rating:</strong><br>
                                                                        <span class="badge bg-<?php echo $review['overall_rating'] >= 4 ? 'success' : ($review['overall_rating'] >= 3 ? 'warning' : 'danger'); ?> fs-6">
                                                                            <?php echo $review['overall_rating']; ?>/5
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <strong>Status:</strong><br>
                                                                        <span class="badge bg-<?php echo $review['status'] == 'Completed' ? 'success' : 'warning'; ?>">
                                                                            <?php echo $review['status']; ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <?php if ($review['goals_achieved']): ?>
                                                                    <div class="mt-3">
                                                                        <strong>Goals Achieved:</strong>
                                                                        <p class="text-muted small"><?php echo htmlspecialchars($review['goals_achieved']); ?></p>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($review['areas_for_improvement']): ?>
                                                                    <div class="mt-3">
                                                                        <strong>Areas for Improvement:</strong>
                                                                        <p class="text-muted small"><?php echo htmlspecialchars($review['areas_for_improvement']); ?></p>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($review['comments']): ?>
                                                                    <div class="mt-3">
                                                                        <strong>Comments:</strong>
                                                                        <p class="text-muted small"><?php echo htmlspecialchars($review['comments']); ?></p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-footer">
                                                                <small class="text-muted">
                                                                    Review Date: <?php echo formatDate($review['review_date']); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-line-chart display-1 text-muted"></i>
                                                <h5 class="mt-3">No Performance Reviews</h5>
                                                <p class="text-muted">No performance reviews have been conducted yet.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Performance Goals -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Current Goals</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($goals)): ?>
                                            <?php foreach ($goals as $goal): ?>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($goal['goal_title']); ?></h6>
                                                        <p class="card-text small text-muted">
                                                            <?php echo htmlspecialchars(substr($goal['goal_description'], 0, 100)); ?>
                                                            <?php if (strlen($goal['goal_description']) > 100): ?>...<?php endif; ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                Due: <?php echo formatDate($goal['target_date']); ?>
                                                            </small>
                                                            <span class="badge bg-<?php echo $goal['progress'] >= 80 ? 'success' : ($goal['progress'] >= 50 ? 'warning' : 'danger'); ?>">
                                                                <?php echo $goal['progress']; ?>%
                                                            </span>
                                                        </div>
                                                        <div class="progress mt-2" style="height: 6px;">
                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $goal['progress']; ?>%" 
                                                                 aria-valuenow="<?php echo $goal['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No current performance goals set.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Performance Summary -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Performance Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $avgRating = 0;
                                        $totalReviews = count($reviews);
                                        
                                        if ($totalReviews > 0) {
                                            $totalRating = 0;
                                            foreach ($reviews as $review) {
                                                $totalRating += $review['overall_rating'];
                                            }
                                            $avgRating = round($totalRating / $totalReviews, 1);
                                        }
                                        ?>
                                        <div class="text-center">
                                            <h3 class="text-<?php echo $avgRating >= 4 ? 'success' : ($avgRating >= 3 ? 'warning' : 'danger'); ?>">
                                                <?php echo $avgRating > 0 ? $avgRating : 'N/A'; ?>
                                            </h3>
                                            <p class="text-muted">Average Rating</p>
                                            
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <h5 class="text-primary"><?php echo $totalReviews; ?></h5>
                                                    <small class="text-muted">Total Reviews</small>
                                                </div>
                                                <div class="col-6">
                                                    <h5 class="text-success"><?php echo count($goals); ?></h5>
                                                    <small class="text-muted">Active Goals</small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




