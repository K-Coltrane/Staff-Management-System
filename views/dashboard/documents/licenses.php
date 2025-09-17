<?php
// Document Management - Licenses
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Licenses';

// Get licenses
$licenseQuery = "SELECT l.*, ep.first_name, ep.last_name 
                 FROM licenses l 
                 LEFT JOIN employee_profiles ep ON l.employee_id = ep.employee_id
                 ORDER BY l.license_name";
$licenseResult = mysqli_query($conn, $licenseQuery);
$licenses = mysqli_fetch_all($licenseResult, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && canManageEmployees()) {
    $employeeId = sanitize($_POST['employee_id']);
    $licenseName = sanitize($_POST['license_name']);
    $licenseNumber = sanitize($_POST['license_number']);
    $issuingAuthority = sanitize($_POST['issuing_authority']);
    $issueDate = sanitize($_POST['issue_date']);
    $expiryDate = sanitize($_POST['expiry_date']);
    $status = sanitize($_POST['status']);
    
    $insertQuery = "INSERT INTO licenses (employee_id, license_name, license_number, issuing_authority, issue_date, expiry_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("issssss", $employeeId, $licenseName, $licenseNumber, $issuingAuthority, $issueDate, $expiryDate, $status);
    
    if ($insertStmt->execute()) {
        setMessage("License added successfully!", "success");
        header("Location: licenses.php");
        exit();
    } else {
        setMessage("Error adding license: " . $conn->error, "danger");
    }
}

// Get employees for dropdown
$employeesQuery = "SELECT employee_id, first_name, last_name FROM employee_profiles ORDER BY first_name, last_name";
$employeesResult = mysqli_query($conn, $employeesQuery);
$employees = mysqli_fetch_all($employeesResult, MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-file"></i>
                            <div>Document Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="contracts.php" class="menu-link">
                                    <div>Contracts</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="certifications.php" class="menu-link">
                                    <div>Certifications</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="licenses.php" class="menu-link">
                                    <div>Licenses</div>
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
                                        <h5 class="card-title mb-0">Licenses</h5>
                                        <?php if (canManageEmployees()): ?>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLicenseModal">
                                            <i class="bx bx-plus"></i> Add License
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Employee</th>
                                                        <th>License Name</th>
                                                        <th>License Number</th>
                                                        <th>Issuing Authority</th>
                                                        <th>Issue Date</th>
                                                        <th>Expiry Date</th>
                                                        <th>Status</th>
                                                        <?php if (canManageEmployees()): ?>
                                                        <th>Actions</th>
                                                        <?php endif; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($licenses as $license): ?>
                                                        <tr>
                                                            <td><?php echo $license['first_name'] . ' ' . $license['last_name']; ?></td>
                                                            <td><?php echo $license['license_name']; ?></td>
                                                            <td><?php echo $license['license_number']; ?></td>
                                                            <td><?php echo $license['issuing_authority']; ?></td>
                                                            <td><?php echo formatDate($license['issue_date']); ?></td>
                                                            <td><?php echo formatDate($license['expiry_date']); ?></td>
                                                            <td>
                                                                <?php
                                                                $expiryDate = strtotime($license['expiry_date']);
                                                                $today = time();
                                                                $daysUntilExpiry = ($expiryDate - $today) / (60 * 60 * 24);
                                                                
                                                                if ($daysUntilExpiry < 0) {
                                                                    echo '<span class="badge bg-danger">Expired</span>';
                                                                } elseif ($daysUntilExpiry <= 30) {
                                                                    echo '<span class="badge bg-warning">Expiring Soon</span>';
                                                                } else {
                                                                    echo '<span class="badge bg-success">Valid</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <?php if (canManageEmployees()): ?>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary">
                                                                    <i class="bx bx-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
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

    <!-- Add License Modal -->
    <?php if (canManageEmployees()): ?>
    <div class="modal fade" id="addLicenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee *</label>
                            <select class="form-select" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>">
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="license_name" class="form-label">License Name *</label>
                            <input type="text" class="form-control" id="license_name" name="license_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="license_number" class="form-label">License Number *</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="issuing_authority" class="form-label">Issuing Authority *</label>
                            <input type="text" class="form-control" id="issuing_authority" name="issuing_authority" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_date" class="form-label">Issue Date *</label>
                                    <input type="date" class="form-control" id="issue_date" name="issue_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date *</label>
                                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Expired">Expired</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add License</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




