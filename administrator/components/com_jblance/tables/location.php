<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	12 March 2015
 * @file name	:	tables/location.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Locations(jblance)
 */
 defined('_JEXEC') or die('Restricted access');

 class TableLocation extends JTableNested {
 
	/* var $id = null;
	var $title = null;
	var $parent_id = null;
	var $lft = null;
	var $rgt = null;
	var $level = null;
	var $ordering = null;
	var $published = null;
	var $params = null; */
 
 	/**
 	 * @param database A database connector object
 	 */
 	function __construct(JDatabaseDriver $db){
 		parent::__construct('#__jblance_location', 'id', $db);
 	}
 	
 	/**
 	 * Method to retrieve children from parent ID
 	 **/
 	public function getChildren($parent_id){
 		$db		= $this->getDbo();
 	
 		$query	= "SELECT n.id,n.title FROM #__jblance_location AS n, #__jblance_location AS p ".
				  "WHERE n.lft BETWEEN p.lft AND p.rgt AND p.id = ".$db->quote($parent_id)." ORDER BY n.lft";
 		$db->setQuery($query);
 	
 		return $db->loadColumn();
 	}
 	
 	/**
 	 * Method to retrieve parent from child ID
 	 **/
 	public function getParent($child_id){
 		$db		= $this->getDbo();
 	
 		$query	= "SELECT p.id,p.title FROM #__jblance_location AS n, #__jblance_location AS p ".
				  "WHERE n.lft BETWEEN p.lft AND p.rgt AND n.id = ".$db->quote($child_id)." ORDER BY p.lft";
 		$db->setQuery($query);
 	
 		return $db->loadColumn();
 	}
 }
 ?>