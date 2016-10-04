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

class CMGroupBuyingControllerFreeCoupons extends JControllerAdmin
{
	protected $text_prefix = 'COM_CMGROUPBUYING_FREE_COUPON';

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unfeatured', 'featured');
	}

	public function getModel($name = 'FreeCoupon', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function approve()
	{
		$countApproved = 0;
		$countUnapproved = 0;
		$freeCouponIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($freeCouponIdList as $freeCouponId)
		{
			$approved = JModelLegacy::getInstance('FreeCoupon','CMGroupBuyingModel')->getApprovedStatus($freeCouponId);

			if($approved == 0)
			{
				JModelLegacy::getInstance('FreeCoupon','CMGroupBuyingModel')->setApprovedStatus($freeCouponId, 1);
				CMGroupBuyingHelperMail::sendMailForApprovedFreeCoupon($freeCouponId);
				$countApproved++;
			}
			if($approved == 1)
			{
				JModelLegacy::getInstance('FreeCoupon','CMGroupBuyingModel')->setApprovedStatus($freeCouponId, 0);
				$countUnapproved++;
			}
		}

		$message = JText::sprintf('COM_CMGROUPBUYING_FREE_COUPON_APPROVE_MESSAGE', $countApproved, $countUnapproved);
		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=freeCoupons';
		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
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

		$this->setRedirect('index.php?option=com_cmgroupbuying&view=freecoupons');
	}
}
