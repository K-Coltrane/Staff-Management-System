<?php
// Self-Service - Update Profile
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'My Profile';
$error = '';
$success = '';

// Get employee profile
$profileQuery = "SELECT ep.*, u.username, u.email, u.role, d.department_name, p.name as position_name 
                 FROM employee_profiles ep 
                 LEFT JOIN users u ON ep.user_id = u.id 
                 LEFT JOIN departments d ON ep.department_id = d.department_id 
                 LEFT JOIN positions p ON ep.position_id = p.position_id 
                 WHERE ep.user_id = ?";
$profileStmt = $conn->prepare($profileQuery);
$profileStmt->bind_param("i", $_SESSION['user_id']);
$profileStmt->execute();
$profile = $profileStmt->get_result()->fetch_assoc();

if (!$profile) {
    header("Location: ../../../index.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $emergency_contact_name = sanitize($_POST['emergency_contact_name']);
    $emergency_contact_phone = sanitize($_POST['emergency_contact_phone']);
    $emergency_contact_relationship = sanitize($_POST['emergency_contact_relationship']);
    
    if (empty($first_name) || empty($last_name)) {
        $error = "Please fill in all required fields.";
    } else {
        $updateQuery = "UPDATE employee_profiles SET 
                        first_name = ?, last_name = ?, phone = ?, address = ?, city = ?,
                        emergency_contact_name = ?, emergency_contact_phone = ?, emergency_contact_relationship = ?
                        WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssssssi", $first_name, $last_name, $phone, $address, $city, 
                               $emergency_contact_name, $emergency_contact_phone, $emergency_contact_relationship, $_SESSION['user_id']);
        
        if ($updateStmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh profile data
            $profileStmt->execute();
            $profile = $profileStmt->get_result()->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $updateStmt->error;
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Verify current password
        $userQuery = "SELECT password FROM users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $_SESSION['user_id']);
        $userStmt->execute();
        $user = $userStmt->get_result()->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $hashedPassword, $_SESSION['user_id']);
            
            if ($updateStmt->execute()) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

// Get leave balance
$leaveQuery = "SELECT * FROM leave_balances WHERE employee_id = ? AND year = YEAR(CURDATE())";
$leaveStmt = $conn->prepare($leaveQuery);
$leaveStmt->bind_param("i", $profile['employee_id']);
$leaveStmt->execute();
$leaveBalances = $leaveStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                            <li class="menu-item active">
                                <a href="profile.php" class="menu-link">
                                    <div>Update Profile</div>
                                </a>
                            </li>
                            <li class="menu-item">
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
                                        <span class="avatar-initial rounded-circle bg-primary"><?php echo strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)); ?></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?php echo $profile['first_name'] . ' ' . $profile['last_name']; ?></h6>
                                                    <small class="text-body-secondary"><?php echo $profile['role']; ?></small>
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
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Profile Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Personal Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_profile">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="first_name" class="form-label">First Name *</label>
                                                        <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($profile['first_name']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="last_name" class="form-label">Last Name *</label>
                                                        <input type="text" class="form-control" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($profile['last_name']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="city" class="form-label">City</label>
                                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($profile['city']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($profile['address']); ?></textarea>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Emergency Contact Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Emergency Contact</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_profile">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($profile['emergency_contact_name']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                                        <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($profile['emergency_contact_phone']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                                <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" value="<?php echo htmlspecialchars($profile['emergency_contact_relationship']); ?>">
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Update Emergency Contact</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Change Password Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Change Password</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="change_password">
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label">Current Password *</label>
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">New Password *</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-warning">Change Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Profile Summary Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Profile Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="avatar avatar-xl">
                                                <span class="avatar-initial rounded-circle bg-primary fs-3">
                                                    <?php echo strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <h5 class="mt-2"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h5>
                                            <p class="text-muted"><?php echo htmlspecialchars($profile['employee_number']); ?></p>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <strong>Position:</strong> <?php echo htmlspecialchars($profile['position_name'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Department:</strong> <?php echo htmlspecialchars($profile['department_name'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Role:</strong> <?php echo ucfirst($profile['role']); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Hire Date:</strong> <?php echo date('M d, Y', strtotime($profile['hire_date'])); ?>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Salary:</strong> $<?php echo number_format($profile['salary'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Leave Balance Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Leave Balance</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($leaveBalances)): ?>
                                            <?php foreach ($leaveBalances as $balance): ?>
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




