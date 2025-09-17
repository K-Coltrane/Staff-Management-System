<?php
// Research & Publications - Publications
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Publications';

// Get publications
$publicationsQuery = "SELECT p.*, ep.first_name, ep.last_name 
                      FROM research_publications p 
                      LEFT JOIN employee_profiles ep ON p.employee_id = ep.employee_id
                      ORDER BY p.publication_date DESC";
$publicationsResult = mysqli_query($conn, $publicationsQuery);
$publications = mysqli_fetch_all($publicationsResult, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && canManageEmployees()) {
    $employeeId = sanitize($_POST['employee_id']);
    $title = sanitize($_POST['title']);
    $authors = sanitize($_POST['authors']);
    $journal = sanitize($_POST['journal']);
    $publicationDate = sanitize($_POST['publication_date']);
    $doi = sanitize($_POST['doi']);
    $abstract = sanitize($_POST['abstract']);
    $impactFactor = sanitize($_POST['impact_factor']);
    
    $insertQuery = "INSERT INTO research_publications (employee_id, title, authors, journal, publication_date, doi, abstract, impact_factor, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("issssssd", $employeeId, $title, $authors, $journal, $publicationDate, $doi, $abstract, $impactFactor);
    
    if ($insertStmt->execute()) {
        setMessage("Publication added successfully!", "success");
        header("Location: publications.php");
        exit();
    } else {
        setMessage("Error adding publication: " . $conn->error, "danger");
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
                            <li class="menu-item active">
                                <a href="publications.php" class="menu-link">
                                    <div>Publications</div>
                                </a>
                            </li>
                            <li class="menu-item">
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
                                        <h5 class="card-title mb-0">Research Publications</h5>
                                        <?php if (canManageEmployees()): ?>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
                                            <i class="bx bx-plus"></i> Add Publication
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php displayMessage(); ?>
                                        
                                        <?php if (!empty($publications)): ?>
                                            <div class="row">
                                                <?php foreach ($publications as $publication): ?>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card h-100">
                                                            <div class="card-header">
                                                                <h6 class="card-title mb-0">
                                                                    <?php echo htmlspecialchars($publication['title']); ?>
                                                                </h6>
                                                                <small class="text-muted">
                                                                    By: <?php echo $publication['first_name'] . ' ' . $publication['last_name']; ?>
                                                                </small>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text">
                                                                    <strong>Authors:</strong> <?php echo htmlspecialchars($publication['authors']); ?><br>
                                                                    <strong>Journal:</strong> <?php echo htmlspecialchars($publication['journal']); ?><br>
                                                                    <strong>Publication Date:</strong> <?php echo formatDate($publication['publication_date']); ?><br>
                                                                    <?php if ($publication['doi']): ?>
                                                                        <strong>DOI:</strong> 
                                                                        <a href="https://doi.org/<?php echo $publication['doi']; ?>" target="_blank" class="text-decoration-none">
                                                                            <?php echo $publication['doi']; ?>
                                                                        </a><br>
                                                                    <?php endif; ?>
                                                                    <?php if ($publication['impact_factor']): ?>
                                                                        <strong>Impact Factor:</strong> <?php echo $publication['impact_factor']; ?><br>
                                                                    <?php endif; ?>
                                                                </p>
                                                                
                                                                <?php if ($publication['abstract']): ?>
                                                                    <div class="mt-3">
                                                                        <strong>Abstract:</strong>
                                                                        <p class="text-muted small">
                                                                            <?php echo htmlspecialchars(substr($publication['abstract'], 0, 200)); ?>
                                                                            <?php if (strlen($publication['abstract']) > 200): ?>
                                                                                <span class="text-primary" data-bs-toggle="collapse" data-bs-target="#abstract_<?php echo $publication['publication_id']; ?>" style="cursor: pointer;">
                                                                                    ...read more
                                                                                </span>
                                                                                <div class="collapse" id="abstract_<?php echo $publication['publication_id']; ?>">
                                                                                    <?php echo htmlspecialchars(substr($publication['abstract'], 200)); ?>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-footer">
                                                                <small class="text-muted">
                                                                    Added: <?php echo formatDate($publication['created_at']); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-book-open display-1 text-muted"></i>
                                                <h5 class="mt-3">No Publications</h5>
                                                <p class="text-muted">No research publications have been added yet.</p>
                                                <?php if (canManageEmployees()): ?>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
                                                    <i class="bx bx-plus"></i> Add First Publication
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

    <!-- Add Publication Modal -->
    <?php if (canManageEmployees()): ?>
    <div class="modal fade" id="addPublicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Publication</h5>
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
                            <label for="title" class="form-label">Publication Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="authors" class="form-label">Authors *</label>
                            <input type="text" class="form-control" id="authors" name="authors" required placeholder="e.g., John Doe, Jane Smith">
                        </div>
                        <div class="mb-3">
                            <label for="journal" class="form-label">Journal/Conference *</label>
                            <input type="text" class="form-control" id="journal" name="journal" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="publication_date" class="form-label">Publication Date *</label>
                                    <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="impact_factor" class="form-label">Impact Factor</label>
                                    <input type="number" class="form-control" id="impact_factor" name="impact_factor" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="doi" class="form-label">DOI</label>
                            <input type="text" class="form-control" id="doi" name="doi" placeholder="e.g., 10.1000/182">
                        </div>
                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstract</label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="4" placeholder="Enter the abstract..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Publication</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




