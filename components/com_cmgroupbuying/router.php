<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

function CMGroupBuyingBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['view']))
	{
		if($query['view'] == 'dealprevue' || $query['view'] == 'freecouponprevue'
				|| $query['view'] == 'todaydeal' || $query['view'] == 'todayfreecoupon'
				|| $query['view'] == 'cart' || $query['view'] == 'checkout' || $query['view'] == 'orders'
				|| $query['view'] == 'alldeals' || $query['view'] == 'freecoupons'
				|| $query['view'] == 'activedeals' || $query['view'] == 'activefreecoupons'
				|| $query['view'] == 'upcomingdeals' || $query['view'] == 'upcomingfreecoupons'
				|| $query['view'] == 'expireddeals' || $query['view'] == 'expiredfreecoupons'
				|| $query['view'] == 'partner' || $query['view'] == 'dealmanagement'
				|| $query['view'] == 'freecouponmanagement'|| $query['view'] == 'products')
		{
			unset($query['view']);
		}
		elseif($query['view'] == 'deal' || $query['view'] == 'freecoupon'
				|| $query['view'] == 'order' || $query['view'] == 'rssfeeds'
				|| $query['view'] == 'search' || $query['view'] == 'dealsubmission'
				|| $query['view'] == 'partnermanagement'
				|| $query['view'] == 'staffmanagement'
				|| $query['view'] == 'freecouponsubmission'
				|| $query['view'] == 'category'
				|| $query['view'] == 'coupon'
				|| $query['view'] == 'product')
		{
			$segments[] = $query['view'];
			unset($query['view']);
		}
		else
		{
			$segments[] = $query['view'];
		}

		if(isset($query['navigation']))
		{
				$segments[] = $query['navigation'];
				unset($query['navigation']);
		}

		if(isset($query['id']))
		{
				$segments[] = $query['id'];
				unset($query['id']);
		}

		if(isset($query['alias']))
		{
				$segments[] = $query['alias'];
				unset($query['alias']);
		}

		if(isset($query['download']))
		{
				$segments[] = $query['download'];
				unset($query['download']);
		}
	}

	if(isset($query['type']))
	{
			$segments[] = $query['type'];
			unset($query['type']);
	}

	if(isset($query['location']))
	{
			$segments[] = $query['location'];
			unset($query['location']);
	}

	return $segments;
}

function CMGroupBuyingParseRoute($segments)
{
	$vars = array();

	switch($segments[0])
	{
		case 'deal':
			$vars['view'] = 'deal';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			if(isset($segments[2]))
				$vars['alias'] = $segments[2];
			break;
		case 'freecoupon':
			$vars['view'] = 'freecoupon';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			if(isset($segments[2]))
				$vars['alias'] = $segments[2];
			break;
		case 'order':
			$vars['view'] = 'order';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			break;
		case 'todaydeal':
			$vars['view'] = 'todaydeal';
			break;
		case 'todayfreecoupon':
			$vars['view'] = 'todayfreecoupon';
			break;
		case 'freecoupons':
			$vars['view'] = 'freecoupons';
			break;
		case 'alldeals':
			$vars['view'] = 'alldeals';
			break;
		case 'activedeals':
			$vars['view'] = 'activedeals';
			break;
		case 'upcomingdeals':
			$vars['view'] = 'upcomingdeals';
			break;
		case 'expireddeals':
			$vars['view'] = 'expireddeals';
			break;
		case 'activefreecoupons':
			$vars['view'] = 'activefreecoupons';
			break;
		case 'upcomingfreecoupons':
			$vars['view'] = 'upcomingfreecoupons';
			break;
		case 'expiredfreecoupons':
			$vars['view'] = 'expiredfreecoupons';
			break;
		case 'cart':
			$vars['view'] = 'cart';
			break;
		case 'checkout':
			$vars['view'] = 'checkout';
			break;
		case 'search':
			$vars['view'] = 'search';
			break;
		case 'orders':
			$vars['view'] = 'orders';
			break;
		case 'partner':
			$vars['view'] = 'partner';
			break;
		case 'dealmanagement':
			$vars['view'] = 'dealmanagement';
			break;
		case 'dealsubmission':
			$vars['view'] = 'dealsubmission';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			break;
		case 'freecouponmanagement':
			$vars['view'] = 'freecouponmanagement';
			break;
		case 'freecouponsubmission':
			$vars['view'] = 'freecouponsubmission';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			break;
		case 'rssfeeds':
			$vars['view'] = 'rssfeeds';
			if(isset($segments[1]))
				$vars['type'] = $segments[1];
			if(isset($segments[2]))
				$vars['location'] = $segments[2];
			break;
		case 'partnermanagement':
			$vars['view'] = 'partnermanagement';
			if(isset($segments[1]))
				$vars['navigation'] = $segments[1];
			if(isset($segments[2]))
				$vars['id'] = $segments[2];
			break;
		case 'staffmanagement':
			$vars['view'] = 'staffmanagement';
			if(isset($segments[1]))
				$vars['navigation'] = $segments[1];
			if(isset($segments[2]))
				$vars['id'] = $segments[2];
			break;
		case 'category':
			$vars['view'] = 'category';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			break;
		case 'coupon':
			$vars['view'] = 'coupon';
			if(isset($segments[1]))
			{
				$vars['download'] = $segments[1];
				$vars['tmpl'] = 'component';
			}
			break;
		case 'products':
			$vars['view'] = 'products';
			break;
		case 'product':
			$vars['view'] = 'product';
			if(isset($segments[1]))
				$vars['id'] = $segments[1];
			if(isset($segments[2]))
			{
				$vars['alias'] = str_replace(':', '-', $segments[2]);
			}
			break;
		default:
			$vars['view'] = '';
			break;
	}

	return $vars;
}