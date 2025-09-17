<?php
// Employee Management - Family Information
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Family Information';

// Get employee ID from URL or session
$employeeId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// If no ID provided and user is not admin/manager, show their own info
if (!$employeeId && !canManageEmployees()) {
    $employee = getEmployeeByUserId($_SESSION['user_id']);
    if ($employee) {
        $employeeId = $employee['employee_id'];
    } else {
        header("Location: ../../../index.php");
        exit();
    }
}

// Get family information
$familyQuery = "SELECT fi.*, ep.first_name, ep.last_name 
                FROM family_information fi 
                LEFT JOIN employee_profiles ep ON fi.employee_id = ep.employee_id
                WHERE fi.employee_id = ? 
                ORDER BY fi.relationship";
$familyStmt = $conn->prepare($familyQuery);
$familyStmt->bind_param("i", $employeeId);
$familyStmt->execute();
$family = $familyStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get employee info
$employeeQuery = "SELECT * FROM employee_profiles WHERE employee_id = ?";
$employeeStmt = $conn->prepare($employeeQuery);
$employeeStmt->bind_param("i", $employeeId);
$employeeStmt->execute();
$employee = $employeeStmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && canManageEmployees()) {
    $relationship = sanitize($_POST['relationship']);
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $emergency_contact = isset($_POST['emergency_contact']) ? 1 : 0;
    
    $insertQuery = "INSERT INTO family_information (employee_id, relationship, name, phone, email, address, emergency_contact, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("isssssi", $employeeId, $relationship, $name, $phone, $email, $address, $emergency_contact);
    
    if ($insertStmt->execute()) {
        setMessage("Family member added successfully!", "success");
        header("Location: family-info.php?id=" . $employeeId);
        exit();
    } else {
        setMessage("Error adding family member: " . $conn->error, "danger");
    }
}
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
                            <li class="menu-item">
                                <a href="employment-history.php" class="menu-link">
                                    <div>Employment History</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="family-info.php" class="menu-link">
                                    <div>Family Information</div>
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
                                            Family Information
                                            <?php if ($employee): ?>
                                                - <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>
                                            <?php endif; ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <!-- Add Family Member Form (Admin/Manager only) -->
                                        <?php if (canManageEmployees()): ?>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="card border-primary">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Add Family Member</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form method="POST" action="">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="relationship" class="form-label">Relationship *</label>
                                                                        <select class="form-select" id="relationship" name="relationship" required>
                                                                            <option value="">Select Relationship</option>
                                                                            <option value="Spouse">Spouse</option>
                                                                            <option value="Child">Child</option>
                                                                            <option value="Parent">Parent</option>
                                                                            <option value="Sibling">Sibling</option>
                                                                            <option value="Other">Other</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="name" class="form-label">Full Name *</label>
                                                                        <input type="text" class="form-control" id="name" name="name" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="phone" class="form-label">Phone</label>
                                                                        <input type="tel" class="form-control" id="phone" name="phone">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="email" class="form-label">Email</label>
                                                                        <input type="email" class="form-control" id="email" name="email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="mb-3">
                                                                        <label for="address" class="form-label">Address</label>
                                                                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <div class="form-check mt-4">
                                                                            <input class="form-check-input" type="checkbox" id="emergency_contact" name="emergency_contact">
                                                                            <label class="form-check-label" for="emergency_contact">
                                                                                Emergency Contact
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="bx bx-plus"></i> Add Family Member
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Family Members List -->
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="mb-3">Family Members</h6>
                                                <?php if (!empty($family)): ?>
                                                    <div class="row">
                                                        <?php foreach ($family as $member): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <div>
                                                                                <h6 class="card-title"><?php echo $member['name']; ?></h6>
                                                                                <p class="card-text">
                                                                                    <strong>Relationship:</strong> <?php echo $member['relationship']; ?><br>
                                                                                    <?php if ($member['phone']): ?>
                                                                                        <strong>Phone:</strong> <?php echo $member['phone']; ?><br>
                                                                                    <?php endif; ?>
                                                                                    <?php if ($member['email']): ?>
                                                                                        <strong>Email:</strong> <?php echo $member['email']; ?><br>
                                                                                    <?php endif; ?>
                                                                                    <?php if ($member['address']): ?>
                                                                                        <strong>Address:</strong> <?php echo $member['address']; ?><br>
                                                                                    <?php endif; ?>
                                                                                </p>
                                                                            </div>
                                                                            <div>
                                                                                <?php if ($member['emergency_contact']): ?>
                                                                                    <span class="badge bg-danger">Emergency Contact</span>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center py-5">
                                                        <i class="bx bx-user-plus display-1 text-muted"></i>
                                                        <h5 class="mt-3">No Family Information</h5>
                                                        <p class="text-muted">No family members have been added yet.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




