<?php
session_start();
session_unset();
session_destroy();

// hapus cookie
setcookie ("rememberUser", "", time() - 3600, "/", "", false, true);
header ("Location: login.php");
exit;
?>