<?php
// Include necessary files
include_once '../../config/database.php';
include_once '../../includes/functions.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Initialize $user_role
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
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

    <title>Staff Management System - Leave Management</title>

    <meta name="description" content="Staff Management System" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/vendor/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../../../assets/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="../../assets/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

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
        <?php include '../../includes/sidebar.php'; ?>
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
              <h4 class="fw-bold py-3 mb-4">Leave Management</h4>

              <!-- Leave Management Content -->
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0">All Leave Requests</h5>
                      <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                          <i class="icon-base bx bx-plus me-2"></i>Apply Leave
                        </button>
                        <select class="form-select" style="width: auto;">
                          <option>All Status</option>
                          <option>Pending</option>
                          <option>Approved</option>
                          <option>Rejected</option>
                        </select>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Employee</th>
                              <th>Leave Type</th>
                              <th>Start Date</th>
                              <th>End Date</th>
                              <th>Days</th>
                              <th>Status</th>
                              <th>Applied Date</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            // Get leave requests from database
                            $leaveQuery = "SELECT lr.*, ep.first_name, ep.last_name 
                                         FROM leave_requests lr 
                                         LEFT JOIN employee_profiles ep ON lr.employee_id = ep.employee_id 
                                         ORDER BY lr.created_at DESC";
                            $leaveResult = mysqli_query($conn, $leaveQuery);
                            
                            if ($leaveResult && mysqli_num_rows($leaveResult) > 0):
                                while ($leave = mysqli_fetch_assoc($leaveResult)):
                            ?>
                            <tr>
                              <td><?php echo $leave['leave_id']; ?></td>
                              <td><?php echo $leave['first_name'] . ' ' . $leave['last_name']; ?></td>
                              <td>
                                <span class="badge bg-<?php echo $leave['leave_type'] == 'Annual' ? 'primary' : ($leave['leave_type'] == 'Sick' ? 'danger' : 'info'); ?>">
                                  <?php echo $leave['leave_type']; ?>
                                </span>
                              </td>
                              <td><?php echo date('M d, Y', strtotime($leave['start_date'])); ?></td>
                              <td><?php echo date('M d, Y', strtotime($leave['end_date'])); ?></td>
                              <td><?php echo $leave['days_requested']; ?></td>
                              <td>
                                <span class="badge bg-<?php echo $leave['status'] == 'Approved' ? 'success' : ($leave['status'] == 'Rejected' ? 'danger' : 'warning'); ?>">
                                  <?php echo $leave['status']; ?>
                                </span>
                              </td>
                              <td><?php echo date('M d, Y', strtotime($leave['created_at'])); ?></td>
                              <td>
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0);">View Details</a></li>
                                    <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
                                    <li><a class="dropdown-item leave-approve" href="#" data-leave-id="<?php echo $leave['leave_id']; ?>">Approve</a></li>
                                    <li><a class="dropdown-item leave-reject" href="#" data-leave-id="<?php echo $leave['leave_id']; ?>">Reject</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger leave-delete" href="#" data-leave-id="<?php echo $leave['leave_id']; ?>">Delete</a></li>
                                  </ul>
                                </div>
                              </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                              <td colspan="9" class="text-center">No leave requests found</td>
                            </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            <!-- / Content -->

            <!-- Apply Leave Modal -->
            <div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form id="apply-leave-form">
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select class="form-select" name="employee_id" required>
                          <?php
                          $empRes = mysqli_query($conn, "SELECT employee_id, first_name, last_name FROM employee_profiles ORDER BY first_name");
                          while ($e = mysqli_fetch_assoc($empRes)) {
                            echo '<option value="'.(int)$e['employee_id'].'">'.htmlspecialchars($e['first_name'].' '.$e['last_name'])."</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Leave Type</label>
                        <select class="form-select" name="leave_type" required>
                          <option value="Annual">Annual</option>
                          <option value="Sick">Sick</option>
                          <option value="Personal">Personal</option>
                          <option value="Maternity">Maternity</option>
                          <option value="Paternity">Paternity</option>
                          <option value="Emergency">Emergency</option>
                          <option value="Other">Other</option>
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Start Date</label>
                          <input type="date" class="form-control" name="start_date" required />
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">End Date</label>
                          <input type="date" class="form-control" name="end_date" required />
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Days Requested</label>
                        <input type="number" class="form-control" name="days_requested" min="1" required />
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

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
    <script>
    $(function(){
      function act(action, payload){
        return $.post('ajax/leave_actions.php', $.extend({action: action}, payload));
      }
      $('.leave-approve').on('click', function(e){ e.preventDefault(); const id=$(this).data('leave-id'); act('approve',{leave_id:id}).done(()=>location.reload()); });
      $('.leave-reject').on('click', function(e){ e.preventDefault(); const id=$(this).data('leave-id'); act('reject',{leave_id:id}).done(()=>location.reload()); });
      $('.leave-delete').on('click', function(e){ e.preventDefault(); if(!confirm('Delete this leave request?')) return; const id=$(this).data('leave-id'); act('delete',{leave_id:id}).done(()=>location.reload()); });

      $('#apply-leave-form').on('submit', function(e){
        e.preventDefault();
        const data = $(this).serializeArray().reduce((a,x)=>{a[x.name]=x.value; return a;}, {});
        act('apply', data).done(()=>{ location.reload(); }).fail((xhr)=>{
          alert('Failed to apply leave: ' + (xhr.responseJSON?.error || 'Unknown error'));
        });
      });
    });
    </script>
  </body>
</html>