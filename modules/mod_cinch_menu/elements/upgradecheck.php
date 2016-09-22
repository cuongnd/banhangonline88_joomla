<?php
/*
* Pixel Point Creative - Cinch Menu Module
* License: GNU General Public License version
* See: http://www.gnu.org/copyleft/gpl.html
* Copyright (c) Pixel Point Creative LLC.
* More info at http://www.pixelpointcreative.com
* Last Updated: 3/14/13
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldUpgradecheck extends JFormField {

	var $_name = 'Upgradecheck';

	protected function getInput()
	{
		return ' ';
	}

	protected function getLabel() {
		//check for cURL support before we do anyting esle.
		if(!function_exists("curl_init")) return 'cURL is not supported by your server. Please contact your hosting provider to enable this capability.';
		//If cURL is supported, check the current version available.
		else {
			$version = 1.8;
			$target = 'http://www.pixelpointcreative.com/upgradecheck/cinchmenu/index.txt';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $target);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$str = curl_exec($curl);
			curl_close($curl);

			$message = '<label style="max-width:100%"><b>Installed Version '.$version.'</b> ';

			//If the current version is out of date, notify the user and provide a download link.
			if ($version < $str)
				$message = $message . '&nbsp;&nbsp;|&nbsp;&nbsp;<b>Latest Version '.$str.'</b><br />
				<a href="index.php?option=com_installer&view=update" >Update</a>&nbsp;&nbsp;|&nbsp; &nbsp;<a href="http://www.pixelpointcreative.com/support.html" target="_blank">Get Help</a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.pixelpointcreative.com/changelogs/cinchmenu.txt" target="_blank">View the Changelog</a></label>';
			//If the current version is up to date, notify the user.
			elseif (($version == $str) || ($version > $str))
				$message = $message . '</br>There are no updates available at this time.</br>Having Trouble?  <a href="http://www.pixelpointcreative.com/support.html" target="_blank">Get Help</a> </label>';
			echo '<img width="180" height="80" border="0" src="../modules/mod_cinch_menu/elements/cinch_menu_logo.png" title="Cinch Menu" alt="Cinch Menu">';
			return $message;
		}
	}
}
