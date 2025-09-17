<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo SITE_NAME; ?> - <?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <?php if (isset($pageCss) && is_array($pageCss)): ?>
        <?php foreach ($pageCss as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $css; ?>" />
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Helpers -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/js/helpers.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/config.js"></script>
    
    <style>
        /* Custom styles for collapsible sidebar */
        body.sidebar-collapsed .layout-menu {
            margin-left: -16.25rem;
        }
        
        body.sidebar-collapsed .layout-page {
            padding-left: 0 !important;
        }
        
        .layout-menu {
            transition: margin-left 0.3s ease;
        }
        
        .layout-page {
            transition: padding-left 0.3s ease;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        @media (max-width: 1199.98px) {
            .layout-menu {
                margin-left: -16.25rem;
            }
            
            body.sidebar-mobile-show .layout-menu {
                margin-left: 0;
            }
            
            body.sidebar-mobile-show .sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar overlay -->
    <div class="sidebar-overlay"></div>
    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Include sidebar -->
            <?php include '../../includes/sidebar.php'; ?>
            
            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" id="mobile-toggle">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Toggle sidebar button -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <a href="javascript:void(0);" class="nav-link" id="sidebar-toggle">
                                    <i class="bx bx-menu bx-sm"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search...">
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo BASE_URL; ?>assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo BASE_URL; ?>assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></span>
                                                    <small class="text-muted"><?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Role'; ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="profile.php">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="settings.php">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->