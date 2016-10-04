<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(is_file(JPATH_ROOT.'/libraries/juloalib/Lib.php')){
    include_once(JPATH_ROOT.'/libraries/juloalib/Lib.php');
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
	define('CLI',1);
} else {
	define('CLI',0);
}

if(!defined('JPATH_IMAGES_FOLDER')){
	define('JPATH_IMAGES_FOLDER',JPATH_ROOT."/images/com_adsmanager/contents");
}
if(!defined('JURI_IMAGES_FOLDER')){
	define('JURI_IMAGES_FOLDER',JURI::root()."images/com_adsmanager/contents");
}

/**
 * Guess what? TModel,Controller are interfaces in Joomla! 3.0. Holly smoke, Batman!
 */
if(!class_exists('TController')) {
	if(interface_exists('JController')) {
		abstract class TController extends JControllerLegacy {}
	} else {
		jimport('joomla.application.component.controller');
		class TController extends JController {}
	}
}


if(!class_exists('TModel')) {
	if(interface_exists('JModel')) {
		abstract class TModel extends JModelLegacy {}
	} else {
		jimport('joomla.application.component.model');
		class TModel extends JModel {}
	}
}
if(!class_exists('TView')) {
	if(interface_exists('JView')) {
		abstract class TView extends JViewLegacy {}
	} else {
		jimport('joomla.application.component.view');
		class TView extends JView {}
	}
}

if (version_compare(JVERSION,'3.0.0','>=')) {
	define('JOOMLA_J3',1);		
} else {
	define('JOOMLA_J3',0);
}



require_once('phpcompat.php');
require_once('route.php');
require_once('pagination.php');
require_once('tools.php');
require_once('pane.php');
require_once('mail.php');
require_once('tconf.php');
require_once('link.php');
require_once('cron.php');
if(version_compare(JVERSION, '1.6', 'ge')) {
    require_once('tpermissions.php');
}
require_once('ttext.php');
require_once('tdatabase.php');
require_once(JPATH_ROOT.'/components/com_adsmanager/helpers/image.php');

require_once(JPATH_ROOT."/administrator/components/com_adsmanager/helpers/select.php");


// Load Paidsystem
if ( file_exists( JPATH_ROOT. "/components/com_paidsystem/api.paidsystem.php")) 
{
	define('PAIDSYSTEM',1);
	require_once(JPATH_ROOT . "/components/com_paidsystem/api.paidsystem.php");
} else {
	define('PAIDSYSTEM',0);
}


if (CLI == 0) {
	//special override for images
	$app = JFactory::getApplication();
	
	$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
	if (is_file($templateDir.'/html/com_adsmanager/images/nopic.gif')) {
		define('ADSMANAGER_NOPIC_IMG',JURI::root() . 'templates/' . $app->getTemplate().'/html/com_adsmanager/images/nopic.gif');
	} else {
		define('ADSMANAGER_NOPIC_IMG',JURI::root() . 'components/com_adsmanager/images/nopic.gif');
	}
}


// Special config
$config = TConf::getConfig();
if (isset($config->special))
	define('ADSMANAGER_SPECIAL',$config->special);
else
	define('ADSMANAGER_SPECIAL','');

//Community Builder settings
if (file_exists(JPATH_ROOT.'/components/com_comprofiler/')) {
	define('COMMUNITY_BUILDER',1);
	if (($config->comprofiler == 2)&&(file_exists(JPATH_ROOT.'/components/com_comprofiler/plugin/user/plug_adsmanager-tab'))) {
		define('COMMUNITY_BUILDER_ADSTAB',1);
	} else {
		define('COMMUNITY_BUILDER_ADSTAB',0);
	}
    if (($config->comprofiler == 2)&&(file_exists(JPATH_ROOT.'/components/com_comprofiler/plugin/user/plug_adsmanagerfavorite-tab'))) {
		define('COMMUNITY_BUILDER_ADSFAVORITETAB',1);
	} else {
		define('COMMUNITY_BUILDER_ADSFAVORITETAB',0);
	}
} else {
	define('COMMUNITY_BUILDER',0);
	define('COMMUNITY_BUILDER_ADSTAB',0);
    define('COMMUNITY_BUILDER_ADSFAVORITETAB',0);
}

if (file_exists(JPATH_ROOT.'/components/com_community/')) {
	define('JOMSOCIAL',1);
    
	if ($config->comprofiler == 2) {
		define('JOMSOCIAL_ADSTAB',1);
	} else {
		define('JOMSOCIAL_ADSTAB',0);
	}
} else {
	define('JOMSOCIAL',0);
	define('JOMSOCIAL_ADSTAB',0);
}


//Jquery non conflict mode
if (CLI == 0) {
	//Lib/core.php is called in router.php. In this case, if joomla is set to use add suffix, url is like XXXX.feed
	// there is no format=feed. This will be transform to format=feed only onAfterRoute so little hack here to force format
	// for getDocument otherview document will be JDocumentHTML
	if (strpos($_SERVER['REQUEST_URI'],'.feed') !== false) {
		JRequest::setVar('format', 'feed');
	}
	if (method_exists('JuloaLib','loadJquery')) {
		JuloaLib::loadJquery();
	}
}

require_once(JPATH_ROOT.'/components/com_adsmanager/helpers/category.php');

if (!function_exists("getMultiLangText")) {
	function getMultiLangText($value) {
		$values = @json_decode($value);
		$lg = JFactory::getLanguage();
		$currenttag =  str_replace("-","_",$lg->getTag());
		if ($values != null) {
			if (@$values->$currenttag != "")
				return $values->$currenttag;
			else if (($currenttag != "en_GB")&&(@$values->en_GB != ""))
				return $values->en_GB;
			else {
				foreach($values as $tag => $val) {
					if ($val != null)
						return $val;
				}
			}
		} else
			return null;
	}
}

function cleanAdsManagerCache()
{
	$conf = JFactory::getConfig();

	$options = array(
			'defaultgroup' => 'com_adsmanager',
			'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache'));
	$cache = JCache::getInstance('callback', $options);
	$cache->clean();
}
// Merge the language overrides
$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
$jlang = JFactory::getLanguage();
$jlang->load("com_adsmanager", $paths[0], 'en-GB', true);
$jlang->load("com_adsmanager", $paths[0], null, true);
$jlang->load("com_adsmanager", $paths[1], 'en-GB', true);
$jlang->load("com_adsmanager", $paths[1], null, true);

function loadAdsManagerCss() {
	if (!defined( '_ADSMANAGER_CSS' )) {
		/** ensure that functions are declared only once */
		define( '_ADSMANAGER_CSS', 1 );
		$uri = JFactory::getURI();
		$baseurl = JURI::root();
			
		$document = JFactory::getDocument();
		
		$conf = TConf::getConfig();
			
		if(!isset($conf->bootstrap_loading) || $conf->bootstrap_loading != 2){
			include_once(JPATH_ROOT.'/libraries/juloalib/Lib.php');
			JuloaLib::loadCSS('bootstrap2');
		}
			
		$app = JFactory::getApplication();
		$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
		if (is_file($templateDir.'/html/com_adsmanager/css/adsmanager.css')) {
			$templateDir = JURI::root() . 'templates/' . $app->getTemplate();
			$document->addStyleSheet($templateDir.'/html/com_adsmanager/css/adsmanager.css');
		} else {
			$document->addStyleSheet($baseurl.'components/com_adsmanager/css/adsmanager.css');
		}
	}	
}

function getImagePath($imageName = '') {
    if($imageName == '') {
        return false;
    }
    
    $app = JFactory::getApplication();
    $templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
    if (is_file($templateDir.'/html/com_adsmanager/images/'.$imageName)) {
        $templateDir = JURI::root() . 'templates/' . $app->getTemplate();
        return $templateDir.'/html/com_adsmanager/images/'.$imageName;
    } else {
        return JURI::root().'components/com_adsmanager/images/'.$imageName;
    }
}