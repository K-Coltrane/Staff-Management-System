<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    // Case-insensitive check for 'admin' role
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin';
}

// Check if user is manager
function isManager() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'manager';
}

// Check if user is staff
function isStaff() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'staff';
}

// Check if user has admin or manager privileges
function isAdminOrManager() {
    return isAdmin() || isManager();
}

// Check if user can manage users (admin only)
function canManageUsers() {
    return isAdmin();
}

// Check if user can manage employees (admin and manager)
function canManageEmployees() {
    return isAdmin() || isManager();
}

// Check if user can approve leave (admin and manager)
function canApproveLeave() {
    return isAdmin() || isManager();
}

// Check if user can promote staff (admin and manager)
function canPromoteStaff() {
    return isAdmin() || isManager();
}

// Check if user can view reports (admin and manager)
function canViewReports() {
    return isAdmin() || isManager();
}

// Check if user can manage alerts (admin only)
function canManageAlerts() {
    return isAdmin();
}

// Display flash messages
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        echo '<div class="alert alert-' . $messageType . ' alert-dismissible" role="alert">';
        echo $_SESSION['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Clear the message
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Set flash message
function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Redirect to a URL
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Sanitize input - Modified to handle missing connection
function sanitize($input) {
    global $conn;
    
    // First apply basic sanitization
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    
    // Only use mysqli_real_escape_string if connection is available
    if ($conn && $conn instanceof mysqli) {
        return mysqli_real_escape_string($conn, $input);
    }
    
    // Return the basically sanitized input if no connection
    return $input;
}

// Format date
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

// Get user by ID
function getUserById($userId) {
    global $conn;
    
    // Check if connection exists
    if (!$conn) {
        return false;
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Get employee by user ID
function getEmployeeByUserId($userId) {
    global $conn;
    
    // Check if connection exists
    if (!$conn) {
        return false;
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM employee_profiles WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Get all departments
function getAllDepartments() {
    global $conn;
    
    // Check if connection exists
    if (!$conn) {
        return array();
    }
    
    $query = "SELECT * FROM departments ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $departments = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = $row;
        }
    }
    
    return $departments;
}

// Get all positions
function getAllPositions() {
    global $conn;
    
    // Check if connection exists
    if (!$conn) {
        return array();
    }
    
    $query = "SELECT p.*, d.name as department_name FROM positions p JOIN departments d ON p.department_id = d.department_id ORDER BY p.name ASC";
    $result = mysqli_query($conn, $query);
    
    $positions = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $positions[] = $row;
        }
    }
    
    return $positions;
}

// Get positions by department
function getPositionsByDepartment($departmentId) {
    global $conn;
    
    // Check if connection exists
    if (!$conn) {
        return array();
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM positions WHERE department_id = ? ORDER BY name ASC");
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $positions = array();
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
    
    return $positions;
}
?>
