<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class CMGroupBuyingModelCoupons extends JModelList
{
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'order_id', 'a.order_id',
				'item_id', 'a.item_id',
				'partner_id', 'a.partner_id',
				'deal_id', 'a.deal_id',
				'option_id', 'a.option_id',
				'user_id', 'a.user_id',
				'coupon_code', 'a.coupon_code',
				'expired_date', 'a.expired_date',
				'coupon_status', 'a.coupon_status'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		if($layout = JFactory::getApplication()->input->get('layout', '', 'word'))
		{
			$this->context .= '.'.$layout;
		}

		$order = $this->getUserStateFromRequest($this->context.'.filter.order', 'filter_order', '');
		$this->setState('filter.order', $order);

		$partner = $this->getUserStateFromRequest($this->context.'.filter.partner', 'filter_partner', '');
		$this->setState('filter.partner', $partner);

		$deal = $this->getUserStateFromRequest($this->context.'.filter.deal', 'filter_deal', '');
		$this->setState('filter.deal', $deal);

		$buyer = $this->getUserStateFromRequest($this->context.'.filter.buyer', 'filter_buyer', '');
		$this->setState('filter.buyer', $buyer);
		
		$status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
		$this->setState('filter.status', $status);

		// List state information.
		parent::populateState('a.order_id', 'desc');
	}

	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.order');
		$id .= ':'.$this->getState('filter.partner');
		$id .= ':'.$this->getState('filter.deal');
		$id .= ':'.$this->getState('filter.buyer');
		$id .= ':'.$this->getState('filter.status');
		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__cmgroupbuying_coupons AS a');

		// Filter by order
		$order = $this->getState('filter.order');

		if(is_numeric($order))
		{
			$query->where('a.order_id = ' . (int) $order);
		}

		// Filter by partner
		$partner = $this->getState('filter.partner');

		if(is_numeric($partner))
		{
			$query->where('a.partner_id = ' . (int) $partner);
		}

		// Filter by deal
		$deal = $this->getState('filter.deal');

		if(is_numeric($deal))
		{
			$query->where('a.deal_id = ' . (int) $deal);
		}

		// Filter by buyer
		$buyer = $this->getState('filter.buyer');

		if(is_numeric($buyer))
		{
			$query->where('a.user_id = ' . (int) $buyer);
		}

		// Filter by status
		$status = $this->getState('filter.status');

		if(is_numeric($status))
		{
			$query->where('a.coupon_status = ' . (int) $status);
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
}