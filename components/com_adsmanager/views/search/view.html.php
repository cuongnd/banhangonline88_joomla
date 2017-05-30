<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/configuration.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/field.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/searchmodule.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/category.php');
require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewSearch extends TView
{
	function display($tpl = null)
	{
		jimport( 'joomla.session.session' );	
        $currentSession = JSession::getInstance('none',array());
        $defaultvalues = $currentSession->get("search_fields",array());

        $catid = JRequest::getInt('catid', 0 );
        if ($catid == 0)
       		$catid = $currentSession->get("searchfieldscatid",0);
        
		$app = JFactory::getApplication();
        $text_search = $currentSession->get("tsearch",$app->getUserStateFromRequest('com_adsmanager.front_content.tsearch','tsearch',""));

        $type = "table";
		
        $fieldmodel  = new AdsmanagerModelField();
        $searchmodel  = new AdsmanagerModelSearchmodule();
        $field_values = array();
        $searchconfig = $searchmodel->getSearchModuleConfiguration();
        $simple_fields = $searchmodel->getSearchFields("simple");
        $advanced_fields = $searchmodel->getSearchFields("advanced");
        $field_values = $fieldmodel->getFieldValues();
		
        foreach($simple_fields as $field)
        {
            if ($field->cbfieldvalues != "-1")
            {
                /*get CB value fields */
                $cbfieldvalues = $fieldmodel->getCBFieldValues($field->cbfieldvalues);
                $field_values[$field->fieldid] = $cbfieldvalues;
            }
        }
        foreach($advanced_fields as $field)
        {
            if ($field->cbfieldvalues != "-1")
            {
                /*get CB value fields */
                $cbfieldvalues = $fieldmodel->getCBFieldValues($field->cbfieldvalues);
                $field_values[$field->fieldid] = $cbfieldvalues;
            }
        }

		
        $confmodel = new AdsmanagerModelConfiguration();
        $conf = $confmodel->getConfiguration();
		
        $categorymodel = new AdsmanagerModelCategory();
	
        $moduleclass_sfx = '';
		
        $search_by_cat = 1;
        $search_by_text = 0;
		
		
        $rootid = 0 ;
		
        switch($conf->single_category_selection_type) {
            default:
            case 'normal':
            case 'color':
            case 'combobox':
                $cats = $categorymodel->getFlatTree(true, false, $nbcontents, 'read',$rootid);
                break;
            case 'cascade':
                $cats = $categorymodel->getCategoriesPerLevel(true, false, $nbcontents, 'read',$rootid);
                break;
        }

        $baseurl = JURI::base();

        $field = new JHTMLAdsmanagerField($conf,$field_values,"2",$fieldmodel->getPlugins());//0 =>list

        $url = "index.php";
        $this->assignRef('search_by_cat',$search_by_cat);
        $this->assignRef('search_by_text',$search_by_text);
        $this->assignRef('text_search',$text_search);
        $this->assignRef('conf',$conf);
        $this->assignRef('cats',$cats);
        $this->assignRef('catid',$catid);
        $this->assignRef('simple_fields',$simple_fields);
        $this->assignRef('advanced_fields',$advanced_fields);
		$this->assignRef('field',$field);
        $this->assignRef('defaultvalues',$defaultvalues);
        $this->assignRef('rootid',$rootid);
		
		parent::display($tpl);
	}
	
	function selectCategories($id, $level, $children,&$catid,$root_allowed,$link,$current_cat_only =0) {
		if (@$children[$id]) {
			foreach ($children[$id] as $row) {
				if (($root_allowed == 1)||(!@$children[$row->id])) {
					if ($current_cat_only == 0)
					{?>
					<option value="<?php echo TRoute::_("$link&catid=".$row->id); ?>" <?php if ($row->id == $catid) { echo "selected='selected'"; } ?>>
					<?php echo $level.$row->name; ?>
					</option>
					<?php 
					}
					else if ($row->id == $catid)
					{
						echo $level.$row->name;
					}
				}
				$this->selectCategories($row->id, $level.$row->name." >> ", $children,$catid,$root_allowed,$link,$current_cat_only);
			}
		}
	}
}
