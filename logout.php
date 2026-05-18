<?php
require_once 'includes/config.php';
if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}
session_destroy();
header('Location: ' . APP_URL . '/index.php');
exit();
