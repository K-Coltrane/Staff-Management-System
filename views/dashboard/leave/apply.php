<?php
// Leave Management - Apply for Leave
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Apply for Leave';
$error = '';
$success = '';

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_type = sanitize($_POST['leave_type']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = sanitize($_POST['reason']);
    
    // Calculate days requested
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days_requested = $start->diff($end)->days + 1;
    
    // Validation
    if (empty($leave_type) || empty($start_date) || empty($end_date)) {
        $error = "Please fill in all required fields.";
    } elseif ($start_date > $end_date) {
        $error = "Start date cannot be after end date.";
    } elseif ($start_date < date('Y-m-d')) {
        $error = "Start date cannot be in the past.";
    } else {
        // Check leave balance
        $availableBalance = 0;
        foreach ($balances as $balance) {
            if ($balance['leave_type'] == $leave_type) {
                $availableBalance = $balance['remaining_days'];
                break;
            }
        }
        
        if ($days_requested > $availableBalance) {
            $error = "Insufficient leave balance. Available: $availableBalance days, Requested: $days_requested days.";
        } else {
            // Insert leave request
            $insertQuery = "INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, days_requested, reason, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("isssis", $employee['employee_id'], $leave_type, $start_date, $end_date, $days_requested, $reason);
            
            if ($insertStmt->execute()) {
                $success = "Leave request submitted successfully!";
                // Refresh page to show updated balances
                header("Location: apply.php?success=1");
                exit();
            } else {
                $error = "Error submitting leave request. Please try again.";
            }
        }
    }
}

// Get recent leave requests
$recentQuery = "SELECT * FROM leave_requests WHERE employee_id = ? ORDER BY created_at DESC LIMIT 5";
$recentStmt = $conn->prepare($recentQuery);
$recentStmt->bind_param("i", $employee['employee_id']);
$recentStmt->execute();
$recentRequests = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base bx bx-calendar"></i>
                            <div>Leave Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="apply.php" class="menu-link">
                                    <div>Apply for Leave</div>
                                </a>
                            </li>
                            <li class="menu-item">
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
                                        <h5 class="card-title mb-0">Apply for Leave</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($error): ?>
                                            <div class="alert alert-danger"><?php echo $error; ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if ($success || isset($_GET['success'])): ?>
                                            <div class="alert alert-success">Leave request submitted successfully!</div>
                                        <?php endif; ?>

                                        <form method="POST" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="leave_type" class="form-label">Leave Type *</label>
                                                        <select class="form-select" id="leave_type" name="leave_type" required>
                                                            <option value="">Select Leave Type</option>
                                                            <option value="Annual">Annual Leave</option>
                                                            <option value="Sick">Sick Leave</option>
                                                            <option value="Personal">Personal Leave</option>
                                                            <option value="Maternity">Maternity Leave</option>
                                                            <option value="Paternity">Paternity Leave</option>
                                                            <option value="Emergency">Emergency Leave</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="start_date" class="form-label">Start Date *</label>
                                                        <input type="date" class="form-control" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="end_date" class="form-label">End Date *</label>
                                                        <input type="date" class="form-control" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason</label>
                                                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please provide a reason for your leave request"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Leave Balance Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Leave Balance</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($balances)): ?>
                                            <?php foreach ($balances as $balance): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><?php echo $balance['leave_type']; ?></span>
                                                    <span class="badge bg-primary"><?php echo $balance['remaining_days']; ?> days</span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No leave balance found</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Recent Requests Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Recent Requests</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($recentRequests)): ?>
                                            <?php foreach ($recentRequests as $request): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <small class="text-muted"><?php echo $request['leave_type']; ?></small><br>
                                                        <small><?php echo date('M d, Y', strtotime($request['start_date'])); ?></small>
                                                    </div>
                                                    <span class="badge bg-<?php echo $request['status'] == 'Approved' ? 'success' : ($request['status'] == 'Rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo $request['status']; ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No recent requests</p>
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
    <script>
        // Auto-calculate days when dates change
        document.getElementById('start_date').addEventListener('change', calculateDays);
        document.getElementById('end_date').addEventListener('change', calculateDays);
        
        function calculateDays() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                if (diffDays > 0) {
                    // You can display this somewhere if needed
                    console.log('Days requested:', diffDays);
                }
            }
        }
    </script>
</body>
</html>




