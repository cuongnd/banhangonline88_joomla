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
while(ob_get_level() > 1)
	ob_end_clean();

$format = 'csv';
if(!empty($this->export_params['format'])) {
	$format = strtolower($this->export_params['format']);
	if(!in_array($format, array('csv','xls')))
		$format = 'csv';
}
$separator = ';';
$force_quote = true;

$export = hikaserial::get('shop.helper.spreadsheet');
$export->init($format, 'hikaserial_export', $separator, $force_quote);

if(empty($this->rows)) {
	$export->send();
	exit;
}

$first = array_keys(get_object_vars(reset($this->rows)));
$export->writeLine($first);

foreach($this->rows as $row) {
	$data = array();
	foreach($first as $k) {
		if(!isset($row->$k)) {
			$data[] = '';
			continue;
		}

		if(in_array($k, array('serial_assign_date')))
			$data[] = hikaserial::getDate($row->$k, '%Y-%m-%d %H:%M:%S');
		else
			$data[] = $row->$k;
	}
	$export->writeLine($data);
}

$export->send();
exit;
