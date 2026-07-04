<?php
// Mbatha's iPhone Plug - Admin Logout

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

header("Location: login.php");
exit;
?>
