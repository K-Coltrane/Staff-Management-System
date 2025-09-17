<?php
// Thin wrapper to reuse existing settings UI under dash, but standardize includes
include_once '../../config/database.php';
include_once '../../includes/functions.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit; }
// Delegate to existing settings page for now
require_once __DIR__ . '/dash/settings.php';


