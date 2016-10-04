<?php
/**
 * This file is taken from com_media
 * There are some changes to let partners in CMGroupBuying only have access to their own folders.
 */

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Media component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class CMGroupBuyingViewImages extends JViewLegacy
{
	function display($tpl = null)
	{
		// CMGroupBuying - Start
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$getUnpublished	= false;
		$partner		= JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id, $getUnpublished);

		if(empty($partner))
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl  = 'index.php';
			$app->enqueueMessage( $message, 'error');
			$app->redirect($redirectUrl);
		}

		// CMGroupBuying - End
		$config = JComponentHelper::getParams('com_media');
		$lang	= JFactory::getLanguage();
		$append	= '';

		// CMGroupBuying - Start
		$lang->load('com_media');
		// CMGroupBuying - End

		JHtml::_('behavior.framework', true);
		JHtml::_('script', 'media/popup-imagemanager.js', true, true);
		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

		if($lang->isRTL())
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->config = $config;
		$this->state = $this->get('state');
		$this->folderList = $this->get('folderList');
		$this->require_ftp = $ftp;

		// CMGroupBuying - Start
		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$this->assignRef('configuration', $configuration);
		// CMGroupBuying - End

		parent::display($tpl);
	}
}
