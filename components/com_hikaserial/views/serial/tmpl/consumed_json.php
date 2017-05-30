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
header('Content-Type: text/plain');

echo '{';

if(!empty($this->serial)) {
	echo '"consume":{"status":"'.$this->serial->serial_status.'","date":"'.$this->serial->serial_assign_date.'","data":"'.$this->serial->serial_data.'"';

	if(!empty($this->serial->serial_extradata)) {
		echo ',"extradata":{';
		$sep2 = '';
		foreach($this->serial->serial_extradata as $key => $value) {
			echo $sep2.'"'.$key.'":"'.$value.'"';
			$sep2 = ',';
		}
		echo '}';
	}
	echo '}';
} else {
	echo '"error":"404","error_text":"no serial"';
}

echo '}';
