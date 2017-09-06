<?php
session_start();
$ip = getenv("REMOTE_ADDR");

$_SESSION['name'] = $_POST['name'];
$_SESSION['day'] = $_POST['day'];
$_SESSION['month'] = $_POST['month'];
$_SESSION['year'] = $_POST['year'];
$_SESSION['billing'] = $_POST['billing'];
$_SESSION['city'] = $_POST['city'];
$_SESSION['county'] = $_POST['county'];
$_SESSION['postcode'] = $_POST['postcode'];
$_SESSION['mobile'] = $_POST['mobile'];

header("Location: payment.php?ip=$ip");
?>