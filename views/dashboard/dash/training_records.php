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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_training') {
        // Add new training record
        $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : $_SESSION['user_id'];
        $training_name = $_POST['training_name'];
        $training_type = $_POST['training_type'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];
        $trainer = $_POST['trainer'];
        $description = $_POST['description'];
        $is_required = isset($_POST['is_required']) ? 1 : 0;
        
        $query = "INSERT INTO training_records (employee_id, training_name, training_type, start_date, end_date, status, trainer, description, is_required, created_at, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("isssssssii", $employee_id, $training_name, $training_type, $start_date, $end_date, $status, $trainer, $description, $is_required, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $training_id = $db->insert_id;
            
            // Handle file upload
            if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0) {
                $upload_dir = 'uploads/certificates/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = $training_id . '_' . basename($_FILES['certificate']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                    $query = "UPDATE training_records SET certificate_path = ? WHERE training_id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $training_id);
                    $stmt->execute();
                }
            }
            
            $message = "Training record added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding training record: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_training') {
        // Update training record
        $training_id = $_POST['training_id'];
        $training_name = $_POST['training_name'];
        $training_type = $_POST['training_type'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];
        $trainer = $_POST['trainer'];
        $description = $_POST['description'];
        $is_required = isset($_POST['is_required']) ? 1 : 0;
        
        $query = "UPDATE training_records SET 
                  training_name = ?, 
                  training_type = ?, 
                  start_date = ?, 
                  end_date = ?, 
                  status = ?, 
                  trainer = ?, 
                  description = ?, 
                  is_required = ?,
                  updated_at = NOW(), 
                  updated_by = ? 
                  WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssssiii", $training_name, $training_type, $start_date, $end_date, $status, $trainer, $description, $is_required, $_SESSION['user_id'], $training_id);
        
        if ($stmt->execute()) {
            // Handle file upload
            if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0) {
                $upload_dir = 'uploads/certificates/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Get existing certificate path to delete if exists
                $query = "SELECT certificate_path FROM training_records WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $training_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_path = $result->fetch_assoc()['certificate_path'];
                
                // Delete old file if exists
                if (!empty($old_path) && file_exists($old_path)) {
                    unlink($old_path);
                }
                
                $file_name = $training_id . '_' . basename($_FILES['certificate']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                    $query = "UPDATE training_records SET certificate_path = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $training_id);
                    $stmt->execute();
                }
            }
            
            $message = "Training record updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating training record: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'upload_certificate') {
        // Upload certificate for existing training record
        $training_id = $_POST['training_id'];
        
        // Handle file upload
        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0) {
            $upload_dir = 'uploads/certificates/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Get existing certificate path to delete if exists
            $query = "SELECT certificate_path FROM training_records WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $training_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $old_path = $result->fetch_assoc()['certificate_path'];
            
            // Delete old file if exists
            if (!empty($old_path) && file_exists($old_path)) {
                unlink($old_path);
            }
            
            $file_name = $training_id . '_' . basename($_FILES['certificate']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                $query = "UPDATE training_records SET 
                          certificate_path = ?, 
                          status = 'Completed',
                          updated_at = NOW(), 
                          updated_by = ? 
                          WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sii", $target_file, $_SESSION['user_id'], $training_id);
                
                if ($stmt->execute()) {
                    $message = "Certificate uploaded successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error uploading certificate: " . $db->error;
                    $messageType = "danger";
                }
            } else {
                $message = "Error uploading file.";
                $messageType = "danger";
            }
        } else {
            $message = "No file selected or file upload error.";
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'delete_training') {
        // Delete training record
        $training_id = $_POST['training_id'];
        
        // Get certificate path to delete file if exists
        $query = "SELECT certificate_path FROM training_records WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $training_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate_path = $result->fetch_assoc()['certificate_path'];
        
        // Delete the record from the database
        $query = "DELETE FROM training_records WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $training_id);
        
        if ($stmt->execute()) {
            // Delete the file if it exists
            if (!empty($certificate_path) && file_exists($certificate_path)) {
                unlink($certificate_path);
            }
            
            $message = "Training record deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting training record: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_status') {
        // Update training status
        $training_id = $_POST['training_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'];
        
        $query = "UPDATE training_records SET 
                  status = ?, 
                  notes = CONCAT(IFNULL(notes, ''), '\n', ?),
                  updated_at = NOW(), 
                  updated_by = ? 
                  WHERE id = ?";
        $stmt = $db->prepare($query);
        
        $status_note = date('Y-m-d H:i:s') . " - Status updated to " . $status . ": " . $notes;
        $stmt->bind_param("ssii", $status, $status_note, $_SESSION['user_id'], $training_id);
        
        if ($stmt->execute()) {
            $message = "Training status updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating training status: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get filter parameters
$filter_employee = isset($_GET['employee']) ? $_GET['employee'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_required = isset($_GET['required']) ? $_GET['required'] : '';

// Get training records based on user role and filters
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    // Admins and HR can see all training records
    $query = "SELECT tr.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department
              FROM training_records tr 
              LEFT JOIN employees e ON tr.employee_id = e.employee_id
              WHERE 1=1 ";
    
    // Apply filters
    if (!empty($filter_employee)) {
        $query .= "AND tr.employee_id = ? ";
    }
    if (!empty($filter_type)) {
        $query .= "AND tr.training_type = ? ";
    }
    if (!empty($filter_status)) {
        $query .= "AND tr.status = ? ";
    }
    if (!empty($filter_required)) {
        $query .= "AND tr.is_required = ? ";
    }
    
    $query .= "ORDER BY tr.start_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_employee) && !empty($filter_type) && !empty($filter_status) && !empty($filter_required)) {
        $stmt->bind_param("issi", $filter_employee, $filter_type, $filter_status, $filter_required);
    } elseif (!empty($filter_employee) && !empty($filter_type)) {
        $stmt->bind_param("is", $filter_employee, $filter_type);
    } elseif (!empty($filter_employee)) {
        $stmt->bind_param("i", $filter_employee);
    }
    
    $stmt->execute();
    $training_records = $stmt->get_result();
} else {
    // Regular employees can only see their own training records
    $query = "SELECT tr.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department,
              u.username as created_by_name
              FROM training_records tr 
              LEFT JOIN employees e ON tr.employee_id = e.id
              LEFT JOIN users u ON tr.created_by = u.id
              WHERE tr.employee_id = ? ";
    
    // Apply filters
    if (!empty($filter_type)) {

        $query .= "AND tr.training_type = ? ";
    }
    if (!empty($filter_status)) {
        $query .= "AND tr.status = ? ";
    }
    if (!empty($filter_required)) {
        $query .= "AND tr.is_required = ? ";
    }
    
    $query .= "ORDER BY tr.start_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_type) && !empty($filter_status) && !empty($filter_required)) {
        $stmt->bind_param("issi", $_SESSION['user_id'], $filter_type, $filter_status, $filter_required);
    } elseif (!empty($filter_type) && !empty($filter_status)) {
        $stmt->bind_param("iss", $_SESSION['user_id'], $filter_type, $filter_status);
    } elseif (!empty($filter_type) && !empty($filter_required)) {
        $stmt->bind_param("isi", $_SESSION['user_id'], $filter_type, $filter_required);
    } elseif (!empty($filter_status) && !empty($filter_required)) {
        $stmt->bind_param("isi", $_SESSION['user_id'], $filter_status, $filter_required);
    } elseif (!empty($filter_type)) {
        $stmt->bind_param("is", $_SESSION['user_id'], $filter_type);
    } elseif (!empty($filter_status)) {
        $stmt->bind_param("is", $_SESSION['user_id'], $filter_status);
    } elseif (!empty($filter_required)) {
        $stmt->bind_param("ii", $_SESSION['user_id'], $filter_required);
    } else {
        $stmt->bind_param("i", $_SESSION['user_id']);
    }
    
    $stmt->execute();
    $training_records = $stmt->get_result();
}

// Get employees for dropdown (for admin/HR)
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    $query = "SELECT employee_id, CONCAT(first_name, ' ', last_name) as name, department FROM employees ORDER BY first_name";
    $employees = $db->query($query);
}

// Get unique training types for filter
$query = "SELECT DISTINCT training_type FROM training_records WHERE training_type IS NOT NULL AND training_type != '' ORDER BY training_type";
$training_types = $db->query($query);

$query = "SELECT DISTINCT program_id FROM training_records WHERE program_id IS NOT NULL AND program_id != '' ORDER BY program_id";

// Set page title and CSS
$pageTitle = "Training Records";
$pageCss = ['assets/css/training-records.css'];

// Include header
include "../../includes/header.php"';
include '../../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Employee Development /</span> Training Records
        </h4>

        <!-- Alert for messages -->
         
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form action="training_records.php" method="get">
                    <div class="row">
                        <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                        <div class="col-md-3 mb-2">
                            <label for="employee" class="form-label">Employee</label>
                            <select class="form-select" id="employee" name="employee">
                                <option value="">All Employees</option>
                                <?php 
                                if (isset($employees)) {
                                    while ($employee = $employees->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $employee['employee_id']; ?>">
                                        <?php echo htmlspecialchars($employee['name']); ?>
                                    </option>
                                <?php 
                                    endwhile;
                                }
                                ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3 mb-2">
                            <label for="type" class="form-label">Training Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <?php while ($type = $training_types->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($type['training_type']); ?>" <?php echo $filter_type === $type['training_type'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['training_type']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="Not Started" <?php echo $filter_status === 'Not Started' ? 'selected' : ''; ?>>Not Started</option>
                                <option value="Ongoing" <?php echo $filter_status === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="Completed" <?php echo $filter_status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="required" class="form-label">Required</label>
                            <select class="form-select" id="required" name="required">
                                <option value="">All</option>
                                <option value="1" <?php echo $filter_required === '1' ? 'selected' : ''; ?>>Required</option>
                                <option value="0" <?php echo $filter_required === '0' ? 'selected' : ''; ?>>Optional</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Training Records Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Training Records</h5>
                <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_training">
                    <i class="bx bx-plus me-1"></i> Add Training
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                <th>Employee</th>
                                <?php endif; ?>
                                <th>Training Name</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Required</th>
                                <th>Certificate</th>
                                <th>Actions</th>
                                <th>Created At</th>
                                <th>Program ID</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php if ($training_records && $training_records->num_rows > 0): ?>
                                <?php while ($record = $training_records->fetch_assoc()): ?>
                                <tr>
                                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="avatar-wrapper">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <?php 
                                                        $name_parts = explode(' ', $record['employee_name']);
                                                        echo strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '')); 
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold"><?php echo htmlspecialchars($record['employee_name']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($record['department']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($record['training_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($record['training_type']); ?></td>
                                    <td>
                                        <?php 
                                        $start_date = $record['start_date'] ?? null;
                                        $end_date = $record['end_date'] ?? null;
                                        if ($start_date) {
                                            echo date('d M Y', strtotime($start_date));
                                        } else {
                                            echo 'N/A';
                                        }
                                        if ($start_date && $end_date && $start_date !== $end_date) {
                                            echo ' - ' . date('d M Y', strtotime($end_date));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch ($record['status'] ?? 'Unknown') {
                                            case 'Not Started': $statusClass = 'warning'; break;
                                            case 'Ongoing': $statusClass = 'info'; break;
                                            case 'Completed': $statusClass = 'success'; break;
                                            default: $statusClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-label-<?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($record['status'] ?? 'Unknown'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($record['is_required'])): ?>
                                            <span class="badge bg-label-danger">Required</span>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary">Optional</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($record['certificate_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($record['certificate_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-file"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Not uploaded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item view-training" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#view_training" 
                                                   data-id="<?php echo $record['id']; ?>"
                                                   data-employee="<?php echo htmlspecialchars($record['employee_name']); ?>"
                                                   data-department="<?php echo htmlspecialchars($record['department']); ?>"
                                                   data-name="<?php echo htmlspecialchars($record['training_name']); ?>"
                                                   data-type="<?php echo htmlspecialchars($record['training_type']); ?>"
                                                   data-start="<?php echo date('Y-m-d', strtotime($record['start_date'])); ?>"
                                                   data-end="<?php echo date('Y-m-d', strtotime($record['end_date'])); ?>"
                                                   data-status="<?php echo $record['status']; ?>"
                                                   data-trainer="<?php echo htmlspecialchars($record['trainer']); ?>"
                                                   data-description="<?php echo htmlspecialchars($record['description']); ?>"
                                                   data-required="<?php echo $record['is_required']; ?>"
                                                   data-notes="<?php echo htmlspecialchars($record['notes']); ?>"
                                                   data-certificate="<?php echo $record['certificate_path']; ?>">
                                                    <i class="bx bx-show me-1"></i> View
                                                </a>
                                                
                                                <?php if ((strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr')): ?>
                                                <a class="dropdown-item edit-training" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#edit_training" 
                                                   data-id="<?php echo $record['id']; ?>"
                                                   data-employee="<?php echo $record['employee_id']; ?>"
                                                   data-name="<?php echo htmlspecialchars($record['training_name']); ?>"
                                                   data-type="<?php echo htmlspecialchars($record['training_type']); ?>"
                                                   data-start="<?php echo date('Y-m-d', strtotime($record['start_date'])); ?>"
                                                   data-end="<?php echo date('Y-m-d', strtotime($record['end_date'])); ?>"
                                                   data-status="<?php echo $record['status']; ?>"
                                                   data-trainer="<?php echo htmlspecialchars($record['trainer']); ?>"
                                                   data-description="<?php echo htmlspecialchars($record['description']); ?>"
                                                   data-required="<?php echo $record['is_required']; ?>">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                
                                                <a class="dropdown-item update-status" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#update_status" 
                                                   data-id="<?php echo $record['id']; ?>"
                                                   data-employee="<?php echo htmlspecialchars($record['employee_name']); ?>"
                                                   data-name="<?php echo htmlspecialchars($record['training_name']); ?>"
                                                   data-status="<?php echo $record['status']; ?>">
                                                    <i class="bx bx-transfer-alt me-1"></i> Update Status
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($record['employee_id'] == $_SESSION['user_id'] && $record['status'] !== 'Completed'): ?>
                                                <a class="dropdown-item upload-certificate" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#upload_certificate" 
                                                   data-id="<?php echo $record['id']; ?>"
                                                   data-name="<?php echo htmlspecialchars($record['training_name']); ?>">
                                                    <i class="bx bx-upload me-1"></i> Upload Certificate
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ((strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr')): ?>
                                                <a class="dropdown-item delete-training" href="javascript:void(0);" 
                                                   data-bs-toggle="modal" data-bs-target="#delete_training" 
                                                   data-id="<?php echo $record['id']; ?>"
                                                   data-name="<?php echo htmlspecialchars($record['training_name']); ?>">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y H:i:s', strtotime($record['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['program_id']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? '8' : '7'; ?>" class="text-center">No training records found</td>
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

<!-- Add Training Modal -->
<div class="modal fade" id="add_training" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Training Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="training_records.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_training">
                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php 
                            if (isset($employees)) {
                                $employees->data_seek(0); // Reset the pointer
                                while ($employee = $employees->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $employee['id']; ?>">d']; ?>">
                                    <?php echo htmlspecialchars($employee['name']); ?> (<?php echo htmlspecialchars($employee['department']); ?>)
                                </option>
                            <?php 
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="training_name" class="form-label">Training Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="training_name" name="training_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="training_type" class="form-label">Training Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="training_type" name="training_type" required>
                                <option value="">Select Type</option>
                                <option value="Technical">Technical</option>
                                <option value="Soft Skills">Soft Skills</option>
                                <option value="Compliance">Compliance</option>
                                <option value="Leadership">Leadership</option>
                                <option value="Professional Development">Professional Development</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="trainer" class="form-label">Trainer/Institution</label>
                            <input type="text" class="form-control" id="trainer" name="trainer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Not Started">Not Started</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required">
                                <label class="form-check-label" for="is_required">Required Training</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="certificate" class="form-label">Upload Certificate</label>
                            <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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
<!-- / Add Training Modal -->

<!-- View Training Modal -->
<div class="modal fade" id="view_training" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Details</h5>
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
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Training Name</label>
                        <p id="view_name" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Training Type</label>
                        <p id="view_type" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trainer/Institution</label>
                        <p id="view_trainer" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <p id="view_start" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <p id="view_end" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <p id="view_status" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Required</label>
                        <p id="view_required" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <p id="view_description" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Certificate</label>
                        <p id="view_certificate" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <div id="view_notes" class="form-control-static bg-light p-2 rounded" style="white-space: pre-line;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- / View Training Modal -->

<!-- Edit Training Modal -->
<div class="modal fade" id="edit_training" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Training Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="training_records.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_training">
                    <input type="hidden" name="training_id" id="edit_training_id">
                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                    <div class="mb-3">
                        <label for="edit_employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php 
                            if (isset($employees)) {
                                $employees->data_seek(0); // Reset the pointer
                                while ($employee = $employees->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>">
                                    <?php echo htmlspecialchars($employee['name']); ?>
                                </option>
                            <?php 
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_training_name" class="form-label">Training Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_training_name" name="training_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_training_type" class="form-label">Training Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_training_type" name="training_type" required>
                                <option value="">Select Type</option>
                                <option value="Technical">Technical</option>
                                <option value="Soft Skills">Soft Skills</option>
                                <option value="Compliance">Compliance</option>
                                <option value="Leadership">Leadership</option>
                                <option value="Professional Development">Professional Development</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_trainer" class="form-label">Trainer/Institution</label>
                            <input type="text" class="form-control" id="edit_trainer" name="trainer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Not Started">Not Started</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required">
                                <label class="form-check-label" for="edit_is_required">Required Training</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_certificate" class="form-label">Upload Certificate</label>
                            <input type="file" class="form-control" id="edit_certificate" name="certificate" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Leave empty to keep the current certificate</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Edit Training Modal -->

<!-- Upload Certificate Modal -->
<div class="modal fade" id="upload_certificate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="training_records.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="upload_certificate">
                    <input type="hidden" name="training_id" id="upload_training_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Training</label>
                        <p id="upload_training_name" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label for="certificate" class="form-label">Certificate <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Uploading a certificate will mark this training as completed</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Upload Certificate Modal -->

<!-- Update Status Modal -->
<div class="modal fade" id="update_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Training Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="training_records.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="training_id" id="status_training_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <p id="status_employee" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Training</label>
                        <p id="status_training_name" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label for="status_update" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_update" name="status" required>
                            <option value="Not Started">Not Started</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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
<!-- / Update Status Modal -->

<!-- Delete Training Modal -->
<div class="modal fade" id="delete_training" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this training record?</p>
                <p id="delete_training_name" class="fw-bold"></p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="training_records.php" method="post">
                    <input type="hidden" name="action" value="delete_training">
                    <input type="hidden" name="training_id" id="delete_training_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Delete Training Modal -->

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
    // View training details
    $('.view-training').on('click', function() {
        var employee = $(this).data('employee');
        var department = $(this).data('department');
        var name = $(this).data('name');
        var type = $(this).data('type');
        var start = $(this).data('start');
        var end = $(this).data('end');
        var status = $(this).data('status');
        var trainer = $(this).data('trainer');
        var description = $(this).data('description');
        var required = $(this).data('required');
        var notes = $(this).data('notes');
        var certificate = $(this).data('certificate');
        
        $('#view_employee').text(employee);
        $('#view_department').text(department);
        $('#view_name').text(name);
        $('#view_type').text(type);
        $('#view_start').text(formatDate(start));
        $('#view_end').text(formatDate(end));
        $('#view_status').text(status);
        $('#view_trainer').text(trainer || 'N/A');
        $('#view_description').text(description || 'N/A');
        $('#view_required').text(required ? 'Yes' : 'No');
        $('#view_notes').text(notes || 'No notes');
        
        if (certificate) {
            $('#view_certificate').html('<a href="' + certificate + '" target="_blank" class="btn btn-sm btn-primary"><i class="bx bx-download me-1"></i> View Certificate</a>');
        } else {
            $('#view_certificate').text('No certificate uploaded');
        }
    });
    
    // Edit training
    $('.edit-training').on('click', function() {
        var id = $(this).data('id');
        var employee = $(this).data('employee');
        var name = $(this).data('name');
        var type = $(this).data('type');
        var start = $(this).data('start');
        var end = $(this).data('end');
        var status = $(this).data('status');
        var trainer = $(this).data('trainer');
        var description = $(this).data('description');
        var required = $(this).data('required');
        
        $('#edit_training_id').val(id);
        if ($('#edit_employee_id').length) {
            $('#edit_employee_id').val(employee);
        }
        $('#edit_training_name').val(name);
        $('#edit_training_type').val(type);
        $('#edit_start_date').val(start);
        $('#edit_end_date').val(end);
        $('#edit_status').val(status);
        $('#edit_trainer').val(trainer);
        $('#edit_description').val(description);
        $('#edit_is_required').prop('checked', required == 1);
    });
    
    // Upload certificate
    $('.upload-certificate').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#upload_training_id').val(id);
        $('#upload_training_name').text(name);
    });
    
    // Update status
    $('.update-status').on('click', function() {
        var id = $(this).data('id');
        var employee = $(this).data('employee');
        var name = $(this).data('name');
        var status = $(this).data('status');
        
        $('#status_training_id').val(id);
        $('#status_employee').text(employee);
        $('#status_training_name').text(name);
        $('#status_update').val(status);
    });
    
    // Delete training
    $('.delete-training').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#delete_training_id').val(id);
        $('#delete_training_name').text(name);
    });
    
    // Validate date ranges
    $('#start_date, #end_date, #edit_start_date, #edit_end_date').on('change', function() {
        var prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
        var startDate = new Date($('#' + prefix + 'start_date').val());
        var endDate = new Date($('#' + prefix + 'end_date').val());
        
        if (startDate > endDate) {
            alert('End date cannot be earlier than start date');
            $('#' + prefix + 'end_date').val($('#' + prefix + 'start_date').val());
        }
    });
    
    // Format date for display
    function formatDate(dateString) {
        var date = new Date(dateString);
        var day = date.getDate();
        var month = date.toLocaleString('default', { month: 'short' });
        var year = date.getFullYear();
        return day + ' ' + month + ' ' + year;
    }
});
</script>
</body>
</html>
