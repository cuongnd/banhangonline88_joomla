<?php
/**
 * @package         Modals
 * @version         8.0.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * System Plugin that places a Modals code block into the text
 */
class PlgSystemModals extends JPlugin
{
	private $_alias       = 'modals';
	private $_title       = 'MODALS';
	private $_lang_prefix = 'MDL';

	private $_init   = false;
	private $_helper = null;

	public function onAfterRoute()
	{
		$this->getHelper();
	}

	public function onContentPrepare($context, &$article, &$params)
	{
		if ($context == 'com_zoo.element.textarea')
		{
			return;
		}

		if (!$this->getHelper())
		{
			return;
		}

		$this->_helper->onContentPrepare($article, $context, $params);
	}

	public function onAfterDispatch()
	{
		if (!$this->getHelper())
		{
			return;
		}

		$this->_helper->onAfterDispatch();
	}

	public function onAfterRender()
	{
		if (!$this->getHelper())
		{
			return;
		}

		$this->_helper->onAfterRender();
	}

	/*
	 * Below methods are general functions used in most of the Regular Labs extensions
	 * The reason these are not placed in the Regular Labs Library files is that they also
	 * need to be used when the Regular Labs Library is not installed
	 */

	/**
	 * Create the helper object
	 *
	 * @return object The plugins helper object
	 */
	private function getHelper()
	{
		// Already initialized, so return
		if ($this->_init)
		{
			return $this->_helper;
		}

		$this->_init = true;

		if (!$this->isFrameworkEnabled())
		{
			return false;
		}

		if (JFactory::getApplication()->input->get('option') == 'com_plugins')
		{
			return false;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/protect.php';

		if (RLProtect::isProtectedPage($this->_alias, 1))
		{
			return false;
		}

		// Load plugin parameters
		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$params = RLParameters::getInstance()->getPluginParams($this->_name);

		// allow in admin?
		if (!$params->enable_admin && RLProtect::isAdmin())
		{
			return false;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/helper.php';
		$this->_helper = RLHelper::getPluginHelper($this, $params);

		return $this->_helper;
	}

	/**
	 * Check if the Regular Labs Library is enabled
	 *
	 * @return bool
	 */
	private function isFrameworkEnabled()
	{
		// Return false if Regular Labs Library is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$regularlabs = JPluginHelper::getPlugin('system', 'regularlabs');
		if (!isset($regularlabs->name))
		{
			$this->throwError($this->_lang_prefix . '_REGULAR_LABS_LIBRARY_NOT_ENABLED');

			return false;
		}

		return true;
	}

	/**
	 * Check if the Regular Labs Library is installed
	 *
	 * @return bool
	 */
	private function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists(JPATH_PLUGINS . '/system/regularlabs/regularlabs.php'))
		{
			$this->throwError($this->_lang_prefix . '_REGULAR_LABS_LIBRARY_NOT_INSTALLED');

			return false;
		}

		return true;
	}

	/**
	 * Place an error in the message queue
	 */
	private function throwError($text)
	{
		// Return if page is not an admin page or the admin login page
		if (
			!JFactory::getApplication()->isAdmin()
			|| JFactory::getUser()->get('guest')
		)
		{
			return;
		}

		// load the admin language file
		JFactory::getLanguage()->load('plg_' . $this->_type . '_' . $this->_name, JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name);

		$text = JText::_($text) . ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title));

		// Check if message is not already in queue
		$messagequeue = JFactory::getApplication()->getMessageQueue();
		foreach ($messagequeue as $message)
		{
			if ($message['message'] == $text)
			{
				return;
			}
		}

		JFactory::getApplication()->enqueueMessage($text, 'error');
	}
}

