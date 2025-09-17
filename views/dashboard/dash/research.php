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
    if ($_POST['action'] === 'add_publication') {
        // Add new publication
        $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : $_SESSION['user_id'];
        $title = $_POST['title'];
        $publication_date = $_POST['publication_date'];
        $journal_publisher = $_POST['journal_publisher'];
        $abstract = $_POST['abstract'];
        $authors = $_POST['authors'];
        $doi = $_POST['doi'];
        $status = (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? 'approved' : 'pending';
        
        $query = "INSERT INTO research_publications (employee_id, title, publication_date, journal_publisher, abstract, authors, doi, status, created_at, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("isssssssi", $employee_id, $title, $publication_date, $journal_publisher, $abstract, $authors, $doi, $status, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $publication_id = $db->insert_id;
            
            // Handle file upload
            if (isset($_FILES['publication_file']) && $_FILES['publication_file']['error'] === 0) {
                $upload_dir = 'uploads/publications/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = $publication_id . '_' . basename($_FILES['publication_file']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['publication_file']['tmp_name'], $target_file)) {
                    $query = "UPDATE research_publications SET file_path = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $publication_id);
                    $stmt->execute();
                }
            }
            
            $message = "Publication added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding publication: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'add_conference') {
        // Add new conference
        $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : $_SESSION['user_id'];
        $conference_name = $_POST['conference_name'];
        $location = $_POST['location'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $paper_title = $_POST['paper_title'];
        $role = $_POST['role'];
        $description = $_POST['description'];
        $status = (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? 'approved' : 'pending';
        
        $query = "INSERT INTO conferences (employee_id, conference_name, location, start_date, end_date, paper_title, role, description, status, created_at, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("issssssssi", $employee_id, $conference_name, $location, $start_date, $end_date, $paper_title, $role, $description, $status, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $conference_id = $db->insert_id;
            
            // Handle file upload
            if (isset($_FILES['conference_file']) && $_FILES['conference_file']['error'] === 0) {
                $upload_dir = 'uploads/conferences/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = $conference_id . '_' . basename($_FILES['conference_file']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['conference_file']['tmp_name'], $target_file)) {
                    $query = "UPDATE conferences SET file_path = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("si", $target_file, $conference_id);
                    $stmt->execute();
                }
            }
            
            $message = "Conference participation added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding conference participation: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_publication_status') {
        // Update publication status
        $publication_id = $_POST['publication_id'];
        $status = $_POST['status'];
        $comments = $_POST['comments'];
        
        $query = "UPDATE research_publications SET status = ?, comments = ?, updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssii", $status, $comments, $_SESSION['user_id'], $publication_id);
        
        if ($stmt->execute()) {
            $message = "Publication status updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating publication status: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_conference_status') {
        // Update conference status
        $conference_id = $_POST['conference_id'];
        $status = $_POST['status'];
        $comments = $_POST['comments'];
        
        $query = "UPDATE conferences SET status = ?, comments = ?, updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssii", $status, $comments, $_SESSION['user_id'], $conference_id);
        
        if ($stmt->execute()) {
            $message = "Conference participation status updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating conference participation status: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'delete_publication') {
        // Delete publication
        $publication_id = $_POST['publication_id'];
        
        // Get file path to delete the file
        $query = "SELECT file_path FROM research_publications WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $publication_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file_path = $result->fetch_assoc()['file_path'];
        
        // Delete the record from the database
        $query = "DELETE FROM research_publications WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $publication_id);
        
        if ($stmt->execute()) {
            // Delete the file if it exists
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
            
            $message = "Publication deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting publication: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'delete_conference') {
        // Delete conference
        $conference_id = $_POST['conference_id'];
        
        // Get file path to delete the file
        $query = "SELECT file_path FROM conferences WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $conference_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file_path = $result->fetch_assoc()['file_path'];
        
        // Delete the record from the database
        $query = "DELETE FROM conferences WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $conference_id);
        
        if ($stmt->execute()) {
            // Delete the file if it exists
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
            
            $message = "Conference participation deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting conference participation: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get filter parameters
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_journal = isset($_GET['journal']) ? $_GET['journal'] : '';
$filter_employee = isset($_GET['employee']) ? $_GET['employee'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Get publications based on user role and filters
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    // Admins and HR can see all publications
    $query = "SELECT rp.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department
              FROM research_publications rp 
              LEFT JOIN employees e ON rp.employee_id = e.employee_id
              WHERE 1=1 ";
    
    // Apply filters
    if (!empty($filter_year)) {
        $query .= "AND YEAR(rp.publication_date) = ? ";
    }
    if (!empty($filter_journal)) {
        $query .= "AND rp.journal_publisher LIKE ? ";
    }
    if (!empty($filter_employee)) {
        $query .= "AND rp.employee_id = ? ";
    }
    if (!empty($filter_status)) {
        $query .= "AND rp.status = ? ";
    }
    
    $query .= "ORDER BY rp.publication_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_year) && !empty($filter_journal) && !empty($filter_employee) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("isss", $filter_year, $filter_journal, $filter_employee, $filter_status);
    } elseif (!empty($filter_year) && !empty($filter_journal) && !empty($filter_employee)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("isi", $filter_year, $filter_journal, $filter_employee);
    } elseif (!empty($filter_year) && !empty($filter_journal) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("iss", $filter_year, $filter_journal, $filter_status);
    } elseif (!empty($filter_year) && !empty($filter_employee) && !empty($filter_status)) {
        $stmt->bind_param("iis", $filter_year, $filter_employee, $filter_status);
    } elseif (!empty($filter_journal) && !empty($filter_employee) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("sis", $filter_journal, $filter_employee, $filter_status);
    } elseif (!empty($filter_year) && !empty($filter_journal)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("is", $filter_year, $filter_journal);
    } elseif (!empty($filter_year) && !empty($filter_employee)) {
        $stmt->bind_param("ii", $filter_year, $filter_employee);
    } elseif (!empty($filter_year) && !empty($filter_status)) {
        $stmt->bind_param("is", $filter_year, $filter_status);
    } elseif (!empty($filter_journal) && !empty($filter_employee)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("si", $filter_journal, $filter_employee);
    } elseif (!empty($filter_journal) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("ss", $filter_journal, $filter_status);
    } elseif (!empty($filter_employee) && !empty($filter_status)) {
        $stmt->bind_param("is", $filter_employee, $filter_status);
    } elseif (!empty($filter_year)) {
        $stmt->bind_param("i", $filter_year);
    } elseif (!empty($filter_journal)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("s", $filter_journal);
    } elseif (!empty($filter_employee)) {
        $stmt->bind_param("i", $filter_employee);
    } elseif (!empty($filter_status)) {
        $stmt->bind_param("s", $filter_status);
    }
    
    $stmt->execute();
    $publications = $stmt->get_result();
} else {
    // Regular employees can only see their own publications
    $query = "SELECT rp.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department,
              u.username as created_by_name
              FROM research_publications rp 
              LEFT JOIN employees e ON rp.employee_id = e.id
              LEFT JOIN users u ON rp.created_by = u.id
              WHERE rp.employee_id = ? ";
    
    // Apply filters
    if (!empty($filter_year)) {
        $query .= "AND YEAR(rp.publication_date) = ? ";
    }
    if (!empty($filter_journal)) {
        $query .= "AND rp.journal_publisher LIKE ? ";
    }
    if (!empty($filter_status)) {
        $query .= "AND rp.status = ? ";
    }
    
    $query .= "ORDER BY rp.publication_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_year) && !empty($filter_journal) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("iiss", $_SESSION['user_id'], $filter_year, $filter_journal, $filter_status);
    } elseif (!empty($filter_year) && !empty($filter_journal)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("iis", $_SESSION['user_id'], $filter_year, $filter_journal);
    } elseif (!empty($filter_year) && !empty($filter_status)) {
        $stmt->bind_param("iis", $_SESSION['user_id'], $filter_year, $filter_status);
    } elseif (!empty($filter_journal) && !empty($filter_status)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("iss", $_SESSION['user_id'], $filter_journal, $filter_status);
    } elseif (!empty($filter_year)) {
        $stmt->bind_param("ii", $_SESSION['user_id'], $filter_year);
    } elseif (!empty($filter_journal)) {
        $filter_journal = "%$filter_journal%";
        $stmt->bind_param("is", $_SESSION['user_id'], $filter_journal);
    } elseif (!empty($filter_status)) {
        $stmt->bind_param("is", $_SESSION['user_id'], $filter_status);
    } else {
        $stmt->bind_param("i", $_SESSION['user_id']);
    }
    
    $stmt->execute();
    $publications = $stmt->get_result();
}

// Get conferences based on user role and filters
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    // Admins and HR can see all conferences
    $query = "SELECT c.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department
              FROM conferences c 
              LEFT JOIN employees e ON c.employee_id = e.employee_id
              WHERE 1=1 ";
    
    // Apply filters
    if (!empty($filter_year)) {
        $query .= "AND (YEAR(c.start_date) = ? OR YEAR(c.end_date) = ?) ";
    }
    if (!empty($filter_type) && $filter_type === 'conference') {
        // Already filtering for conferences
    }
    if (!empty($filter_employee)) {
        $query .= "AND c.employee_id = ? ";
    }
    if (!empty($filter_status)) {
        $query .= "AND c.status = ? ";
    }
    
    $query .= "ORDER BY c.start_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_year) && !empty($filter_employee) && !empty($filter_status)) {
        $stmt->bind_param("iiis", $filter_year, $filter_year, $filter_employee, $filter_status);
    } elseif (!empty($filter_year) && !empty($filter_employee)) {
        $stmt->bind_param("iii", $filter_year, $filter_year, $filter_employee);
    } elseif (!empty($filter_year) && !empty($filter_status)) {
        $stmt->bind_param("iis", $filter_year, $filter_year, $filter_status);
    } elseif (!empty($filter_employee) && !empty($filter_status)) {
        $stmt->bind_param("is", $filter_employee, $filter_status);
    } elseif (!empty($filter_year)) {
        $stmt->bind_param("ii", $filter_year, $filter_year);
    } elseif (!empty($filter_employee)) {
        $stmt->bind_param("i", $filter_employee);
    } elseif (!empty($filter_status)) {
        $stmt->bind_param("s", $filter_status);
    }
    
    $stmt->execute();
    $conferences = $stmt->get_result();
} else {
    // Regular employees can only see their own conferences
    $query = "SELECT c.*, 
              CONCAT(e.first_name, ' ', e.last_name) as employee_name,
              e.department,
              u.username as created_by_name
              FROM conferences c 
              LEFT JOIN employees e ON c.employee_id = e.id
              LEFT JOIN users u ON c.created_by = u.id
              WHERE c.employee_id = ? ";
    
    // Apply filters
    if (!empty($filter_year)) {
        $query .= "AND (YEAR(c.start_date) = ? OR YEAR(c.end_date) = ?) ";
    }
    if (!empty($filter_type) && $filter_type === 'conference') {
        // Already filtering for conferences
    }
    if (!empty($filter_status)) {
        $query .= "AND c.status = ? ";
    }
    
    $query .= "ORDER BY c.start_date DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters based on filters
    if (!empty($filter_year) && !empty($filter_status)) {
        $stmt->bind_param("iiis", $_SESSION['user_id'], $filter_year, $filter_year, $filter_status);
    } elseif (!empty($filter_year)) {
        $stmt->bind_param("iii", $_SESSION['user_id'], $filter_year, $filter_year);
    } elseif (!empty($filter_status)) {
        $stmt->bind_param("is", $_SESSION['user_id'], $filter_status);
    } else {
        $stmt->bind_param("i", $_SESSION['user_id']);
    }
    
    $stmt->execute();
    $conferences = $stmt->get_result();
}

// Get employees for dropdown (for admin/HR)
if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') {
    $query = "SELECT employee_id, CONCAT(first_name, ' ', last_name) as name, department FROM employees ORDER BY first_name";
    $employees = $db->query($query);
}

// Get unique years for filter
$query = "SELECT DISTINCT YEAR(publication_date) as year FROM research_publications 
          UNION 
          SELECT DISTINCT YEAR(start_date) as year FROM conferences 
          ORDER BY year DESC";
$years = $db->query($query);

// Get unique journals for filter
$query = "SELECT DISTINCT journal_name FROM research_publications WHERE journal_name IS NOT NULL AND journal_name != '' ORDER BY journal_name";
$journals = $db->query($query);

// Set page title and CSS
$pageTitle = "Research & Publications";
$pageCss = ['assets/css/research-publications.css'];

// Include header
include "../../includes/header.php"';
include '../../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Academic /</span> Research & Publications
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
                <form action="research_publications.php" method="get">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-select" id="year" name="year">
                                <option value="">All Years</option>
                                <?php while ($year = $years->fetch_assoc()): ?>
                                    <option value="<?php echo $year['year']; ?>" <?php echo $filter_year == $year['year'] ? 'selected' : ''; ?>>
                                        <?php echo $year['year']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="publication" <?php echo $filter_type === 'publication' ? 'selected' : ''; ?>>Publication</option>
                                <option value="conference" <?php echo $filter_type === 'conference' ? 'selected' : ''; ?>>Conference</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="journal" class="form-label">Journal/Publisher</label>
                            <select class="form-select" id="journal" name="journal">
                                <option value="">All Journals</option>
                                <?php while ($journal = $journals->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($journal['journal_name']); ?>">
                                        <?php echo htmlspecialchars($journal['journal_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                        <div class="col-md-2 mb-2">
                            <label for="employee" class="form-label">Employee</label>
                            <select class="form-select" id="employee" name="employee">
                                <option value="">All Employees</option>
                                <?php 
                                $employees->data_seek(0); // Reset the pointer
                                while ($employee = $employees->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $employee['employee_id']; ?>">
                                        <?php echo htmlspecialchars($employee['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-2 mb-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $filter_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="researchTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="publications-tab" data-bs-toggle="tab" data-bs-target="#publications" type="button" role="tab" aria-controls="publications" aria-selected="true">
                    <i class="bx bx-book me-1"></i> Publications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="conferences-tab" data-bs-toggle="tab" data-bs-target="#conferences" type="button" role="tab" aria-controls="conferences" aria-selected="false">
                    <i class="bx bx-calendar-event me-1"></i> Conferences
                </button>
            </li>
        </ul>

        <div class="tab-content" id="researchTabContent">
            <!-- Publications Tab -->
            <div class="tab-pane fade show active" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Research Publications</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_publication">
                            <i class="bx bx-plus me-1"></i> Add Publication
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                        <th>Employee</th>
                                        <?php endif; ?>
                                        <th>Title</th>
                                        <th>Journal/Publisher</th>
                                        <th>Publication Date</th>
                                        <th>Authors</th>
                                        <th>DOI</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php if ($publications && $publications->num_rows > 0): ?>
                                        <?php while ($publication = $publications->fetch_assoc()): ?>
                                        <tr>
                                            <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                <?php 
                                                                $name_parts = explode(' ', $publication['employee_name'] ?? 'N/A');
                                                                echo strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '')); 
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-sem ibold"><?php echo htmlspecialchars($publication['employee_name'] ?? 'N/A'); ?></span>
                                                        <small class="text-muted"><?php echo htmlspecialchars($publication['department'] ?? 'N/A'); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                            <td><strong><?php echo htmlspecialchars($publication['title'] ?? 'N/A'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($publication['journal_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('d M Y', strtotime($publication['publication_date'] ?? '1970-01-01')); ?></td>
                                            <td><?php echo htmlspecialchars($publication['authors'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($publication['doi'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php 
                                                $status = $publication['status'] ?? 'unknown';
                                                switch($status) {
                                                    case 'pending': $statusClass = 'warning'; break;
                                                    case 'approved': $statusClass = 'success'; break;
                                                    case 'rejected': $statusClass = 'danger'; break;
                                                    default: $statusClass = 'info';
                                                }
                                                ?>
                                                <span class="badge bg-label-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y H:i:s', strtotime($publication['created_at'] ?? '1970-01-01')); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item view-publication" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#view_publication" 
                                                           data-id="<?php echo $publication['id']; ?>"
                                                           data-employee="<?php echo htmlspecialchars($publication['employee_name']); ?>"
                                                           data-department="<?php echo htmlspecialchars($publication['department']); ?>"
                                                           data-title="<?php echo htmlspecialchars($publication['title']); ?>"
                                                           data-journal="<?php echo htmlspecialchars($publication['journal_publisher']); ?>"
                                                           data-date="<?php echo date('d M Y', strtotime($publication['publication_date'])); ?>"
                                                           data-authors="<?php echo htmlspecialchars($publication['authors']); ?>"
                                                           data-doi="<?php echo htmlspecialchars($publication['doi']); ?>"
                                                           data-abstract="<?php echo htmlspecialchars($publication['abstract']); ?>"
                                                           data-status="<?php echo ucfirst($publication['status']); ?>"
                                                           data-comments="<?php echo htmlspecialchars($publication['comments']); ?>"
                                                           data-file="<?php echo $publication['file_path']; ?>">
                                                            <i class="bx bx-show me-1"></i> View
                                                        </a>
                                                        
                                                        <?php if ((strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') && $publication['status'] === 'pending'): ?>
                                                        <a class="dropdown-item update-publication-status" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#update_publication_status" 
                                                           data-id="<?php echo $publication['id']; ?>"
                                                           data-employee="<?php echo htmlspecialchars($publication['employee_name']); ?>"
                                                           data-title="<?php echo htmlspecialchars($publication['title']); ?>"
                                                           data-journal="<?php echo htmlspecialchars($publication['journal_publisher']); ?>"
                                                           data-date="<?php echo date('d M Y', strtotime($publication['publication_date'])); ?>">
                                                            <i class="bx bx-check-circle me-1"></i> Update Status
                                                        </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($publication['employee_id'] == $_SESSION['user_id'] || strtolower($_SESSION['role']) === 'admin'): ?>
                                                        <a class="dropdown-item delete-publication" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#delete_publication" 
                                                           data-id="<?php echo $publication['id']; ?>"
                                                           data-title="<?php echo htmlspecialchars($publication['title']); ?>">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?php echo (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? '8' : '7'; ?>" class="text-center">No publications found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conferences Tab -->
            <div class="tab-pane fade" id="conferences" role="tabpanel" aria-labelledby="conferences-tab">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Conference Participations</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_conference">
                            <i class="bx bx-plus me-1"></i> Add Conference
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                        <th>Employee</th>
                                        <?php endif; ?>
                                        <th>Conference Name</th>
                                        <th>Location</th>
                                        <th>Dates</th>
                                        <th>Paper Title</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php if ($conferences && $conferences->num_rows > 0): ?>
                                        <?php while ($conference = $conferences->fetch_assoc()): ?>
                                        <tr>
                                            <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                <?php 
                                                                $name_parts = explode(' ', $conference['employee_name']);
                                                                echo strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '')); 
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold"><?php echo htmlspecialchars($conference['employee_name']); ?></span>
                                                        <small class="text-muted"><?php echo htmlspecialchars($conference['department']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                            <td><strong><?php echo htmlspecialchars($conference['conference_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($conference['location']); ?></td>
                                            <td>
                                                <?php 
                                                echo date('d M Y', strtotime($conference['start_date']));
                                                if ($conference['start_date'] != $conference['end_date']) {
                                                    echo ' - ' . date('d M Y', strtotime($conference['end_date']));
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($conference['paper_title']); ?></td>
                                            <td><?php echo htmlspecialchars($conference['role']); ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = '';
                                                switch($conference['status']) {
                                                    case 'pending': $statusClass = 'warning'; break;
                                                    case 'approved': $statusClass = 'success'; break;
                                                    case 'rejected': $statusClass = 'danger'; break;
                                                    default: $statusClass = 'info';
                                                }
                                                ?>
                                                <span class="badge bg-label-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($conference['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y H:i:s', strtotime($conference['created_at'])); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item view-conference" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#view_conference" 
                                                           data-id="<?php echo $conference['id']; ?>"
                                                           data-employee="<?php echo htmlspecialchars($conference['employee_name']); ?>"
                                                           data-department="<?php echo htmlspecialchars($conference['department']); ?>"
                                                           data-name="<?php echo htmlspecialchars($conference['conference_name']); ?>"
                                                           data-location="<?php echo htmlspecialchars($conference['location']); ?>"
                                                           data-start="<?php echo date('d M Y', strtotime($conference['start_date'])); ?>"
                                                           data-end="<?php echo date('d M Y', strtotime($conference['end_date'])); ?>"
                                                           data-paper="<?php echo htmlspecialchars($conference['paper_title']); ?>"
                                                           data-role="<?php echo htmlspecialchars($conference['role']); ?>"
                                                           data-description="<?php echo htmlspecialchars($conference['description']); ?>"
                                                           data-status="<?php echo ucfirst($conference['status']); ?>"
                                                           data-comments="<?php echo htmlspecialchars($conference['comments']); ?>"
                                                           data-file="<?php echo $conference['file_path']; ?>">
                                                            <i class="bx bx-show me-1"></i> View
                                                        </a>
                                                        
                                                        <?php if ((strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') && $conference['status'] === 'pending'): ?>
                                                        <a class="dropdown-item update-conference-status" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#update_conference_status" 
                                                           data-id="<?php echo $conference['id']; ?>"
                                                           data-employee="<?php echo htmlspecialchars($conference['employee_name']); ?>"
                                                           data-name="<?php echo htmlspecialchars($conference['conference_name']); ?>"
                                                           data-location="<?php echo htmlspecialchars($conference['location']); ?>"
                                                           data-dates="<?php 
                                                                echo date('d M Y', strtotime($conference['start_date']));
                                                                if ($conference['start_date'] != $conference['end_date']) {
                                                                    echo ' - ' . date('d M Y', strtotime($conference['end_date']));
                                                                }
                                                            ?>">
                                                            <i class="bx bx-check-circle me-1"></i> Update Status
                                                        </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($conference['employee_id'] == $_SESSION['user_id'] || strtolower($_SESSION['role']) === 'admin'): ?>
                                                        <a class="dropdown-item delete-conference" href="javascript:void(0);" 
                                                           data-bs-toggle="modal" data-bs-target="#delete_conference" 
                                                           data-id="<?php echo $conference['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($conference['conference_name']); ?>">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?php echo (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr') ? '8' : '7'; ?>" class="text-center">No conference participations found</td>
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

<!-- Add Publication Modal -->
<div class="modal fade" id="add_publication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="research_publications.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_publication">
                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php 
                            $employees->data_seek(0); // Reset the pointer
                            while ($employee = $employees->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>">
                                    <?php echo htmlspecialchars($employee['name']); ?> (<?php echo htmlspecialchars($employee['department']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="journal_publisher" class="form-label">Journal/Publisher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="journal_publisher" name="journal_publisher" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="publication_date" class="form-label">Publication Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="authors" class="form-label">Authors <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="authors" name="authors" required>
                            <small class="text-muted">Separate multiple authors with commas</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="doi" class="form-label">DOI</label>
                            <input type="text" class="form-control" id="doi" name="doi">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="abstract" class="form-label">Abstract <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="4" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="publication_file" class="form-label">Upload Publication (PDF)</label>
                            <input type="file" class="form-control" id="publication_file" name="publication_file" accept=".pdf,.doc,.docx">
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
<!-- / Add Publication Modal -->

<!-- View Publication Modal -->
<div class="modal fade" id="view_publication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publication Details</h5>
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
                        <label class="form-label">Title</label>
                        <p id="view_title" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Journal/Publisher</label>
                        <p id="view_journal" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Publication Date</label>
                        <p id="view_date" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Authors</label>
                        <p id="view_authors" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">DOI</label>
                        <p id="view_doi" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Abstract</label>
                        <p id="view_abstract" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <p id="view_status" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File</label>
                        <p id="view_file" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Comments</label>
                        <p id="view_comments" class="form-control-static"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- / View Publication Modal -->

<!-- Update Publication Status Modal -->
<div class="modal fade" id="update_publication_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Publication Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="research_publications.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_publication_status">
                    <input type="hidden" name="publication_id" id="status_publication_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <p id="status_employee" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publication</label>
                        <p id="status_publication" class="form-control-static"></p>
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
<!-- / Update Publication Status Modal -->

<!-- Delete Publication Modal -->
<div class="modal fade" id="delete_publication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this publication?</p>
                <p id="delete_publication_title" class="fw-bold"></p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="research_publications.php" method="post">
                    <input type="hidden" name="action" value="delete_publication">
                    <input type="hidden" name="publication_id" id="delete_publication_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Delete Publication Modal -->

<!-- Add Conference Modal -->
<div class="modal fade" id="add_conference" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Conference Participation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="research_publications.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_conference">
                    <?php if (strtolower($_SESSION['role']) === 'admin' || strtolower($_SESSION['role']) === 'hr'): ?>
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php 
                            $employees->data_seek(0); // Reset the pointer
                            while ($employee = $employees->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>">
                                    <?php echo htmlspecialchars($employee['name']); ?> (<?php echo htmlspecialchars($employee['department']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="conference_name" class="form-label">Conference Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="conference_name" name="conference_name" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required>
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
                            <label for="paper_title" class="form-label">Paper Title</label>
                            <input type="text" class="form-control" id="paper_title" name="paper_title">
                            <small class="text-muted">Leave blank if no paper was presented</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Speaker">Speaker</option>
                                <option value="Presenter">Presenter</option>
                                <option value="Panelist">Panelist</option>
                                <option value="Attendee">Attendee</option>
                                <option value="Organizer">Organizer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="conference_file" class="form-label">Upload Certificate/Presentation</label>
                            <input type="file" class="form-control" id="conference_file" name="conference_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
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
<!-- / Add Conference Modal -->

<!-- View Conference Modal -->
<div class="modal fade" id="view_conference" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Conference Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
              data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee</label>
                        <p id="view_conf_employee" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <p id="view_conf_department" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Conference Name</label>
                        <p id="view_conf_name" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <p id="view_conf_location" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dates</label>
                        <p id="view_conf_dates" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Paper Title</label>
                        <p id="view_conf_paper" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <p id="view_conf_role" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <p id="view_conf_description" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <p id="view_conf_status" class="form-control-static"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File</label>
                        <p id="view_conf_file" class="form-control-static"></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Comments</label>
                        <p id="view_conf_comments" class="form-control-static"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- / View Conference Modal -->

<!-- Update Conference Status Modal -->
<div class="modal fade" id="update_conference_status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Conference Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="research_publications.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_conference_status">
                    <input type="hidden" name="conference_id" id="status_conference_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <p id="status_conf_employee" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conference</label>
                        <p id="status_conf_details" class="form-control-static"></p>
                    </div>
                    <div class="mb-3">
                        <label for="conf_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="conf_status" name="status" required>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="conf_comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="conf_comments" name="comments" rows="3"></textarea>
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
<!-- / Update Conference Status Modal -->

<!-- Delete Conference Modal -->
<div class="modal fade" id="delete_conference" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Conference</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this conference participation?</p>
                <p id="delete_conference_name" class="fw-bold"></p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form action="research_publications.php" method="post">
                    <input type="hidden" name="action" value="delete_conference">
                    <input type="hidden" name="conference_id" id="delete_conference_id">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Delete Conference Modal -->

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
    // View publication details
    $('.view-publication').on('click', function() {
        var employee = $(this).data('employee');
        var department = $(this).data('department');
        var title = $(this).data('title');
        var journal = $(this).data('journal');
        var date = $(this).data('date');
        var authors = $(this).data('authors');
        var doi = $(this).data('doi');
        var abstract = $(this).data('abstract');
        var status = $(this).data('status');
        var comments = $(this).data('comments');
        var file = $(this).data('file');
        
        $('#view_employee').text(employee);
        $('#view_department').text(department);
        $('#view_title').text(title);
        $('#view_journal').text(journal);
        $('#view_date').text(date);
        $('#view_authors').text(authors);
        $('#view_doi').text(doi || 'N/A');
        $('#view_abstract').text(abstract);
        $('#view_status').text(status);
        $('#view_comments').text(comments || 'No comments');
        
        if (file) {
            $('#view_file').html('<a href="' + file + '" target="_blank" class="btn btn-sm btn-primary"><i class="bx bx-download me-1"></i> Download</a>');
        } else {
            $('#view_file').text('No file uploaded');
        }
    });
    
    // Update publication status
    $('.update-publication-status').on('click', function() {
        var id = $(this).data('id');
        var employee = $(this).data('employee');
        var title = $(this).data('title');
        var journal = $(this).data('journal');
        var date = $(this).data('date');
        
        $('#status_publication_id').val(id);
        $('#status_employee').text(employee);
        $('#status_publication').text(title + ' (' + journal + ', ' + date + ')');
    });
    
    // Delete publication
    $('.delete-publication').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        
        $('#delete_publication_id').val(id);
        $('#delete_publication_title').text(title);
    });
    
    // View conference details
    $('.view-conference').on('click', function() {
        var employee = $(this).data('employee');
        var department = $(this).data('department');
        var name = $(this).data('name');
        var location = $(this).data('location');
        var start = $(this).data('start');
        var end = $(this).data('end');
        var paper = $(this).data('paper');
        var role = $(this).data('role');
        var description = $(this).data('description');
        var status = $(this).data('status');
        var comments = $(this).data('comments');
        var file = $(this).data('file');
        
        $('#view_conf_employee').text(employee);
        $('#view_conf_department').text(department);
        $('#view_conf_name').text(name);
        $('#view_conf_location').text(location);
        
        if (start === end) {
            $('#view_conf_dates').text(start);
        } else {
            $('#view_conf_dates').text(start + ' - ' + end);
        }
        
        $('#view_conf_paper').text(paper || 'N/A');
        $('#view_conf_role').text(role);
        $('#view_conf_description').text(description || 'N/A');
        $('#view_conf_status').text(status);
        $('#view_conf_comments').text(comments || 'No comments');
        
        if (file) {
            $('#view_conf_file').html('<a href="' + file + '" target="_blank" class="btn btn-sm btn-primary"><i class="bx bx-download me-1"></i> Download</a>');
        } else {
            $('#view_conf_file').text('No file uploaded');
        }
    });
    
    // Update conference status
    $('.update-conference-status').on('click', function() {
        var id = $(this).data('id');
        var employee = $(this).data('employee');
        var name = $(this).data('name');
        var location = $(this).data('location');
        var dates = $(this).data('dates');
        
        $('#status_conference_id').val(id);
        $('#status_conf_employee').text(employee);
        $('#status_conf_details').text(name + ' (' + location + ', ' + dates + ')');
    });
    
    // Delete conference
    $('.delete-conference').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#delete_conference_id').val(id);
        $('#delete_conference_name').text(name);
    });
    
    // Validate date ranges
    $('#start_date, #end_date').on('change', function() {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($('#end_date').val());
        
        if (startDate > endDate) {
            alert('End date cannot be earlier than start date');
            $('#end_date').val($('#start_date').val());
        }
    });
});
</script>
</body>
</html>
