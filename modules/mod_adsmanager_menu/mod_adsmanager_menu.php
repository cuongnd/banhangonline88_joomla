<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/category.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/configuration.php');



if (!function_exists("displayMenuCats")) {
	function displayMenuCats($id, $level, &$children,$current_list,$displaynumads,$rootid) {
		global $cur_template;
		$catid = JRequest::getInt('catid', -1 );
		
		if (@$children[$id]) {
			foreach ($children[$id] as $row) {
				 if ($row->id == $catid) 
				 	$class = "current active";
				 else if (@$current_list[count($current_list) - 1 -$level] == $row->id)
				 	$class = "deeper parent active";
				 else
				 	$class= "";
				 ?>
				 <li class="<?php echo $class?>">
				 <?php
				 if ($rootid != 0) {
					$link = TRoute::_("index.php?option=com_adsmanager&view=list&rootid=$rootid&catid=".$row->id);
				 } else {
				 	$link = TRoute::_("index.php?option=com_adsmanager&view=list&catid=".$row->id);
				 }
				 if ($displaynumads == 1)
				 {
					echo '<a href="'.$link.'" ><span>'.$row->name.' ('.$row->num_ads.')</span></a>';	
				 }
				 else
				 {
					echo '<a href="'.$link.'" ><span>'.$row->name.'</span></a>';
				 }
				 if (@$current_list[count($current_list) - 1 -$level] == $row->id)
				 {
				 	echo "<ul>";
					displayMenuCats($row->id, $level+1, $children,$current_list,$displaynumads,$rootid);
					echo "</ul>";
				 }
				 ?>
				 </li>
				 <?php
			}
		}
	}
}

/****************************************************/
$catid = JRequest::getInt('catid', -1 );
$displaynumads = $params->def('displaynumads',1);

$catmodel  = new AdsmanagerModelCategory();
$rootid = (int)$params->def('rootid',0);
$cats = $catmodel->getCatTree(true,true,$nbcontents,"read",$rootid);

$displayhome = $params->def('displayhome',1);
$displaywritead = $params->def('displaywritead',1);
$displayprofile = $params->def('displayprofile',1);
$displaymyads = $params->def('displaymyads',1);
$displayrules = $params->def('displayrules',1);
$displayallads = $params->def('displayallads',1);
$displaycategories = $params->def('displaycategories',1);
$displayfavorites = $params->def('displayfavorites',1);
$displayseparators = $params->def('displayseparators',1);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if ($displaycategories == 1) {
	$cc = $catmodel->getCategories(true,"read",$rootid);
	$orderlist = array();
	// first pass - collect children
	foreach ($cc as $v ) {
		$orderlist[$v->id] = $v;
	}
	
	$current_list[] = $catid;
	if ($catid != -1)
	{
		$current = $catid;
		while((isset($orderlist[$current])) && ($orderlist[$current]->parent != 0))
		{
				$current_list[] = $orderlist[$current]->parent;
				$current = $orderlist[$current]->parent;
		}
	}
}

$link_show_profile = TLink::getProfileLink();
$link_show_user = TLink::getMyAdsLink();

$user = JFactory::getUser();

if ($rootid!=0) {
	$urlparamroot = "&rootid=$rootid";
} else {
	$urlparamroot = "";
}

$link_front = TRoute::_("index.php?option=com_adsmanager&view=front$urlparamroot");
$link_write_ad = TRoute::_("index.php?option=com_adsmanager&task=write$urlparamroot");
$link_show_rules = TRoute::_("index.php?option=com_adsmanager&view=rules$urlparamroot");
$link_show_all = TRoute::_("index.php?option=com_adsmanager&view=list$urlparamroot");
$link_favorites = TLink::getMyFavoritesLink();

require(JModuleHelper::getLayoutPath('mod_adsmanager_menu',$params->get( 'layout','default')));
$content="";
$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
JTable::addIncludePath($path);