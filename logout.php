<?php
session_start();
session_destroy();
header("Location: OBS.php");
exit;
?>