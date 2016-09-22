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
switch($this->format) {
	case 'xml':
		echo $this->loadTemplate('xml');
		exit;

	case 'json':
		echo $this->loadTemplate('json');
		exit;

	case 'html':
	default:
		echo $this->loadTemplate('html');
		break;
}
