<?php
session_start();
header('Content-Type: application/json');
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Unauthorized']); exit; }
function isManager(){ return isset($_SESSION['role']) && in_array(strtolower($_SESSION['role']), ['admin','manager'], true); }

$action = $_POST['action'] ?? '';
$leaveId = isset($_POST['leave_id']) ? (int)$_POST['leave_id'] : 0;

try {
  switch ($action) {
    case 'approve':
      if (!isManager()) throw new Exception('Forbidden', 403);
      $stmt = $conn->prepare('UPDATE leave_requests SET status = "Approved", approved_by = ?, approved_date = NOW() WHERE leave_id = ?');
      $stmt->bind_param('ii', $_SESSION['user_id'], $leaveId);
      $stmt->execute();
      echo json_encode(['ok'=>true]);
      break;
    case 'reject':
      if (!isManager()) throw new Exception('Forbidden', 403);
      $stmt = $conn->prepare('UPDATE leave_requests SET status = "Rejected", approved_by = ?, approved_date = NOW() WHERE leave_id = ?');
      $stmt->bind_param('ii', $_SESSION['user_id'], $leaveId);
      $stmt->execute();
      echo json_encode(['ok'=>true]);
      break;
    case 'delete':
      // allow owner or manager
      if (isManager()) {
        $stmt = $conn->prepare('DELETE FROM leave_requests WHERE leave_id = ?');
        $stmt->bind_param('i', $leaveId);
        $stmt->execute();
        echo json_encode(['ok'=>true]);
      } else {
        // ensure the leave belongs to user
        $stmt = $conn->prepare('DELETE FROM leave_requests WHERE leave_id = ? AND employee_id = (SELECT employee_id FROM employee_profiles WHERE user_id = ?)');
        $stmt->bind_param('ii', $leaveId, $_SESSION['user_id']);
        $stmt->execute();
        echo json_encode(['ok'=> $stmt->affected_rows>0]);
      }
      break;
    case 'apply':
      $employeeId = (int)($_POST['employee_id'] ?? 0);
      $leaveType = $_POST['leave_type'] ?? '';
      $startDate = $_POST['start_date'] ?? '';
      $endDate = $_POST['end_date'] ?? '';
      $days = (int)($_POST['days_requested'] ?? 0);
      $reason = $_POST['reason'] ?? '';
      if ($employeeId<=0 || !$leaveType || !$startDate || !$endDate || $days<=0) throw new Exception('Invalid input', 400);
      $stmt = $conn->prepare('INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, days_requested, reason, status, created_at) VALUES (?,?,?,?,?,?,"Pending", NOW())');
      $stmt->bind_param('isssis', $employeeId, $leaveType, $startDate, $endDate, $days, $reason);
      $stmt->execute();
      echo json_encode(['ok'=>true]);
      break;
    default:
      throw new Exception('Unknown action', 400);
  }
} catch (Exception $e) {
  $code = $e->getCode() ?: 500;
  http_response_code($code);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}


