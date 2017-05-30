<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

$jinput = JFactory::getApplication()->input;

if($controller = $jinput->get('controller', '', 'word'))
{
	$path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';

	if(file_exists($path))
	{
		require_once $path;
	}
}

// Define values for uploader - Start
$view = $jinput->get('view', '', 'word');

if($view == "images" || $view == "imagesList")
{
	$params = JComponentHelper::getParams('com_media');

	$db = JFactory::getDbo();
	$query = $db->getQuery(true);

	$query->select($db->quoteName('partner_folder'))
		->from($db->quoteName('#__cmgroupbuying_configuration'))
		->where($db->quoteName('id') . ' = ' . $db->quote('1'));

	$db->setQuery($query);
	$partnerFolder  = $db->loadResult();

	$mediaBase = JPATH_ROOT. '/' .$params->get('image_path', 'images');

	if($partnerFolder != '')
	{
		$mediaBase = $mediaBase . '/' . $partnerFolder;
	}

	if(!is_dir($mediaBase))
	{
		mkdir($mediaBase);
	}

	$user = JFactory::getUser();
	$mediaBase = $mediaBase . '/' . $user->username;

	if(!is_dir($mediaBase))
	{
		mkdir($mediaBase);
	}

	$mediaBaseUrl = JURI::root().$params->get($path, 'images');
	$mediaBaseUrl = $mediaBaseUrl . '/' . $partnerFolder. '/' . $user->username;

	$indexFile = JPATH_ROOT . '/components/com_cmgroupbuying/index.html';

	if(!file_exists($mediaBase . '/index.html') && file_exists($indexFile))
	{
		copy($indexFile, $mediaBase . '/index.html');
	}

	define('COM_CMGROUPBUYING_PARTNER_BASE', $mediaBase);
	define('COM_CMGROUPBUYING_PARTNER_BASEURL', $mediaBaseUrl);
}
// Define values for uploader - End

if(version_compare(JVERSION, '3.0.0', 'ge')):
	// Add JavaScript Frameworks
	JHtml::_('bootstrap.framework');
	JHtml::_('bootstrap.tooltip');
endif;

// Create the controller
$classname = 'CMGroupBuyingController'.ucfirst($controller);
$controller = new $classname();

// Perform the Request task
$controller->execute($jinput->get('task'));
$controller->redirect();
