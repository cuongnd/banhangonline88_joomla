<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

/**
 * JComments Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.Captcha
 * @since		1.5
 */
class plgAdsmanagercontentCaptcha extends JPlugin
{
	
	public function ADSonContentBeforeSave() {
		return $this->checkCaptcha();
	}
    public function ADSonUserBeforeSave() {
		return $this->checkCaptcha();
	}
	public function ADSonMessageBeforeSend() {
		return $this->checkCaptcha();
	}

	public function ADSonContentAfterForm($content) {	
		return $this->displayCaptcha();
	}
	
	public function ADSonUserAfterForm($user) {
		return $this->displayCaptcha();
	}
	public function ADSonMessageAfterForm($content) {
		return $this->displayCaptcha();
	}
	
	public function checkCaptcha() {
        
        $displayEdit = $this->params->get('edit_form', '0');
        $displayMessage = $this->params->get('message_form', '0');
        $displayProfile = $this->params->get('profile_form', '0');
        
        $view = JFactory::getApplication()->input->get('view','');
        $task = JFactory::getApplication()->input->get('task','');
        
        if(($displayEdit == 1 && $task == 'save') ||
           ($view == 'profile' && $displayProfile == 1) ||
           ($view == 'message' && $displayMessage == 1) ||
           ($task == 'sendmessage' && $displayMessage == 1) ||
           ($task == 'saveprofile' && $displayProfile == 1)) {
            $code = JRequest::getVar('code_captcha','');
            $session = JFactory::getSession();

            if ($session->get('security_code') != $code) {
                throw new Exception(JText::_('ADSMANAGER_ERROR_BAD_CAPTCHA'));
            }
            return true;
        }
        return '';
	}
	
	public function displayCaptcha()
	{
        $displayEdit = $this->params->get('edit_form', '0');
        $displayMessage = $this->params->get('message_form', '0');
        $displayProfile = $this->params->get('profile_form', '0');
        
        $view = JFactory::getApplication()->input->get('view','');
        $task = JFactory::getApplication()->input->get('task','');
        
        if((($view == 'edit' || $task =='write') && $displayEdit == 1) ||
           ($view == 'profile' && $displayProfile == 1) ||
           ($view == 'message' && $displayMessage == 1)) {
            if(version_compare(JVERSION,'1.6.0','>=')){
                $url = JURI::base() . "plugins/adsmanagercontent/captcha/captcha/";
            } else {
                $url = JURI::base() . "plugins/adsmanagercontent/captcha/";
            }
            $img = '<img src="'.$url.'generate.php?r='.time().'" />';

            $html  = "<tr><td>".JText::_('ADSMANAGER_SECURITY_CODE')."</td><td>";
            $html .= "$img<br/><input class='inputbox' type='text' name='code_captcha' value='' size='17' />";
            $html .= "</td></tr>";
            return $html;
        }
        return '';
	}
}