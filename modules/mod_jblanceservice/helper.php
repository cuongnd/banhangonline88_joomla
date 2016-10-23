<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	28 January 2015
 * @file name	:	modules/mod_jblanceservice/helper.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
 // no direct access
 defined('_JEXEC') or die('Restricted access');
 include_once(JPATH_ADMINISTRATOR.'/components/com_jblance/helpers/jblance.php');	//include this helper file to make the class accessible in all other PHP files
 include_once(JPATH_ADMINISTRATOR.'/components/com_jblance/helpers/link.php');	//include this helper file to make the class accessible in all other PHP files
 JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jblance/tables');

class ModJblanceServiceHelper {	
	
	public static function getLatestServices(&$params){
		$db	  = JFactory::getDbo();
		$user = JFactory::getUser();
		$now  = JFactory::getDate();
		
		$limit_service  = intval($params->get('limit_service', 20));
		$ordering 		= $params->get('ordering', 's.id');
		
		if($ordering == 'rand()'){
			$direction = '';
		}
		else {
			$direction 	= $params->get('direction', 1) ? 'DESC' : 'ASC';
		}
		
		$orderby = ' ORDER BY '.$ordering.' '.$direction;
		
		$query = "SELECT s.* FROM #__jblance_service s".
				" WHERE s.approved=1".
				$orderby.
				" LIMIT 0,".$limit_service;	//echo $query;
		$db->setQuery($query);
		$db->execute();
		$total = $db->getNumRows();
	
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		return $rows;
	}
}
?>