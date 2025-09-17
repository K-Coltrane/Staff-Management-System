<?php
// Document Management - Contracts
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Document Management - Contracts';
$error = '';
$success = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document_file'])) {
    $employee_id = $_POST['employee_id'];
    $document_type = 'Contract';
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $expiry_date = $_POST['expiry_date'];
    
    // File upload handling
    $uploadDir = '../../../assets/uploads/documents/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($_FILES['document_file']['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['document_file']['tmp_name'], $targetPath)) {
        $fileSize = $_FILES['document_file']['size'];
        $mimeType = $_FILES['document_file']['type'];
        
        $query = "INSERT INTO documents (employee_id, document_type, title, file_path, file_size, mime_type, description, expiry_date, uploaded_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssisssi", $employee_id, $document_type, $title, $targetPath, $fileSize, $mimeType, $description, $expiry_date, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = "Contract document uploaded successfully!";
        } else {
            $error = "Error uploading document: " . $stmt->error;
        }
    } else {
        $error = "Error uploading file.";
    }
}

// Get employees for dropdown
$employees = [];
$empQuery = "SELECT ep.employee_id, ep.first_name, ep.last_name, ep.employee_number 
             FROM employee_profiles ep 
             WHERE ep.employment_status = 'Active' 
             ORDER BY ep.first_name, ep.last_name";
$empResult = mysqli_query($conn, $empQuery);
if ($empResult) {
    while ($row = mysqli_fetch_assoc($empResult)) {
        $employees[] = $row;
    }
}

// Get contract documents
$contractsQuery = "SELECT d.*, ep.first_name, ep.last_name, ep.employee_number, u.username as uploaded_by_name 
                   FROM documents d 
                   LEFT JOIN employee_profiles ep ON d.employee_id = ep.employee_id 
                   LEFT JOIN users u ON d.uploaded_by = u.id 
                   WHERE d.document_type = 'Contract' 
                   ORDER BY d.uploaded_date DESC";
$contractsResult = mysqli_query($conn, $contractsQuery);
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
                            <i class="menu-icon icon-base bx bx-file"></i>
                            <div>Document Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="contracts.php" class="menu-link">
                                    <div>Contracts</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="certifications.php" class="menu-link">
                                    <div>Certifications</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="licenses.php" class="menu-link">
                                    <div>Licenses</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="performance-reviews.php" class="menu-link">
                                    <div>Performance Reviews</div>
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
                                        <h5 class="card-title mb-0">Contract Documents</h5>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                            <i class="bx bx-upload"></i> Upload Contract
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Employee</th>
                                                        <th>File Size</th>
                                                        <th>Expiry Date</th>
                                                        <th>Uploaded By</th>
                                                        <th>Upload Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($contractsResult && mysqli_num_rows($contractsResult) > 0): ?>
                                                        <?php while ($contract = mysqli_fetch_assoc($contractsResult)): ?>
                                                            <tr>
                                                                <td>
                                                                    <h6 class="mb-0"><?php echo htmlspecialchars($contract['title']); ?></h6>
                                                                    <?php if ($contract['description']): ?>
                                                                        <small class="text-muted"><?php echo htmlspecialchars($contract['description']); ?></small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo htmlspecialchars($contract['first_name'] . ' ' . $contract['last_name']); ?>
                                                                    <br><small class="text-muted"><?php echo $contract['employee_number']; ?></small>
                                                                </td>
                                                                <td><?php echo round($contract['file_size'] / 1024, 2); ?> KB</td>
                                                                <td>
                                                                    <?php if ($contract['expiry_date']): ?>
                                                                        <?php 
                                                                        $expiryDate = strtotime($contract['expiry_date']);
                                                                        $isExpired = $expiryDate < time();
                                                                        ?>
                                                                        <span class="<?php echo $isExpired ? 'text-danger' : 'text-success'; ?>">
                                                                            <?php echo date('M d, Y', $expiryDate); ?>
                                                                        </span>
                                                                        <?php if ($isExpired): ?>
                                                                            <br><small class="text-danger">Expired</small>
                                                                        <?php endif; ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">No expiry</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($contract['uploaded_by_name']); ?></td>
                                                                <td><?php echo date('M d, Y', strtotime($contract['uploaded_date'])); ?></td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="<?php echo $contract['file_path']; ?>" target="_blank">View</a></li>
                                                                            <li><a class="dropdown-item" href="<?php echo $contract['file_path']; ?>" download>Download</a></li>
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li><a class="dropdown-item text-danger" href="delete.php?id=<?php echo $contract['document_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No contract documents found</td>
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

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Contract Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee *</label>
                            <select class="form-select" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>">
                                        <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_number'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_file" class="form-label">Document File *</label>
                            <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max 10MB)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




