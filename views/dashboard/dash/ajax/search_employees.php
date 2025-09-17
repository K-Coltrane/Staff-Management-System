<?php
// Include database connection
require_once '../../../config/database.php';

// Get search parameters
$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$department = isset($_GET['department']) ? trim($_GET['department']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build the query
$query = "SELECT e.*, 
                 d.department_name AS department, 
                 p.position_title AS position 
          FROM employees e 
          LEFT JOIN departments d ON e.department_id = d.department_id 
          LEFT JOIN positions p ON e.position_id = p.position_id 
          WHERE 1=1";

// Add filters
if (!empty($name)) {
    $query .= " AND (e.first_name LIKE ? OR e.last_name LIKE ?)";
}
if (!empty($department)) {
    $query .= " AND d.department_name = ?";
}
if (!empty($status)) {
    $query .= " AND e.status = ?";
}

$query .= " ORDER BY e.employee_id DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);

// Bind parameters dynamically
$params = [];
$types = '';
if (!empty($name)) {
    $params[] = "%$name%";
    $params[] = "%$name%";
    $types .= 'ss';
}
if (!empty($department)) {
    $params[] = $department;
    $types .= 's';
}
if (!empty($status)) {
    $params[] = $status;
    $types .= 's';
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Generate the HTML for the filtered results
if ($result->num_rows > 0) {
    while ($employee = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) . '</td>';
        echo '<td>' . htmlspecialchars($employee['department'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($employee['position'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($employee['email'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($employee['phone'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($employee['status'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($employee['hire_date'] ?? 'N/A') . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7" class="text-center">No employees found</td></tr>';
}

$stmt->close();
$conn->close();
?>
