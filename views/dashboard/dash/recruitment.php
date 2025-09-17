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

// Handle job posting creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_job') {
        // Create new job posting
        $title = $_POST['title'];
        $department = $_POST['department'];
        $location = $_POST['location'];
        $positions = $_POST['positions'];
        $description = $_POST['description'];
        $requirements = $_POST['requirements'];
        $salary_range = $_POST['salary_range'];
        $status = $_POST['status'];
        $closing_date = $_POST['closing_date'];
        
        $query = "INSERT INTO job_postings (title, department, location, positions, description, requirements, salary_range, status, closing_date, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssssss", $title, $department, $location, $positions, $description, $requirements, $salary_range, $status, $closing_date);
        
        if ($stmt->execute()) {
            $message = "Job posting created successfully!";
            $messageType = "success";
        } else {
            $message = "Error creating job posting: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_job') {
        // Update existing job posting
        $job_id = $_POST['job_id'];
        $title = $_POST['title'];
        $department = $_POST['department'];
        $location = $_POST['location'];
        $positions = $_POST['positions'];
        $description = $_POST['description'];
        $requirements = $_POST['requirements'];
        $salary_range = $_POST['salary_range'];
        $status = $_POST['status'];
        $closing_date = $_POST['closing_date'];
        
        $query = "UPDATE job_postings SET title = ?, department = ?, location = ?, positions = ?, description = ?, 
                  requirements = ?, salary_range = ?, status = ?, closing_date = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssssssi", $title, $department, $location, $positions, $description, $requirements, $salary_range, $status, $closing_date, $job_id);
        
        if ($stmt->execute()) {
            $message = "Job posting updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating job posting: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'delete_job') {
        // Delete job posting
        $job_id = $_POST['job_id'];
        
        $query = "DELETE FROM job_postings WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $job_id);
        
        if ($stmt->execute()) {
            $message = "Job posting deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting job posting: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'update_candidate_status') {
        // Update candidate status
        $candidate_id = $_POST['candidate_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'];
        
        $query = "UPDATE candidates SET status = ?, notes = CONCAT(notes, '\n', NOW(), ' - Status changed to ', ?, ': ', ?), 
                  updated_at = NOW(), updated_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssii", $status, $status, $notes, $_SESSION['user_id'], $candidate_id);
        
        if ($stmt->execute()) {
            $message = "Candidate status updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating candidate status: " . $db->error;
            $messageType = "danger";
        }
    } elseif ($_POST['action'] === 'schedule_interview') {
        // Schedule interview
        $candidate_id = $_POST['candidate_id'];
        $interview_date = $_POST['interview_date'];
        $interview_time = $_POST['interview_time'];
        $interview_type = $_POST['interview_type'];
        $interviewers = $_POST['interviewers'];
        $location = $_POST['location'];
        $notes = $_POST['notes'];
        
        $query = "INSERT INTO interviews (candidate_id, interview_date, interview_time, interview_type, interviewers, location, notes, created_by, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("issssssi", $candidate_id, $interview_date, $interview_time, $interview_type, $interviewers, $location, $notes, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            // Update candidate status
            $query = "UPDATE candidates SET status = 'interview_scheduled', updated_at = NOW(), updated_by = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $_SESSION['user_id'], $candidate_id);
            $stmt->execute();
            
            $message = "Interview scheduled successfully!";
            $messageType = "success";
        } else {
            $message = "Error scheduling interview: " . $db->error;
            $messageType = "danger";
        }
    }
}

// Get all job postings
$query = "SELECT jp.job_posting_id as posting_id, jp.title, jp.department, jp.location, jp.positions, jp.status, jp.closing_date, 
                 jp.description, jp.requirements, jp.salary_range, 
                 (SELECT COUNT(*) FROM applicants WHERE job_posting_id = jp.job_posting_id) as applicants_count 
          FROM job_postings jp 
          ORDER BY jp.created_at DESC";
$job_postings = $db->query($query);

// Get all candidates
$query = "SELECT a.*, jp.title as job_title, jp.department 
          FROM applicants a 
          LEFT JOIN job_postings jp ON a.job_posting_id = jp.job_posting_id 
          ORDER BY a.created_at DESC";
$candidates = $db->query($query);

// Get upcoming interviews
$query = "SELECT i.*, c.first_name, c.last_name, jp.title as job_title 
          FROM interviews i 
          LEFT JOIN candidates c ON i.candidate_id = c.id 
          LEFT JOIN job_postings jp ON c.job_id = jp.posting_id 
          WHERE i.interview_date >= CURDATE() 
          ORDER BY i.interview_date ASC, i.interview_time ASC";
$interviews = $db->query($query);

// Set page title and CSS
$pageTitle = "Recruitment";
$pageCss = ['assets/css/recruitment.css'];

// Include header
include "../../includes/header.php"';
include '../../../includes/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Recruitment
        </h4>

        <!-- Alert for messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Recruitment Tabs -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recruitment Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_job">
                    <i class="bx bx-plus me-1"></i> Add Job Posting
                </button>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#job_postings" aria-controls="job_postings" aria-selected="true">
                            <i class="bx bx-briefcase me-1"></i> Job Postings
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#candidates" aria-controls="candidates" aria-selected="false">
                            <i class="bx bx-user me-1"></i> Candidates
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#interviews" aria-controls="interviews" aria-selected="false">
                            <i class="bx bx-calendar me-1"></i> Interviews
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- Job Postings Tab -->
                    <div class="tab-pane fade show active" id="job_postings" role="tabpanel">
                        <div class="table-responsive text-nowrap mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                        <th>Positions</th>
                                        <th>Status</th>
                                        <th>Closing Date</th>
                                        <th>Applicants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php while ($job = $job_postings->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($job['title'] ?? 'N/A'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($job['department'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($job['location'] ?? 'N/A'); ?></td>
                                        <td><?php echo $job['positions'] ?? 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-label-<?php echo ($job['status'] ?? 'closed') === 'open' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($job['status'] ?? 'closed'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo isset($job['closing_date']) ? date('d M Y', strtotime($job['closing_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-label-info"><?php echo $job['applicants_count'] ?? 0; ?></span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-job" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#edit_job"
                                                       data-id="<?php echo $job['posting_id']; ?>"
                                                       data-title="<?php echo htmlspecialchars($job['title']); ?>"
                                                       data-department="<?php echo htmlspecialchars($job['department']); ?>"
                                                       data-location="<?php echo htmlspecialchars($job['location']); ?>"
                                                       data-positions="<?php echo $job['positions']; ?>"
                                                       data-description="<?php echo htmlspecialchars($job['description']); ?>"
                                                       data-requirements="<?php echo htmlspecialchars($job['requirements']); ?>"
                                                       data-salary="<?php echo htmlspecialchars($job['salary_range']); ?>"
                                                       data-status="<?php echo $job['status']; ?>"
                                                       data-closing="<?php echo $job['closing_date']; ?>">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item job-status-change" href="javascript:void(0);" 
                                                       data-job-id="<?php echo $job['id']; ?>" 
                                                       data-status="<?php echo $job['status'] === 'open' ? 'closed' : 'open'; ?>">
                                                        <i class="bx bx-<?php echo $job['status'] === 'open' ? 'x' : 'check'; ?> me-1"></i> 
                                                        <?php echo $job['status'] === 'open' ? 'Close' : 'Open'; ?>
                                                    </a>
                                                    <a class="dropdown-item delete-job" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#delete_job" 
                                                       data-id="<?php echo $job['id']; ?>">
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
                    
                    <!-- Candidates Tab -->
                    <div class="tab-pane fade" id="candidates" role="tabpanel">
                        <div class="table-responsive text-nowrap mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Job Position</th>
                                        <th>Department</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="avatar-wrapper">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            <?php echo strtoupper(substr($candidate['first_name'], 0, 1) . substr($candidate['last_name'], 0, 1)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold"><?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($candidate['job_title']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['department']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['email']); ?></td>
                                        <td><?php echo htmlspecialchars($candidate['phone']); ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = '';
                                            switch($candidate['status']) {
                                                case 'new': $statusClass = 'info'; break;
                                                case 'shortlisted': $statusClass = 'success'; break;
                                                case 'interview_scheduled': $statusClass = 'primary'; break;
                                                case 'rejected': $statusClass = 'danger'; break;
                                                case 'hired': $statusClass = 'success'; break;
                                                default: $statusClass = 'secondary';
                                            }
                                            ?>
                                            <span class="badge bg-label-<?php echo $statusClass; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $candidate['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($candidate['created_at'])); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item view-candidate" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#view_candidate" 
                                                       data-id="<?php echo $candidate['id']; ?>">
                                                        <i class="bx bx-show me-1"></i> View
                                                    </a>
                                                    <a class="dropdown-item candidate-status-change" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#update_candidate_status" 
                                                       data-id="<?php echo $candidate['id']; ?>" 
                                                       data-status="shortlisted">
                                                        <i class="bx bx-check-circle me-1"></i> Shortlist
                                                    </a>
                                                    <a class="dropdown-item schedule-interview" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#schedule_interview" 
                                                       data-id="<?php echo $candidate['id']; ?>">
                                                        <i class="bx bx-calendar me-1"></i> Schedule Interview
                                                    </a>
                                                    <a class="dropdown-item download-resume" href="<?php echo $candidate['resume_path']; ?>" download>
                                                        <i class="bx bx-download me-1"></i> Download Resume
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
                    
                    <!-- Interviews Tab -->
                    <div class="tab-pane fade" id="interviews" role="tabpanel">
                        <div class="table-responsive text-nowrap mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Candidate Name</th>
                                        <th>Job Position</th>
                                        <th>Interview Date</th>
                                        <th>Interview Time</th>
                                        <th>Type</th>
                                        <th>Interviewers</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php while ($interview = $interviews->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="avatar-wrapper">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            <?php echo strtoupper(substr($interview['first_name'], 0, 1) . substr($interview['last_name'], 0, 1)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold"><?php echo htmlspecialchars($interview['first_name'] . ' ' . $interview['last_name']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($interview['job_title']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($interview['interview_date'])); ?></td>
                                        <td><?php echo $interview['interview_time']; ?></td>
                                        <td><?php echo ucfirst($interview['interview_type']); ?></td>
                                        <td><?php echo htmlspecialchars($interview['interviewers']); ?></td>
                                        <td><?php echo htmlspecialchars($interview['location']); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-interview" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#edit_interview" 
                                                       data-id="<?php echo $interview['id']; ?>">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item add-feedback" href="javascript:void(0);" 
                                                       data-bs-toggle="modal" data-bs-target="#add_interview_feedback" 
                                                       data-id="<?php echo $interview['id']; ?>">
                                                        <i class="bx bx-comment me-1"></i> Add Feedback
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

<!-- Add Job Modal -->
<div class="modal fade" id="add_job" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Job Posting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="recruitment.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_job">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="department" name="department" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="positions" class="form-label">No. of Positions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="positions" name="positions" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="closing_date" class="form-label">Closing Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="closing_date" name="closing_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary_range" class="form-label">Salary Range</label>
                            <input type="text" class="form-control" id="salary_range" name="salary_range">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="requirements" class="form-label">Requirements <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4" required></textarea>
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
<!-- / Add Job Modal -->

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
    // Edit job
    $('.edit-job').on('click', function() {
        var jobId = $(this).data('id'); // Use posting_id here
        var title = $(this).data('title');
        var department = $(this).data('department');
        var location = $(this).data('location');
        var positions = $(this).data('positions');
        var description = $(this).data('description');
        var requirements = $(this).data('requirements');
        var salary = $(this).data('salary');
        var status = $(this).data('status');
        var closing = $(this).data('closing');
        
        $('#edit_job_id').val(jobId);
        $('#edit_title').val(title);
        $('#edit_department').val(department);
        $('#edit_location').val(location);
        $('#edit_positions').val(positions);
        $('#edit_description').val(description);
        $('#edit_requirements').val(requirements);
        $('#edit_salary_range').val(salary);
        $('#edit_status').val(status);
        $('#edit_closing_date').val(closing);
    });
    
    // Job status change
    $('.job-status-change').on('click', function(e) {
        e.preventDefault();
        var jobId = $(this).data('job-id');
        var status = $(this).data('status');
        
        $.ajax({
            url: 'ajax/update_job_status.php',
            type: 'POST',
            data: {
                job_id: jobId,
                status: status
            },
            success: function(response) {
                location.reload();
            }
        });
    });
    
    // Candidate status change
    $('.candidate-status-change').on('click', function() {
        var candidateId = $(this).data('id');
        var status = $(this).data('status');
        
        $('#candidate_id').val(candidateId);
        $('#candidate_status').val(status);
    });
    
    // Schedule interview
    $('.schedule-interview').on('click', function() {
        var candidateId = $(this).data('id');
        $('#interview_candidate_id').val(candidateId);
    });
});
</script>
</body>
</html>
