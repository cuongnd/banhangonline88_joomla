<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingControllerDeals extends JControllerAdmin
{
	protected $text_prefix = 'COM_CMGROUPBUYING_DEAL';

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unfeatured', 'featured');
	}

	public function getModel($name = 'Deal', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		$values = array('featured' => 1, 'unfeatured' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');

		if(empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if(!$model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_cmgroupbuying&view=deals');
	}

	public function tip()
	{
		$countTipped = 0;
		$countUntipped = 0;
		$dealIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($dealIdList as $dealId)
		{
			$tipped = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getTippedStatus($dealId);

			if($tipped == 0)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setTippedStatus($dealId, 1);
				CMGroupBuyingHelperMail::sendMailForTippedDeal($dealId);
				$countTipped++;
			}
			elseif($tipped == 1)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setTippedStatus($dealId, 0);
				$countUntipped++;
			}
		}

		$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_TIP_MESSAGE', $countTipped, $countUntipped);
		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=deals';
		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
	}

	public function void()
	{
		$countVoided = 0;
		$countUnvoided = 0;
		$dealIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($dealIdList as $dealId)
		{
			$voided = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getVoidedStatus($dealId);

			if($voided == 0)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setVoidedStatus($dealId, 1);
				CMGroupBuyingHelperMail::sendMailForVoidedDeal($dealId);
				$countVoided++;
			}
			elseif($voided == 1)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setVoidedStatus($dealId, 0);
				$countUnvoided++;
			}
		}

		$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_VOID_MESSAGE', $countVoided, $countUnvoided);
		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=deals';
		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
	}

	public function approve()
	{
		$countApproved = 0;
		$countUnapproved = 0;
		$dealIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($dealIdList as $dealId)
		{
			$approved = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getApprovedStatus($dealId);

			if($approved == 0)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setApprovedStatus($dealId, 1);
				CMGroupBuyingHelperMail::sendMailForApprovedDeal($dealId);
				$countApproved++;
			}
			if($approved == 1)
			{
				JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->setApprovedStatus($dealId, 0);
				$countUnapproved++;
			}
		}

		$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_APPROVE_MESSAGE', $countApproved, $countUnapproved);
		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=deals';
		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
	}
}
