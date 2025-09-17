<?php
// Include necessary files
include_once '../../../config/database.php';
include_once '../../../includes/functions.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize $user_role
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // Default to 'guest' if not set
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

    <title>Staff Management System - Dashboard</title>

    <meta name="description" content="Staff Management System" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../../../assets/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../../assets/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="../../../assets/css/core.css" />
    <link rel="stylesheet" href="../../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/fonts/flag-icons.css" />
    <link rel="stylesheet" href="assets/libs/apex-charts/apex-charts.css" />

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
        <aside id="layout-menu" class="layout-menu menu-vertical menu">
          <div class="app-brand demo">
            <a href="dashboard.php" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg
                    width="25"
                    viewBox="0 0 25 42"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                      <path
                        d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                        id="path-1"></path>
                      <path
                        d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                        id="path-3"></path>
                      <path
                        d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                        id="path-4"></path>
                      <path
                        d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                        id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                          <g id="Mask" transform="translate(0.000000, 8.000000)">
                            <mask id="mask-2" fill="white">
                              <use xlink:href="#path-1"></use>
                            </mask>
                            <use fill="currentColor" xlink:href="#path-1"></use>
                            <g id="Path-3" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-3"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                            </g>
                            <g id="Path-4" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-4"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                            </g>
                          </g>
                          <g
                            id="Triangle"
                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                            <use fill="currentColor" xlink:href="#path-5"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                          </g>
                        </g>
                      </g>
                    </g>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-bold ms-2">Staff MS</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="icon-base bx bx-chevron-left"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="index.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-home-smile"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>

            <!-- User Management -->
            <li class="menu-item">
              <a href="user_management.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-user"></i>
                <div data-i18n="User Management">User Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="user_management.php" class="menu-link">
                    <div data-i18n="All Users">All Users</div>
                  </a>
                </li>
                <?php if ($user_role == 'admin'): ?>
                <li class="menu-item">
                  <a href="../users/add.php" class="menu-link">
                    <div data-i18n="Add User">Add User</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../users/list.php" class="menu-link">
                    <div data-i18n="User List">User List</div>
                  </a>
                </li>
                <?php endif; ?>
              </ul>
            </li>

            <!-- Recruitment & Onboarding -->
            <li class="menu-item">
              <a href="recruitment.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-briefcase"></i>
                <div data-i18n="Recruitment">Recruitment & Onboarding</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="recruitment.php" class="menu-link">
                    <div data-i18n="Job Postings">Job Postings</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../recruitment/job-postings.php" class="menu-link">
                    <div data-i18n="Manage Jobs">Manage Jobs</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Employee Profiles -->
            <li class="menu-item">
              <a href="../employees/list.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-id-card"></i>
                <div data-i18n="Employee Profiles">Employee Profiles</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../employees/list.php" class="menu-link">
                    <div data-i18n="All Employees">All Employees</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../employees/add.php" class="menu-link">
                    <div data-i18n="Add Employee">Add Employee</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../employees/employment-history.php" class="menu-link">
                    <div data-i18n="Employment History">Employment History</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../employees/family-info.php" class="menu-link">
                    <div data-i18n="Family Information">Family Information</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Document Management -->
            <li class="menu-item">
              <a href="../documents/contracts.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-file"></i>
                <div data-i18n="Document Management">Document Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../documents/contracts.php" class="menu-link">
                    <div data-i18n="Contracts">Contracts</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../documents/certifications.php" class="menu-link">
                    <div data-i18n="Certifications">Certifications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../documents/licenses.php" class="menu-link">
                    <div data-i18n="Licenses">Licenses</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../documents/performance-reviews.php" class="menu-link">
                    <div data-i18n="Performance Reviews">Performance Reviews</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Leave Management -->
            <li class="menu-item">
              <a href="../leave/apply.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-calendar"></i>
                <div data-i18n="Leave Management">Leave Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../leave/apply.php" class="menu-link">
                    <div data-i18n="Apply for Leave">Apply for Leave</div>
                  </a>
                </li>
                <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
                <li class="menu-item">
                  <a href="../leave/approve.php" class="menu-link">
                    <div data-i18n="Approve/Reject Leave">Approve/Reject Leave</div>
                  </a>
                </li>
                <?php endif; ?>
                <li class="menu-item">
                  <a href="../leave/balance.php" class="menu-link">
                    <div data-i18n="Leave Balance">Leave Balance</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Staff Promotion -->
            <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
            <li class="menu-item">
              <a href="../promotion/promote.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-trending-up"></i>
                <div data-i18n="Staff Promotion">Staff Promotion</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../promotion/promote.php" class="menu-link">
                    <div data-i18n="Promote Staff">Promote Staff</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../promotion/history.php" class="menu-link">
                    <div data-i18n="Promotion History">Promotion History</div>
                  </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Research Publications & Conferences -->
            <li class="menu-item">
              <a href="../research/publications.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-book"></i>
                <div data-i18n="Research & Publications">Research & Publications</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../research/publications.php" class="menu-link">
                    <div data-i18n="Publications">Publications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../research/conferences.php" class="menu-link">
                    <div data-i18n="Conferences">Conferences</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Self-Service -->
            <li class="menu-item">
              <a href="../self-service/profile.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-cog"></i>
                <div data-i18n="Self-Service">Self-Service</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../self-service/profile.php" class="menu-link">
                    <div data-i18n="Update Profile">Update Profile</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../self-service/payslips.php" class="menu-link">
                    <div data-i18n="View Payslips">View Payslips</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../self-service/leave-request.php" class="menu-link">
                    <div data-i18n="Leave Requests">Leave Requests</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Training & Professional Development -->
            <li class="menu-item">
              <a href="../training/requirements.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-certification"></i>
                <div data-i18n="Training">Training & Development</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../training/requirements.php" class="menu-link">
                    <div data-i18n="Training Requirements">Training Requirements</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../training/progress.php" class="menu-link">
                    <div data-i18n="Training Progress">Training Progress</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Performance Management -->
            <li class="menu-item">
              <a href="../performance/reviews.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-line-chart"></i>
                <div data-i18n="Performance">Performance Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../performance/reviews.php" class="menu-link">
                    <div data-i18n="Performance Reviews">Performance Reviews</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../performance/progress.php" class="menu-link">
                    <div data-i18n="Performance Progress">Performance Progress</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Alerts & Notifications -->
            <?php if ($user_role == 'admin'): ?>
            <li class="menu-item">
              <a href="../alerts/notifications.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-bell"></i>
                <div data-i18n="Alerts & Notifications">Alerts & Notifications</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../alerts/notifications.php" class="menu-link">
                    <div data-i18n="All Notifications">All Notifications</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../alerts/performance.php" class="menu-link">
                    <div data-i18n="Performance Reviews">Performance Reviews</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../alerts/certifications.php" class="menu-link">
                    <div data-i18n="Certification Renewals">Certification Renewals</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../alerts/training.php" class="menu-link">
                    <div data-i18n="Training Requirements">Training Requirements</div>
                  </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Reports -->
            <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
            <li class="menu-item">
              <a href="../reports/employee.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-bar-chart"></i>
                <div data-i18n="Reports">Reports</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="../reports/employee.php" class="menu-link">
                    <div data-i18n="Employee Reports">Employee Reports</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../reports/leave.php" class="menu-link">
                    <div data-i18n="Leave Reports">Leave Reports</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="../reports/performance.php" class="menu-link">
                    <div data-i18n="Performance Reports">Performance Reports</div>
                  </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Settings -->
            <?php if ($user_role == 'admin'): ?>
            <li class="menu-item">
              <a href="settings.php" class="menu-link">
                <i class="menu-icon icon-base bx bx-cog"></i>
                <div data-i18n="Settings">Settings</div>
              </a>
            </li>
            <?php endif; ?>
          </ul>
        </aside>
        <!-- / Menu -->

        <div class="menu-mobile-toggler d-xl-none rounded-1">
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
            <i class="bx bx-menu icon-base"></i>
            <i class="bx bx-chevron-right icon-base"></i>
          </a>
        </div>

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <nav
            class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base bx bx-menu icon-md"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item navbar-search-wrapper mb-0">
                  <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                    <i class="icon-base bx bx-search"></i>
                    <span class="d-inline-block text-body-secondary fw-normal">Search</span>
                  </a>
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                  <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <span class="position-relative">
                      <i class="icon-base bx bx-bell icon-md"></i>
                      <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end p-0">
                    <li class="dropdown-menu-header border-bottom">
                      <div class="dropdown-header d-flex align-items-center py-3">
                        <h6 class="mb-0 me-auto">Notifications</h6>
                        <div class="d-flex align-items-center h6 mb-0">
                          <span class="badge bg-label-primary me-2">New</span>
                          <a
                            href="javascript:void(0)"
                            class="dropdown-notifications-all p-2"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Mark all as read"
                            ><i class="icon-base bx bx-envelope-open text-heading"></i
                          ></a>
                        </div>
                      </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                      <ul class="list-group list-group-flush">
                        <!-- Notification items will be dynamically loaded here -->
                        <?php
                        // Example notification items
                        $notifications = [
                            [
                                'title' => 'Leave Request Approved',
                                'message' => 'Your leave request has been approved',
                                'time' => '1h ago'
                            ],
                            [
                                'title' => 'Performance Review Due',
                                'message' => 'Your annual performance review is due next week',
                                'time' => '2d ago'
                            ]
                        ];
                        
                        foreach ($notifications as $notification):
                        ?>
                        <li class="list-group-item list-group-item-action dropdown-notifications-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                  <i class="icon-base bx bx-envelope"></i>
                                </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="small mb-0"><?php echo $notification['title']; ?></h6>
                              <small class="mb-1 d-block text-body"><?php echo $notification['message']; ?></small>
                              <small class="text-body-secondary"><?php echo $notification['time']; ?></small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                              <a href="javascript:void(0)" class="dropdown-notifications-read"
                                ><span class="badge badge-dot"></span
                              ></a>
                              <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                ><span class="icon-base bx bx-x"></span
                              ></a>
                            </div>
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
                <!--/ Notifications -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="assets/img/avatars/1.png" alt class="rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="self-service/profile.php">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-0"><?php echo $_SESSION['username']; ?></h6>
                            <small class="text-body-secondary"><?php echo $_SESSION['role']; ?></small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="self-service/profile.php">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="self-service/settings.php">
                        <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Content goes here -->
              <div class="row">
                <div class="col-xxl-8 mb-6 order-0">
                  <div class="card">
                    <div class="d-flex align-items-start row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary mb-3">Welcome <?php echo $_SESSION['username']; ?>! 🎉</h5>
                          <p class="mb-6">
                            You have <?php echo rand(2, 10); ?> pending tasks today.<br />Check your dashboard for updates.
                          </p>

                          <a href="tasks/pending.php" class="btn btn-sm btn-label-primary">View Tasks</a>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-6">
                          <img
                            src="assets/img/illustrations/man-with-laptop.png"
                            height="175"
                            class="scaleX-n1-rtl"
                            alt="View Badge User" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                      <div class="card h-100">
                        <div class="card-body pb-4">
                          <span class="d-block fw-medium mb-1">Total Staff</span>
                          <h4 class="card-title mb-0">
                            <?php
                            $staffQuery = "SELECT COUNT(*) as total FROM employee_profiles WHERE employment_status = 'Active'";
                            $staffResult = mysqli_query($conn, $staffQuery);
                            $staffCount = mysqli_fetch_assoc($staffResult)['total'];
                            echo $staffCount;
                            ?>
                          </h4>
                        </div>
                        <div class="pb-3 pe-1">
                          <a href="../employees/list.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between mb-4">
                            <div class="avatar flex-shrink-0">
                              <i class="icon-base bx bx-calendar text-primary"></i>
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt6"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                <a class="dropdown-item" href="../leave/apply.php">View More</a>
                                <a class="dropdown-item" href="../leave/balance.php">Leave Balance</a>
                              </div>
                            </div>
                          </div>
                          <p class="mb-1">Pending Leave Requests</p>
                          <h4 class="card-title mb-3">
                            <?php
                            $leaveQuery = "SELECT COUNT(*) as total FROM leave_requests WHERE status = 'Pending'";
                            $leaveResult = mysqli_query($conn, $leaveQuery);
                            $leaveCount = mysqli_fetch_assoc($leaveResult)['total'];
                            echo $leaveCount;
                            ?>
                          </h4>
                          <small class="text-success fw-medium">
                            <i class="icon-base bx bx-up-arrow-alt"></i> Active Requests
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Additional Stats -->
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                              <i class="icon-base bx bx-briefcase text-info"></i>
                            </div>
                            <div>
                              <span class="d-block fw-medium mb-1">Job Postings</span>
                              <h4 class="card-title mb-0">
                                <?php
                                $jobsQuery = "SELECT COUNT(*) as total FROM job_postings WHERE status = 'Open'";
                                $jobsResult = mysqli_query($conn, $jobsQuery);
                                $jobsCount = mysqli_fetch_assoc($jobsResult)['total'];
                                echo $jobsCount;
                                ?>
                              </h4>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                              <i class="icon-base bx bx-user-check text-success"></i>
                            </div>
                            <div>
                              <span class="d-block fw-medium mb-1">New Hires</span>
                              <h4 class="card-title mb-0">
                                <?php
                                $hiresQuery = "SELECT COUNT(*) as total FROM employee_profiles WHERE hire_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                                $hiresResult = mysqli_query($conn, $hiresQuery);
                                $hiresCount = mysqli_fetch_assoc($hiresResult)['total'];
                                echo $hiresCount;
                                ?>
                              </h4>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Recent Activities -->
              <div class="row">
                <div class="col-lg-8 mb-6">
                  <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">Recent Activities</h5>
                      <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                      <div class="timeline">
                        <?php
                        // Get recent activities
                        $activities = [
                            ['icon' => 'bx-user-plus', 'title' => 'New Employee Added', 'description' => 'John Doe joined the Marketing team', 'time' => '2 hours ago', 'color' => 'success'],
                            ['icon' => 'bx-calendar-check', 'title' => 'Leave Request Approved', 'description' => 'Jane Smith\'s vacation request was approved', 'time' => '4 hours ago', 'color' => 'info'],
                            ['icon' => 'bx-briefcase', 'title' => 'Job Posting Created', 'description' => 'New position: Senior Developer', 'time' => '1 day ago', 'color' => 'primary'],
                            ['icon' => 'bx-trending-up', 'title' => 'Performance Review', 'description' => 'Mike Johnson completed his quarterly review', 'time' => '2 days ago', 'color' => 'warning'],
                            ['icon' => 'bx-certification', 'title' => 'Training Completed', 'description' => 'Sarah Wilson finished Leadership Training', 'time' => '3 days ago', 'color' => 'success']
                        ];
                        
                        foreach ($activities as $activity):
                        ?>
                        <div class="timeline-item">
                          <div class="timeline-marker bg-<?php echo $activity['color']; ?>"></div>
                          <div class="timeline-content">
                            <div class="d-flex align-items-center">
                              <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-<?php echo $activity['color']; ?>">
                                  <i class="icon-base <?php echo $activity['icon']; ?>"></i>
                                </span>
                              </div>
                              <div class="flex-grow-1">
                                <h6 class="mb-0"><?php echo $activity['title']; ?></h6>
                                <p class="text-muted mb-0"><?php echo $activity['description']; ?></p>
                              </div>
                              <small class="text-muted"><?php echo $activity['time']; ?></small>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="col-lg-4 mb-6">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                      <div class="d-grid gap-2">
                        <?php if (canManageEmployees()): ?>
                        <a href="../employees/add.php" class="btn btn-primary">
                          <i class="icon-base bx bx-user-plus me-2"></i>Add Employee
                        </a>
                        <a href="../leave/approve.php" class="btn btn-warning">
                          <i class="icon-base bx bx-calendar-check me-2"></i>Approve Leave
                        </a>
                        <a href="../promotion/promote.php" class="btn btn-success">
                          <i class="icon-base bx bx-trending-up me-2"></i>Promote Staff
                        </a>
                        <?php endif; ?>
                        <a href="../self-service/profile.php" class="btn btn-outline-primary">
                          <i class="icon-base bx bx-user me-2"></i>My Profile
                        </a>
                        <a href="../leave/apply.php" class="btn btn-outline-info">
                          <i class="icon-base bx bx-calendar me-2"></i>Apply Leave
                        </a>
                        <a href="../self-service/payslips.php" class="btn btn-outline-secondary">
                          <i class="icon-base bx bx-receipt me-2"></i>View Payslips
                        </a>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upcoming Events -->
                  <div class="card mt-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">Upcoming Events</h5>
                    </div>
                    <div class="card-body">
                      <?php
                      $events = [
                          ['title' => 'Team Meeting', 'date' => 'Today, 2:00 PM', 'type' => 'meeting'],
                          ['title' => 'Performance Review', 'date' => 'Tomorrow, 10:00 AM', 'type' => 'review'],
                          ['title' => 'Training Session', 'date' => 'Friday, 3:00 PM', 'type' => 'training'],
                          ['title' => 'Company Event', 'date' => 'Next Monday', 'type' => 'event']
                      ];
                      
                      foreach ($events as $event):
                      ?>
                      <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                          <span class="avatar-initial rounded-circle bg-<?php echo $event['type'] == 'meeting' ? 'primary' : ($event['type'] == 'review' ? 'warning' : ($event['type'] == 'training' ? 'info' : 'success')); ?>">
                            <i class="icon-base bx bx-<?php echo $event['type'] == 'meeting' ? 'calendar' : ($event['type'] == 'review' ? 'line-chart' : ($event['type'] == 'training' ? 'certification' : 'party')); ?>"></i>
                          </span>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0"><?php echo $event['title']; ?></h6>
                          <small class="text-muted"><?php echo $event['date']; ?></small>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Department Overview -->
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">Department Overview</h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <?php
                        // Get department statistics
                        $deptQuery = "SELECT d.department_name, COUNT(ep.employee_id) as employee_count 
                                      FROM departments d 
                                      LEFT JOIN employee_profiles ep ON d.department_id = ep.department_id 
                                      GROUP BY d.department_id, d.department_name 
                                      ORDER BY employee_count DESC";
                        $deptResult = mysqli_query($conn, $deptQuery);
                        $departments = mysqli_fetch_all($deptResult, MYSQLI_ASSOC);
                        
                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                        $colorIndex = 0;
                        
                        foreach ($departments as $dept):
                        ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                          <div class="card border-<?php echo $colors[$colorIndex % count($colors)]; ?>">
                            <div class="card-body text-center">
                              <div class="avatar avatar-lg mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-<?php echo $colors[$colorIndex % count($colors)]; ?>">
                                  <i class="icon-base bx bx-buildings"></i>
                                </span>
                              </div>
                              <h5 class="card-title"><?php echo $dept['department_name']; ?></h5>
                              <h3 class="text-<?php echo $colors[$colorIndex % count($colors)]; ?>"><?php echo $dept['employee_count']; ?></h3>
                              <p class="text-muted mb-0">Employees</p>
                            </div>
                          </div>
                        </div>
                        <?php 
                        $colorIndex++;
                        endforeach; 
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                  <div class="mb-2 mb-md-0">
                    ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    , Staff Management System
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="assets/vendor/libs/pickr/pickr.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/dashboards-analytics.js"></script>
    
    <style>
      .timeline {
        position: relative;
        padding-left: 30px;
      }
      .timeline-item {
        position: relative;
        margin-bottom: 30px;
      }
      .timeline-marker {
        position: absolute;
        left: -35px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
      }
      .timeline-content {
        margin-left: 20px;
      }
      .timeline::before {
        content: '';
        position: absolute;
        left: -29px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
      }
    </style>
  </body>
</html>
