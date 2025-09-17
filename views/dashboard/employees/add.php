<?php
// Employee Management - Add Employee
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and has permission
if (!isLoggedIn() || !canManageEmployees()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Add Employee';

// Get departments and positions for dropdowns
$departments = getAllDepartments();
$positions = getAllPositions();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $departmentId = sanitize($_POST['department_id']);
    $positionId = sanitize($_POST['position_id']);
    $hireDate = sanitize($_POST['hire_date']);
    $salary = sanitize($_POST['salary']);
    $employmentStatus = sanitize($_POST['employment_status']);
    
    // Create user account first
    $username = strtolower($firstName . '.' . $lastName);
    $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
    $role = 'staff'; // New employees are staff by default
    
    $userQuery = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("ssss", $username, $email, $password, $role);
    
    if ($userStmt->execute()) {
        $userId = $conn->insert_id;
        
        // Create employee profile
        $employeeQuery = "INSERT INTO employee_profiles (user_id, first_name, last_name, email, phone, department_id, position_id, hire_date, salary, employment_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $employeeStmt = $conn->prepare($employeeQuery);
        $employeeStmt->bind_param("issssiisss", $userId, $firstName, $lastName, $email, $phone, $departmentId, $positionId, $hireDate, $salary, $employmentStatus);
        
        if ($employeeStmt->execute()) {
            setMessage("Employee added successfully!", "success");
            header("Location: list.php");
            exit();
        } else {
            setMessage("Error adding employee profile: " . $conn->error, "danger");
        }
    } else {
        setMessage("Error creating user account: " . $conn->error, "danger");
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
                            <li class="menu-item active">
                                <a href="add.php" class="menu-link">
                                    <div>Add Employee</div>
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
                                        <h5 class="card-title mb-0">Add New Employee</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <form method="POST" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="first_name" class="form-label">First Name *</label>
                                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="last_name" class="form-label">Last Name *</label>
                                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email *</label>
                                                        <input type="email" class="form-control" id="email" name="email" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="tel" class="form-control" id="phone" name="phone">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="department_id" class="form-label">Department *</label>
                                                        <select class="form-select" id="department_id" name="department_id" required>
                                                            <option value="">Select Department</option>
                                                            <?php foreach ($departments as $dept): ?>
                                                                <option value="<?php echo $dept['department_id']; ?>">
                                                                    <?php echo $dept['department_name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="position_id" class="form-label">Position *</label>
                                                        <select class="form-select" id="position_id" name="position_id" required>
                                                            <option value="">Select Position</option>
                                                            <?php foreach ($positions as $pos): ?>
                                                                <option value="<?php echo $pos['position_id']; ?>">
                                                                    <?php echo $pos['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="hire_date" class="form-label">Hire Date *</label>
                                                        <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="salary" class="form-label">Salary</label>
                                                        <input type="number" class="form-control" id="salary" name="salary" step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="employment_status" class="form-label">Employment Status *</label>
                                                        <select class="form-select" id="employment_status" name="employment_status" required>
                                                            <option value="Active">Active</option>
                                                            <option value="Inactive">Inactive</option>
                                                            <option value="On Leave">On Leave</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bx bx-plus"></i> Add Employee
                                                    </button>
                                                    <a href="list.php" class="btn btn-outline-secondary">
                                                        <i class="bx bx-arrow-back"></i> Back to List
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
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




