<?php
// Employee Management - Employment History
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Employment History';

// Get employee ID from URL or session
$employeeId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// If no ID provided and user is not admin/manager, show their own history
if (!$employeeId && !canManageEmployees()) {
    $employee = getEmployeeByUserId($_SESSION['user_id']);
    if ($employee) {
        $employeeId = $employee['employee_id'];
    } else {
        header("Location: ../../../index.php");
        exit();
    }
}

// Get employment history
$historyQuery = "SELECT eh.*, ep.first_name, ep.last_name, d.department_name, p.name as position_name 
                 FROM employment_history eh 
                 LEFT JOIN employee_profiles ep ON eh.employee_id = ep.employee_id
                 LEFT JOIN departments d ON eh.department_id = d.department_id
                 LEFT JOIN positions p ON eh.position_id = p.position_id
                 WHERE eh.employee_id = ? 
                 ORDER BY eh.start_date DESC";
$historyStmt = $conn->prepare($historyQuery);
$historyStmt->bind_param("i", $employeeId);
$historyStmt->execute();
$history = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get current employee info
$employeeQuery = "SELECT ep.*, d.department_name, p.name as position_name 
                  FROM employee_profiles ep 
                  LEFT JOIN departments d ON ep.department_id = d.department_id
                  LEFT JOIN positions p ON ep.position_id = p.position_id
                  WHERE ep.employee_id = ?";
$employeeStmt = $conn->prepare($employeeQuery);
$employeeStmt->bind_param("i", $employeeId);
$employeeStmt->execute();
$employee = $employeeStmt->get_result()->fetch_assoc();
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
                            <i class="menu-icon icon-base bx bx-id-card"></i>
                            <div>Employee Profiles</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="list.php" class="menu-link">
                                    <div>All Employees</div>
                                </a>
                            </li>
                            <?php if (canManageEmployees()): ?>
                            <li class="menu-item">
                                <a href="add.php" class="menu-link">
                                    <div>Add Employee</div>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="menu-item active">
                                <a href="employment-history.php" class="menu-link">
                                    <div>Employment History</div>
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
                                        <h5 class="card-title mb-0">
                                            Employment History
                                            <?php if ($employee): ?>
                                                - <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>
                                            <?php endif; ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($employee): ?>
                                            <!-- Current Position -->
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <div class="card border-primary">
                                                        <div class="card-header bg-primary text-white">
                                                            <h6 class="mb-0">Current Position</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong>Position:</strong> <?php echo $employee['position_name']; ?><br>
                                                                    <strong>Department:</strong> <?php echo $employee['department_name']; ?><br>
                                                                    <strong>Status:</strong> 
                                                                    <span class="badge bg-<?php echo $employee['employment_status'] == 'Active' ? 'success' : 'warning'; ?>">
                                                                        <?php echo $employee['employment_status']; ?>
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Hire Date:</strong> <?php echo formatDate($employee['hire_date']); ?><br>
                                                                    <strong>Salary:</strong> $<?php echo number_format($employee['salary'], 2); ?><br>
                                                                    <strong>Email:</strong> <?php echo $employee['email']; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Employment History -->
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="mb-3">Employment History</h6>
                                                <?php if (!empty($history)): ?>
                                                    <div class="timeline">
                                                        <?php foreach ($history as $index => $record): ?>
                                                            <div class="timeline-item">
                                                                <div class="timeline-marker bg-primary"></div>
                                                                <div class="timeline-content">
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <h6 class="card-title"><?php echo $record['position_name']; ?></h6>
                                                                            <p class="card-text">
                                                                                <strong>Department:</strong> <?php echo $record['department_name']; ?><br>
                                                                                <strong>Start Date:</strong> <?php echo formatDate($record['start_date']); ?><br>
                                                                                <?php if ($record['end_date']): ?>
                                                                                    <strong>End Date:</strong> <?php echo formatDate($record['end_date']); ?><br>
                                                                                <?php endif; ?>
                                                                                <?php if ($record['salary']): ?>
                                                                                    <strong>Salary:</strong> $<?php echo number_format($record['salary'], 2); ?><br>
                                                                                <?php endif; ?>
                                                                                <?php if ($record['notes']): ?>
                                                                                    <strong>Notes:</strong> <?php echo $record['notes']; ?>
                                                                                <?php endif; ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center py-5">
                                                        <i class="bx bx-history display-1 text-muted"></i>
                                                        <h5 class="mt-3">No Employment History</h5>
                                                        <p class="text-muted">No employment history records found.</p>
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
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .timeline-content {
            margin-left: 20px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




