<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die( 'Restricted access' );
	
require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/configuration.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/content.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/adsmanager.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/column.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/category.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/content.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/field.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/user.php');

require_once(JPATH_ROOT."/components/com_adsmanager/helpers/field.php");

$uri = JFactory::getURI();
$baseurl = JURI::base();
	
loadAdsManagerCss();

if (!defined('_ADSMANAGER_MODULE_ADS')) {
	define( '_ADSMANAGER_MODULE_ADS', 1 );
	function isNewContent($date,$nbdays) {
		$time = strtotime($date);
		if ($time >= (time()-($nbdays*24*3600)))
			return true;
		else
			return false;
	}
	
	function reorderDate( $date ){
		$format = JText::_('ADSMANAGER_DATE_FORMAT_LC');
		
		if ($date && (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$date,$regs))) {
			$date = mktime( 0, 0, 0, $regs[2], $regs[3], $regs[1] );
			$date = $date > -1 ? strftime( $format, $date) : '-';
		}
		return $date;
	}
	
	function recurseSearch($rows,&$list,$catid){
		if(isset($rows))
		{
			foreach($rows as $row) {
				if ($row->parent == $catid)
				{
					$list[]= $row->id;
					recurseSearch($rows,$list,$row->id);
				}
			}
		}
	}
}

if ( file_exists( JPATH_BASE. "/components/com_paidsystem/api.paidsystem.php")) 
{
	require_once(JPATH_BASE . "/components/com_paidsystem/api.paidsystem.php");
}

$sort_sql = intval($params->get( 'random',0));

$catid = $params->get('catselect',"0");
$catselect = $catid;

$confmodel  = new AdsmanagerModelConfiguration();
$conf = $confmodel->getConfiguration();
$nb_images = $conf->nb_images;
$nb_ads = intval($params->get( 'nb_ads', 3 )) ;
$rootid = $params->get('rootid',null);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$contentmodel  = new AdsmanagerModelContent();
$contents = $contentmodel->getLatestContents($nb_ads,$sort_sql,$catselect,$rootid);

if (function_exists("getMaxPaidSystemImages"))
{
	$nb_images += getMaxPaidSystemImages();
}

if ($rootid!=0) {
	$urlparamroot = "&rootid=$rootid";
} else {
	$urlparamroot = "";
}

$uri = JFactory::getURI();
$baseurl = JURI::base();

$catmodel		 = new AdsmanagerModelCategory();
$columnmodel	 = new AdsmanagerModelColumn();
$fieldmodel	     = new AdsmanagerModelField();
$usermodel		 = new AdsmanagerModelUser();
$adsmanagermodel = new AdsmanagerModelAdsmanager();

$user	= JFactory::getUser();
$userid = $user->id;

$columns = $columnmodel->getColumns($catid);
$fColumns = $fieldmodel->getFieldsbyColumns();
		
$field_values = $fieldmodel->getFieldValues();	
$plugins = $fieldmodel->getPlugins();
$field = new JHTMLAdsmanagerField($conf,$field_values,"1",$plugins,null);
	
require(JModuleHelper::getLayoutPath('mod_adsmanager_table',$params->get( 'layout','table')));
$content="";
$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
JTable::addIncludePath($path);