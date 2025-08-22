<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_admin();

// This file is already implemented in admin/manage_verifiers.php
// Redirect to manage_verifiers.php
header("Location: admin/manage_verifiers.php");
exit();
?>