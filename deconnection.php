<?php
session_start();
session_destroy();
header("Location: ./SignIN_UP.php");
exit();
?>