<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/common.php';

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerPartnerManagement extends JControllerForm
{
	protected $view_list;

	public function __construct($config = array())
	{
		$this->view_list = "partnermanagement";
		parent::__construct($config);
	}

	public function cancel($key = NULL)
	{
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
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
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_submission');
			$redirectUrl = base64_encode($redirectUrl);
			$redirectUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=".$redirectUrl, false);
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$partnerId = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerIdByUserId($user->id);

		if(empty($partnerId))
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['partner_submit_new_deal'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$data = JFactory::getApplication()->input->post->get('jform', array(), 'array');
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_list');
		$model = $this->getModel();
		$return = $model->save($data);

		if($return === true)
		{
			//Only send mail for first submission
			//not send mail when update a pending deal
			if(empty($data['id']))
			{
				CMGroupBuyingHelperMail::sendMailForNewDeal($data['name'], $user->name);
			}

			$message = JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_SUCCESSFUL');
			$type = '';
		}
		else
		{
			JFactory::getApplication()->setUserState('com_cmgroupbuying.edit.partnermanagement.data', $data);
			$message = $model->getError();
			$type = 'error';
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_submission&id=' . $data['id']);
		}

			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
	}

	function getModel($name = 'PartnerManagement', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}