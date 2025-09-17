<?php


// Base URL - adjust this to match your project folder name
define('BASE_URL', 'http://localhost/Work/');

// Site settings
define('SITE_NAME', 'Staff Records Management System');
define('ADMIN_EMAIL', 'admin@example.com');

// File upload paths
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('DOCUMENT_DIR', UPLOAD_DIR . 'documents/');
define('PROFILE_PIC_DIR', UPLOAD_DIR . 'profile_pictures/');
define('RESUME_DIR', UPLOAD_DIR . 'resumes/');

// Create upload directories if they don't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(DOCUMENT_DIR)) {
    mkdir(DOCUMENT_DIR, 0777, true);
}
if (!file_exists(PROFILE_PIC_DIR)) {
    mkdir(PROFILE_PIC_DIR, 0777, true);
}
if (!file_exists(RESUME_DIR)) {
    mkdir(RESUME_DIR, 0777, true);
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . '/database.php';
?>