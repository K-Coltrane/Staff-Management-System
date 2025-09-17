<?php
include_once '../../config/database.php';
include_once '../../includes/functions.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit; }
?>
<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recruitment & Onboarding</title>
    <link rel="stylesheet" href="../../assets/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include '../../includes/sidebar.php'; ?>
        <div class="layout-page">
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Recruitment & Onboarding</h4>
              <div class="card">
                <div class="card-body">
                  <div class="d-flex gap-2">
                    <a class="btn btn-primary" href="job_postings.php">Job Postings</a>
                    <a class="btn btn-outline-primary" href="applicants.php">Applicants</a>
                    <a class="btn btn-outline-primary" href="interviews.php">Interviews</a>
                  </div>
                </div>
              </div>
            </div>
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                  <div class="mb-2 mb-md-0">© <script>document.write(new Date().getFullYear())</script>, Staff Management System</div>
                </div>
              </div>
            </footer>
          </div>
        </div>
      </div>
    </div>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/js/main.js"></script>
  </body>
 </html>


