<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/factory.php');

// Deprecated file. Shouldn't need to require/include this file, but if you are, please load the below instead:
require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/facebook.php');

JLog::add('/components/com_jfbconnect/libraries/facebook.php is deprecated. You no longer need to require this file. Use JFBCFactory::provider("facebook") instead', JLog::WARNING, 'deprecated');
