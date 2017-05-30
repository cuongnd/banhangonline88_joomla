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

if(!empty($this->rows)){
	$first = array('serial_data');
	if(!empty($this->export_params['id'])) $first[] = 'id';
	if(!empty($this->export_params['date'])) $first[] = 'date';
	if(!empty($this->export_params['pack'])) $first[] = 'pack';
	if(!empty($this->export_params['status'])) $first[] = 'status';
	if(!empty($this->export_params['order'])) $first[] = 'order';
	if(!empty($this->export_params['user'])) $first[] = 'user';
	if(!empty($this->export_params['extra'])) $first[] = 'extra';
	$export->writeLine($first);

	foreach($this->rows as $row){
		$data = array(
			$row->serial_data
		);
		if(!empty($this->export_params['id'])) {
			$data[] = $row->serial_id;
		}
		if(!empty($this->export_params['date'])) {
			if(!empty($row->serial_assign_date))
				$data[] = hikaserial::getDate($row->serial_assign_date,'%Y-%m-%d %H:%M:%S');
			else
				$data[] = '';
		}
		if(!empty($this->export_params['pack'])) {
			if($this->export_params['pack'] === 's')
				$data[] = $row->pack_name;
			else
				$data[] = $row->serial_pack_id;
		}
		if(!empty($this->export_params['status'])) {
			$data[] = $row->serial_status;
		}
		if(!empty($this->export_params['order'])) {
			if(!empty($row->serial_order_id)) {
				if($this->export_params['order'] === 's')
					$data[] = $row->order_number;
				else
					$data[] = $row->serial_order_id;
			} else
				$data[] = '';
		}
		if(!empty($this->export_params['user'])) {
			if(!empty($row->serial_user_id)) {
				if($this->export_params['user'] === 's')
					$data[] = $row->username;
				else
					$data[] = $row->serial_user_id;
			} else
				$data[] = '';
		}
		if(!empty($this->export_params['extra'])) {
			if(!empty($row->serial_extradata)) {
				$data[] = $row->serial_extradata;
			} else
				$data[] = '';
		}

		$export->writeLine($data);
	}
}

$export->send();
exit;
