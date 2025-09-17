<footer class="content-footer footer bg-footer-theme">
                <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                    <div class="mb-2 mb-md-0">
                        © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>
                    </div>
                </div>
            </footer>
            <!-- / Footer -->
            <div class="content-backdrop fade"></div>
        </div>
        <!-- / Content wrapper -->
    </div>
    <!-- / Layout page -->
</div>
<!-- / Layout container -->
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/js/menu.js"></script>

<!-- Main JS -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sidebar-toggle.js"></script>

<!-- Page JS -->
<?php if (isset($pageJs) && is_array($pageJs)): ?>
    <?php foreach ($pageJs as $js): ?>
        <script src="<?php echo BASE_URL . $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>