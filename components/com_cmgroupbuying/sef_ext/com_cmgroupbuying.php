<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// This plug-in is based on sh404sef's example plug-in

// No direct access
defined('_JEXEC') or die;

JPlugin::loadLanguage('com_cmgroupbuying');
global $sh_LANG;
$sefConfig = &shRouter::shGetConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);

if($dosef == false) return;

// Load language file
//$shLangIso = shLoadPluginLanguage('com_cmgroupbuying', $shLangIso, 'COM_SH404SEF_CREATE_NEW');

$noTask = false;

$option = isset($option) ? $option : null;
$view = isset($view) ? $view : null;   // make sure $view is defined
$id = isset($id) ? $id : null;
$alias = isset($alias) ? $alias : null;
$Itemid = isset($Itemid) ? $Itemid : null;
$menu = JFactory::getApplication()->getMenu();

$db = JFactory::getDBO();
$query = $db->getQuery(true);

$query->select('sh404sef_deal_alias, sh404sef_category_alias, sh404sef_partner_alias, sh404sef_free_coupon_alias')
	->from($db->quoteName('#__cmgroupbuying_configuration'))
	->where($db->quoteName('id') . ' = ' . $db->quote('1'));

$db->setQuery($query);
$configuration  = $db->loadAssoc();

switch($view)
{
	case 'freecoupon':
		shRemoveFromGETVarsList('view');
		if($id != null)
		{
			$query->clear()
				->select('*')
				->from($db->quoteName('#__cmgroupbuying_free_coupons'))
				->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$coupon = $db->loadAssoc();

			if(!empty($coupon))
			{
				if($configuration['sh404sef_free_coupon_alias'] != '')
				{
					$title[] = $configuration['sh404sef_free_coupon_alias'];
				}

				$title[] = $coupon['alias'];
				shRemoveFromGETVarsList('id');
				shRemoveFromGETVarsList('alias');
			}
			else
			{
				$dosef = false;
			}
		}
		else
		{
			$title[] = '/';
		}
		break;

	case 'deal':
		shRemoveFromGETVarsList('view');

		if($id != null)
		{
			$query->clear()
				->select('*')
				->from($db->quoteName('#__cmgroupbuying_deals'))
				->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$deal = $db->loadAssoc();

			if(!empty($deal))
			{
				if($configuration['sh404sef_deal_alias'] != '')
				{
					$title[] = $configuration['sh404sef_deal_alias'];
				}

				$title[] = $deal['alias'];
				shRemoveFromGETVarsList('id');
				shRemoveFromGETVarsList('alias');
			}
			else
			{
				$dosef = false;
			}
		}
		else
		{
			$title[] = '/';
		}
		break;

	case 'order':
		$item = $menu->getItems('link', 'index.php?option=com_cmgroupbuying&view=order', true);
		$title[] =$item->alias;
		break;

	case 'orders':
		$title[] = getMenuTitle($option, null, $Itemid, null, $shLangName);
		break;

	case 'search':
		$item = $menu->getItems('link', 'index.php?option=com_cmgroupbuying&view=search', true);
		$title[] =$item->alias;
		break;

	case 'partner':
		if($configuration['sh404sef_partner_alias'] != '')
		{
			$title[] = $configuration['sh404sef_partner_alias'];
		}

		$title[] = getMenuTitle($option, null, $Itemid, null, $shLangName);
		break;

	case 'dealmap':
	case 'dealmanagement':
	case 'dealsubmission':
	case 'freecouponmanagement':
	case 'freecouponsubmission':
	case 'rssfeeds':
	case 'cart':
	case 'checkout':
	case 'todaydeal':
	case 'freecoupons':
	case 'activefreecoupons':
	case 'expiredfreecoupons':
	case 'upcomingfreecoupons':
	case 'alldeals':
	case 'activedeals':
	case 'upcomingdeals':
	case 'expireddeals':
	case 'freecoupons':
		$title[] = getMenuTitle($option, null, $Itemid, null, $shLangName);
		break;

	case 'category':
		if($id == null)
		{
			$dosef = false;
		}
		else
		{
			$query->clear()
				->select('*')
				->from($db->quoteName('#__cmgroupbuying_categories'))
				->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$category = $db->loadAssoc();

			if($db->getErrorNum())
			{
				$dosef = false;
			}
			else
			{
				if($configuration['sh404sef_category_alias'] != '')
				{
					$title[] = $configuration['sh404sef_category_alias'];
				}
				$title[] = $category['alias'];
				shRemoveFromGETVarsList('id');
			}
		}
		break;

	case 'partnerdeals':
		if($id == null)
		{
			$dosef = false;
		}
		else
		{
			$query->clear()
				->select('*')
				->from($db->quoteName('#__cmgroupbuying_partners'))
				->where($db->quoteName('id') . ' = ' . $db->quote($id));
			$db->setQuery($query);
			$partner = $db->loadAssoc();

			if($db->getErrorNum())
			{
				$dosef = false;
			}
			else
			{
				if($configuration['sh404sef_partner_alias'] != '')
				{
					$title[] = $configuration['sh404sef_partner_alias'];
				}
				$title[] = $partner['alias'];
				shRemoveFromGETVarsList('id');
			}
		}
		break;

	default:
		$dosef = false;
		break;
}

// Remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('task');

if(!empty($Itemid))
{
	shRemoveFromGETVarsList('Itemid');
}

if(!empty($limit))
{
	shRemoveFromGETVarsList('limit');
}

if(isset($limitstart))
{
	shRemoveFromGETVarsList('limitstart'); // Limitstart can be zero
}

shRemoveFromGETVarsList('view');

// Remove "?lang=xx" in URL
// CMGroupBuying doesn't support multi-language at the present time

shRemoveFromGETVarsList('lang');

if($dosef)
{
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
	(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
	(isset($shLangName) ? @$shLangName : null));
}