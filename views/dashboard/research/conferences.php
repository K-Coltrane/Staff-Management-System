<?php
// Research & Publications - Conferences
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Conferences';

// Get conferences
$conferencesQuery = "SELECT c.*, ep.first_name, ep.last_name 
                     FROM conference_participations c 
                     LEFT JOIN employee_profiles ep ON c.employee_id = ep.employee_id
                     ORDER BY c.conference_date DESC";
$conferencesResult = mysqli_query($conn, $conferencesQuery);
$conferences = mysqli_fetch_all($conferencesResult, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && canManageEmployees()) {
    $employeeId = sanitize($_POST['employee_id']);
    $conferenceName = sanitize($_POST['conference_name']);
    $location = sanitize($_POST['location']);
    $conferenceDate = sanitize($_POST['conference_date']);
    $presentationTitle = sanitize($_POST['presentation_title']);
    $participationType = sanitize($_POST['participation_type']);
    $notes = sanitize($_POST['notes']);
    
    $insertQuery = "INSERT INTO conference_participations (employee_id, conference_name, location, conference_date, presentation_title, participation_type, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("issssss", $employeeId, $conferenceName, $location, $conferenceDate, $presentationTitle, $participationType, $notes);
    
    if ($insertStmt->execute()) {
        setMessage("Conference participation added successfully!", "success");
        header("Location: conferences.php");
        exit();
    } else {
        setMessage("Error adding conference participation: " . $conn->error, "danger");
    }
}

// Get employees for dropdown
$employeesQuery = "SELECT employee_id, first_name, last_name FROM employee_profiles ORDER BY first_name, last_name";
$employeesResult = mysqli_query($conn, $employeesQuery);
$employees = mysqli_fetch_all($employeesResult, MYSQLI_ASSOC);
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
                            <i class="menu-icon icon-base bx bx-book"></i>
                            <div>Research & Publications</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="publications.php" class="menu-link">
                                    <div>Publications</div>
                                </a>
                            </li>
                            <li class="menu-item active">
                                <a href="conferences.php" class="menu-link">
                                    <div>Conferences</div>
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
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Conference Participations</h5>
                                        <?php if (canManageEmployees()): ?>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addConferenceModal">
                                            <i class="bx bx-plus"></i> Add Conference
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <?php if (!empty($conferences)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Conference Name</th>
                                                            <th>Location</th>
                                                            <th>Date</th>
                                                            <th>Presentation Title</th>
                                                            <th>Type</th>
                                                            <th>Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($conferences as $conference): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $conference['first_name'] . ' ' . $conference['last_name']; ?></strong>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($conference['conference_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($conference['location']); ?></td>
                                                                <td><?php echo formatDate($conference['conference_date']); ?></td>
                                                                <td>
                                                                    <?php if ($conference['presentation_title']): ?>
                                                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($conference['presentation_title']); ?>">
                                                                            <?php echo htmlspecialchars($conference['presentation_title']); ?>
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">N/A</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $conference['participation_type'] == 'Speaker' ? 'primary' : ($conference['participation_type'] == 'Attendee' ? 'success' : 'info'); ?>">
                                                                        <?php echo $conference['participation_type']; ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php if ($conference['notes']): ?>
                                                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?php echo htmlspecialchars($conference['notes']); ?>">
                                                                            <?php echo htmlspecialchars($conference['notes']); ?>
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">N/A</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-calendar-event display-1 text-muted"></i>
                                                <h5 class="mt-3">No Conference Participations</h5>
                                                <p class="text-muted">No conference participations have been recorded yet.</p>
                                                <?php if (canManageEmployees()): ?>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addConferenceModal">
                                                    <i class="bx bx-plus"></i> Add First Conference
                                                </button>
                                                <?php endif; ?>
                                            </div>
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

    <!-- Add Conference Modal -->
    <?php if (canManageEmployees()): ?>
    <div class="modal fade" id="addConferenceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Conference Participation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee *</label>
                            <select class="form-select" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>">
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="conference_name" class="form-label">Conference Name *</label>
                            <input type="text" class="form-control" id="conference_name" name="conference_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="conference_date" class="form-label">Conference Date *</label>
                            <input type="date" class="form-control" id="conference_date" name="conference_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="presentation_title" class="form-label">Presentation Title</label>
                            <input type="text" class="form-control" id="presentation_title" name="presentation_title">
                        </div>
                        <div class="mb-3">
                            <label for="participation_type" class="form-label">Participation Type *</label>
                            <select class="form-select" id="participation_type" name="participation_type" required>
                                <option value="">Select Type</option>
                                <option value="Speaker">Speaker</option>
                                <option value="Presenter">Presenter</option>
                                <option value="Attendee">Attendee</option>
                                <option value="Organizer">Organizer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Conference</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




