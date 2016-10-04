<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

// Access check
if(!JFactory::getUser()->authorise('core.manage', 'com_cmgroupbuying'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');


if(version_compare(JVERSION, '3.0.0', 'lt')):
	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.min.js');
	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery-noconflict.js');
	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/bootstrap.min.js');
else:
	JHtml::_('bootstrap.framework');
	JHtml::_('bootstrap.tooltip');
endif;

JFactory::getDocument()->addStyleSheet('components/com_cmgroupbuying/assets/css/bootstrap.min.css');
JFactory::getDocument()->addStyleSheet('components/com_cmgroupbuying/assets/css/style.css');

// Component controller
$controller = JControllerLegacy::getInstance('cmgroupbuying');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();