<?php
// Recruitment - Job Postings Management
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Job Postings';
$error = '';
$success = '';

// Handle form submission for new job posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $requirements = sanitize($_POST['requirements']);
    $department_id = $_POST['department_id'];
    $position_id = $_POST['position_id'];
    $salary_range = sanitize($_POST['salary_range']);
    $employment_type = $_POST['employment_type'];
    $closing_date = $_POST['closing_date'];
    
    if (empty($title) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {
        $query = "INSERT INTO job_postings (title, description, requirements, department_id, position_id, salary_range, employment_type, status, posted_by, closing_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'Open', ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssiissss", $title, $description, $requirements, $department_id, $position_id, $salary_range, $employment_type, $_SESSION['user_id'], $closing_date);
        
        if ($stmt->execute()) {
            $success = "Job posting created successfully!";
        } else {
            $error = "Error creating job posting: " . $stmt->error;
        }
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE job_postings SET status = ? WHERE job_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $job_id);
    
    if ($stmt->execute()) {
        $success = "Job posting status updated successfully!";
    } else {
        $error = "Error updating job posting status.";
    }
}

// Get departments and positions for dropdowns
$departments = [];
$positions = [];

$deptQuery = "SELECT * FROM departments ORDER BY department_name";
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

// Get all job postings
$jobsQuery = "SELECT j.*, d.department_name, p.name as position_name, u.username as posted_by_name 
              FROM job_postings j 
              LEFT JOIN departments d ON j.department_id = d.department_id 
              LEFT JOIN positions p ON j.position_id = p.position_id 
              LEFT JOIN users u ON j.posted_by = u.id 
              ORDER BY j.posted_date DESC";
$jobsResult = mysqli_query($conn, $jobsQuery);
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
                            <i class="menu-icon icon-base bx bx-briefcase"></i>
                            <div>Recruitment</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="job-postings.php" class="menu-link">
                                    <div>Job Postings</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="applicants.php" class="menu-link">
                                    <div>Applicants</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="interviews.php" class="menu-link">
                                    <div>Interviews</div>
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
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Job Postings</h5>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJobModal">
                                            <i class="bx bx-plus"></i> Create Job Posting
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Department</th>
                                                        <th>Position</th>
                                                        <th>Type</th>
                                                        <th>Status</th>
                                                        <th>Posted By</th>
                                                        <th>Closing Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($jobsResult && mysqli_num_rows($jobsResult) > 0): ?>
                                                        <?php while ($job = mysqli_fetch_assoc($jobsResult)): ?>
                                                            <tr>
                                                                <td>
                                                                    <h6 class="mb-0"><?php echo htmlspecialchars($job['title']); ?></h6>
                                                                    <small class="text-muted"><?php echo substr($job['description'], 0, 100); ?>...</small>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($job['department_name'] ?? 'N/A'); ?></td>
                                                                <td><?php echo htmlspecialchars($job['position_name'] ?? 'N/A'); ?></td>
                                                                <td><?php echo $job['employment_type']; ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $job['status'] == 'Open' ? 'success' : ($job['status'] == 'Closed' ? 'danger' : 'warning'); ?>">
                                                                        <?php echo $job['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($job['posted_by_name']); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($job['closing_date'])); ?></td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="view-job.php?id=<?php echo $job['job_id']; ?>">View</a></li>
                                                                            <li><a class="dropdown-item" href="edit-job.php?id=<?php echo $job['job_id']; ?>">Edit</a></li>
                                                                            <li><a class="dropdown-item" href="applicants.php?job_id=<?php echo $job['job_id']; ?>">View Applicants</a></li>
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li>
                                                                                <form method="POST" style="display: inline;">
                                                                                    <input type="hidden" name="action" value="update_status">
                                                                                    <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                                                                    <input type="hidden" name="status" value="<?php echo $job['status'] == 'Open' ? 'Closed' : 'Open'; ?>">
                                                                                    <button type="submit" class="dropdown-item">
                                                                                        <?php echo $job['status'] == 'Open' ? 'Close' : 'Reopen'; ?>
                                                                                    </button>
                                                                                </form>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center">No job postings found</td>
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

    <!-- Create Job Modal -->
    <div class="modal fade" id="createJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Job Posting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Job Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employment_type" class="form-label">Employment Type *</label>
                                    <select class="form-select" id="employment_type" name="employment_type" required>
                                        <option value="">Select Type</option>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['department_id']; ?>">
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position_id" class="form-label">Position</label>
                                    <select class="form-select" id="position_id" name="position_id">
                                        <option value="">Select Position</option>
                                        <?php foreach ($positions as $pos): ?>
                                            <option value="<?php echo $pos['position_id']; ?>">
                                                <?php echo htmlspecialchars($pos['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salary_range" class="form-label">Salary Range</label>
                                    <input type="text" class="form-control" id="salary_range" name="salary_range" placeholder="e.g., $50,000 - $70,000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="closing_date" class="form-label">Closing Date</label>
                                    <input type="date" class="form-control" id="closing_date" name="closing_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Job Posting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




