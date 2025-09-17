<?php
// User Management - List Users
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'User Management';
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
                            <li class="menu-item active">
                                <a href="list.php" class="menu-link">
                                    <div>All Users</div>
                                </a>
                            </li>
                            <li class="menu-item">
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
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">All Users</h5>
                                        <a href="add.php" class="btn btn-primary">
                                            <i class="bx bx-plus"></i> Add New User
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        // Get all users
                                        $query = "SELECT u.*, ep.first_name, ep.last_name, ep.employee_number 
                                                FROM users u 
                                                LEFT JOIN employee_profiles ep ON u.id = ep.user_id 
                                                ORDER BY u.created_at DESC";
                                        $result = mysqli_query($conn, $query);
                                        ?>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Employee</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                                        <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                                            <tr>
                                                                <td><?php echo $user['id']; ?></td>
                                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'manager' ? 'warning' : 'info'); ?>">
                                                                        <?php echo ucfirst($user['role']); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php if ($user['first_name']): ?>
                                                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                                        <br><small class="text-muted"><?php echo $user['employee_number']; ?></small>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">No profile</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="edit.php?id=<?php echo $user['id']; ?>">Edit</a></li>
                                                                            <li><a class="dropdown-item" href="view.php?id=<?php echo $user['id']; ?>">View</a></li>
                                                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="delete.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No users found</td>
                                                        </tr>
                                                    <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




