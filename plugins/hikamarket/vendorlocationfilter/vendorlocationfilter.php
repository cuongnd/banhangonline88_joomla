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
class plgHikamarketVendorlocationfilter extends JPlugin {

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
		$init = true;
		return true;
	}

	public function onBeforeVendorListingDisplay(&$view, &$params) {
		$app = JFactory::getApplication();
		if($app->isAdmin() || !$this->init())
			return;

		$use_search_module = (int)@$this->pluginParams['use_search_module'];
		$empty_is_all = (int)@$this->pluginParams['empty_is_all'];


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

		$user_id = hikashop_loadUser();
		if(empty($user_id) && !$use_search_module) {
			if(!empty($this->pluginParams['hide_for_guest'])) {
				if(in_array('zip', $filter_mode))
					$params['filter']['hide_zip_guest'] = '(vendor.'. $vendorZipColumn.' IS NULL OR vendor.'. $vendorZipColumn.' = \'\' OR vendor.'. $vendorZipColumn.' = \'*\')';
				if(in_array('city', $filter_mode))
					$params['filter']['hide_city_guest'] = '(vendor.'. $vendorCitiesColumn.' IS NULL OR vendor.'. $vendorCitiesColumn.' = \'\' OR vendor.'. $vendorCitiesColumn.' = \'*\')';
			}
			return;
		}

		if( (in_array('zip', $filter_mode) && !empty($userZip)) || (in_array('city', $filter_mode) && !empty($userCity)) || $location_search !== null) {
			if(in_array('zip', $filter_mode)) {
				$f = array();
				$stars = '*';
				if($location_search !== null || !empty($userZip)) {
					if($location_search !== null) {
						if(HIKASHOP_J30)
							$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->escape($location_search, true).'%\'';
						else
							$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($location_search, true).'%\'';
					} else {
						if(HIKASHOP_J30)
							$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->escape($userZip, true).'%\'';
						else
							$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($userZip, true).'%\'';
					}
					if(!empty($userZip)) {
						for($i = strlen($userZip) - 1; $i >= 0; $i--) {
							$z = substr($userZip, 0, $i) . $stars;

							if(HIKASHOP_J30)
								$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->escape($z, true).'%\'';
							else
								$f[] = 'vendor.'.$vendorZipColumn.' LIKE \'%'.$this->db->getEscaped($z, true).'%\'';
							$stars .= '*';
						}
					}
				}
				unset($stars);

				if(!empty($f))
					$params['filter']['zip_filter'] = '('.implode(') OR (', $f).')';
			}

			if(in_array('city', $filter_mode) && ($location_search !== null || !empty($userCity))) {
				if($location_search !== null) {
					if(HIKASHOP_J30)
						$params['filter']['city_filter'] = 'vendor.'.$vendorCitiesColumn.' LIKE \'%'.$this->db->escape($location_search, true).'%\'';
					else
						$params['filter']['city_filter'] = 'vendor.'.$vendorCitiesColumn.' LIKE \'%'.$this->db->getEscaped($location_search, true).'%\'';
				} else {
					if(HIKASHOP_J30)
						$params['filter']['city_filter'] = 'vendor.'.$vendorCitiesColumn.' LIKE \'%'.$this->db->escape($userCity, true).'%\'';
					else
						$params['filter']['city_filter'] = 'vendor.'.$vendorCitiesColumn.' LIKE \'%'.$this->db->getEscaped($userCity, true).'%\'';
				}
			}
		} elseif(!empty($this->pluginParams['hide_for_guest'])) {
			if(in_array('zip', $filter_mode))
				$params['filter']['zip_filter'] = '(vendor.'. $vendorZipColumn.' IS NULL OR vendor.'. $vendorZipColumn.' = \'\' OR vendor.'. $vendorZipColumn.' = \'*\')';
			if(in_array('city', $filter_mode))
				$params['filter']['city_filter'] = '(vendor.'. $vendorCitiesColumn.' IS NULL OR vendor.'. $vendorCitiesColumn.' = \'\' OR vendor.'. $vendorCitiesColumn.' = \'*\')';
		}

		if(isset($params['filter']['zip_filter']) && isset($params['filter']['city_filter'])) {
			$params['filter']['location_filter'] = '(' . $params['filter']['zip_filter'] . ') OR (' . $params['filter']['city_filter'] . ')';
			unset($params['filter']['zip_filter']);
			unset($params['filter']['city_filter']);
		}
	}
}
