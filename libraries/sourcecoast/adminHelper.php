<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

class SCAdminHelper
{
    static function addAutotuneToolbarItem()
    {
        JToolBarHelper::divider();

        SCStringUtilities::loadLanguage('com_jfbconnect');
        $autotuneModel = JModelLegacy::getInstance('AutoTune', 'JFBConnectModel');
        $upToDate = $autotuneModel->isUpToDate();

        if(defined('SC30')):
        $icon = 'options';
        endif; //SC30
        if(defined('SC16')):
        $icon = 'config';
        endif; //SC16

        if ($upToDate)
            JToolBarHelper::custom('autotune', $icon, $icon, JText::_('COM_JFBCONNECT_BUTTON_AUTOTUNE'), false);
        else
            JToolBarHelper::custom('autotune', $icon, $icon, JText::_('COM_JFBCONNECT_BUTTON_AUTOTUNE_RECOMMENDED'), false);
    }

    static function getAutotuneControlIconText()
    {
        SCStringUtilities::loadLanguage('com_jfbconnect');
        $autotuneModel = JModelLegacy::getInstance('AutoTune', 'JFBConnectModel');
        $upToDate = $autotuneModel->isUpToDate();

        if ($upToDate)
            return JText::_('COM_JFBCONNECT_BUTTON_AUTOTUNE');
        else
        {
            if(defined('SC16')):
            return '<span>'.JText::_('COM_JFBCONNECT_BUTTON_AUTOTUNE_RECOMMENDED').'<br/><span class="update-badge">!</span></span>';
            endif; //SC16
            if(defined('SC30')):
            return '<span style="color:blue"><strong>'.JText::_('COM_JFBCONNECT_BUTTON_AUTOTUNE_RECOMMENDED').'</strong></span>';
            endif; //SC30
        }
    }

    //Copied from Joomla 3.0 site and slightly modified as the ordering column was removed on the menu table, so an error is thrown.
    static function menu_linkoptions($all = false, $unassigned = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Get a list of the menu items
        $query->select('m.id, m.parent_id, m.title, m.menutype');
        $query->from($db->quoteName('#__menu') . ' AS m');
        $query->where($db->quoteName('m.published') . ' = 1');
        $query->order('m.menutype, m.parent_id');//, m.ordering'); //SC
        $db->setQuery($query);

        $mitems = $db->loadObjectList();

        if (!$mitems)
        {
            $mitems = array();
        }

        // Establish the hierarchy of the menu
        $children = array();

        // First pass - collect children
        foreach ($mitems as $v)
        {
            $pt = $v->parent_id;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }

        // Second pass - get an indent list of the items
        jimport('legacy.html.menu'); //SC
        $list = JHtmlMenu::TreeRecurse((int) $mitems[0]->parent_id, '', array(), $children, 9999, 0, 0); //SC

        // Code that adds menu name to Display of Page(s)

        $mitems = array();
        if ($all | $unassigned)
        {
            $mitems[] = JHtml::_('select.option', '<OPTGROUP>', JText::_('JOPTION_MENUS'));

            if ($all)
            {
                $mitems[] = JHtml::_('select.option', 0, JText::_('JALL'));
            }
            if ($unassigned)
            {
                $mitems[] = JHtml::_('select.option', -1, JText::_('JOPTION_UNASSIGNED'));
            }

            $mitems[] = JHtml::_('select.option', '</OPTGROUP>');
        }

        $lastMenuType = null;
        $tmpMenuType = null;
        foreach ($list as $list_a)
        {
            if ($list_a->menutype != $lastMenuType)
            {
                if ($tmpMenuType)
                {
                    $mitems[] = JHtml::_('select.option', '</OPTGROUP>');
                }
                $mitems[] = JHtml::_('select.option', '<OPTGROUP>', $list_a->menutype);
                $lastMenuType = $list_a->menutype;
                $tmpMenuType = $list_a->menutype;
            }

            $mitems[] = JHtml::_('select.option', $list_a->id, $list_a->title);
        }
        if ($lastMenuType !== null)
        {
            $mitems[] = JHtml::_('select.option', '</OPTGROUP>');
        }

        return $mitems;
    }
}