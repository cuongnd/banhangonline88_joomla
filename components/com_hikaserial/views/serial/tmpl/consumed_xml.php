<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
header('Content-Type: text/xml');
?><hikaserial>
<?php
if(!empty($this->serial)) {
?>
	<consume>
		<serial status="<?php echo $this->serial->serial_status; ?>" date="<?php echo $this->serial->serial_assign_date;?>">
			<data><![CDATA[<?php echo $this->serial->serial_data; ?>]]></data>
			<extradata>
<?php
		if(!empty($this->serial->serial_extradata)) {
			foreach($this->serial->serial_extradata as $key => $value) {
				echo "\t\t\t\t<".$key.'><![CDATA['.$value.']]></'.$key.">\r\n";
			}
		}
?>
			</extradata>
		</serial>
	</consume>
<?php
} else {
?>	<error num="404">no serial</error>
<?php
}
?>
</hikaserial>
