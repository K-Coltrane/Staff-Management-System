<?php
// Leave Management - Leave Balance
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Leave Balance';

// Get employee profile
$employeeQuery = "SELECT ep.*, u.username FROM employee_profiles ep 
                  LEFT JOIN users u ON ep.user_id = u.id 
                  WHERE ep.user_id = ?";
$employeeStmt = $conn->prepare($employeeQuery);
$employeeStmt->bind_param("i", $_SESSION['user_id']);
$employeeStmt->execute();
$employee = $employeeStmt->get_result()->fetch_assoc();

if (!$employee) {
    header("Location: ../../../index.php");
    exit();
}

// Get leave balances
$balanceQuery = "SELECT * FROM leave_balances WHERE employee_id = ? AND year = YEAR(CURDATE())";
$balanceStmt = $conn->prepare($balanceQuery);
$balanceStmt->bind_param("i", $employee['employee_id']);
$balanceStmt->execute();
$balances = $balanceStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get leave requests for current year
$requestsQuery = "SELECT * FROM leave_requests WHERE employee_id = ? AND YEAR(start_date) = YEAR(CURDATE()) ORDER BY created_at DESC";
$requestsStmt = $conn->prepare($requestsQuery);
$requestsStmt->bind_param("i", $employee['employee_id']);
$requestsStmt->execute();
$requests = $requestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-calendar"></i>
                            <div>Leave Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="apply.php" class="menu-link">
                                    <div>Apply for Leave</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="balance.php" class="menu-link">
                                    <div>Leave Balance</div>
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
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Leave Balance - <?php echo date('Y'); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($balances)): ?>
                                            <div class="row">
                                                <?php foreach ($balances as $balance): ?>
                                                    <div class="col-md-4 mb-4">
                                                        <div class="card border">
                                                            <div class="card-body text-center">
                                                                <h3 class="text-primary"><?php echo $balance['remaining_days']; ?></h3>
                                                                <p class="mb-1"><?php echo $balance['leave_type']; ?> Days</p>
                                                                <small class="text-muted">
                                                                    <?php echo $balance['used_days']; ?> used of <?php echo $balance['total_days']; ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-calendar-x display-1 text-muted"></i>
                                                <h5 class="mt-3">No Leave Balance</h5>
                                                <p class="text-muted">No leave balance found for this year.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Recent Leave Requests -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Recent Leave Requests</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($requests)): ?>
                                            <?php foreach ($requests as $request): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $request['leave_type']; ?></h6>
                                                        <small class="text-muted">
                                                            <?php echo date('M d', strtotime($request['start_date'])); ?> - 
                                                            <?php echo date('M d, Y', strtotime($request['end_date'])); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-<?php echo $request['status'] == 'Approved' ? 'success' : ($request['status'] == 'Rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo $request['status']; ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No leave requests this year</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




