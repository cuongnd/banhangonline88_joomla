<?php
/**    
 * SocialBacklinks User helper file
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * SocialBacklinks User helper class
 * 
 * @static
 */
class SBHelpersUser extends JObject
{
	/**
	 * Returns list of all super administrators
	 * @return array
	 */
	public static function getSuperAdministrators( )
	{
		$query = 'SELECT u.* FROM `#__users` AS u'
				.' INNER JOIN `#__user_usergroup_map` uum ON uum.`user_id` = u.`id`'
				.' WHERE uum.`group_id` = 8'; // 8 - identifier of Super Users group
		
		$db = JFactory::getDBO( );
		$db->setQuery( $query );
		
		return $db->loadObjectList( );
	}
}
