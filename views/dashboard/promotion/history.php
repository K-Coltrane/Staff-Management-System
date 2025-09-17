<?php
// Staff Promotion - Promotion History
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and has permission
if (!isLoggedIn() || !canPromoteStaff()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Promotion History';

// Get promotion history
$promotionQuery = "SELECT ph.*, ep.first_name, ep.last_name, d.department_name, p.name as position_name, u.username as promoted_by_name
                   FROM promotion_history ph
                   LEFT JOIN employee_profiles ep ON ph.employee_id = ep.employee_id
                   LEFT JOIN departments d ON ph.department_id = d.department_id
                   LEFT JOIN positions p ON ph.position_id = p.position_id
                   LEFT JOIN users u ON ph.promoted_by = u.id
                   ORDER BY ph.promotion_date DESC";
$promotionResult = mysqli_query($conn, $promotionQuery);
$promotions = mysqli_fetch_all($promotionResult, MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-trending-up"></i>
                            <div>Staff Promotion</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="promote.php" class="menu-link">
                                    <div>Promote Staff</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="history.php" class="menu-link">
                                    <div>Promotion History</div>
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
                                        <h5 class="card-title mb-0">Promotion History</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($promotions)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Previous Position</th>
                                                            <th>New Position</th>
                                                            <th>Department</th>
                                                            <th>Promotion Date</th>
                                                            <th>Salary Change</th>
                                                            <th>Promoted By</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($promotions as $promotion): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $promotion['first_name'] . ' ' . $promotion['last_name']; ?></strong>
                                                                </td>
                                                                <td><?php echo $promotion['previous_position']; ?></td>
                                                                <td>
                                                                    <strong class="text-primary"><?php echo $promotion['position_name']; ?></strong>
                                                                </td>
                                                                <td><?php echo $promotion['department_name']; ?></td>
                                                                <td><?php echo formatDate($promotion['promotion_date']); ?></td>
                                                                <td>
                                                                    <?php if ($promotion['previous_salary'] && $promotion['new_salary']): ?>
                                                                        <?php 
                                                                        $salaryChange = $promotion['new_salary'] - $promotion['previous_salary'];
                                                                        $changePercent = ($salaryChange / $promotion['previous_salary']) * 100;
                                                                        ?>
                                                                        <span class="text-<?php echo $salaryChange >= 0 ? 'success' : 'danger'; ?>">
                                                                            $<?php echo number_format($salaryChange, 2); ?>
                                                                            (<?php echo number_format($changePercent, 1); ?>%)
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">N/A</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo $promotion['promoted_by_name']; ?></td>
                                                                <td>
                                                                    <?php if ($promotion['reason']): ?>
                                                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($promotion['reason']); ?>">
                                                                            <?php echo htmlspecialchars($promotion['reason']); ?>
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">No reason provided</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-trending-up display-1 text-muted"></i>
                                                <h5 class="mt-3">No Promotion History</h5>
                                                <p class="text-muted">No promotions have been recorded yet.</p>
                                                <a href="promote.php" class="btn btn-primary">
                                                    <i class="bx bx-plus"></i> Promote Staff
                                                </a>
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




