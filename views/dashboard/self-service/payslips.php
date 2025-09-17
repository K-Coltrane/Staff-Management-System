<?php
// Self-Service - View Payslips
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'View Payslips';

// Get employee profile
$employee = getEmployeeByUserId($_SESSION['user_id']);
if (!$employee) {
    header("Location: ../../../index.php");
    exit();
}

// Get payslips for the employee
$payslipsQuery = "SELECT * FROM payslips WHERE employee_id = ? ORDER BY pay_period_start DESC";
$payslipsStmt = $conn->prepare($payslipsQuery);
$payslipsStmt->bind_param("i", $employee['employee_id']);
$payslipsStmt->execute();
$payslips = $payslipsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                            <li class="menu-item active">
                                <a href="payslips.php" class="menu-link">
                                    <div>View Payslips</div>
                                </a>
                            </li>
                            <li class="menu-item">
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
                                        <h5 class="card-title mb-0">My Payslips</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($payslips)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Pay Period</th>
                                                            <th>Basic Salary</th>
                                                            <th>Allowances</th>
                                                            <th>Deductions</th>
                                                            <th>Net Pay</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($payslips as $payslip): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo formatDate($payslip['pay_period_start']); ?> - 
                                                                    <?php echo formatDate($payslip['pay_period_end']); ?>
                                                                </td>
                                                                <td>$<?php echo number_format($payslip['basic_salary'], 2); ?></td>
                                                                <td>$<?php echo number_format($payslip['allowances'], 2); ?></td>
                                                                <td>$<?php echo number_format($payslip['deductions'], 2); ?></td>
                                                                <td>
                                                                    <strong>$<?php echo number_format($payslip['net_pay'], 2); ?></strong>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $payslip['status'] == 'Paid' ? 'success' : 'warning'; ?>">
                                                                        <?php echo $payslip['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewPayslip(<?php echo $payslip['payslip_id']; ?>)">
                                                                        <i class="bx bx-show"></i> View
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-success" onclick="downloadPayslip(<?php echo $payslip['payslip_id']; ?>)">
                                                                        <i class="bx bx-download"></i> Download
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-receipt display-1 text-muted"></i>
                                                <h5 class="mt-3">No Payslips Available</h5>
                                                <p class="text-muted">No payslips have been generated for you yet.</p>
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

    <!-- Payslip Details Modal -->
    <div class="modal fade" id="payslipModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payslip Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="payslipDetails">
                    <!-- Payslip details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printPayslip()">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewPayslip(payslipId) {
            // In a real application, this would fetch payslip details via AJAX
            document.getElementById('payslipDetails').innerHTML = `
                <div class="text-center">
                    <h6>Payslip #${payslipId}</h6>
                    <p class="text-muted">Payslip details would be loaded here in a real application.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Basic Salary:</strong> $5,000.00<br>
                            <strong>Allowances:</strong> $500.00<br>
                            <strong>Total Earnings:</strong> $5,500.00
                        </div>
                        <div class="col-md-6">
                            <strong>Tax:</strong> $550.00<br>
                            <strong>Other Deductions:</strong> $200.00<br>
                            <strong>Net Pay:</strong> $4,750.00
                        </div>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('payslipModal')).show();
        }

        function downloadPayslip(payslipId) {
            // In a real application, this would trigger a download
            alert('Payslip download would start here for payslip #' + payslipId);
        }

        function printPayslip() {
            window.print();
        }
    </script>
</body>
</html>




