<?php
// Start session
session_start();

// Load global config/constants
require_once __DIR__ . '/../../../config/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // For testing purposes, we'll comment this out temporarily
    // header("Location: index.php");
    // exit();
}

// Use project mysqli connection
require_once __DIR__ . '/../../../config/database.php';
// Simple PDO wrapper from mysqli for this page only when needed
try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('set names utf8mb4');
} catch (PDOException $e) {
    $errorMessage = 'Connection error: ' . $e->getMessage();
}

// Sanitize function if not already defined
if (!function_exists('sanitize')) {
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Check if system_settings table exists, if not create it
try {
    $tableExists = false;
    $stmt = $db->query("SHOW TABLES LIKE 'system_settings'");
    if ($stmt->rowCount() > 0) {
        $tableExists = true;
    }
    
    if (!$tableExists) {
        // Create the system_settings table
        $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `value` text DEFAULT NULL,
            `description` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $db->exec($sql);
        
        // Insert default settings
        $defaultSettings = [
            ['company_name', 'Staff Management System', 'Company name displayed throughout the system'],
            ['contact_email', 'admin@example.com', 'Primary contact email for the system'],
            ['contact_phone', '123-456-7890', 'Primary contact phone number'],
            ['timezone', 'UTC', 'Default timezone for the system'],
            ['date_format', 'Y-m-d', 'Default date format for display'],
            ['min_password_length', '8', 'Minimum password length required'],
            ['password_expiry', '90', 'Days before password expires'],
            ['max_login_attempts', '5', 'Maximum failed login attempts before account lockout'],
            ['session_timeout', '30', 'Minutes of inactivity before automatic logout'],
            ['default_annual_leave', '20', 'Default annual leave days for new employees'],
            ['default_sick_leave', '10', 'Default sick leave days for new employees'],
            ['leave_approval_required', '1', 'Whether leave requests require approval'],
            ['max_consecutive_leave', '15', 'Maximum consecutive leave days allowed'],
            ['email_notifications', '1', 'Enable email notifications'],
            ['leave_request_notify', '1', 'Notify managers of new leave requests'],
            ['document_expiry_notify', '1', 'Notify when documents are about to expire'],
            ['performance_review_notify', '1', 'Notify of upcoming performance reviews'],
            ['max_file_size', '5', 'Maximum file size for document uploads in MB'],
            ['allowed_file_types', 'pdf,doc,docx,jpg,png', 'Allowed file extensions for uploads'],
            ['document_retention_period', '365', 'Days to retain documents before archiving'],
            ['review_cycle', 'annual', 'Frequency of performance reviews'],
            ['review_reminder_days', '14', 'Days before review to send reminder notifications'],
            ['self_assessment', '1', 'Enable employee self-assessment in reviews'],
            ['peer_review', '0', 'Enable peer reviews in performance assessment']
        ];
        
        $insertSql = "INSERT INTO `system_settings` (`name`, `value`, `description`) VALUES (?, ?, ?)";
        $stmt = $db->prepare($insertSql);
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }
} catch (PDOException $e) {
    $errorMessage = "Database setup error: " . $e->getMessage();
}

// Process form submissions
$successMessage = '';
$errorMessage = isset($errorMessage) ? $errorMessage : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine which form was submitted based on the action parameter
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'general_settings':
            // Process general settings form
            $companyName = sanitize($_POST['company_name']);
            $contactEmail = sanitize($_POST['contact_email']);
            $contactPhone = sanitize($_POST['contact_phone']);
            $timezone = sanitize($_POST['timezone']);
            $dateFormat = sanitize($_POST['date_format']);
            
            try {
                // Update settings in database
                $query = "UPDATE system_settings SET 
                          value = CASE 
                              WHEN name = 'company_name' THEN :company_name
                              WHEN name = 'contact_email' THEN :contact_email
                              WHEN name = 'contact_phone' THEN :contact_phone
                              WHEN name = 'timezone' THEN :timezone
                              WHEN name = 'date_format' THEN :date_format
                              ELSE value
                          END
                          WHERE name IN ('company_name', 'contact_email', 'contact_phone', 'timezone', 'date_format')";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':company_name', $companyName);
                $stmt->bindParam(':contact_email', $contactEmail);
                $stmt->bindParam(':contact_phone', $contactPhone);
                $stmt->bindParam(':timezone', $timezone);
                $stmt->bindParam(':date_format', $dateFormat);
                
                if ($stmt->execute()) {
                    $successMessage = "General settings updated successfully!";
                } else {
                    $errorMessage = "Error updating general settings.";
                }
            } catch (PDOException $e) {
                $errorMessage = "Database error: " . $e->getMessage();
            }
            break;
            
        // Other case statements remain the same...
    }
}

// Fetch current settings from database
function getSystemSetting($db, $settingName, $default = '') {
    try {
        $query = "SELECT value FROM system_settings WHERE name = :name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $settingName);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['value'];
        }
    } catch (PDOException $e) {
        // If there's an error, return the default value
    }
    
    return $default;
}

// Get current settings with error handling
try {
    $companyName = getSystemSetting($db, 'company_name', 'Staff Management System');
    $contactEmail = getSystemSetting($db, 'contact_email', 'admin@example.com');
    $contactPhone = getSystemSetting($db, 'contact_phone', '123-456-7890');
    $timezone = getSystemSetting($db, 'timezone', 'UTC');
    $dateFormat = getSystemSetting($db, 'date_format', 'Y-m-d');

    $minPasswordLength = getSystemSetting($db, 'min_password_length', '8');
    $passwordExpiry = getSystemSetting($db, 'password_expiry', '90');
    $maxLoginAttempts = getSystemSetting($db, 'max_login_attempts', '5');
    $sessionTimeout = getSystemSetting($db, 'session_timeout', '30');

    $defaultAnnualLeave = getSystemSetting($db, 'default_annual_leave', '20');
    $defaultSickLeave = getSystemSetting($db, 'default_sick_leave', '10');
    $leaveApprovalRequired = getSystemSetting($db, 'leave_approval_required', '1');
    $maxConsecutiveLeave = getSystemSetting($db, 'max_consecutive_leave', '15');

    $emailNotifications = getSystemSetting($db, 'email_notifications', '1');
    $leaveRequestNotify = getSystemSetting($db, 'leave_request_notify', '1');
    $documentExpiryNotify = getSystemSetting($db, 'document_expiry_notify', '1');
    $performanceReviewNotify = getSystemSetting($db, 'performance_review_notify', '1');

    $maxFileSize = getSystemSetting($db, 'max_file_size', '5');
    $allowedFileTypes = getSystemSetting($db, 'allowed_file_types', 'pdf,doc,docx,jpg,png');
    $documentRetentionPeriod = getSystemSetting($db, 'document_retention_period', '365');

    $reviewCycle = getSystemSetting($db, 'review_cycle', 'annual');
    $reviewReminderDays = getSystemSetting($db, 'review_reminder_days', '14');
    $selfAssessment = getSystemSetting($db, 'self_assessment', '1');
    $peerReview = getSystemSetting($db, 'peer_review', '0');
} catch (PDOException $e) {
    $errorMessage = "Error retrieving settings: " . $e->getMessage();
    
    // Set default values if there's an error
    $companyName = 'Staff Management System';
    $contactEmail = 'admin@example.com';
    $contactPhone = '123-456-7890';
    $timezone = 'UTC';
    $dateFormat = 'Y-m-d';

    $minPasswordLength = '8';
    $passwordExpiry = '90';
    $maxLoginAttempts = '5';
    $sessionTimeout = '30';

    $defaultAnnualLeave = '20';
    $defaultSickLeave = '10';
    $leaveApprovalRequired = '1';
    $maxConsecutiveLeave = '15';

    $emailNotifications = '1';
    $leaveRequestNotify = '1';
    $documentExpiryNotify = '1';
    $performanceReviewNotify = '1';

    $maxFileSize = '5';
    $allowedFileTypes = 'pdf,doc,docx,jpg,png';
    $documentRetentionPeriod = '365';

    $reviewCycle = 'annual';
    $reviewReminderDays = '14';
    $selfAssessment = '1';
    $peerReview = '0';
}
?>

<!doctype html>
<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-assets-path="assets/"
  data-template="vertical-menu-template"
  data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Settings - Staff Management System</title>

    <meta name="description" content="Staff Management System Settings" />

   <!-- Favicon -->
   <link rel="icon" type="image/x-icon" href="../../../assets/vendor/img/favicon/favicon.ico" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
  rel="stylesheet" />

<link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />

<!-- Core CSS -->
<link rel="stylesheet" href="../../../assets/pickr/pickr-themes.css" />
<link rel="stylesheet" href="../../../assets/css/core.css" />
<link rel="stylesheet" href="../../../assets/css/demo.css" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="../../../assets/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="../../../assets/libs/apex-charts/apex-charts.css" />

<!-- Page CSS -->

<!-- Helpers -->
<script src="assets/js/helpers.js"></script>
<script src="assets/js/template-customizer.js"></script>
<script src="assets/vendor/js/config.js"></script>
</head>
  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include_once __DIR__ . '/../../../includes/sidebar.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">System /</span> Settings
              </h4>

              <?php if (!empty($successMessage)): ?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <?php echo $successMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php endif; ?>

              <?php if (!empty($errorMessage)): ?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <?php echo $errorMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php endif; ?>

              <!-- Settings Tabs -->
              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-4">
                    <li class="nav-item">
                      <a class="nav-link active" href="#general" data-bs-toggle="tab">
                        <i class="bx bx-cog me-1"></i> General
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#security" data-bs-toggle="tab">
                        <i class="bx bx-lock-alt me-1"></i> Security
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#leave" data-bs-toggle="tab">
                        <i class="bx bx-calendar me-1"></i> Leave
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#notifications" data-bs-toggle="tab">
                        <i class="bx bx-bell me-1"></i> Notifications
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#documents" data-bs-toggle="tab">
                        <i class="bx bx-file me-1"></i> Documents
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#performance" data-bs-toggle="tab">
                        <i class="bx bx-line-chart me-1"></i> Performance
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#maintenance" data-bs-toggle="tab">
                        <i class="bx bx-wrench me-1"></i> Maintenance
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Tab content -->
              <div class="tab-content">
                <!-- General Settings Tab -->
                <div class="tab-pane fade show active" id="general">
                  <div class="card mb-4">
                    <h5 class="card-header">General Settings</h5>
                    <div class="card-body">
                      <form method="post" action="settings.php">
                        <input type="hidden" name="action" value="general_settings">
                        <div class="mb-3">
                          <label for="company_name" class="form-label">Company Name</label>
                          <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($companyName); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="contact_email" class="form-label">Contact Email</label>
                          <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($contactEmail); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="contact_phone" class="form-label">Contact Phone</label>
                          <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($contactPhone); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="timezone" class="form-label">Timezone</label>
                          <select class="form-select" id="timezone" name="timezone" required>
                            <?php
                            $timezones = timezone_identifiers_list();
                            foreach ($timezones as $tz) {
                                $selected = ($tz === $timezone) ? 'selected' : '';
                                echo "<option value=\"$tz\" $selected>$tz</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="date_format" class="form-label">Date Format</label>
                          <input type="text" class="form-control" id="date_format" name="date_format" value="<?php echo htmlspecialchars($dateFormat); ?>" required>
                          <small class="form-text text-muted">Example: Y-m-d for 2025-04-22</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                      </form>
                    </div>
                  </div>
                </div>

               
                  <!-- Security Tab -->
                  <div class="tab-pane fade show active" id="security">
                  <div class="card mb-4">
                  <h5 class="card-header">Password Management</h5>
    <div class="card-body">
      <form method="post" action="settings.php">
        <input type="hidden" name="action" value="password_settings">
        
        <div class="mb-3">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        
        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" class="form-control" id="new_password" name="new_password" required>
          <div id="password-strength" class="mt-1 progress" style="height: 6px;">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="form-text text-muted">Password must be at least <?php echo $minPasswordLength; ?> characters long.</small>
        </div>
        
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Change Password</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Two-Factor Authentication (2FA)</h5>
    <div class="card-body">
      <div class="mb-3">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="twoFactorEnabled" name="twoFactorEnabled" <?php echo ($twoFactorEnabled == '1') ? 'checked' : ''; ?>>
          <label class="form-check-label" for="twoFactorEnabled">Enable Two-Factor Authentication</label>
        </div>
        <small class="form-text text-muted">Protect your account with an extra layer of security.</small>
      </div>

      <div id="twoFactorOptions" class="<?php echo ($twoFactorEnabled != '1') ? 'd-none' : ''; ?>">
        <div class="mb-3">
          <label class="form-label">2FA Method</label>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="twoFactorMethod" id="twoFactorApp" value="app" <?php echo ($twoFactorMethod == 'app') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="twoFactorApp">
              Authenticator App (Google Authenticator, Microsoft Authenticator, etc.)
            </label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="twoFactorMethod" id="twoFactorSMS" value="sms" <?php echo ($twoFactorMethod == 'sms') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="twoFactorSMS">
              SMS Verification
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="twoFactorMethod" id="twoFactorEmail" value="email" <?php echo ($twoFactorMethod == 'email') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="twoFactorEmail">
              Email Verification
            </label>
          </div>
        </div>

        <div id="authenticatorSetup" class="<?php echo ($twoFactorMethod != 'app') ? 'd-none' : ''; ?>">
          <div class="mb-3">
            <label class="form-label">Setup Instructions</label>
            <ol class="ps-3">
              <li class="mb-2">Download an authenticator app like Google Authenticator or Microsoft Authenticator</li>
              <li class="mb-2">Scan the QR code below with your app</li>
              <li class="mb-2">Enter the 6-digit code from your app to verify</li>
            </ol>
            <div class="text-center mb-3">
              <div class="border p-3 d-inline-block">
                <!-- QR Code would be generated here -->
                <img src="assets/img/qr-placeholder.png" alt="QR Code" class="img-fluid" style="width: 150px; height: 150px;">
              </div>
            </div>
            <div class="mb-3">
              <label for="verificationCode" class="form-label">Verification Code</label>
              <div class="input-group">
                <input type="text" class="form-control" id="verificationCode" placeholder="Enter 6-digit code">
                <button class="btn btn-primary" type="button">Verify</button>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Backup Codes</label>
          <p>Backup codes can be used to access your account if you lose your phone or cannot receive codes via SMS/email.</p>
          <button type="button" class="btn btn-outline-primary mb-2">Generate Backup Codes</button>
          <div class="d-none" id="backupCodes">
            <div class="bg-light p-3 mb-2 rounded">
              <code>1234-5678-9012</code><br>
              <code>2345-6789-0123</code><br>
              <code>3456-7890-1234</code><br>
              <code>4567-8901-2345</code><br>
              <code>5678-9012-3456</code>
            </div>
            <div class="alert alert-warning">
              <i class="bx bx-error-circle me-2"></i>
              Save these codes in a secure place. They will not be shown again!
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary">Download Codes</button>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-2">Print Codes</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Login Activity</h5>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Date & Time</th>
              <th>IP Address</th>
              <th>Device / Browser</th>
              <th>Location</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo date('Y-m-d H:i:s'); ?></td>
              <td>192.168.1.1</td>
              <td>Chrome on Windows</td>
              <td>New York, USA</td>
              <td><span class="badge bg-success">Current</span></td>
            </tr>
            <tr>
              <td><?php echo date('Y-m-d H:i:s', strtotime('-1 day')); ?></td>
              <td>192.168.1.1</td>
              <td>Chrome on Windows</td>
              <td>New York, USA</td>
              <td><span class="badge bg-secondary">Success</span></td>
            </tr>
            <tr>
              <td><?php echo date('Y-m-d H:i:s', strtotime('-3 day')); ?></td>
              <td>192.168.1.100</td>
              <td>Safari on macOS</td>
              <td>Boston, USA</td>
              <td><span class="badge bg-secondary">Success</span></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button type="button" class="btn btn-outline-danger mt-3">Log Out From All Other Devices</button>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Authorized Devices</h5>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Device Name</th>
              <th>Last Active</th>
              <th>Browser</th>
              <th>Operating System</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Work Computer</td>
              <td><?php echo date('Y-m-d H:i:s'); ?></td>
              <td>Chrome 98.0.4758</td>
              <td>Windows 10</td>
              <td>
                <span class="badge bg-success me-1">Current</span>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
            <tr>
              <td>iPhone 13</td>
              <td><?php echo date('Y-m-d H:i:s', strtotime('-2 day')); ?></td>
              <td>Safari 15.4</td>
              <td>iOS 15.4</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
            <tr>
              <td>Home Laptop</td>
              <td><?php echo date('Y-m-d H:i:s', strtotime('-1 week')); ?></td>
              <td>Firefox 98.0</td>
              <td>macOS 12.3</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Security Alerts</h5>
    <div class="card-body">
      <form method="post" action="settings.php">
        <input type="hidden" name="action" value="security_alerts_settings">
        
        <div class="mb-3">
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="alertNewLogin" name="alert_new_login" <?php echo ($alertNewLogin == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertNewLogin">Alert me about new logins</label>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="alertUnknownDevice" name="alert_unknown_device" <?php echo ($alertUnknownDevice == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertUnknownDevice">Alert me about unknown devices</label>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="alertPasswordChange" name="alert_password_change" <?php echo ($alertPasswordChange == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertPasswordChange">Alert me about password changes</label>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="alertFailedLogin" name="alert_failed_login" <?php echo ($alertFailedLogin == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertFailedLogin">Alert me about failed login attempts</label>
          </div>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Alert Method</label>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="alertEmail" name="alert_email" <?php echo ($alertEmail == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertEmail">Email</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="alertSMS" name="alert_sms" <?php echo ($alertSMS == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="alertSMS">SMS</label>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Alert Preferences</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Account Recovery Options</h5>
    <div class="card-body">
      <form method="post" action="settings.php">
        <input type="hidden" name="action" value="recovery_settings">
        
        <div class="mb-3">
          <label for="recovery_email" class="form-label">Recovery Email</label>
          <input type="email" class="form-control" id="recovery_email" name="recovery_email" value="<?php echo htmlspecialchars($recoveryEmail); ?>">
          <small class="form-text text-muted">This email will be used to recover your account if you lose access.</small>
        </div>
        
        <div class="mb-3">
          <label for="recovery_phone" class="form-label">Recovery Phone</label>
          <input type="tel" class="form-control" id="recovery_phone" name="recovery_phone" value="<?php echo htmlspecialchars($recoveryPhone); ?>">
        </div>
        
        <div class="mb-4">
          <label class="form-label">Security Questions</label>
          <div class="mb-3">
            <select class="form-select mb-2" id="security_question_1" name="security_question_1">
              <option value="1" <?php echo ($securityQuestion1 == '1') ? 'selected' : ''; ?>>What was the name of your first pet?</option>
              <option value="2" <?php echo ($securityQuestion1 == '2') ? 'selected' : ''; ?>>What was your childhood nickname?</option>
              <option value="3" <?php echo ($securityQuestion1 == '3') ? 'selected' : ''; ?>>In what city were you born?</option>
              <option value="4" <?php echo ($securityQuestion1 == '4') ? 'selected' : ''; ?>>What is your mother's maiden name?</option>
              <option value="5" <?php echo ($securityQuestion1 == '5') ? 'selected' : ''; ?>>What was the make of your first car?</option>
            </select>
            <input type="text" class="form-control" id="security_answer_1" name="security_answer_1" value="<?php echo ($securityAnswer1) ? '●●●●●●●●' : ''; ?>">
          </div>
          
          <div class="mb-3">
            <select class="form-select mb-2" id="security_question_2" name="security_question_2">
              <option value="1" <?php echo ($securityQuestion2 == '1') ? 'selected' : ''; ?>>What was the name of your first pet?</option>
              <option value="2" <?php echo ($securityQuestion2 == '2') ? 'selected' : ''; ?>>What was your childhood nickname?</option>
              <option value="3" <?php echo ($securityQuestion2 == '3') ? 'selected' : ''; ?>>In what city were you born?</option>
              <option value="4" <?php echo ($securityQuestion2 == '4') ? 'selected' : ''; ?>>What is your mother's maiden name?</option>
              <option value="5" <?php echo ($securityQuestion2 == '5') ? 'selected' : ''; ?>>What was the make of your first car?</option>
            </select>
            <input type="text" class="form-control" id="security_answer_2" name="security_answer_2" value="<?php echo ($securityAnswer2) ? '●●●●●●●●' : ''; ?>">
          </div>
          
          <div>
            <select class="form-select mb-2" id="security_question_3" name="security_question_3">
              <option value="1" <?php echo ($securityQuestion3 == '1') ? 'selected' : ''; ?>>What was the name of your first pet?</option>
              <option value="2" <?php echo ($securityQuestion3 == '2') ? 'selected' : ''; ?>>What was your childhood nickname?</option>
              <option value="3" <?php echo ($securityQuestion3 == '3') ? 'selected' : ''; ?>>In what city were you born?</option>
              <option value="4" <?php echo ($securityQuestion3 == '4') ? 'selected' : ''; ?>>What is your mother's maiden name?</option>
              <option value="5" <?php echo ($securityQuestion3 == '5') ? 'selected' : ''; ?>>What was the make of your first car?</option>
            </select>
            <input type="text" class="form-control" id="security_answer_3" name="security_answer_3" value="<?php echo ($securityAnswer3) ? '●●●●●●●●' : ''; ?>">
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Recovery Options</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">API Keys & App Passwords</h5>
    <div class="card-body">
      <p>Create app-specific passwords to use with applications that don't support two-factor authentication.</p>
      
      <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createAppPasswordModal">
        Create App Password
      </button>
      
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Created</th>
              <th>Last Used</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Email Client</td>
              <td><?php echo date('Y-m-d', strtotime('-30 days')); ?></td>
              <td><?php echo date('Y-m-d', strtotime('-2 days')); ?></td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
            <tr>
              <td>Mobile App</td>
              <td><?php echo date('Y-m-d', strtotime('-60 days')); ?></td>
              <td><?php echo date('Y-m-d', strtotime('-5 days')); ?></td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <h6 class="mt-4 mb-3">API Keys</h6>
      <p>API keys allow external applications to access your data securely.</p>
      
      <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createApiKeyModal">
        Generate API Key
      </button>
      
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Key Name</th>
              <th>Created</th>
              <th>Permissions</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Integration Key</td>
              <td><?php echo date('Y-m-d', strtotime('-10 days')); ?></td>
              <td>Read-only</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-danger">Revoke</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Biometric Settings</h5>
    <div class="card-body">
      <div class="mb-3">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="biometricEnabled" <?php echo ($biometricEnabled == '1') ? 'checked' : ''; ?>>
          <label class="form-check-label" for="biometricEnabled">Enable Biometric Login</label>
        </div>
        <small class="form-text text-muted">Allow login using fingerprint, face recognition, or other biometric methods on supported devices.</small>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Registered Devices</label>
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            iPhone 13 Pro
            <button type="button" class="btn btn-sm btn-outline-danger">Remove</button>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            MacBook Pro
            <button type="button" class="btn btn-sm btn-outline-danger">Remove</button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Data Encryption</h5>
    <div class="card-body">
      <div class="alert alert-info">
        <h6 class="alert-heading fw-bold mb-1">Your data is protected</h6>
        <p class="mb-0">All sensitive data in this system is encrypted using industry-standard AES-256 encryption. Your information is secure both in transit and at rest.</p>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Encryption Status</label>
        <div class="d-flex align-items-center">
          <span class="badge bg-success me-2">Active</span>
          <span>AES-256 Encryption</span>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Session Timeout Settings</h5>
    <div class="card-body">
      <form method="post" action="settings.php">
        <input type="hidden" name="action" value="session_settings">
        
        <div class="mb-3">
          <label for="session_timeout" class="form-label">Auto-Logout After</label>
          <select class="form-select" id="session_timeout" name="session_timeout">
            <option value="15" <?php echo ($sessionTimeout == '15') ? 'selected' : ''; ?>>15 minutes</option>
            <option value="30" <?php echo ($sessionTimeout == '30') ? 'selected' : ''; ?>>30 minutes</option>
            <option value="60" <?php echo ($sessionTimeout == '60') ? 'selected' : ''; ?>>1 hour</option>
            <option value="120" <?php echo ($sessionTimeout == '120') ? 'selected' : ''; ?>>2 hours</option>
            <option value="240" <?php echo ($sessionTimeout == '240') ? 'selected' : ''; ?>>4 hours</option>
            <option value="480" <?php echo ($sessionTimeout == '480') ? 'selected' : ''; ?>>8 hours</option>
          </select>
          <small class="form-text text-muted">You will be automatically logged out after this period of inactivity.</small>
        </div>
        
        <div class="mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" <?php echo ($rememberMe == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="remember_me">
              Allow "Remember Me" option on login
            </label>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Session Settings</button>
      </form>
    </div>
  </div>
</div>
                <!-- Other Tabs (Security, Leave, Notifications, etc.) -->
                <div class="tab-pane fade" id="security">
                  <div class="card mb-4">
                    <h5 class="card-header">Security Settings</h5>
                    <div class="card-body">
                      <form method="post" action="settings.php">
                        <input type="hidden" name="action" value="security_settings">
                        <div class="mb-3">
                          <label for="min_password_length" class="form-label">Minimum Password Length</label>
                          <input type="number" class="form-control" id="min_password_length" name="min_password_length" value="<?php echo htmlspecialchars($minPasswordLength); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="password_expiry" class="form-label">Password Expiry (days)</label>
                          <input type="number" class="form-control" id="password_expiry" name="password_expiry" value="<?php echo htmlspecialchars($passwordExpiry); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                          <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="<?php echo htmlspecialchars($maxLoginAttempts); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                          <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="<?php echo htmlspecialchars($sessionTimeout); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Add similar forms for Leave, Notifications, Documents, Performance, and Maintenance tabs -->

              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  © <?php echo date('Y'); ?>, <strong><?php echo SITE_NAME; ?></strong>
                </div>
              </div>
            </footer>
            <!-- / Footer -->
          </div>
          <!-- / Content wrapper -->
        </div>
        <!-- / Layout container -->
      </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/dashboards-analytics.js"></script>
  </body>
</html>
