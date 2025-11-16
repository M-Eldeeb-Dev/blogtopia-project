<?php
require_once '../Config/auth.php';
logoutUser();
header("Location: ../index.php");
exit();
?>