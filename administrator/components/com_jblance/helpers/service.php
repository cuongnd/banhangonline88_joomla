<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	11 November 2014
 * @file name	:	helpers/service.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
defined('_JEXEC') or die('Restricted access');

class ServiceHelper {
	
	/**
	 * This function calculates the service fee for the addons subscribed by the user
	 * 
	 * @param integer $service_id id of service
	 * @param array $addons addons user has subscribed to
	 * @param string $user_type
	 * 
	 * @return float $project_fee calculated project commission
	 */
	function calculateServiceTotalPrice($service_id, $addons){
		
		$return = array();
		$totalPrice = 0;
		$totalDuration = 0;
		$service	= JTable::getInstance('service', 'Table');
		$service->load($service_id);
		
		$totalPrice += $service->price;
		$totalDuration += $service->duration;
		
		$registry = new JRegistry();
		$registry->loadString($service->extras);
		$extras = $registry->toArray();
		
		if(is_array($addons)){
			foreach($addons as $addonKey=>$addonValue){
				if(array_key_exists($addonKey, $extras)){
					$totalPrice = $totalPrice + $extras[$addonKey]['price'];
					if($addonKey === 'fast'){
						$totalDuration = $totalDuration + $extras[$addonKey]['duration'] - $service->duration;
					}
					else
						$totalDuration = $totalDuration + $extras[$addonKey]['duration'];
				}
				
			}
		}
		
		$return['totalPrice'] = $totalPrice;
		$return['totalDuration'] = $totalDuration;
		//print_r($return);
		return $return;
	}
	
	function getAddonDetails($service_id, $addons){
		$return = array();
		$service	= JTable::getInstance('service', 'Table');
		$service->load($service_id);
		
		$registryExtras = new JRegistry();
		$registryExtras->loadString($service->extras);
		$extras = $registryExtras->toArray();
		
		$registryAddons = new JRegistry();
		$registryAddons->loadString($addons);
		$addons = $registryAddons->toArray();
		
		if(is_array($addons)){
			foreach($addons as $addonKey=>$addonValue){
				if(array_key_exists($addonKey, $extras)){
					$return[$addonKey] = $extras[$addonKey];
				}
			}
		}
		
		//print_r($return);
		return $return;
	}
	
}