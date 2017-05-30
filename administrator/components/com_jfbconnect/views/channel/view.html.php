<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/includes/views.php');

class JFBConnectViewChannel extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        JToolBarHelper::apply('channel.apply', 'Save');
        JToolBarHelper::save('channel.save', 'Save & Close');
        JToolBarHelper::cancel('channel.cancel', 'Cancel');

        JFactory::getDocument()->addScriptDeclaration('var jfbc_language_click_save="'.JText::_('COM_JFBCONNECT_CHANNEL_CLICK_SAVE_LOAD_SETTINGS_LABEL').'";');
        JFactory::getDocument()->addScriptDeclaration('var jfbc_language_select_provider="'.JText::_('COM_JFBCONNECT_CHANNEL_SELECT_PROVIDER_CHANNEL_LABEL').'";');


        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        $title = "JFBConnect: Social Channels";

        JToolBarHelper::title($title, 'jfbconnect.png');

        SCAdminHelper::addAutotuneToolbarItem();

        parent::display($tpl);
    }
}
