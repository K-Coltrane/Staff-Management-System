<?php
// Self-Service - Leave Requests
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'My Leave Requests';

// Get employee profile
$employee = getEmployeeByUserId($_SESSION['user_id']);
if (!$employee) {
    header("Location: ../../../index.php");
    exit();
}

// Get leave requests for the employee
$requestsQuery = "SELECT lr.*, lt.leave_type_name 
                  FROM leave_requests lr 
                  LEFT JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.employee_id = ? 
                  ORDER BY lr.created_at DESC";
$requestsStmt = $conn->prepare($requestsQuery);
$requestsStmt->bind_param("i", $employee['employee_id']);
$requestsStmt->execute();
$requests = $requestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle new leave request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    $leaveTypeId = sanitize($_POST['leave_type_id']);
    $startDate = sanitize($_POST['start_date']);
    $endDate = sanitize($_POST['end_date']);
    $reason = sanitize($_POST['reason']);
    
    // Calculate days requested
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $daysRequested = $start->diff($end)->days + 1;
    
    $insertQuery = "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, days_requested, reason, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iissis", $employee['employee_id'], $leaveTypeId, $startDate, $endDate, $daysRequested, $reason);
    
    if ($insertStmt->execute()) {
        setMessage("Leave request submitted successfully!", "success");
        header("Location: leave-request.php");
        exit();
    } else {
        setMessage("Error submitting leave request: " . $conn->error, "danger");
    }
}

// Get leave types for dropdown
$leaveTypesQuery = "SELECT * FROM leave_types ORDER BY leave_type_name";
$leaveTypesResult = mysqli_query($conn, $leaveTypesQuery);
$leaveTypes = mysqli_fetch_all($leaveTypesResult, MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-cog"></i>
                            <div>Self-Service</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="profile.php" class="menu-link">
                                    <div>Update Profile</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="payslips.php" class="menu-link">
                                    <div>View Payslips</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="leave-request.php" class="menu-link">
                                    <div>Leave Requests</div>
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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">My Leave Requests</h5>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                                            <i class="bx bx-plus"></i> New Request
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <?php if (!empty($requests)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Leave Type</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Days</th>
                                                            <th>Status</th>
                                                            <th>Applied On</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($requests as $request): ?>
                                                            <tr>
                                                                <td><?php echo $request['leave_type_name']; ?></td>
                                                                <td><?php echo formatDate($request['start_date']); ?></td>
                                                                <td><?php echo formatDate($request['end_date']); ?></td>
                                                                <td><?php echo $request['days_requested']; ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $request['status'] == 'Approved' ? 'success' : ($request['status'] == 'Rejected' ? 'danger' : 'warning'); ?>">
                                                                        <?php echo $request['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo formatDate($request['created_at']); ?></td>
                                                                <td>
                                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($request['reason']); ?>">
                                                                        <?php echo htmlspecialchars($request['reason']); ?>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-calendar-x display-1 text-muted"></i>
                                                <h5 class="mt-3">No Leave Requests</h5>
                                                <p class="text-muted">You haven't submitted any leave requests yet.</p>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                                                    <i class="bx bx-plus"></i> Submit First Request
                                                </button>
                                            </div>
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

    <!-- New Leave Request Modal -->
    <div class="modal fade" id="newRequestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type *</label>
                            <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                <?php foreach ($leaveTypes as $type): ?>
                                    <option value="<?php echo $type['leave_type_id']; ?>">
                                        <?php echo $type['leave_type_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date *</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date *</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Please provide a reason for your leave request..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_request" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




