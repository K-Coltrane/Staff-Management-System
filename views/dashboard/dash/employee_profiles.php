<?php
// Start session
session_start();

// Include database connection and configuration
require_once '../../../config/database.php';
require_once '../../../includes/functions.php';
require_once '../../../config/config.php';

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'hr')) {
    header("Location: index.php?error=unauthorized");
    exit;
}

// Create database connection
global $conn;
$db = $conn;

// Process form submissions
$message = '';
$messageType = '';

// Handle employee creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_employee') {
        // Create new employee
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $department_id = $_POST['department_id'];
        $position_id = $_POST['position_id'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $emergency_contact = $_POST['emergency_contact'];

        $query = "INSERT INTO employees (first_name, last_name, email, phone, department_id, position_id, hire_date, salary, status, address, emergency_contact, created_at, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssiiisdssi", $first_name, $last_name, $email, $phone, $department_id, $position_id, $hire_date, $salary, $status, $address, $emergency_contact, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $employee_id = $db->insert_id;

            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $upload_dir = 'uploads/employees/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_name = $employee_id . '_' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $query = "UPDATE employees SET profile_image = ? WHERE employee_id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $employee_id);
                    $stmt->execute();
                }
            }

            $message = "Employee profile created successfully!";
            $messageType = "success";
        } else {
            $message = "Error creating employee profile: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_employee') {
        // Update existing employee
        $employee_id = $_POST['employee_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $department_id = $_POST['department_id'];
        $position_id = $_POST['position_id'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $emergency_contact = $_POST['emergency_contact'];
        
        $query = "UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, department_id = ?, 
                  position_id = ?, hire_date = ?, salary = ?, status = ?, address = ?, emergency_contact = ?, 
                  updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssiiisdssii", $first_name, $last_name, $email, $phone, $department_id, $position_id, $hire_date, $salary, $status, $address, $emergency_contact, $_SESSION['user_id'], $employee_id);
        
        if ($stmt->execute()) {
            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $upload_dir = 'uploads/employees/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = $employee_id . '_' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $query = "UPDATE employees SET profile_image = ? WHERE employee_id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $employee_id);
                    $stmt->execute();
                }
            }
            
            $message = "Employee profile updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating employee profile: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'delete_employee') {
        // Delete employee
        $employee_id = $_POST['employee_id'];
        
        $query = "DELETE FROM employees WHERE employee_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $employee_id);
        
        if ($stmt->execute()) {
            $message = "Employee profile deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting employee profile: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get all employees
$query = "SELECT e.*, 
                 d.department_name AS department, 
                 p.position_title AS position, 
                 CONCAT(u.username) AS created_by_name 
          FROM employees e 
          LEFT JOIN departments d ON e.department_id = d.department_id 
          LEFT JOIN positions p ON e.position_id = p.position_id 
          LEFT JOIN users u ON e.created_by = u.user_id 
          ORDER BY e.employee_id DESC";
$employees = $db->query($query);

// Get departments for dropdown
$query = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department";
$departments = $db->query($query);

// Set page title and CSS
$pageTitle = "Employee Profiles";
$pageCss = ['assets/css/employee-profiles.css'];

// Include header
include "../../includes/header.php"';
include '../../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Employee Profiles
            
        </h4>

        <!-- Alert for messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Employee Profiles Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Employee Profiles</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_employee">
                    <i class="bx bx-plus me-1"></i> Add Employee
                </button>
            </div>
            
            <!-- Search Filter -->
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="search-name" class="form-control" placeholder="Search Name">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="search-department" class="form-select">
                            <option value="">All Departments</option>
                            <?php while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($dept['department']); ?>"><?php echo htmlspecialchars($dept['department']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="search-status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button id="search-btn" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Search
                        </button>
                    </div>
                </div>
                
                <!-- Employees Table -->
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Emergency Contact</th>
                                <th>Status</th>
                                <th>Hire Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php if ($employees && $employees->num_rows > 0): ?>
                                <?php while ($employee = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <?php if (!empty($employee['profile_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($employee['profile_image'] ?? 'default-profile.png'); ?>" alt="Profile" class="rounded-circle">
                                                <?php else: ?>
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <?php echo strtoupper(substr($employee['first_name'] ?? '', 0, 1) . substr($employee['last_name'] ?? '', 0, 1)); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold"><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></span>
                                                <small class="text-muted">ID: <?php echo $employee['employee_id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['department'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['position'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['email'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['address'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['emergency_contact'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        $status = $employee['status'] ?? 'unknown';
                                        switch ($status) {
                                            case 'active': $statusClass = 'success'; break;
                                            case 'on_leave': $statusClass = 'warning'; break;
                                            case 'terminated': $statusClass = 'danger'; break;
                                            default: $statusClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-label-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($employee['hire_date'])); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item view-employee" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#view_employee" 
                                                   data-id="<?php echo $employee['employee_id']; ?>">
                                                    <i class="bx bx-show me-1"></i> View
                                                </a>
                                                <a class="dropdown-item edit-employee" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#edit_employee"
                                                   data-id="<?php echo $employee['id']; ?>"
                                                   data-first-name="<?php echo htmlspecialchars($employee['first_name']); ?>"
                                                   data-last-name="<?php echo htmlspecialchars($employee['last_name']); ?>"
                                                   data-email="<?php echo htmlspecialchars($employee['email']); ?>"
                                                   data-phone="<?php echo htmlspecialchars($employee['phone']); ?>"
                                                   data-department="<?php echo htmlspecialchars($employee['department']); ?>"
                                                   data-position="<?php echo htmlspecialchars($employee['position']); ?>"
                                                   data-hire-date="<?php echo $employee['hire_date']; ?>"
                                                   data-salary="<?php echo $employee['salary']; ?>"
                                                   data-status="<?php echo $employee['status']; ?>"
                                                   data-address="<?php echo htmlspecialchars($employee['address']); ?>"
                                                   data-emergency-contact="<?php echo htmlspecialchars($employee['emergency_contact']); ?>">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item employee-status-change" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#update_employee_status" 
                                                   data-id="<?php echo $employee['id']; ?>" 
                                                   data-status="<?php echo $employee['status']; ?>">
                                                    <i class="bx bx-transfer-alt me-1"></i> Change Status
                                                </a>
                                                <a class="dropdown-item delete-employee" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#delete_employee" 
                                                   data-id="<?php echo $employee['id']; ?>">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No employees found</td>
                                </tr>
                            <?php endif; ?>
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

<!-- Add Employee Modal -->
<div class="modal fade" id="add_employee" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="employee_profiles.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_employee">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="department_id" name="department_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position_id" class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="position_id" name="position_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">Salary <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="salary" name="salary" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="on_leave">On Leave</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="emergency_contact" name="emergency_contact" rows="2" required></textarea>
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
<!-- / Add Employee Modal -->

<!-- Edit Employee Modal -->
<div class="modal fade" id="edit_employee" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="employee_profiles.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_employee">
                    <input type="hidden" name="employee_id" id="edit_employee_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_phone" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_department_id" name="department_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_position_id" class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_position_id" name="position_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_hire_date" name="hire_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_salary" class="form-label">Salary <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="edit_salary" name="salary" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="on_leave">On Leave</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="edit_profile_image" name="profile_image" accept="image/*">
                            <small class="text-muted">Leave blank to keep current image</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_emergency_contact" name="emergency_contact" rows="2" required></textarea>
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
<!-- / Edit Employee Modal -->

<!-- View Employee Modal -->
<div class="modal fade" id="view_employee" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="employee_details_content">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading employee details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- / View Employee Modal -->

<!-- Update Employee Status Modal -->
<div class="modal fade" id="update_employee_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Employee Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="employee_profiles.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_employee_status">
                    <input type="hidden" name="employee_id" id="status_employee_id">
                    <div class="mb-3">
                        <label for="employee_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="status_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Update Employee Status Modal -->

<!-- Delete Employee Modal -->
<div class="modal fade" id="delete_employee" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this employee?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="employee_profiles.php" method="post">
                    <input type="hidden" name="action" value="delete_employee">
                    <input type="hidden" name="employee_id" id="delete_employee_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Delete Employee Modal -->

<!-- Core JS -->
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/menu.js"></script>

<!-- Main JS -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sidebar-toggle.js"></script>

<!-- Page JS -->
<script>
$(document).ready(function() {
    // Edit employee
    $('.edit-employee').on('click', function() {
        var employeeId = $(this).data('id');
        var firstName = $(this).data('first-name');
        var lastName = $(this).data('last-name');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var department = $(this).data('department');
        var position = $(this).data('position');
        var hireDate = $(this).data('hire-date');
        var salary = $(this).data('salary');
        var status = $(this).data('status');
        var address = $(this).data('address');
        var emergencyContact = $(this).data('emergency-contact');
        
        $('#edit_employee_id').val(employeeId);
        $('#edit_first_name').val(firstName);
        $('#edit_last_name').val(lastName);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit_department_id').val(department);
        $('#edit_position_id').val(position);
        $('#edit_hire_date').val(hireDate);
        $('#edit_salary').val(salary);
        $('#edit_status').val(status);
        $('#edit_address').val(address);
        $('#edit_emergency_contact').val(emergencyContact);
    });

    // View employee
    $('.view-employee').on('click', function() {
        var employeeId = $(this).data('id');
        
        // Reset content
        $('#employee_details_content').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading employee details...</p></div>');
        
        // Load employee details via AJAX
        $.ajax({
            url: 'ajax/get_employee_details.php',
            type: 'GET',
            data: {
                employee_id: employeeId
            },
            success: function(response) {
                $('#employee_details_content').html(response);
            },
            error: function() {
                $('#employee_details_content').html('<div class="alert alert-danger">Error loading employee details.</div>');
            }
        });
    });

    // Update employee status
    $('.employee-status-change').on('click', function() {
        var employeeId = $(this).data('id');
        var status = $(this).data('status');
        
        $('#status_employee_id').val(employeeId);
        $('#employee_status').val(status);
    });

    // Delete employee
    $('.delete-employee').on('click', function() {
        var employeeId = $(this).data('id');
        $('#delete_employee_id').val(employeeId);
    });

    // Search functionality
    $('#search-btn').on('click', function() {
        var name = $('#search-name').val();
        var department = $('#search-department').val();
        var status = $('#search-status').val();

        // Send AJAX request
        $.ajax({
            url: 'ajax/search_employees.php',
            type: 'GET',
            data: {
                name: name,
                department: department,
                status: status
            },
            success: function(response) {
                // Update the table body with the filtered results
                $('table tbody').html(response);
            },
            error: function() {
                $('table tbody').html('<tr><td colspan="7" class="text-center text-danger">Error fetching results</td></tr>');
            }
        });
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search-btn').on('click', function() {
            var name = $('#search-name').val();
            var department = $('#search-department').val();
            var status = $('#search-status').val();

            // Send AJAX request
            $.ajax({
                url: 'ajax/search_employees.php',
                type: 'GET',
                data: {
                    name: name,
                    department: department,
                    status: status
                },
                success: function(response) {
                    // Update the table body with the filtered results
                    $('table tbody').html(response);
                },
                error: function() {
                    $('table tbody').html('<tr><td colspan="7" class="text-center text-danger">Error fetching results</td></tr>');
                }
            });
        });
    });
</script>
</body>
</html>
