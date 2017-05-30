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
define('QR_CACHEABLE', true);
define('QR_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
define('QR_LOG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('QR_FIND_BEST_MASK', true);
define('QR_FIND_FROM_RANDOM', false);
define('QR_DEFAULT_MASK', 2);
define('QR_PNG_MAXIMUM_SIZE',  1024);
