<?php
// Training & Development - Training Progress
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Training Progress';

// Get employee profile
$employee = getEmployeeByUserId($_SESSION['user_id']);
if (!$employee) {
    header("Location: ../../../index.php");
    exit();
}

// Get training records for the employee
$trainingQuery = "SELECT tr.*, t.training_name, t.description, t.duration, t.status as training_status
                  FROM training_records tr 
                  LEFT JOIN trainings t ON tr.training_id = t.training_id
                  WHERE tr.employee_id = ? 
                  ORDER BY tr.completion_date DESC";
$trainingStmt = $conn->prepare($trainingQuery);
$trainingStmt->bind_param("i", $employee['employee_id']);
$trainingStmt->execute();
$trainings = $trainingStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get available trainings
$availableQuery = "SELECT * FROM trainings WHERE status = 'Active' ORDER BY training_name";
$availableResult = mysqli_query($conn, $availableQuery);
$availableTrainings = mysqli_fetch_all($availableResult, MYSQLI_ASSOC);
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
                            <li class="menu-item">
                                <a href="requirements.php" class="menu-link">
                                    <div>Training Requirements</div>
                                </a>
                            </li>
                            <li class="menu-item active">
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
                        <div class="row">
                            <!-- My Training Progress -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">My Training Progress</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($trainings)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Training Name</th>
                                                            <th>Duration</th>
                                                            <th>Status</th>
                                                            <th>Completion Date</th>
                                                            <th>Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($trainings as $training): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $training['training_name']; ?></strong>
                                                                    <?php if ($training['description']): ?>
                                                                        <br><small class="text-muted"><?php echo htmlspecialchars($training['description']); ?></small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo $training['duration']; ?> hours</td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $training['status'] == 'Completed' ? 'success' : ($training['status'] == 'In Progress' ? 'warning' : 'secondary'); ?>">
                                                                        <?php echo $training['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php echo $training['completion_date'] ? formatDate($training['completion_date']) : 'N/A'; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($training['score']): ?>
                                                                        <span class="badge bg-<?php echo $training['score'] >= 80 ? 'success' : ($training['score'] >= 60 ? 'warning' : 'danger'); ?>">
                                                                            <?php echo $training['score']; ?>%
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
                                                <i class="bx bx-certification display-1 text-muted"></i>
                                                <h5 class="mt-3">No Training Records</h5>
                                                <p class="text-muted">You haven't completed any training yet.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Available Trainings -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Available Trainings</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($availableTrainings)): ?>
                                            <?php foreach ($availableTrainings as $available): ?>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?php echo $available['training_name']; ?></h6>
                                                        <p class="card-text small text-muted">
                                                            Duration: <?php echo $available['duration']; ?> hours<br>
                                                            <?php if ($available['description']): ?>
                                                                <?php echo htmlspecialchars(substr($available['description'], 0, 100)); ?>
                                                                <?php if (strlen($available['description']) > 100): ?>...<?php endif; ?>
                                                            <?php endif; ?>
                                                        </p>
                                                        <button class="btn btn-sm btn-outline-primary">
                                                            <i class="bx bx-plus"></i> Enroll
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No available trainings at the moment.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Training Statistics -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Training Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $completedCount = 0;
                                        $inProgressCount = 0;
                                        $totalHours = 0;
                                        
                                        foreach ($trainings as $training) {
                                            if ($training['status'] == 'Completed') {
                                                $completedCount++;
                                                $totalHours += $training['duration'];
                                            } elseif ($training['status'] == 'In Progress') {
                                                $inProgressCount++;
                                            }
                                        }
                                        ?>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="text-success"><?php echo $completedCount; ?></h4>
                                                <small class="text-muted">Completed</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-warning"><?php echo $inProgressCount; ?></h4>
                                                <small class="text-muted">In Progress</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <h5 class="text-primary"><?php echo $totalHours; ?></h5>
                                            <small class="text-muted">Total Training Hours</small>
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




