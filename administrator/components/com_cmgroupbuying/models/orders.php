<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class CMGroupBuyingModelOrders extends JModelList
{
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'deal_id', 'a.deal_id',
				'value', 'a.value',
				'quantity', 'a.quantity',
				'buyer_id', 'a.buyer_id',
				'buyer_info', 'a.buyer_info',
				'friend_info', 'a.friend_info',
				'payment_name', 'a.payment_name',
				'transaction_info', 'a.transaction_info',
				'created_date', 'a.created_date',
				'expired_date', 'a.expired_date',
				'paid_date', 'a.paid_date',
				'status', 'status'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Adjust the context to support modal layouts.
		if($layout = JFactory::getApplication()->input->get('layout', '', 'word'))
		{
			$this->context .= '.'.$layout;
		}

		$payment = $this->getUserStateFromRequest($this->context.'.filter.payment', 'filter_payment', '');
		$this->setState('filter.payment', $payment);

		$status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
		$this->setState('filter.status', $status);

		$deal_id = $this->getUserStateFromRequest($this->context.'.filter.deal_id', 'filter_deal_id', '');
		$this->setState('filter.deal_id', $deal_id);

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.deal_id');
		$id .= ':'.$this->getState('filter.payment');
		$id .= ':'.$this->getState('filter.status');
		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__cmgroupbuying_orders AS a');

		// Filter by payment
		$payment = $this->getState('filter.payment');

		if($payment != '*' && $payment != '' )
		{
			$query->where('a.payment_name = "' . addslashes($payment) . '"');
		}

		// Filter by status
		$status = $this->getState('filter.status');

		if(is_numeric($status))
		{
			$query->where('a.status = ' . (int) $status);
		}

		// Filter by deal id
		$deal_id = $this->getState('filter.deal_id');

		if(is_numeric($deal_id))
		{
			$query->where('a.deal_id = ' . (int) $deal_id);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if(version_compare(JVERSION, '3.0.0', 'lt')):
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		elseif(version_compare(JVERSION, '3.0.0', 'ge')):
			$query->order($db->escape($orderCol.' '.$orderDirn));
		endif;

		return $query;
	}

	public function getOrdersByDealId($dealId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE deal_id = " . $dealId;
		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}
}