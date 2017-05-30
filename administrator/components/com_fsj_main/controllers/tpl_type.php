<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class fsj_mainControllertpl_type extends JControllerForm
{
	function __construct($config = array())
	{
		// make sure to set the view_list class name otherwise joomla does stupid stuff with plurals (and we like to just append s to be simple)
		$this->view_list = 'tpl_types';

		parent::__construct($config);
	}
	
	function cancel($key = NULL)
	{
		$result = parent::cancel();
		
	}
	
	function save($key = NULL, $urlVar = NULL)
	{
		$result = parent::save();
		
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{

		$tmpl   = JRequest::getCmd('tmpl');
		$layout = JRequest::getCmd('layout', 'edit');
		$popup = JRequest::getCmd('popup');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&layout=' . $layout;
		}

		if ($popup)
		{
			$append .= '&popup=' . $popup;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}	

	protected function allowAdd($data = array())
	{
		return true;
		
		// NEEDS TO CHECK IF YOU CAN ADD A RECORD IN THE CURRENT CATEGORY!
		// Initialise variables.
		/*$user = JFactory::getUser();
		$categoryId = JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow = $user->authorise('core.create', 'com_fsj_main.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}*/
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		return true;
		
		/*
		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_fsj_main.article.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_fsj_main.article.' . $recordId))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);*/
	}
/*
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Article', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_fsj_main&view=articles' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}*/
}
