<?php
// Create complete database schema for Staff Records Management System
require_once __DIR__ . '/config/config.php';

echo "<h2>Creating Complete Database Schema</h2>";

// Check database connection
if (!$conn) {
    echo "❌ Database connection failed!<br>";
    exit;
} else {
    echo "✅ Database connection successful!<br>";
}

// Array of all table creation queries
$tables = [
    // Departments table
    "CREATE TABLE IF NOT EXISTS departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        manager_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    // Positions table
    "CREATE TABLE IF NOT EXISTS positions (
        position_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        department_id INT,
        salary_min DECIMAL(10,2),
        salary_max DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
    )",
    
    // Employee Profiles table
    "CREATE TABLE IF NOT EXISTS employee_profiles (
        employee_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        employee_number VARCHAR(20) UNIQUE,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        middle_name VARCHAR(50),
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        phone VARCHAR(20),
        address TEXT,
        city VARCHAR(50),
        state VARCHAR(50),
        postal_code VARCHAR(20),
        country VARCHAR(50),
        emergency_contact_name VARCHAR(100),
        emergency_contact_phone VARCHAR(20),
        emergency_contact_relationship VARCHAR(50),
        position_id INT,
        department_id INT,
        hire_date DATE,
        employment_status ENUM('Active', 'Inactive', 'Terminated', 'On Leave'),
        salary DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE SET NULL,
        FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
    )",
    
    // Family Information table
    "CREATE TABLE IF NOT EXISTS family_information (
        family_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        relationship ENUM('Spouse', 'Child', 'Parent', 'Sibling', 'Other'),
        full_name VARCHAR(100) NOT NULL,
        date_of_birth DATE,
        phone VARCHAR(20),
        address TEXT,
        is_emergency_contact BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE
    )",
    
    // Job Postings table
    "CREATE TABLE IF NOT EXISTS job_postings (
        job_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        requirements TEXT,
        department_id INT,
        position_id INT,
        salary_range VARCHAR(50),
        employment_type ENUM('Full-time', 'Part-time', 'Contract', 'Internship'),
        status ENUM('Open', 'Closed', 'On Hold'),
        posted_by INT,
        posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        closing_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
        FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE SET NULL,
        FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
    )",
    
    // Applicants table
    "CREATE TABLE IF NOT EXISTS applicants (
        applicant_id INT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        resume_file VARCHAR(255),
        cover_letter TEXT,
        status ENUM('Applied', 'Under Review', 'Interviewed', 'Rejected', 'Hired'),
        applied_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        notes TEXT
    )",
    
    // Interviews table
    "CREATE TABLE IF NOT EXISTS interviews (
        interview_id INT AUTO_INCREMENT PRIMARY KEY,
        applicant_id INT NOT NULL,
        interviewer_id INT NOT NULL,
        interview_date DATETIME,
        interview_type ENUM('Phone', 'Video', 'In-person', 'Panel'),
        status ENUM('Scheduled', 'Completed', 'Cancelled', 'Rescheduled'),
        notes TEXT,
        rating INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Documents table
    "CREATE TABLE IF NOT EXISTS documents (
        document_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        document_type ENUM('Contract', 'Certification', 'License', 'Performance Review', 'Other'),
        title VARCHAR(100) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_size INT,
        mime_type VARCHAR(100),
        description TEXT,
        expiry_date DATE,
        uploaded_by INT,
        uploaded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
    )",
    
    // Leave Management table
    "CREATE TABLE IF NOT EXISTS leave_requests (
        leave_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        leave_type ENUM('Annual', 'Sick', 'Personal', 'Maternity', 'Paternity', 'Emergency', 'Other'),
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        days_requested INT NOT NULL,
        reason TEXT,
        status ENUM('Pending', 'Approved', 'Rejected', 'Cancelled'),
        approved_by INT,
        approved_date TIMESTAMP NULL,
        rejection_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
    )",
    
    // Leave Balances table
    "CREATE TABLE IF NOT EXISTS leave_balances (
        balance_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        leave_type ENUM('Annual', 'Sick', 'Personal', 'Maternity', 'Paternity'),
        total_days INT DEFAULT 0,
        used_days INT DEFAULT 0,
        remaining_days INT DEFAULT 0,
        year YEAR NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        UNIQUE KEY unique_employee_leave_year (employee_id, leave_type, year)
    )",
    
    // Staff Promotions table
    "CREATE TABLE IF NOT EXISTS staff_promotions (
        promotion_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        old_position_id INT,
        new_position_id INT NOT NULL,
        old_salary DECIMAL(10,2),
        new_salary DECIMAL(10,2),
        promotion_date DATE NOT NULL,
        reason TEXT,
        approved_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (old_position_id) REFERENCES positions(position_id) ON DELETE SET NULL,
        FOREIGN KEY (new_position_id) REFERENCES positions(position_id) ON DELETE CASCADE,
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
    )",
    
    // Research Publications table
    "CREATE TABLE IF NOT EXISTS research_publications (
        publication_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        authors TEXT,
        journal_name VARCHAR(255),
        publication_date DATE,
        doi VARCHAR(100),
        url VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE
    )",
    
    // Conferences table
    "CREATE TABLE IF NOT EXISTS conferences (
        conference_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        conference_name VARCHAR(255) NOT NULL,
        presentation_title VARCHAR(255),
        conference_date DATE,
        location VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE
    )",
    
    // Training Requirements table
    "CREATE TABLE IF NOT EXISTS training_requirements (
        requirement_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        department_id INT,
        position_id INT,
        is_mandatory BOOLEAN DEFAULT FALSE,
        validity_period INT, -- in months
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
        FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE SET NULL
    )",
    
    // Training Records table
    "CREATE TABLE IF NOT EXISTS training_records (
        training_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        requirement_id INT,
        training_name VARCHAR(255) NOT NULL,
        provider VARCHAR(255),
        start_date DATE,
        end_date DATE,
        status ENUM('Not Started', 'In Progress', 'Completed', 'Expired'),
        certificate_file VARCHAR(255),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (requirement_id) REFERENCES training_requirements(requirement_id) ON DELETE SET NULL
    )",
    
    // Performance Reviews table
    "CREATE TABLE IF NOT EXISTS performance_reviews (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        reviewer_id INT NOT NULL,
        review_period_start DATE NOT NULL,
        review_period_end DATE NOT NULL,
        overall_rating INT, -- 1-5 scale
        goals_achieved TEXT,
        areas_for_improvement TEXT,
        strengths TEXT,
        recommendations TEXT,
        status ENUM('Draft', 'Submitted', 'Approved', 'Completed'),
        review_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Notifications table
    "CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        type ENUM('Info', 'Warning', 'Success', 'Error'),
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // Payslips table
    "CREATE TABLE IF NOT EXISTS payslips (
        payslip_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        pay_period_start DATE NOT NULL,
        pay_period_end DATE NOT NULL,
        basic_salary DECIMAL(10,2),
        allowances DECIMAL(10,2),
        deductions DECIMAL(10,2),
        net_salary DECIMAL(10,2),
        file_path VARCHAR(255),
        generated_by INT,
        generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employee_profiles(employee_id) ON DELETE CASCADE,
        FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
    )"
];

// Create tables
$successCount = 0;
$errorCount = 0;

foreach ($tables as $index => $query) {
    echo "<h3>Creating table " . ($index + 1) . " of " . count($tables) . "</h3>";
    
    if (mysqli_query($conn, $query)) {
        echo "✅ Table created successfully!<br>";
        $successCount++;
    } else {
        echo "❌ Error creating table: " . mysqli_error($conn) . "<br>";
        $errorCount++;
    }
}

// Insert sample data
echo "<h2>Inserting Sample Data</h2>";

// Insert sample departments (using existing table structure)
$departments = [
    "INSERT INTO departments (department_name, location) VALUES ('Human Resources', 'Main Office')",
    "INSERT INTO departments (department_name, location) VALUES ('Information Technology', 'Tech Wing')",
    "INSERT INTO departments (department_name, location) VALUES ('Finance', 'Main Office')",
    "INSERT INTO departments (department_name, location) VALUES ('Operations', 'Operations Center')",
    "INSERT INTO departments (department_name, location) VALUES ('Marketing', 'Creative Hub')"
];

foreach ($departments as $query) {
    if (mysqli_query($conn, $query)) {
        echo "✅ Department inserted<br>";
    }
}

// Insert sample positions (check if positions table exists first)
$positionsCheck = mysqli_query($conn, "SHOW TABLES LIKE 'positions'");
if (mysqli_num_rows($positionsCheck) > 0) {
    $positions = [
        "INSERT INTO positions (name, description, department_id, salary_min, salary_max) VALUES ('HR Manager', 'Human Resources Manager', 1, 50000, 70000)",
        "INSERT INTO positions (name, description, department_id, salary_min, salary_max) VALUES ('Software Developer', 'Software Developer', 2, 40000, 60000)",
        "INSERT INTO positions (name, description, department_id, salary_min, salary_max) VALUES ('Finance Analyst', 'Financial Analyst', 3, 45000, 65000)",
        "INSERT INTO positions (name, description, department_id, salary_min, salary_max) VALUES ('Operations Manager', 'Operations Manager', 4, 55000, 75000)",
        "INSERT INTO positions (name, description, department_id, salary_min, salary_max) VALUES ('Marketing Specialist', 'Marketing Specialist', 5, 35000, 55000)"
    ];
} else {
    echo "⚠️ Positions table doesn't exist, skipping position insertion<br>";
    $positions = [];
}

foreach ($positions as $query) {
    if (mysqli_query($conn, $query)) {
        echo "✅ Position inserted<br>";
    }
}

// Create employee profile for admin user (check if employee_profiles table exists)
$employeeCheck = mysqli_query($conn, "SHOW TABLES LIKE 'employee_profiles'");
if (mysqli_num_rows($employeeCheck) > 0) {
    $adminProfile = "INSERT INTO employee_profiles (user_id, employee_number, first_name, last_name, phone, address, city, position_id, department_id, hire_date, employment_status, salary) 
    VALUES (5, 'EMP001', 'Admin', 'User', '123-456-7890', '123 Admin Street', 'Admin City', 1, 1, CURDATE(), 'Active', 60000)";

    if (mysqli_query($conn, $adminProfile)) {
        echo "✅ Admin employee profile created<br>";
    } else {
        echo "⚠️ Could not create admin employee profile: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "⚠️ Employee profiles table doesn't exist, skipping profile creation<br>";
}

// Insert sample leave balances for admin
$leaveBalance = "INSERT INTO leave_balances (employee_id, leave_type, total_days, used_days, remaining_days, year) 
VALUES (1, 'Annual', 21, 0, 21, YEAR(CURDATE())),
       (1, 'Sick', 10, 0, 10, YEAR(CURDATE()))";

if (mysqli_query($conn, $leaveBalance)) {
    echo "✅ Leave balances created<br>";
}

// Add foreign key constraints after all tables are created
echo "<h2>Adding Foreign Key Constraints</h2>";

$foreignKeys = [
    "ALTER TABLE applicants ADD CONSTRAINT fk_applicants_job_id FOREIGN KEY (job_id) REFERENCES job_postings(job_id) ON DELETE CASCADE",
    "ALTER TABLE interviews ADD CONSTRAINT fk_interviews_applicant_id FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE",
    "ALTER TABLE interviews ADD CONSTRAINT fk_interviews_interviewer_id FOREIGN KEY (interviewer_id) REFERENCES users(id) ON DELETE CASCADE"
];

$fkSuccessCount = 0;
$fkErrorCount = 0;

foreach ($foreignKeys as $fkQuery) {
    if (mysqli_query($conn, $fkQuery)) {
        echo "✅ Foreign key constraint added successfully!<br>";
        $fkSuccessCount++;
    } else {
        echo "❌ Error adding foreign key constraint: " . mysqli_error($conn) . "<br>";
        $fkErrorCount++;
    }
}

echo "<h2>Database Schema Creation Complete!</h2>";
echo "<p>✅ Successfully created: $successCount tables</p>";
echo "<p>❌ Table creation errors: $errorCount tables</p>";
echo "<p>✅ Foreign key constraints added: $fkSuccessCount</p>";
echo "<p>❌ Foreign key errors: $fkErrorCount</p>";

if ($errorCount == 0) {
    echo "<h3>🎉 All tables created successfully!</h3>";
    echo "<p>Your Staff Records Management System database is now ready!</p>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li>Login to the system</li>";
    echo "<li>Manage employees</li>";
    echo "<li>Handle recruitment</li>";
    echo "<li>Process leave requests</li>";
    echo "<li>And much more!</li>";
    echo "</ul>";
}

echo "<br><a href='index.php'>← Back to Login</a>";
?>
