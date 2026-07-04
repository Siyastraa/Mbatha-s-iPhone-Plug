<?php
// Mbatha's iPhone Plug - Customer Logout

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_phone']);
unset($_SESSION['user_address']);

// Provide clean notice
$_SESSION['flash_message'] = "You have logged out successfully. Have a premium day!";
$_SESSION['flash_type'] = "info";

header("Location: ../index.php");
exit;
?>
