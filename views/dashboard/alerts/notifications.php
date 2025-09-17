<?php
// Alerts & Notifications - Notifications Management
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

$pageTitle = 'Alerts & Notifications';
$error = '';
$success = '';

// Handle form submission for new notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $user_id = $_POST['user_id'];
    $title = sanitize($_POST['title']);
    $message = sanitize($_POST['message']);
    $type = $_POST['type'];
    
    if (empty($user_id) || empty($title) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        $query = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $title, $message, $type);
        
        if ($stmt->execute()) {
            $success = "Notification sent successfully!";
        } else {
            $error = "Error sending notification: " . $stmt->error;
        }
    }
}

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read') {
    $notification_id = $_POST['notification_id'];
    
    $query = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        $success = "Notification marked as read!";
    }
}

// Handle mark all as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_all_read') {
    $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $success = "All notifications marked as read!";
    }
}

// Get employees for dropdown (only for admins)
$employees = [];
if (isAdmin()) {
    $empQuery = "SELECT ep.employee_id, ep.first_name, ep.last_name, ep.employee_number, u.id as user_id 
                 FROM employee_profiles ep 
                 LEFT JOIN users u ON ep.user_id = u.id 
                 WHERE ep.employment_status = 'Active' 
                 ORDER BY ep.first_name, ep.last_name";
    $empResult = mysqli_query($conn, $empQuery);
    if ($empResult) {
        while ($row = mysqli_fetch_assoc($empResult)) {
            $employees[] = $row;
        }
    }
}

// Get notifications for current user
$notificationsQuery = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$notificationsStmt = $conn->prepare($notificationsQuery);
$notificationsStmt->bind_param("i", $_SESSION['user_id']);
$notificationsStmt->execute();
$notifications = $notificationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get unread count
$unreadQuery = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$unreadStmt = $conn->prepare($unreadQuery);
$unreadStmt->bind_param("i", $_SESSION['user_id']);
$unreadStmt->execute();
$unreadCount = $unreadStmt->get_result()->fetch_assoc()['unread_count'];

// Auto-generate some sample notifications for demonstration
if (empty($notifications)) {
    $sampleNotifications = [
        [
            'title' => 'Welcome to Staff Management System',
            'message' => 'Welcome! Your account has been set up successfully. You can now access all features of the system.',
            'type' => 'Info'
        ],
        [
            'title' => 'Leave Request Reminder',
            'message' => 'You have 5 days of annual leave remaining this year. Consider planning your vacation time.',
            'type' => 'Warning'
        ],
        [
            'title' => 'Performance Review Due',
            'message' => 'Your annual performance review is scheduled for next week. Please prepare your self-assessment.',
            'type' => 'Info'
        ]
    ];
    
    foreach ($sampleNotifications as $notification) {
        $query = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $_SESSION['user_id'], $notification['title'], $notification['message'], $notification['type']);
        $stmt->execute();
    }
    
    // Refresh notifications
    $notificationsStmt->execute();
    $notifications = $notificationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
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
                            <i class="menu-icon icon-base bx bx-bell"></i>
                            <div>Alerts & Notifications</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="notifications.php" class="menu-link">
                                    <div>All Notifications</div>
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                            <li class="menu-item">
                                <a href="send-notification.php" class="menu-link">
                                    <div>Send Notification</div>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </aside>

            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached">
                    <div class="navbar-nav-right d-flex align-items-center">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <span class="position-relative">
                                        <i class="icon-base bx bx-bell icon-md"></i>
                                        <?php if ($unreadCount > 0): ?>
                                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"><?php echo $unreadCount; ?></span>
                                        <?php endif; ?>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-0">
                                    <li class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h6 class="mb-0 me-auto">Notifications</h6>
                                            <div class="d-flex align-items-center h6 mb-0">
                                                <span class="badge bg-label-primary me-2"><?php echo $unreadCount; ?> New</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush">
                                            <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                                                <li class="list-group-item list-group-item-action dropdown-notifications-item <?php echo !$notification['is_read'] ? 'bg-light' : ''; ?>">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="avatar">
                                                                <span class="avatar-initial rounded-circle bg-<?php echo $notification['type'] == 'Error' ? 'danger' : ($notification['type'] == 'Warning' ? 'warning' : ($notification['type'] == 'Success' ? 'success' : 'info')); ?>">
                                                                    <i class="icon-base bx bx-<?php echo $notification['type'] == 'Error' ? 'x' : ($notification['type'] == 'Warning' ? 'exclamation' : ($notification['type'] == 'Success' ? 'check' : 'info-circle')); ?>"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="small mb-0"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                            <small class="mb-1 d-block text-body"><?php echo htmlspecialchars($notification['message']); ?></small>
                                                            <small class="text-body-secondary"><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></small>
                                                        </div>
                                                        <?php if (!$notification['is_read']): ?>
                                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="action" value="mark_read">
                                                                    <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                                                                </form>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <li class="border-top">
                                        <div class="d-grid p-4">
                                            <a class="btn btn-primary btn-sm d-flex" href="notifications.php">
                                                <small class="align-middle">View all notifications</small>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
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
                                        <h5 class="card-title mb-0">All Notifications</h5>
                                        <div>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="mark_all_read">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="bx bx-check"></i> Mark All as Read
                                                </button>
                                            </form>
                                            <?php if (isAdmin()): ?>
                                                <button class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                                                    <i class="bx bx-plus"></i> Send Notification
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($notifications)): ?>
                                            <?php foreach ($notifications as $notification): ?>
                                                <div class="d-flex align-items-start mb-3 p-3 border rounded <?php echo !$notification['is_read'] ? 'bg-light' : ''; ?>">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar">
                                                            <span class="avatar-initial rounded-circle bg-<?php echo $notification['type'] == 'Error' ? 'danger' : ($notification['type'] == 'Warning' ? 'warning' : ($notification['type'] == 'Success' ? 'success' : 'info')); ?>">
                                                                <i class="icon-base bx bx-<?php echo $notification['type'] == 'Error' ? 'x' : ($notification['type'] == 'Warning' ? 'exclamation' : ($notification['type'] == 'Success' ? 'check' : 'info-circle')); ?>"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                        <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></small>
                                                    </div>
                                                    <?php if (!$notification['is_read']): ?>
                                                        <div class="flex-shrink-0">
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="action" value="mark_read">
                                                                <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bx bx-bell-off display-1 text-muted"></i>
                                                <h5 class="mt-3">No notifications</h5>
                                                <p class="text-muted">You don't have any notifications yet.</p>
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

    <!-- Send Notification Modal -->
    <?php if (isAdmin()): ?>
    <div class="modal fade" id="sendNotificationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Send To *</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Employee</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?php echo $emp['user_id']; ?>">
                                        <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_number'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="Info">Info</option>
                                <option value="Warning">Warning</option>
                                <option value="Success">Success</option>
                                <option value="Error">Error</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




