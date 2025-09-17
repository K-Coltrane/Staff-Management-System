<?php
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Register a new user (employee)
     */
    public function register($firstName, $lastName, $email, $password, $phone, $positionId, $departmentId) {
        // Check if email already exists
        $query = "SELECT employee_id FROM employees WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // User already exists
        }
        
        // Hash password - we'll store this in a separate table since the original schema doesn't have passwords
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // First, create a user_credentials entry (we'll create this table)
            $query = "INSERT INTO user_credentials (email, password) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $email, $hashedPassword);
            $stmt->execute();
            
            // Now insert the employee
            $hireDate = date('Y-m-d'); // Current date as hire date
            $status = 'Active';
            
            $query = "INSERT INTO employees (first_name, last_name, email, phone, position_id, department_id, hire_date, employment_status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssiiss", $firstName, $lastName, $email, $phone, $positionId, $departmentId, $hireDate, $status);
            $stmt->execute();
            
            $employeeId = $this->conn->insert_id;
            
            // Commit transaction
            $this->conn->commit();
            
            return $employeeId;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            return false;
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        $query = "SELECT id, username, password FROM user_credentials WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Login successful
                return true;
            }
        }

        // Login failed
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $query = "SELECT e.*, p.position_title, d.department_name 
                  FROM employees e
                  JOIN positions p ON e.position_id = p.position_id
                  JOIN departments d ON e.department_id = d.department_id
                  WHERE e.employee_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * Change password
     */
    public function changePassword($email, $currentPassword, $newPassword) {
        $query = "SELECT password FROM user_credentials WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            if (password_verify($currentPassword, $row['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $query = "UPDATE user_credentials SET password = ? WHERE email = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("ss", $hashedPassword, $email);
                
                return $stmt->execute();
            }
        }
        
        return false;
    }

    /**
     * Get user ID by email
     */
    public function getUserIdByEmail($email) {
        $query = "SELECT id FROM user_credentials WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }

        return null; // Return null if no user is found
    }
}
?>