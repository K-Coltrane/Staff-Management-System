<?php
// Start session
session_start();

// Include database connection and configuration
require_once '../../../config/database.php';
require_once '../../../includes/functions.php';
require_once '../../../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit;
}

// Create database connection
global $conn;
$db = $conn; 

// Process form submissions
$message = '';
$messageType = '';

// Handle leave request creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_leave') {
        // Create new leave request
        $employee_id = $_SESSION['user_id']; // Auto from session
        $leave_type_id = $_POST['leave_type_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $reason = $_POST['reason'];
        $status = 'pending'; // Default status for new requests
        
        // Calculate number of days
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        $total_days = $interval->days + 1; // Include both start and end dates
        
        $query = "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, total_days, reason, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iississ", $employee_id, $leave_type_id, $start_date, $end_date, $total_days, $reason, $status);
        
        if ($stmt->execute()) {
            $message = "Leave request submitted successfully!";
            $messageType = "success";
        } else {
            $message = "Error submitting leave request: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_leave_status') {
        // Update leave request status
        $leave_id = $_POST['leave_id'];
        $status = $_POST['status'];
        $comments = $_POST['comments'];
        
        $query = "UPDATE leave_requests SET status = ?, comments = ?, updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssii", $status, $comments, $_SESSION['user_id'], $leave_id);
        
        if ($stmt->execute()) {
            $message = "Leave request status updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating leave request status: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'cancel_leave') {
        // Cancel leave request
        $leave_id = $_POST['leave_id'];
        $status = 'cancelled';
        
        $query = "UPDATE leave_requests SET status = ?, updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sii", $status, $_SESSION['user_id'], $leave_id);
        
        if ($stmt->execute()) {
            $message = "Leave request cancelled successfully!";
            $messageType = "success";
        } else {
            $message = "Error cancelling leave request: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get leave requests based on user role
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    // Admins and HR can see all leave requests
    $query = "SELECT lr.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department,
              lt.leave_type_id as leave_type
              FROM leave_requests lr 
              LEFT JOIN employees e ON lr.employee_id = e.employee_id
              LEFT JOIN leave_types lt ON lr.leave_type_id = leave_id
              ORDER BY lr.created_at DESC";
    $leave_requests = $db->query($query);
} else {
    // Regular employees can only see their own leave requests
    $query = "SELECT lr.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department
              FROM leave_requests lr 
              LEFT JOIN employees e ON lr.employee_id = e.employee_id
              WHERE lr.employee_id = ?
              ORDER BY lr.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $leave_requests = $stmt->get_result();
}

// Get leave types
$leave_types = ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave', 'Bereavement Leave', 'Unpaid Leave', 'Other'];

// Get leave balances for the current user
$query = "SELECT 
          SUM(CASE WHEN leave_type_id = 1 AND status = 'approved' THEN total_days ELSE 0 END) as annual_used,
          SUM(CASE WHEN leave_type_id = 2 AND status = 'approved' THEN total_days ELSE 0 END) as sick_used,
          SUM(CASE WHEN leave_type_id = 3 AND status = 'approved' THEN total_days ELSE 0 END) as maternity_used,
          SUM(CASE WHEN leave_type_id NOT IN (1, 2, 3) AND status = 'approved' THEN total_days ELSE 0 END) as other_used
          FROM leave_requests 
          WHERE employee_id = ? AND YEAR(start_date) = YEAR(CURRENT_DATE())";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$leave_balance = $stmt->get_result()->fetch_assoc();

// Define leave entitlements (these could come from a settings table in a real application)
$leave_entitlements = [
    'annual' => 20,
    'sick' => 10,
    'maternity' => 90,
    'other' => 5
];

// Set page title and CSS
$pageTitle = "Leave Management";
$pageCss = ['assets/css/leave-management.css'];

// Include header
include "../../includes/header.php"';
include '../../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Leave Management
        </h4>

        <!-- Alert for messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Leave Balance Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Leave Balance</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Annual Leave</h5>
                                <h2 class="mb-0"><?php echo $leave_entitlements['annual'] - ($leave_balance['annual_used'] ?? 0); ?> days</h2>
                                <p class="card-text"><?php echo $leave_balance['annual_used'] ?? 0; ?> days used</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Sick Leave</h5>
                                <h2 class="mb-0"><?php echo $leave_entitlements['sick'] - ($leave_balance['sick_used'] ?? 0); ?> days</h2>
                                <p class="card-text"><?php echo $leave_balance['sick_used'] ?? 0; ?> days used</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Maternity Leave</h5>
                                <h2 class="mb-0"><?php echo $leave_entitlements['maternity'] - ($leave_balance['maternity_used'] ?? 0); ?> days</h2>
                                <p class="card-text"><?php echo $leave_balance['maternity_used'] ?? 0; ?> days used</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Other Leave</h5>
                                <h2 class="mb-0"><?php echo $leave_entitlements['other'] - ($leave_balance['other_used'] ?? 0); ?> days</h2>
                                <p class="card-text"><?php echo $leave_balance['other_used'] ?? 0; ?> days used</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Management Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Leave Requests</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#apply_leave">
                    <i class="bx bx-plus me-1"></i> Apply for Leave
                </button>
            </div>
            
            <!-- Leave Requests Table -->
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                <th>Employee</th>
                                <?php endif; ?>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php if ($leave_requests && $leave_requests->num_rows > 0): ?>
                                <?php while ($leave = $leave_requests->fetch_assoc()): ?>
                                <tr>
                                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                    <td><?php echo htmlspecialchars($leave['employee_name'] ?? 'N/A'); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($leave['leave_type'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($leave['start_date'] ?? '')); ?></td>
                                    <td><?php echo date('d M Y', strtotime($leave['end_date'] ?? '')); ?></td>
                                    <td><?php echo $leave['total_days'] ?? 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars(substr($leave['reason'] ?? 'N/A', 0, 30)) . (strlen($leave['reason'] ?? '') > 30 ? '...' : ''); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch($leave['status'] ?? 'pending') {
                                            case 'pending': $statusClass = 'warning'; break;
                                            case 'approved': $statusClass = 'success'; break;
                                            case 'rejected': $statusClass = 'danger'; break;
                                            case 'cancelled': $statusClass = 'secondary'; break;
                                            default: $statusClass = 'info';
                                        }
                                        ?>
                                        <span class="badge bg-label-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($leave['status'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($leave['created_at'] ?? '')); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item view-leave" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#view_leave" 
                                                   data-id="<?php echo $leave['id']; ?>"
                                                   data-employee="<?php echo htmlspecialchars($leave['employee_name']); ?>"
                                                   data-department="<?php echo htmlspecialchars($leave['department']); ?>"
                                                   data-type="<?php echo htmlspecialchars($leave['leave_type']); ?>"
                                                   data-start="<?php echo date('d M Y', strtotime($leave['start_date'])); ?>"
                                                   data-end="<?php echo date('d M Y', strtotime($leave['end_date'])); ?>"
                                                   data-days="<?php echo $leave['days']; ?>"
                                                   data-reason="<?php echo htmlspecialchars($leave['reason']); ?>"
                                                   data-status="<?php echo ucfirst($leave['status']); ?>"
                                                   data-comments="<?php echo htmlspecialchars($leave['comments']); ?>"
                                                   data-created="<?php echo date('d M Y', strtotime($leave['created_at'])); ?>"
                                                   data-created-by="<?php echo htmlspecialchars($leave['created_by_name']); ?>">
                                                    <i class="bx bx-show me-1"></i> View
                                                </a>
                                                
                                                <?php if ((strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') && $leave['status'] === 'pending'): ?>
                                                <a class="dropdown-item update-leave-status" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#update_leave_status" 
                                                   data-id="<?php echo $leave['id']; ?>"
                                                   data-employee="<?php echo htmlspecialchars($leave['employee_name']); ?>"
                                                   data-type="<?php echo htmlspecialchars($leave['leave_type']); ?>"
                                                   data-start="<?php echo date('d M Y', strtotime($leave['start_date'])); ?>"
                                                   data-end="<?php echo date('d M Y', strtotime($leave['end_date'])); ?>">
                                                    <i class="bx bx-check-circle me-1"></i> Update Status
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($leave['status'] === 'pending' && $leave['employee_id'] == $_SESSION['user_id']): ?>
                                                <a class="dropdown-item cancel-leave" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#cancel_leave" 
                                                   data-id="<?php echo $leave['id']; ?>">
                                                    <i class="bx bx-x-circle me-1"></i> Cancel
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? '9' : '8'; ?>" class="text-center">No leave requests found</td>
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

<!-- Apply Leave Modal -->
<div class="modal fade" id="apply_leave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply for Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="leave_management.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_leave">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                <?php
                                $leave_types_query = "SELECT * FROM leave_types";
                                $leave_types_result = $db->query($leave_types_query);
                                while ($type = $leave_types_result->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo $type['leave_type_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="days" class="form-label">Number of Days</label>
                            <input type="text" class="form-control" id="days" readonly>
                            <small class="text-muted">This will be calculated automatically</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Apply Leave Modal -->

<!-- View Leave Modal -->
<div class="modal fade" id="view_leave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee</label>
                        <p id="view_employee" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <p id="view_department" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Leave Type</label>
                        <p id="view_type" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <p id="view_status" class="form-control-static"></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start Date</label>
                        <p id="view_start" class="form-control-static"></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">End Date</label>
                        <p id="view_end" class="form-control-static"></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Number of Days</label>
                        <p id="view_days" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Reason</label>
                        <p id="view_reason" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Comments</label>
                        <p id="view_comments" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applied On</label>
                        <p id="view_created" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applied By</label>
                        <p id="view_created_by" class="form-control-static"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- / View Leave Modal -->

<!-- Update Leave Status Modal -->
<div class="modal fade" id="update_leave_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Leave Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="leave_management.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_leave_status">
                    <input type="hidden" name="leave_id" id="status_leave_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <p id="status_employee" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Details</label>
                        <p id="status_details" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
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
<!-- / Update Leave Status Modal -->

<!-- Cancel Leave Modal -->
<div class="modal fade" id="cancel_leave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this leave request?</p>
                <p class="text-warning">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="leave_management.php" method="post">
                    <input type="hidden" name="action" value="cancel_leave">
                    <input type="hidden" name="leave_id" id="cancel_leave_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Cancel Leave</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Cancel Leave Modal -->

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
    // Calculate days between start and end date
    function calculateDays() {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($('#end_date').val());
        
        if (startDate && endDate) {
            // Calculate the time difference in milliseconds
            var timeDiff = endDate.getTime() - startDate.getTime();
            
            // Convert time difference to days and add 1 to include both start and end dates
            var daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24)) + 1;
            
            // Update the days field
            $('#days').val(daysDiff > 0 ? daysDiff : 'N/A');
        }
    }
    
    // Calculate days when start or end date changes
    $('#start_date, #end_date').on('change', calculateDays);
    
    // View leave details
    $('.view-leave').on('click', function() {
        var employee = $(this).data('employee');
        var department = $(this).data('department');
        var type = $(this).data('type');
        $('#view_type').text(type);
        var start = $(this).data('start');
        var end = $(this).data('end');
        var days = $(this).data('days');
        var reason = $(this).data('reason');
        var status = $(this).data('status');
        var comments = $(this).data('comments');
        var created = $(this).data('created');
        var createdBy = $(this).data('created-by');
        
        $('#view_employee').text(employee);
        $('#view_department').text(department);
        $('#view_type').text(type);
        $('#view_start').text(start);
        $('#view_end').text(end);
        $('#view_days').text(days);
        $('#view_reason').text(reason);
        $('#view_status').text(status);
        $('#view_comments').text(comments || 'No comments');
        $('#view_created').text(created);
        $('#view_created_by').text(createdBy);
    });
    
    // Update leave status
    $('.update-leave-status').on('click', function() {
        var leaveId = $(this).data('id');
        var employee = $(this).data('employee');
        var type = $(this).data('type');
        var start = $(this).data('start');
        var end = $(this).data('end');
        
        $('#status_leave_id').val(leaveId);
        $('#status_employee').text(employee);
        $('#status_details').text(type + ' from ' + start + ' to ' + end);
    });
    
    // Cancel leave
    $('.cancel-leave').on('click', function() {
        var leaveId = $(this).data('id');
        $('#cancel_leave_id').val(leaveId);
    });
});
</script>
</body>
</html>
