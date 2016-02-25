<?php
session_start();
session_destroy();
header("Location: /hw6/login.php");
?>