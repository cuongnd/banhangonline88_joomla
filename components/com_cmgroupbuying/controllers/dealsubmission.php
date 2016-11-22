<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/common.php';

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerDealSubmission extends JControllerForm
{
	protected $view_list;

	public function __construct($config = array())
	{
		$this->view_list = "dealsubmission";
		parent::__construct($config);
	}

	public function cancel($key = NULL)
	{
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=dealmanagement');
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function save($data = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		if($user->guest)
		{
			$message = JText::_('COM_CMGROUPBUYING_LOGIN_FIRST');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=dealsubmission');
			$redirectUrl = base64_encode($redirectUrl);
			$redirectUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=".$redirectUrl, false);
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$getUnpublished = false;
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id, $getUnpublished);

		if(empty($partner))
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		if(parent::save($data))
		{
			//Only send mail for first submission
			//not send mail when update a pending deal
			if(empty($data['id']))
			{
				CMGroupBuyingHelperMail::sendMailForNewDeal($data['name'], $user->name);
			}

			$message = JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_SUCCESSFUL');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=dealmanagement');
			$this->setRedirect($redirectUrl, $message);
			$this->redirect();
		}
		else
		{
			JFactory::getApplication()->setUserState('com_cmgroupbuying.edit.partnermanagement.data', $data);
			$message = JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FAILED');
			$type = 'error';
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_submission&id=' . $data['id']);
			$this->setRedirect($redirectUrl, $message);
			$this->redirect();
		}
	}
}