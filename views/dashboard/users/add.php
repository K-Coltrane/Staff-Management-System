<?php
// User Management - Add User
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Add User';
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']);
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $position_id = $_POST['position_id'];
    $department_id = $_POST['department_id'];
    $salary = $_POST['salary'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username or email already exists
        $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Insert user
                $userQuery = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
                $userStmt = $conn->prepare($userQuery);
                $userStmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
                $userStmt->execute();
                $userId = $conn->insert_id;
                
                // Generate employee number
                $employeeNumber = 'EMP' . str_pad($userId, 3, '0', STR_PAD_LEFT);
                
                // Insert employee profile
                $profileQuery = "INSERT INTO employee_profiles (user_id, employee_number, first_name, last_name, phone, address, city, position_id, department_id, hire_date, employment_status, salary) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'Active', ?)";
                $profileStmt = $conn->prepare($profileQuery);
                $profileStmt->bind_param("issssssiid", $userId, $employeeNumber, $first_name, $last_name, $phone, $address, $city, $position_id, $department_id, $salary);
                $profileStmt->execute();
                
                // Insert leave balances
                $leaveTypes = ['Annual', 'Sick', 'Personal'];
                foreach ($leaveTypes as $leaveType) {
                    $totalDays = $leaveType == 'Annual' ? 21 : ($leaveType == 'Sick' ? 10 : 5);
                    $balanceQuery = "INSERT INTO leave_balances (employee_id, leave_type, total_days, used_days, remaining_days, year) VALUES (?, ?, ?, 0, ?, YEAR(CURDATE()))";
                    $balanceStmt = $conn->prepare($balanceQuery);
                    $balanceStmt->bind_param("isii", $userId, $leaveType, $totalDays, $totalDays);
                    $balanceStmt->execute();
                }
                
                // Commit transaction
                mysqli_commit($conn);
                $success = "User created successfully!";
                
                // Redirect to user list
                header("Location: list.php?success=1");
                exit();
                
            } catch (Exception $e) {
                // Rollback transaction
                mysqli_rollback($conn);
                $error = "Error creating user: " . $e->getMessage();
            }
        }
    }
}

// Get departments and positions for dropdowns
$departments = [];
$positions = [];

$deptQuery = "SELECT * FROM departments ORDER BY name";
$deptResult = mysqli_query($conn, $deptQuery);
if ($deptResult) {
    while ($row = mysqli_fetch_assoc($deptResult)) {
        $departments[] = $row;
    }
}

$posQuery = "SELECT * FROM positions ORDER BY name";
$posResult = mysqli_query($conn, $posQuery);
if ($posResult) {
    while ($row = mysqli_fetch_assoc($posResult)) {
        $positions[] = $row;
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
                            <i class="menu-icon icon-base bx bx-user"></i>
                            <div>User Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="list.php" class="menu-link">
                                    <div>All Users</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="add.php" class="menu-link">
                                    <div>Add User</div>
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
                                        <h5 class="card-title mb-0">Add New User</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($error): ?>
                                            <div class="alert alert-danger"><?php echo $error; ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if ($success): ?>
                                            <div class="alert alert-success"><?php echo $success; ?></div>
                                        <?php endif; ?>

                                        <form method="POST" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Account Information</h6>
                                                    <div class="mb-3">
                                                        <label for="username" class="form-label">Username *</label>
                                                        <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email *</label>
                                                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Password *</label>
                                                        <input type="password" class="form-control" id="password" name="password" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="role" class="form-label">Role *</label>
                                                        <select class="form-select" id="role" name="role" required>
                                                            <option value="">Select Role</option>
                                                            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                            <option value="manager" <?php echo (isset($_POST['role']) && $_POST['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                                            <option value="staff" <?php echo (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Personal Information</h6>
                                                    <div class="mb-3">
                                                        <label for="first_name" class="form-label">First Name *</label>
                                                        <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="last_name" class="form-label">Last Name *</label>
                                                        <input type="text" class="form-control" id="last_name" name="last_name" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="address" class="form-label">Address</label>
                                                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="city" class="form-label">City</label>
                                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Employment Information</h6>
                                                    <div class="mb-3">
                                                        <label for="department_id" class="form-label">Department</label>
                                                        <select class="form-select" id="department_id" name="department_id">
                                                            <option value="">Select Department</option>
                                                            <?php foreach ($departments as $dept): ?>
                                                                <option value="<?php echo $dept['department_id']; ?>" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == $dept['department_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="position_id" class="form-label">Position</label>
                                                        <select class="form-select" id="position_id" name="position_id">
                                                            <option value="">Select Position</option>
                                                            <?php foreach ($positions as $pos): ?>
                                                                <option value="<?php echo $pos['position_id']; ?>" <?php echo (isset($_POST['position_id']) && $_POST['position_id'] == $pos['position_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($pos['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="salary" class="form-label">Salary</label>
                                                        <input type="number" class="form-control" id="salary" name="salary" step="0.01" value="<?php echo isset($_POST['salary']) ? htmlspecialchars($_POST['salary']) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <a href="list.php" class="btn btn-secondary me-2">Cancel</a>
                                                <button type="submit" class="btn btn-primary">Create User</button>
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




