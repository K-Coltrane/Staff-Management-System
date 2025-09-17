<?php
// Training Management - Training Requirements
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Training Requirements';
$error = '';
$success = '';

// Handle form submission for new training requirement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $department_id = $_POST['department_id'];
    $position_id = $_POST['position_id'];
    $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
    $validity_period = $_POST['validity_period'];
    
    if (empty($title) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {
        $query = "INSERT INTO training_requirements (title, description, department_id, position_id, is_mandatory, validity_period) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiiii", $title, $description, $department_id, $position_id, $is_mandatory, $validity_period);
        
        if ($stmt->execute()) {
            $success = "Training requirement created successfully!";
        } else {
            $error = "Error creating training requirement: " . $stmt->error;
        }
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

// Get all training requirements
$requirementsQuery = "SELECT tr.*, d.department_name, p.name as position_name 
                      FROM training_requirements tr 
                      LEFT JOIN departments d ON tr.department_id = d.department_id 
                      LEFT JOIN positions p ON tr.position_id = p.position_id 
                      ORDER BY tr.created_at DESC";
$requirementsResult = mysqli_query($conn, $requirementsQuery);

// Get training records for current user
$userTrainingQuery = "SELECT tr.*, tr_req.title as requirement_title, tr_req.is_mandatory 
                      FROM training_records tr 
                      LEFT JOIN training_requirements tr_req ON tr.requirement_id = tr_req.requirement_id 
                      LEFT JOIN employee_profiles ep ON tr.employee_id = ep.employee_id 
                      WHERE ep.user_id = ? 
                      ORDER BY tr.created_at DESC";
$userTrainingStmt = $conn->prepare($userTrainingQuery);
$userTrainingStmt->bind_param("i", $_SESSION['user_id']);
$userTrainingStmt->execute();
$userTraining = $userTrainingStmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-certification"></i>
                            <div>Training & Development</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="requirements.php" class="menu-link">
                                    <div>Training Requirements</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="progress.php" class="menu-link">
                                    <div>Training Progress</div>
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
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Training Requirements</h5>
                                        <?php if (isAdmin()): ?>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRequirementModal">
                                                <i class="bx bx-plus"></i> Add Requirement
                                            </button>
                                        <?php endif; ?>
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
                                                        <th>Validity</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($requirementsResult && mysqli_num_rows($requirementsResult) > 0): ?>
                                                        <?php while ($requirement = mysqli_fetch_assoc($requirementsResult)): ?>
                                                            <tr>
                                                                <td>
                                                                    <h6 class="mb-0"><?php echo htmlspecialchars($requirement['title']); ?></h6>
                                                                    <small class="text-muted"><?php echo substr($requirement['description'], 0, 100); ?>...</small>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($requirement['department_name'] ?? 'All'); ?></td>
                                                                <td><?php echo htmlspecialchars($requirement['position_name'] ?? 'All'); ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $requirement['is_mandatory'] ? 'danger' : 'info'; ?>">
                                                                        <?php echo $requirement['is_mandatory'] ? 'Mandatory' : 'Optional'; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo $requirement['validity_period'] ? $requirement['validity_period'] . ' months' : 'N/A'; ?></td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="view-requirement.php?id=<?php echo $requirement['requirement_id']; ?>">View</a></li>
                                                                            <?php if (isAdmin()): ?>
                                                                                <li><a class="dropdown-item" href="edit-requirement.php?id=<?php echo $requirement['requirement_id']; ?>">Edit</a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="delete-requirement.php?id=<?php echo $requirement['requirement_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No training requirements found</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- My Training Progress Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">My Training Progress</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($userTraining)): ?>
                                            <?php foreach ($userTraining as $training): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($training['training_name']); ?></h6>
                                                        <?php if ($training['requirement_title']): ?>
                                                            <small class="text-muted"><?php echo htmlspecialchars($training['requirement_title']); ?></small>
                                                        <?php endif; ?>
                                                        <?php if ($training['is_mandatory']): ?>
                                                            <br><small class="text-danger">Mandatory</small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-<?php echo $training['status'] == 'Completed' ? 'success' : ($training['status'] == 'In Progress' ? 'warning' : 'secondary'); ?>">
                                                            <?php echo $training['status']; ?>
                                                        </span>
                                                        <?php if ($training['end_date']): ?>
                                                            <br><small class="text-muted"><?php echo date('M d, Y', strtotime($training['end_date'])); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No training records found</p>
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

    <!-- Create Requirement Modal -->
    <?php if (isAdmin()): ?>
    <div class="modal fade" id="createRequirementModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Training Requirement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">All Departments</option>
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
                                        <option value="">All Positions</option>
                                        <?php foreach ($positions as $pos): ?>
                                            <option value="<?php echo $pos['position_id']; ?>">
                                                <?php echo htmlspecialchars($pos['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory">
                                        <label class="form-check-label" for="is_mandatory">
                                            Mandatory Training
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validity_period" class="form-label">Validity Period (months)</label>
                                    <input type="number" class="form-control" id="validity_period" name="validity_period" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Requirement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




