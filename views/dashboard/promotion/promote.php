<?php
// Staff Promotion - Promote Staff
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in and has permission
if (!isLoggedIn() || (!isAdmin() && $_SESSION['role'] != 'manager')) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Promote Staff';
$error = '';
$success = '';

// Handle promotion form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $old_position_id = $_POST['old_position_id'];
    $new_position_id = $_POST['new_position_id'];
    $old_salary = $_POST['old_salary'];
    $new_salary = $_POST['new_salary'];
    $promotion_date = $_POST['promotion_date'];
    $reason = sanitize($_POST['reason']);
    
    if (empty($employee_id) || empty($new_position_id) || empty($promotion_date)) {
        $error = "Please fill in all required fields.";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert promotion record
            $promotionQuery = "INSERT INTO staff_promotions (employee_id, old_position_id, new_position_id, old_salary, new_salary, promotion_date, reason, approved_by) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $promotionStmt = $conn->prepare($promotionQuery);
            $promotionStmt->bind_param("iiiiddsi", $employee_id, $old_position_id, $new_position_id, $old_salary, $new_salary, $promotion_date, $reason, $_SESSION['user_id']);
            $promotionStmt->execute();
            
            // Update employee profile
            $updateQuery = "UPDATE employee_profiles SET position_id = ?, salary = ? WHERE employee_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("idi", $new_position_id, $new_salary, $employee_id);
            $updateStmt->execute();
            
            // Commit transaction
            mysqli_commit($conn);
            $success = "Staff promotion completed successfully!";
            
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error = "Error processing promotion: " . $e->getMessage();
        }
    }
}

// Get employees for dropdown
$employees = [];
$empQuery = "SELECT ep.*, p.name as position_name, d.department_name 
             FROM employee_profiles ep 
             LEFT JOIN positions p ON ep.position_id = p.position_id 
             LEFT JOIN departments d ON ep.department_id = d.department_id 
             WHERE ep.employment_status = 'Active' 
             ORDER BY ep.first_name, ep.last_name";
$empResult = mysqli_query($conn, $empQuery);
if ($empResult) {
    while ($row = mysqli_fetch_assoc($empResult)) {
        $employees[] = $row;
    }
}

// Get positions for dropdown
$positions = [];
$posQuery = "SELECT p.*, d.department_name 
             FROM positions p 
             LEFT JOIN departments d ON p.department_id = d.department_id 
             ORDER BY p.name";
$posResult = mysqli_query($conn, $posQuery);
if ($posResult) {
    while ($row = mysqli_fetch_assoc($posResult)) {
        $positions[] = $row;
    }
}

// Get recent promotions
$recentQuery = "SELECT sp.*, ep.first_name, ep.last_name, ep.employee_number, 
                       old_pos.name as old_position, new_pos.name as new_position,
                       u.username as approved_by_name
                FROM staff_promotions sp 
                LEFT JOIN employee_profiles ep ON sp.employee_id = ep.employee_id 
                LEFT JOIN positions old_pos ON sp.old_position_id = old_pos.position_id 
                LEFT JOIN positions new_pos ON sp.new_position_id = new_pos.position_id 
                LEFT JOIN users u ON sp.approved_by = u.id 
                ORDER BY sp.promotion_date DESC 
                LIMIT 10";
$recentResult = mysqli_query($conn, $recentQuery);
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
                            <i class="menu-icon icon-base bx bx-trending-up"></i>
                            <div>Staff Promotion</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="promote.php" class="menu-link">
                                    <div>Promote Staff</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="history.php" class="menu-link">
                                    <div>Promotion History</div>
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
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Promote Staff Member</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="employee_id" class="form-label">Select Employee *</label>
                                                        <select class="form-select" id="employee_id" name="employee_id" required onchange="loadEmployeeDetails()">
                                                            <option value="">Select Employee</option>
                                                            <?php foreach ($employees as $emp): ?>
                                                                <option value="<?php echo $emp['employee_id']; ?>" 
                                                                        data-position="<?php echo $emp['position_id']; ?>"
                                                                        data-salary="<?php echo $emp['salary']; ?>"
                                                                        data-name="<?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>">
                                                                    <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_number'] . ')'); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="promotion_date" class="form-label">Promotion Date *</label>
                                                        <input type="date" class="form-control" id="promotion_date" name="promotion_date" required value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="old_position_id" class="form-label">Current Position</label>
                                                        <select class="form-select" id="old_position_id" name="old_position_id" readonly>
                                                            <option value="">Select Employee First</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="new_position_id" class="form-label">New Position *</label>
                                                        <select class="form-select" id="new_position_id" name="new_position_id" required>
                                                            <option value="">Select New Position</option>
                                                            <?php foreach ($positions as $pos): ?>
                                                                <option value="<?php echo $pos['position_id']; ?>" data-salary-min="<?php echo $pos['salary_min']; ?>" data-salary-max="<?php echo $pos['salary_max']; ?>">
                                                                    <?php echo htmlspecialchars($pos['name'] . ' (' . $pos['department_name'] . ')'); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="old_salary" class="form-label">Current Salary</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" class="form-control" id="old_salary" name="old_salary" step="0.01" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="new_salary" class="form-label">New Salary *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" class="form-control" id="new_salary" name="new_salary" step="0.01" required>
                                                        </div>
                                                        <div class="form-text" id="salary-range"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="reason" class="form-label">Promotion Reason</label>
                                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please provide a reason for this promotion"></textarea>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Process Promotion</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Recent Promotions Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Recent Promotions</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($recentResult && mysqli_num_rows($recentResult) > 0): ?>
                                            <?php while ($promotion = mysqli_fetch_assoc($recentResult)): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($promotion['first_name'] . ' ' . $promotion['last_name']); ?></h6>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($promotion['old_position']); ?> → 
                                                            <?php echo htmlspecialchars($promotion['new_position']); ?>
                                                        </small>
                                                        <br>
                                                        <small class="text-success">
                                                            $<?php echo number_format($promotion['old_salary']); ?> → 
                                                            $<?php echo number_format($promotion['new_salary']); ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted"><?php echo date('M d, Y', strtotime($promotion['promotion_date'])); ?></small>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No recent promotions</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadEmployeeDetails() {
            const select = document.getElementById('employee_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const positionId = selectedOption.getAttribute('data-position');
                const salary = selectedOption.getAttribute('data-salary');
                const name = selectedOption.getAttribute('data-name');
                
                // Update old position and salary
                document.getElementById('old_position_id').value = positionId;
                document.getElementById('old_salary').value = salary;
            } else {
                document.getElementById('old_position_id').value = '';
                document.getElementById('old_salary').value = '';
            }
        }
        
        // Update salary range when new position is selected
        document.getElementById('new_position_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const salaryMin = selectedOption.getAttribute('data-salary-min');
            const salaryMax = selectedOption.getAttribute('data-salary-max');
            
            if (salaryMin && salaryMax) {
                document.getElementById('salary-range').textContent = `Salary range: $${parseInt(salaryMin).toLocaleString()} - $${parseInt(salaryMax).toLocaleString()}`;
            } else {
                document.getElementById('salary-range').textContent = '';
            }
        });
    </script>
</body>
</html>




