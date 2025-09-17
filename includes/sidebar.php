<?php
// Ensure global config is loaded so constants like SITE_NAME are defined
if (!defined('SITE_NAME')) {
	require_once __DIR__ . '/../config/config.php';
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                        <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="path-3"></path>
                        <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="path-4"></path>
                        <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z" id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                            <g id="Icon" transform="translate(27.000000, 15.000000)">
                                <g id="Mask" transform="translate(0.000000, 8.000000)">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <use fill="#696cff" xlink:href="#path-1"></use>
                                    <g id="Path-3" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-3"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                    </g>
                                    <g id="Path-4" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-4"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                    </g>
                                </g>
                                <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                    <use fill="#696cff" xlink:href="#path-5"></use>
                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2"><?php echo SITE_NAME; ?></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <a href="index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- User Management -->
        <li class="menu-item <?php echo $current_page == 'user_management.php' ? 'active' : ''; ?>">
            <a href="user_management.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="User Management">User Management</div>
            </a>
        </li>

        <!-- Recruitment & Onboarding -->
        <li class="menu-item <?php echo in_array($current_page, ['recruitment.php', 'job_postings.php', 'applicants.php', 'interviews.php']) ? 'active open' : ''; ?>">
            <a href="recruitment.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-briefcase"></i>
                <div data-i18n="Recruitment">Recruitment & Onboarding</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'job_postings.php' ? 'active' : ''; ?>">
                    <a href="job_postings.php" class="menu-link">
                        <div data-i18n="Job Postings">Job Postings</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'applicants.php' ? 'active' : ''; ?>">
                    <a href="applicants.php" class="menu-link">
                        <div data-i18n="Applicants">Applicants</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'interviews.php' ? 'active' : ''; ?>">
                    <a href="interviews.php" class="menu-link">
                        <div data-i18n="Interviews">Interviews</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Employee Profiles -->
        <li class="menu-item <?php echo in_array($current_page, ['employee_profiles.php', 'personal_info.php', 'employment_history.php', 'family_info.php']) ? 'active open' : ''; ?>">
            <a href="employee_profiles.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div data-i18n="Employee Profiles">Employee Profiles</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'employee_profiles.php' ? 'active' : ''; ?>">
                    <a href="employee_profiles.php" class="menu-link">
                        <div data-i18n="All Profiles">All Profiles</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'personal_info.php' ? 'active' : ''; ?>">
                    <a href="personal_info.php" class="menu-link">
                        <div data-i18n="Personal Info">Personal Info</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'employment_history.php' ? 'active' : ''; ?>">
                    <a href="employment_history.php" class="menu-link">
                        <div data-i18n="Employment History">Employment History</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'family_info.php' ? 'active' : ''; ?>">
                    <a href="family_info.php" class="menu-link">
                        <div data-i18n="Family Info">Family Info</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Document Management -->
        <li class="menu-item <?php echo in_array($current_page, ['documents.php', 'contracts.php', 'certifications.php', 'licenses.php', 'performance_reviews.php']) ? 'active open' : ''; ?>">
            <a href="documents.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Document Management">Document Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'documents.php' ? 'active' : ''; ?>">
                    <a href="documents.php" class="menu-link">
                        <div data-i18n="All Documents">All Documents</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'contracts.php' ? 'active' : ''; ?>">
                    <a href="contracts.php" class="menu-link">
                        <div data-i18n="Contracts">Contracts</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'certifications.php' ? 'active' : ''; ?>">
                    <a href="certifications.php" class="menu-link">
                        <div data-i18n="Certifications">Certifications</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'licenses.php' ? 'active' : ''; ?>">
                    <a href="licenses.php" class="menu-link">
                        <div data-i18n="Licenses">Licenses</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'performance_reviews.php' ? 'active' : ''; ?>">
                    <a href="performance_reviews.php" class="menu-link">
                        <div data-i18n="Performance Reviews">Performance Reviews</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Leave Management -->
        <li class="menu-item <?php echo in_array($current_page, ['leave_management.php', 'leave_apply.php', 'leave_approve.php', 'leave_balance.php']) ? 'active open' : ''; ?>">
            <a href="leave_management.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Leave Management">Leave Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'leave_management.php' ? 'active' : ''; ?>">
                    <a href="leave_management.php" class="menu-link">
                        <div data-i18n="All Leaves">All Leaves</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'leave_apply.php' ? 'active' : ''; ?>">
                    <a href="leave_apply.php" class="menu-link">
                        <div data-i18n="Apply Leave">Apply Leave</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'leave_approve.php' ? 'active' : ''; ?>">
                    <a href="leave_approve.php" class="menu-link">
                        <div data-i18n="Approve Leave">Approve Leave</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'leave_balance.php' ? 'active' : ''; ?>">
                    <a href="leave_balance.php" class="menu-link">
                        <div data-i18n="Leave Balance">Leave Balance</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Staff Promotion -->
        <li class="menu-item <?php echo in_array($current_page, ['promotion.php', 'promotion_history.php']) ? 'active open' : ''; ?>">
            <a href="promotion.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-trending-up"></i>
                <div data-i18n="Staff Promotion">Staff Promotion</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'promotion.php' ? 'active' : ''; ?>">
                    <a href="promotion.php" class="menu-link">
                        <div data-i18n="Promote Staff">Promote Staff</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'promotion_history.php' ? 'active' : ''; ?>">
                    <a href="promotion_history.php" class="menu-link">
                        <div data-i18n="Promotion History">Promotion History</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Research Publications & Conferences -->
        <li class="menu-item <?php echo in_array($current_page, ['research.php', 'publications.php', 'conferences.php']) ? 'active open' : ''; ?>">
            <a href="research.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Research">Research & Conferences</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'research.php' ? 'active' : ''; ?>">
                    <a href="research.php" class="menu-link">
                        <div data-i18n="Research Overview">Research Overview</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'publications.php' ? 'active' : ''; ?>">
                    <a href="publications.php" class="menu-link">
                        <div data-i18n="Publications">Publications</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'conferences.php' ? 'active' : ''; ?>">
                    <a href="conferences.php" class="menu-link">
                        <div data-i18n="Conferences">Conferences</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Self-Service -->
        <li class="menu-item <?php echo in_array($current_page, ['self_service.php', 'profile_update.php', 'payslips.php', 'leave_request.php']) ? 'active open' : ''; ?>">
            <a href="self_service.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-check"></i>
                <div data-i18n="Self-Service">Self-Service</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'self_service.php' ? 'active' : ''; ?>">
                    <a href="self_service.php" class="menu-link">
                        <div data-i18n="Self-Service Portal">Self-Service Portal</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'profile_update.php' ? 'active' : ''; ?>">
                    <a href="profile_update.php" class="menu-link">
                        <div data-i18n="Update Profile">Update Profile</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'payslips.php' ? 'active' : ''; ?>">
                    <a href="payslips.php" class="menu-link">
                        <div data-i18n="View Payslips">View Payslips</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'leave_request.php' ? 'active' : ''; ?>">
                    <a href="leave_request.php" class="menu-link">
                        <div data-i18n="Request Leave">Request Leave</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Training & Professional Development -->
        <li class="menu-item <?php echo in_array($current_page, ['training.php', 'training_requirements.php', 'training_progress.php']) ? 'active open' : ''; ?>">
            <a href="training.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-graduation-cap"></i>
                <div data-i18n="Training">Training & Development</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'training.php' ? 'active' : ''; ?>">
                    <a href="training.php" class="menu-link">
                        <div data-i18n="Training Overview">Training Overview</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'training_requirements.php' ? 'active' : ''; ?>">
                    <a href="training_requirements.php" class="menu-link">
                        <div data-i18n="Requirements">Requirements</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'training_progress.php' ? 'active' : ''; ?>">
                    <a href="training_progress.php" class="menu-link">
                        <div data-i18n="Progress Tracking">Progress Tracking</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Performance Management -->
        <li class="menu-item <?php echo in_array($current_page, ['performance.php', 'performance_reviews.php', 'performance_progress.php']) ? 'active open' : ''; ?>">
            <a href="performance.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-target-lock"></i>
                <div data-i18n="Performance">Performance Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo $current_page == 'performance.php' ? 'active' : ''; ?>">
                    <a href="performance.php" class="menu-link">
                        <div data-i18n="Performance Overview">Performance Overview</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'performance_reviews.php' ? 'active' : ''; ?>">
                    <a href="performance_reviews.php" class="menu-link">
                        <div data-i18n="Reviews">Reviews</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $current_page == 'performance_progress.php' ? 'active' : ''; ?>">
                    <a href="performance_progress.php" class="menu-link">
                        <div data-i18n="Progress Tracking">Progress Tracking</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Alerts & Notifications -->
        <li class="menu-item <?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>">
            <a href="notifications.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bell"></i>
                <div data-i18n="Notifications">Alerts & Notifications</div>
            </a>
        </li>

        <!-- Settings -->
        <li class="menu-item <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
            <a href="settings.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->