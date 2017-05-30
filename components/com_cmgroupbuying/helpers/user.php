<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperUser
{
	public static function getUserProfile($userId, $profileSetting)
	{
		$profile = array(
			"name" => JFactory::getUser()->name,
			"firstname" => "",
			"lastname" => "",
			"address" => "",
			"city" => "",
			"state" => "",
			"zip" => "",
			"phone" => "",
			"email" => JFactory::getUser()->email
		);

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('profile'))
			->from($db->quoteName('#__cmgroupbuying_user_profile'))
			->where($db->quoteName('id') . ' = ' . $db->quote('1'));

		$db->setQuery($query);
		$value = $db->loadResult();

		if($value == "joomla")
		{
			$userId = (int) $userId;
			// Load the profile data from the database.
			$db = JFactory::getDbo();
			$db->setQuery(
				'SELECT profile_key, profile_value FROM #__user_profiles' .
				' WHERE user_id = '. $db->quote($userId) . ' AND profile_key LIKE ' . $db->quote('profile.%') .
				' ORDER BY ordering'
			);
			$results = $db->loadAssocList();

			if(!empty($results))
			{
				$tempProfile = array();

				foreach($results as $result)
				{
					$tempProfile[$result['profile_key']] = json_decode($result['profile_value']);
				}

				$profile["firstname"] = '';
				$profile["lastname"] = '';
				$profile["address"] = isset($tempProfile['profile.address1']) ? $tempProfile['profile.address1'] : '';
				$profile["city"] = isset($tempProfile['profile.city']) ? $tempProfile['profile.city'] : '';
				$profile["state"] = isset($tempProfile['profile.region']) ? $tempProfile['profile.region'] : '';
				$profile["zip"] = isset($tempProfile['profile.postal_code']) ? $tempProfile['profile.postal_code'] : '';
				$profile["phone"] = isset($tempProfile['profile.phone']) ? $tempProfile['profile.phone'] : '';
			}
		}
		elseif($value == "jomsocial")
		{
			if(CMGroupBuyingHelperJomsocial::checkInstalled())
			{
				$jsAPI  = JPATH_ROOT . '/components/com_community/libraries/core.php';
				if(file_exists($jsAPI))
				{
					require_once($jsAPI);

					$user = CFactory::getUser($userId);

					// Please change the following field codes if you use custom field codes
					$addressFieldCode = "FIELD_ADDRESS";
					$cityFieldCode = "FIELD_CITY";
					$stateFieldCode = "FIELD_STATE";
					$zipCodeFieldCode = "";
					$phoneFieldCode = "FIELD_LANDPHONE";

					$profile["firstname"] = '';
					$profile["lastname"] = '';
					$profile["address"] = $user->getInfo($profileSetting['profile_address_value']);
					$profile["city"] = $user->getInfo($profileSetting['profile_city_value']);
					$profile["state"] = $user->getInfo($profileSetting['profile_state_value']);
					$profile["zip"] = $user->getInfo($profileSetting['profile_zip_value']);
					$profile["phone"] = $user->getInfo($profileSetting['profile_phone_value']);
				}
			}
		}
		elseif($value == "cb")
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__comprofiler');
			$query->where('user_id = ' . $userId);
			$db->setQuery($query);
			$userData = $db->loadAssoc();

			if(!empty($userData))
			{
				$nameCol = $profileSetting['profile_name_value'];
				$firstNameCol = $profileSetting['profile_firstname_value'];
				$lastNameCol = $profileSetting['profile_lastname_value'];
				$addressCol = $profileSetting['profile_address_value'];
				$cityCol = $profileSetting['profile_city_value'];
				$stateCol = $profileSetting['profile_state_value'];
				$zipCol = $profileSetting['profile_zip_value'];
				$phoneCol = $profileSetting['profile_phone_value'];

				if(isset($userData[$nameCol]))
					$profile["name"] = $userData[$nameCol];
				
				if(isset($userData[$firstNameCol]))
					$profile["firstname"] = $userData[$firstNameCol];

				if(isset($userData[$lastNameCol]))
					$profile["lastname"] = $userData[$lastNameCol];

				if(isset($userData[$addressCol]))
					$profile["address"] = $userData[$addressCol];

				if(isset($userData[$cityCol]))
					$profile["city"] = $userData[$cityCol];

				if(isset($userData[$stateCol]))
					$profile["state"] = $userData[$stateCol];

				if(isset($userData[$zipCol]))
					$profile["zip"] = $userData[$zipCol];

				if(isset($userData[$phoneCol]))
					$profile["phone"] = $userData[$phoneCol];
			}
		}

		return $profile;
	}
}