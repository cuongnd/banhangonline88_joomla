<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingControllerSubscription extends JControllerLegacy
{
	public function subscribe()
	{
		$jinput = JFactory::getApplication()->input;
		$locationId = $jinput->get('subscription_location', 0, 'int');
		$name = $jinput->get('subscription_name', '', 'string');
		$email = $jinput->get('subscription_email', '', 'string');
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('point_cookie_lifetime, subscription_redirect');

		if($name == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_INVALID_NAME');
			$link = 'index.php';
		}
		elseif(CMGroupBuyingHelperMail::validEmail($email) == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_INVALID_EMAIL');
			$link = 'index.php';
		}
		elseif(is_numeric($locationId))
		{
			if($locationId > 0 && $name != '' && $email != '')
			{
				$cookieLifetime = time() + $configuration['point_cookie_lifetime'] * 60 * 60;
				setcookie("locationSubscription", $locationId, $cookieLifetime, '/');

				if(!include_once(rtrim(JPATH_ADMINISTRATOR,'/').'/components/com_acymailing/helpers/helper.php'))
				{
					// Missing AcyMailing component
					// return false;
					$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);
				}
				else
				{
					$user = new stdClass();
					$user->email = $email;
					$user->name = $name; // This information is optional
					$subscriberClass = acymailing::get('class.subscriber');
					$subId = $subscriberClass->save($user); // This function will return the ID of the user inserted in the AcyMailing table

					if($subId != '')
					{
						$location = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getLocationById($locationId);
						$listId = $this->getACYMailingListId($location['name']);

						if(is_numeric($listId))
						{
							$listIdArray = array($listId); // Id of the lists you want the user to be subscribed to (can be empty)
							$newSubscription = array();

							if(!empty($listIdArray))
							{
								foreach($listIdArray as $id)
								{
									$newList = null;
									$newList['status'] = 1;
									$newSubscription[$id] = $newList;
								}
							}

							if(empty($newSubscription))
							{
								// There is nothing to do because there is no list to subscribe...
								$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);
							}
							else
							{
								$subscriberClass->saveSubscription($subId, $newSubscription);
								$message = JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_SUCCESSFUL');
								$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);
							}
						}
						else
						{
							// There is nothing to do because there is no list to subscribe...
							$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);
						}
					}
				}
			}
			elseif($locationId == 0)
			{
				$cookieLifetime = time() - $configuration['point_cookie_lifetime'] * 60 * 60;
				setcookie("locationSubscription", $locationId, $cookieLifetime, '/');
			}
		}

		$this->setRedirect($link, $message);
		$this->redirect();
	}

	function skip()
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('point_cookie_lifetime, subscription_redirect');
		$cookieLifetime = time() + $configuration['point_cookie_lifetime'] * 60 * 60;
		setcookie("locationSubscription", '-1', $cookieLifetime, '/');
		$skipLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);
		$this->setRedirect($skipLink);
		$this->redirect();
	}

	function getACYMailingListId($locationName)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('listid');
		$query->from('#__acymailing_list');
		$query->where('name = ' . $db->quote($locationName));
		$db->setQuery($query);
		$listId  = $db->loadResult();
		return $listId;
	}
}