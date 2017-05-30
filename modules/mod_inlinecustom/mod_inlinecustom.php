<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;



$module->module = 'com_custom';
//can user edit item if not then skip
//$module->module = "mod_custom";

//load parameters
$params = new JRegistry;
$params->loadString($module->params);
$classes = $params->get('header_class',''); //check header class to see if we should allow inline editing

/*filter module to see if it is being used to load a module if so skip it
[widgetkit]
{loadmodule}
{loadposition}
{module}
{modulepos}
{component}
{article(s)}
*/
$test = preg_match('/\{(?:loadmodule|loadposition|module|modulepos|component|articles?)\s+(.*?)\}/i',$module->content);
if(!$test)
	$test = preg_match('/\[widgetkit\s+(.*?)\]/i',$module->content);
	
$user = JFactory::getUser();	
	
$canEdit = false;

if(version_compare(JVERSION, '3.4.0', 'ge'))
{
	$canEdit = 	($user->authorise('module.edit.frontend','com_modules.module.'.$module->id) || $user->authorise('module.edit.frontend', 'com_modules'));
}
else
{	
	$canEdit = 	($user->authorise('core.edit','com_modules.module.'.$module->id));
}	

//check to see if we user has disabled automatic addition of editable regions and if yes let's bail out
 $cParams = JComponentHelper::getParams('com_arkeditor');
 $autoDrawEditableRegionsComponentlist = $cParams->get('component_auto_enable_editable_regions_list',array());
		
if(!empty($autoDrawEditableRegionsComponentlist) && !in_array('com_content',$autoDrawEditableRegionsComponentlist))
{	
	$canEdit = false;
}



if(strpos($classes,'noEdit') === false && $canEdit && !$test)
{
    $inlineIsEnabled = true;
    $cParams = JComponentHelper::getParams('com_arkeditor');
	if(empty($cParams) || !$cParams->get('enable_inline',true))
	{
		$inlineIsEnabled = false;
	}
    
    
    if ($inlineIsEnabled)
	{
		$dataContext = 'module';
		$dataItemType = 'module';
		if($cParams->get('enable_editable_modules_titles',0))
		{	
			$module->title = '{div class=__ARKQUOTE__editable__ARKQUOTE__ data-id=__ARKQUOTE__'.$module->id.'__ARKQUOTE__ data-context=__ARKQUOTE__'.$dataContext.'__ARKQUOTE__ data-type=__ARKQUOTE__title__ARKQUOTE__ data-itemtype=__ARKQUOTE__'.$dataItemType.'__ARKQUOTE__ contenteditable=__ARKQUOTE__true__ARKQUOTE__ style=__ARKQUOTE__display:inline;__ARKQUOTE__}'.$module->title.'{/div}';
		}
		$module->content = '<div class="editable" data-id="'.$module->id.'" data-context="'.$dataContext.'" data-type="body"  data-itemtype="'.$dataItemType.'" contenteditable="true">'.$module->content.'</div>';
	}
}

require JPATH_BASE."/modules/mod_custom/mod_custom.php";