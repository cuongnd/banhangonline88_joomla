<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgHikashopVendorlocationfilter extends JPlugin {

	protected $db = null;
	protected $pluginParams = null;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$pluginsClass = hikashop_get('class.plugins');
		$plugin = $pluginsClass->getByName('hikashop', 'vendorlocationfilter');
		if(!empty($plugin))
			$this->pluginParams = $plugin->params;
		else
			return false;

		if(empty($this->pluginParams) || !isset($this->pluginParams['use_search_module']))
			return false;

		$this->db = JFactory::getDBO();

		$init = defined('HIKAMARKET_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKAMARKET_COMPONENT');
			}
		}
		return $init;
	}

	public function onBeforeProductListingLoad(&$filters, &$order, &$view, &$select, &$select2, &$ON_a, &$ON_b, &$ON_c) {
		$app = JFactory::getApplication();
		if($app->isAdmin() || !$this->init())
			return;

		$use_search_module = (int)@$this->pluginParams['use_search_module'];
		$use_cache = (int)@$this->pluginParams['use_cache'];
		$empty_is_all = (int)@$this->pluginParams['empty_is_all'];
		$include_logged_vendor = (int)@$this->pluginParams['include_logged_vendor'];
		$force_main_vendor = (int)@$this->pluginParams['force_main_vendor'];

		$location_search = null;
		if($use_search_module) {
			$location_search = JRequest::getVar('location_search', null);
			if($location_search !== null) {
				$app->setUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.search', $location_search);
			} else {
				$location_search = $app->getUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.search', null);
			}
		}

		$filter_mode = 'zip;city';
		if(!empty($this->pluginParams['filter_mode']))
			$filter_mode = trim($this->pluginParams['filter_mode']);
		$filter_mode = explode(';', $filter_mode);

		$vendorZipColumn = 'accepted_zip';
		if(!empty($this->pluginParams['vendor_zip_column']))
			$vendorZipColumn = trim($this->pluginParams['vendor_zip_column']);

		$vendorCitiesColumn = 'accepted_cities';
		if(!empty($this->pluginParams['vendor_city_column']))
			$vendorCitiesColumn = trim($this->pluginParams['vendor_city_column']);

		$includedUsersColumn = '';
		if(!empty($this->pluginParams['vendor_userinc_column']))
			$includedUsersColumn = trim($this->pluginParams['vendor_userinc_column']);

		$excludedUsersColumn = '';
		if(!empty($this->pluginParams['vendor_userexc_column']))
			$excludedUsersColumn = trim($this->pluginParams['vendor_userexc_column']);

		$user_id = hikashop_loadUser();
		if(empty($user_id) && !$use_search_module) {
			if(!empty($this->pluginParams['hide_for_guest'])) {
				if(in_array('zip', $filter_mode))
					$filters[] = '(hikam_vendor.'. $vendorZipColumn.' IS NULL OR hikam_vendor.'. $vendorZipColumn.' = \'\' OR hikam_vendor.'. $vendorZipColumn.' = \'*\')';
				if(in_array('city', $filter_mode))
					$filters[] = '(hikam_vendor.'. $vendorCitiesColumn.' IS NULL OR hikam_vendor.'. $vendorCitiesColumn.' = \'\' OR hikam_vendor.'. $vendorCitiesColumn.' = \'*\')';
			}
			return;
		}

		$addressClass = hikashop_get('class.address');
		$addresses = $addressClass->loadUserAddresses($user_id);
		$userZip = 0;
		$userCity = '';
		if(!empty($addresses)) {
			$address = reset($addresses);
			if(!empty($address->address_post_code))
				$userZip = $address->address_post_code;
			if(!empty($address->address_city))
				$userCity = $address->address_city;
		}

		if( (in_array('zip', $filter_mode) && !empty($userZip)) || (in_array('city', $filter_mode) && !empty($userCity)) || $location_search !== null) {

			$vendorIds = null;
			if($use_cache) {
				$vendorIds = $app->getUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.vendor_list');
				$session_userZip = $app->getUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_user_zip');
				$session_userCity = $app->getUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_user_city');
				$session_search = $app->getUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_search');
				if($session_userZip != $userZip || $session_userCity != $userCity || $session_search != $location_search)
					$vendorIds = null;
			}

			if(empty($vendorIds)) {

				$filters = array();

				if(in_array('zip', $filter_mode)) {
					$f = array();
					$stars = '*';
					if($location_search !== null || !empty($userZip)) {
						if($location_search !== null) {
							if(HIKASHOP_J30)
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->escape($location_search, true).'%\'';
							else
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($location_search, true).'%\'';
						} else {
							if(HIKASHOP_J30)
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->escape($userZip, true).'%\'';
							else
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($userZip, true).'%\'';
						}
						for($i = strlen($userZip) - 1; $i >= 0; $i--) {
							$z = substr($userZip, 0, $i) . $stars;

							if(HIKASHOP_J30)
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->escape($z, true).'%\'';
							else
								$f[] = $vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($z, true).'%\'';
							$stars .= '*';
						}
					}
					unset($stars);

					if(!empty($f))
						$filters[] = '('.implode(') OR (', $f).')';
				}

				if(in_array('city', $filter_mode) && ($location_search !== null || !empty($userCity))) {
					if($location_search !== null) {
						if(HIKASHOP_J30)
							$filters[] = $vendorCitiesColumn.' LIKE \'%'.$this->db->escape($location_search, true).'%\'';
						else
							$filters[] = $vendorCitiesColumn.' LIKE \'%'.$this->db->getEscaped($location_search, true).'%\'';
					} else {
						if(HIKASHOP_J30)
							$filters[] = $vendorCitiesColumn.' LIKE \'%'.$this->db->escape($userCity, true).'%\'';
						else
							$filters[] = $vendorCitiesColumn.' LIKE \'%'.$this->db->getEscaped($userCity, true).'%\'';
					}
				}

				$query = 'SELECT vendor_id FROM '.hikamarket::table('vendor').' WHERE ('.implode(') OR (', $filters).')';
				$this->db->setQuery($query);
				try {
					$vendors = $this->db->loadObjectList('vendor_id');
				} catch(Exception $e) {
					$vendors = array();
				}
				if(empty($vendors))
					$vendors = array();
				$vendors = array_keys($vendors);
				$vendorIds = array_combine($vendors, $vendors);
				unset($vendors);

				if($force_main_vendor) {
					$vendorIds[0] = 0;
					$vendorIds[1] = 1;
				}

				if($include_logged_vendor) {
					$currentVendor = hikamarket::loadVendor();
					if(!empty($currentVendor))
						$vendorIds[$currentVendor] = $currentVendor;
				}

				if($use_cache) {
					$app->setUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.vendor_list', $vendorIds);
					$app->setUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_user_zip', $userZip);
					$app->setUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_user_city', $userCity);
					$app->setUserState(HIKAMARKET_COMPONENT.'.vendor_location_filter.cache_search', $location_search);
				}
			}

			if(!empty($vendorIds))
				$filters[] = '(hikam_vendor.vendor_id IS NULL OR hikam_vendor.vendor_id IN ('.implode(',',$vendorIds).'))';

		} elseif(!empty($this->pluginParams['hide_for_guest'])) {
			if(in_array('zip', $filter_mode))
				$filters['vendor_zip_filter'] = '(hikam_vendor.'. $vendorZipColumn.' IS NULL OR hikam_vendor.'. $vendorZipColumn.' = \'\' OR hikam_vendor.'. $vendorZipColumn.' = \'*\')';
			if(in_array('city', $filter_mode))
				$filters['vendor_city_filter'] = '(hikam_vendor.'. $vendorCitiesColumn.' IS NULL OR hikam_vendor.'. $vendorCitiesColumn.' = \'\' OR hikam_vendor.'. $vendorCitiesColumn.' = \'*\')';
		}

		if(isset($filters['vendor_city_filter']) && isset($filters['vendor_zip_filter'])) {
			$filters['vendor_location_filter'] = '(' . $filters['vendor_city_filter'] . ') OR (' . $filters['vendor_zip_filter'] . ')';
			unset($filters['vendor_city_filter']);
			unset($filters['vendor_zip_filter']);
		}
	}
}
