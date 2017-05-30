<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
$jfbcLibraryFile = JPATH_ROOT . '/components/com_jfbconnect/libraries/facebook.php';
if (!JFile::exists($jfbcLibraryFile))
{
    echo "JFBConnect not found. Please reinstall.";
    return;
}
require_once ($jfbcLibraryFile);

require_once(dirname(__FILE__) . '/helper.php');
$helper = new modJFBCSocialShareHelper($params);

$fbClient = JFBConnectFacebookLibrary::getInstance();
$fbAppId = JFBCFactory::provider('facebook')->appId;
$renderKey = $fbClient->getSocialTagRenderKey();
$renderKeyString = " key=".$renderKey;

//General
$url = $params->get('url');
$href = '';
if(!$url)
    $url = SCSocialUtilities::getStrippedUrl();
if ($url)
    $href = ' href='.$url;
$layout = $params->get('layout_style');
$orientation = $params->get('orientation');
$position = $params->get('position');
$margin = $params->get('margin');
$padding = $params->get('padding');
$backgroundColor = $params->get('background_color');
$floatTop = trim($params->get('float_position_top'));
$floatLeft = trim($params->get('float_position_left'));

//Advanced
$userIntro = $params->get('user_intro');

//Facebook
$facebookEnable = $params->get('facebook_enable');
$facebookShareEnable = $params->get('facebook_share_enable');
$facebookShowFaces = $params->get('facebook_show_faces');
$facebookKidDirectedSite = $params->get('facebook_kid_directed_site');
$facebookWidth = $params->get('facebook_width');
$facebookHeight = $params->get('facebook_height');
$facebookVerb = $params->get('facebook_verb_to_display');
$facebookColorScheme = $params->get('facebook_color_scheme');
$facebookRef = $params->get('facebook_ref');
$facebookShowFacesValue = ($facebookShowFaces?'true':'false');
$facebookKidDirectedSiteValue = ($facebookKidDirectedSite?'true':'false');
$facebookShowShareButtonValue = ($facebookShareEnable?'true':'false');
$facebookHeight = $helper->addPxToString($facebookHeight);
$facebookWidth = $helper->addPxToString($facebookWidth);

//LinkedIn
$linkedinEnable = $params->get('linkedin_enable');
$linkedinShowZero = $params->get('linkedin_show_zero');

//Google
$googleEnable = $params->get('google_enable');
$googleWidth = $params->get('google_width');

//Twitter
$twitterEnable = $params->get('twitter_enable');

if($position == 'fixed') //Float
{
    $groupStyles = 'position: ' . $position .";";

    if(intval($floatTop) < 3000)
    {
        $floatTop = $helper->addPxToString($floatTop == '' ? "0" : $floatTop);
        $groupStyles .= 'top:'.$floatTop . ";";
    }
    else // Float to bottom instead of offset
        $groupStyles .= 'bottom:0px;';

    if(intval($floatLeft) < 3000)
    {
        $floatLeft = $helper->addPxToString($floatLeft == '' ? "0" : $floatLeft);
        $groupStyles .= 'left:'.$floatLeft . ";";
    }
    else // Float to right instead of offset
        $groupStyles .= 'right:0px;';

    $groupStyles .= 'width: auto;';
}
else
    $groupStyles = '';

if($margin != '')
    $groupStyles .= 'margin:'.$helper->addPxToString($margin).";";
if($padding != '')
    $groupStyles .= 'padding:'.$helper->addPxToString($padding).";";
if($backgroundColor != '')
    $groupStyles .= 'background-color:'.$backgroundColor.';';

require(JModuleHelper::getLayoutPath('mod_jfbcsocialshare'));
?>
