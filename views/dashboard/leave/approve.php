<?php
// Leave Management - Approve/Reject Leave
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and has permission
if (!isLoggedIn() || !canApproveLeave()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Approve/Reject Leave';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = sanitize($_POST['request_id']);
    $action = sanitize($_POST['action']);
    $comments = sanitize($_POST['comments']);
    
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    
    $updateQuery = "UPDATE leave_requests SET status = ?, approved_by = ?, approved_at = NOW(), comments = ? WHERE request_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sisi", $status, $_SESSION['user_id'], $comments, $requestId);
    
    if ($updateStmt->execute()) {
        setMessage("Leave request " . strtolower($status) . " successfully!", "success");
        header("Location: approve.php");
        exit();
    } else {
        setMessage("Error updating leave request: " . $conn->error, "danger");
    }
}

// Get pending leave requests
$requestsQuery = "SELECT lr.*, ep.first_name, ep.last_name, ep.email, lt.leave_type_name, d.department_name
                  FROM leave_requests lr
                  LEFT JOIN employee_profiles ep ON lr.employee_id = ep.employee_id
                  LEFT JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  LEFT JOIN departments d ON ep.department_id = d.department_id
                  WHERE lr.status = 'Pending'
                  ORDER BY lr.created_at DESC";
$requestsResult = mysqli_query($conn, $requestsQuery);
$requests = mysqli_fetch_all($requestsResult, MYSQLI_ASSOC);
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
                                <a href="approve.php" class="menu-link">
                                    <div>Approve/Reject Leave</div>
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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Pending Leave Requests</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <?php if (!empty($requests)): ?>
                                            <div class="row">
                                                <?php foreach ($requests as $request): ?>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card border-warning">
                                                            <div class="card-header bg-warning text-dark">
                                                                <h6 class="mb-0">
                                                                    <?php echo $request['first_name'] . ' ' . $request['last_name']; ?>
                                                                    <span class="badge bg-dark ms-2"><?php echo $request['leave_type_name']; ?></span>
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <strong>Department:</strong><br>
                                                                        <?php echo $request['department_name']; ?><br><br>
                                                                        <strong>Start Date:</strong><br>
                                                                        <?php echo formatDate($request['start_date']); ?><br><br>
                                                                        <strong>End Date:</strong><br>
                                                                        <?php echo formatDate($request['end_date']); ?>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <strong>Days Requested:</strong><br>
                                                                        <?php echo $request['days_requested']; ?><br><br>
                                                                        <strong>Reason:</strong><br>
                                                                        <?php echo $request['reason']; ?><br><br>
                                                                        <strong>Applied On:</strong><br>
                                                                        <?php echo formatDate($request['created_at']); ?>
                                                                    </div>
                                                                </div>
                                                                
                                                                <hr>
                                                                
                                                                <form method="POST" action="" class="d-inline">
                                                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="comments_<?php echo $request['request_id']; ?>" class="form-label">Comments (Optional)</label>
                                                                        <textarea class="form-control" id="comments_<?php echo $request['request_id']; ?>" name="comments" rows="2" placeholder="Add comments..."></textarea>
                                                                    </div>
                                                                    <div class="d-flex gap-2">
                                                                        <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                                                            <i class="bx bx-check"></i> Approve
                                                                        </button>
                                                                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                                                            <i class="bx bx-x"></i> Reject
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-calendar-check display-1 text-muted"></i>
                                                <h5 class="mt-3">No Pending Requests</h5>
                                                <p class="text-muted">There are no pending leave requests to review.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




