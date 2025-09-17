<?php
// Start session
session_start();

// Include database connection and configuration
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../config/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit;
}

// Create database connection
global $conn;
$db = $conn;

// Process form submissions
$message = '';
$messageType = '';

// Ensure users.status column exists (portable way)
$colCheckSql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $db->real_escape_string(DB_NAME) . "' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status'";
$colCheckRes = $db->query($colCheckSql);
if ($colCheckRes) {
    $row = $colCheckRes->fetch_assoc();
    if ((int)$row['cnt'] === 0) {
        // Add status column if missing
        @$db->query("ALTER TABLE users ADD COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
}

// Handle user creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_user') {
        // Create new user
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $rawPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = 'user'; // Default role set to 'user'
        $status = isset($_POST['status']) ? $_POST['status'] : 'active';

        // Validation
        if ($rawPassword !== $confirmPassword) {
            $message = "Passwords do not match.";
            $messageType = "danger";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address.";
            $messageType = "danger";
        } elseif ($username === '') {
            $message = "Username is required.";
            $messageType = "danger";
        } else {
            // Uniqueness checks
            $existsStmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $existsStmt->bind_param("ss", $username, $email);
            $existsStmt->execute();
            $exists = $existsStmt->get_result()->num_rows > 0;
            if ($exists) {
                $message = "Username or email already exists.";
                $messageType = "danger";
            } else {
                $password = password_hash($rawPassword, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, email, password, role, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssss", $username, $email, $password, $role, $status);
                if ($stmt->execute()) {
                    $message = "User created successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error creating user: " . $db->error;
                    $messageType = "danger";
                }
            }
        }
    } elseif ($_POST['action'] === 'update_user') {
        // Update existing user
        $user_id = (int)$_POST['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        $status = $_POST['status'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address.";
            $messageType = "danger";
        } elseif ($username === '') {
            $message = "Username is required.";
            $messageType = "danger";
        } else {
            // Ensure uniqueness excluding current user
            $existsStmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ? LIMIT 1");
            $existsStmt->bind_param("ssi", $username, $email, $user_id);
            $existsStmt->execute();
            $exists = $existsStmt->get_result()->num_rows > 0;
            if ($exists) {
                $message = "Username or email already in use by another user.";
                $messageType = "danger";
            } else {
                $query = "UPDATE users SET username = ?, email = ?, role = ?, status = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("ssssi", $username, $email, $role, $status, $user_id);
                if ($stmt->execute()) {
                    // Check if password should be updated
                    if (!empty($_POST['password'])) {
                        $confirmPassword = $_POST['confirm_password'] ?? '';
                        if ($_POST['password'] !== $confirmPassword) {
                            $message = "Passwords do not match.";
                            $messageType = "danger";
                        } else {
                            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                            $query = "UPDATE users SET password = ? WHERE id = ?";
                            $stmt = $db->prepare($query);
                            $stmt->bind_param("si", $password, $user_id);
                            $stmt->execute();
                            $message = "User updated successfully!";
                            $messageType = "success";
                        }
                    } else {
                        $message = "User updated successfully!";
                        $messageType = "success";
                    }
                } else {
                    $message = "Error updating user: " . $db->error;
                    $messageType = "danger";
                }
            }
        }
    } elseif ($_POST['action'] === 'delete_user') {
        // Delete user
        $user_id = $_POST['user_id'];
        
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = "User deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting user: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get all users
$query = "SELECT id as user_id, username, email, role, status, created_at FROM users ORDER BY id DESC";
$result = $db->query($query);

// Set page title and CSS
$pageTitle = "User Management";
$pageCss = ['assets/css/user-management.css'];

// Include header
include "../../includes/header.php";
include '../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">User Management /</span> Users
        </h4>

        <!-- Alert for messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- User Management Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_user">
                    <i class="bx bx-plus me-1"></i> Add User
                </button>
            </div>
            
            <!-- Search Filter -->
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="search-username" class="form-control" placeholder="Username">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="search-role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="hr">HR</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="search-status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button id="search-btn" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Search
                        </button>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge bg-label-<?php echo $row['role'] === 'admin' ? 'primary' : ($row['role'] === 'hr' ? 'success' : 'info'); ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['role'])); ?>
                                    </span>
                                </td>
                                <?php $status = isset($row['status']) && $row['status'] !== null && $row['status'] !== '' ? strtolower($row['status']) : 'active'; ?>
                                <td>
                                    <span class="badge bg-label-<?php echo $status === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($status)); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit-user" href="javascript:void(0);" 
                                               data-bs-toggle="modal" data-bs-target="#edit_user"
                                               data-id="<?php echo $row['user_id']; ?>"
                                               data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                               data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                               data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                               data-status="<?php echo htmlspecialchars($status); ?>">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item status-change" href="javascript:void(0);" 
                                               data-user-id="<?php echo $row['user_id']; ?>"
                                               data-status="<?php echo $status === 'active' ? 'inactive' : 'active'; ?>">
                                                <i class="bx bx-<?php echo $status === 'active' ? 'power-off' : 'check'; ?> me-1"></i> 
                                                <?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?>
                                            </a>
                                            <a class="dropdown-item delete-user" href="javascript:void(0);" 
                                               data-bs-toggle="modal" data-bs-target="#delete_user" 
                                               data-id="<?php echo $row['user_id']; ?>">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
                © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>
            </div>
        </div>
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- / Content wrapper -->

<!-- Add User Modal -->
<div class="modal fade" id="add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="user_management.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="role" value="user"> <!-- Default role set to 'user' -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="edit_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="user_management.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="hr">HR</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Edit User Modal -->

<!-- Delete User Modal -->
<div class="modal fade" id="delete_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="user_management.php" method="post">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Delete User Modal -->

<!-- Core JS -->
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/menu.js"></script>

<!-- Main JS -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

<!-- Page JS -->
<script>
$(document).ready(function() {
    // Edit user
    $('.edit-user').on('click', function() {
        var userId = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var role = $(this).data('role');
        var status = $(this).data('status');
        
        $('#edit_user_id').val(userId);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_role').val(role);
        $('#edit_status').val(status);
    });
    
    // Delete user
    $('.delete-user').on('click', function() {
        var userId = $(this).data('id');
        $('#delete_user_id').val(userId);
    });
    
    // Status change
    $('.status-change').on('click', function(e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        var status = $(this).data('status');
        
        $.ajax({
            url: 'ajax/update_user_status.php',
            type: 'POST',
            data: {
                user_id: userId,
                status: status
            },
            success: function(response) {
                location.reload();
            }
        });
    });
    
    // Search functionality
    $('#search-btn').on('click', function() {
        var username = $('#search-username').val().toLowerCase();
        var role = $('#search-role').val().toLowerCase();
        var status = $('#search-status').val().toLowerCase();
        
        $('table tbody tr').each(function() {
            var rowUsername = $(this).find('td:eq(1)').text().toLowerCase();
            var rowRole = $(this).find('td:eq(3)').text().toLowerCase();
            var rowStatus = $(this).find('td:eq(4)').text().toLowerCase();
            
            var usernameMatch = username === '' || rowUsername.includes(username);
            var roleMatch = role === '' || rowRole.includes(role);
            var statusMatch = status === '' || rowStatus.includes(status);
            
            if (usernameMatch && roleMatch && statusMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
</body>
</html>
