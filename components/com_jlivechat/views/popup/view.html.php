<?php

/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JLiveChatViewPopup extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');
		$operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
		$session = & JFactory::getSession();
		$user = & JFactory::getUser();
		$uri = & JFactory::getURI();
		$document = & JFactory::getDocument();
		$lang = & JFactory::getLanguage();

		$document->setTitle($settings->getSetting('popup_page_title'));

		$currentLanguage = JRequest::getVar('lang', $lang->getTag(), 'get');

		$this->assignRef('current_language', $currentLanguage);

		$specificOperators = trim(JRequest::getVar('operators', '', 'method'));
		$specificDepartment = trim(JRequest::getVar('department', '', 'method'));
		$specificRouteId = JRequest::getInt('routeid', '', 'method');

		$this->assignRef('specific_operators', $specificOperators);
		$this->assignRef('specific_department', $specificDepartment);
		$this->assignRef('specific_route', $specificRouteId);

		$this->assign('scheme', $uri->toString(array('scheme')));

		if($user->get('id') > 0)
		{
			//	User is logged in
			$this->assign('default_name', $user->get('name'));
			$this->assign('default_email', $user->get('email'));
		}
		else
		{
			$this->assign('default_name', JRequest::getVar('default_name', '', 'method'));
			$this->assign('default_email', JRequest::getVar('default_email', '', 'method'));
		}

		$this->assignRef('popup', $popup);
		$this->assignRef('settings', $settings);
		$this->assignRef('session', $session);

		$isCurrentlyOnline = $routing->isOnline($specificOperators, $specificDepartment, $specificRouteId);

		$this->assignRef('is_online', $isCurrentlyOnline);

		$this->addCss();
		$this->addJS($isCurrentlyOnline);

		$this->assign('departments', $operator->getDepartments());
		$this->assign('operators', $operator->getOperators());

		require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jlcdate.php';

		$date = new JLiveChatModelJLCDate();

		$this->assign('current_timestamp', $date->toUnix());
		$this->assign('online_timestamp', ($date->toUnix() - $popup->getOfflineSeconds()));

		$this->assign('chat_session_active', false);

		if($this->session->get('jlc_chat_session_id') > 0 && $this->session->get('jlc_chat_member_id') > 0)
		{
			// There is an existing chat session, check if active
			if($popup->isActive($this->session->get('jlc_chat_session_id'), $this->session->get('jlc_chat_member_id')))
			{
				$this->assign('chat_session_active', true);
			}
		}

		parent::display($tpl);
	}

	function addCss()
	{
		$document = & JFactory::getDocument();

		// YUI Stuff
		$document->addStyleSheet(JURI::root(true).'/components/com_jlivechat/assets/css/fonts-min.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_jlivechat/assets/css/tabview.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_jlivechat/assets/css/menu.css');
		$document->addStyleSheet(JURI::root(true).'/components/com_jlivechat/assets/css/button.css');

		$cssUri1 = JURI::root(true).'/components/com_jlivechat/assets/css/jlivechat.css';
		$cssUri2 = JURI::root(true).'/components/com_jlivechat/assets/css/popup.css';
		$cssUri3 = JURI::root(true).'/components/com_jlivechat/assets/css/custom.css';

		$document->addStyleSheet($cssUri1);
		$document->addStyleSheet($cssUri2);
		$document->addStyleSheet($cssUri3);
	}

	function addJS($isCurrentlyOnline = false)
	{
		(method_exists('JHtmlBehavior', 'mootools')) ? JHTML::_('behavior.mootools') : JHtml::_('behavior.framework');

		$document = & JFactory::getDocument();

		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/yahoo-dom-event.js');
		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/container_core-min.js');
		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/menu-min.js');
		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/element-min.js');
		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/tabview-min.js');
		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/button-min.js');

		// Sound Manager Javascript
		$swfPath = JURI::root(true).'/components/com_jlivechat/assets/swf/';
		$newMessageSoundPath = JURI::root(true).'/components/com_jlivechat/assets/sounds/Sound_2.mp3';

		$soundManagerJS1 = <<<EOF
// Sound Manager Section
// This is a bug workaround for IE web browsers
if (document.all) {
    window.attachEvent('onunload', function() {
	document.all['sm2-container'].removeNode(true);
    });
}

soundManager.url = '{$swfPath}';
soundManager.flashVersion = 9; // optional: shiny features (default = 8)
soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
// enable HTML5 audio support, if you're feeling adventurous. iPad/iPhone will always get this.
soundManager.useHTML5Audio = false;
EOF;

		$soundManagerJS2 = <<<EOF
soundManager.onready(function() {
    // Ready to use; soundManager.createSound() etc. can now be called.
    soundManager.createSound({
	id: 'newMessageSound',
	url: '{$newMessageSoundPath}',
	autoLoad: true,
	autoPlay: false,
	volume: 100,
	type: 'audio/mp3',
	stream: false
    });
});
EOF;

		$document->addScript(JURI::root(true).'/components/com_jlivechat/js/soundmanager2-nodebug-jsmin.js');
		$document->addScriptDeclaration($soundManagerJS1);
		if($isCurrentlyOnline) $document->addScriptDeclaration($soundManagerJS2);
	}
}
