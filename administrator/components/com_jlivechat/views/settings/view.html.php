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

class JLiveChatViewSettings extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');
		$popup = & CMSModel::getInstance('PopupAdmin', 'JLiveChatModel');
		$uri = & JFactory::getURI();
		$editor = & JFactory::getEditor();

		jimport('joomla.language.helper');

		$languages = JLanguageHelper::createLanguageList($settings->getDefaultLanguage());

		// LOCALE SETTINGS
		$timeoffset = array(
			JHTML::_('select.option', 'sys', 'System Timezone Offset (UTC '.(date('Z') / 3600).')'),
			JHTML::_('select.option', -12, JText::_('(UTC -12:00) International Date Line West')),
			JHTML::_('select.option', -11, JText::_('(UTC -11:00) Midway Island, Samoa')),
			JHTML::_('select.option', -10, JText::_('(UTC -10:00) Hawaii')),
			JHTML::_('select.option', -9.5, JText::_('(UTC -09:30) Taiohae, Marquesas Islands')),
			JHTML::_('select.option', -9, JText::_('(UTC -09:00) Alaska')),
			JHTML::_('select.option', -8, JText::_('(UTC -08:00) Pacific Time (US &amp; Canada)')),
			JHTML::_('select.option', -7, JText::_('(UTC -07:00) Mountain Time (US &amp; Canada)')),
			JHTML::_('select.option', -6, JText::_('(UTC -06:00) Central Time (US &amp; Canada), Mexico City')),
			JHTML::_('select.option', -5, JText::_('(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima')),
			JHTML::_('select.option', -4.5, JText::_('(UTC -04:30) Venezuela')),
			JHTML::_('select.option', -4, JText::_('(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz')),
			JHTML::_('select.option', -3.5, JText::_('(UTC -03:30) St. John\'s, Newfoundland, Labrador')),
			JHTML::_('select.option', -3, JText::_('(UTC -03:00) Brazil, Buenos Aires, Georgetown')),
			JHTML::_('select.option', -2, JText::_('(UTC -02:00) Mid-Atlantic')),
			JHTML::_('select.option', -1, JText::_('(UTC -01:00) Azores, Cape Verde Islands')),
			JHTML::_('select.option', 0, JText::_('(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca')),
			JHTML::_('select.option', 1, JText::_('(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris')),
			JHTML::_('select.option', 2, JText::_('(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa')),
			JHTML::_('select.option', 3, JText::_('(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg')),
			JHTML::_('select.option', 3.5, JText::_('(UTC +03:30) Tehran')),
			JHTML::_('select.option', 4, JText::_('(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi')),
			JHTML::_('select.option', 4.5, JText::_('(UTC +04:30) Kabul')),
			JHTML::_('select.option', 5, JText::_('(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent')),
			JHTML::_('select.option', 5.5, JText::_('(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo')),
			JHTML::_('select.option', 5.75, JText::_('(UTC +05:45) Kathmandu')),
			JHTML::_('select.option', 6, JText::_('(UTC +06:00) Almaty, Dhaka')),
			JHTML::_('select.option', 6.30, JText::_('(UTC +06:30) Yagoon')),
			JHTML::_('select.option', 7, JText::_('(UTC +07:00) Bangkok, Hanoi, Jakarta')),
			JHTML::_('select.option', 8, JText::_('(UTC +08:00) Beijing, Perth, Singapore, Hong Kong')),
			JHTML::_('select.option', 8.75, JText::_('(UTC +08:00) Western Australia')),
			JHTML::_('select.option', 9, JText::_('(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk')),
			JHTML::_('select.option', 9.5, JText::_('(UTC +09:30) Adelaide, Darwin, Yakutsk')),
			JHTML::_('select.option', 10, JText::_('(UTC +10:00) Eastern Australia, Guam, Vladivostok')),
			JHTML::_('select.option', 10.5, JText::_('(UTC +10:30) Lord Howe Island (Australia)')),
			JHTML::_('select.option', 11, JText::_('(UTC +11:00) Magadan, Solomon Islands, New Caledonia')),
			JHTML::_('select.option', 11.30, JText::_('(UTC +11:30) Norfolk Island')),
			JHTML::_('select.option', 12, JText::_('(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka')),
			JHTML::_('select.option', 12.75, JText::_('(UTC +12:45) Chatham Island')),
			JHTML::_('select.option', 13, JText::_('(UTC +13:00) Tonga')),
			JHTML::_('select.option', 14, JText::_('(UTC +14:00) Kiribati'))
		);

		$currentOffset = null;

		foreach($timeoffset as $offset)
		{
			if($offset->value == $settings->getSetting('timezone_offset'))
			{
				$currentOffset = $offset;
				break;
			}
		}

		$currentLanguage = null;

		foreach($languages as $language)
		{
			if($language['value'] == $settings->getDefaultLanguage())
			{
				$currentLanguage = $language;
				break;
			}
		}

		// Custom CSS File
		jimport('joomla.filesystem.file');

		$customCSSPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom.css';

		if(file_exists($customCSSPath))
		{
			$customCSS = file_get_contents($customCSSPath);
		}
		else
		{
			$customCSS = '/** '.$customCSSPath.' is missing **/';
		}
		//

		$modPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat'.DIRECTORY_SEPARATOR.'mod_jlivechat.php';

		if(file_exists($modPath))
		{
			$this->assign('module_installed', true);
		}
		else
		{
			$this->assign('module_installed', false);
		}

		$this->assignRef('custom_css', $customCSS);
		$this->assignRef('currentoffset', $currentOffset);
		$this->assignRef('currentlang', $currentLanguage);
		$this->assignRef('timeoffsets', $timeoffset);
		$this->assignRef('languages', $languages);
		$this->assignRef('settings', $settings);
		$this->assignRef('popup', $popup);
		$this->assignRef('uri', $uri);
		$this->assignRef('editor', $editor);
		$this->assignRef('mainframe', $mainframe);

		$hostedMode = $settings->isHostedMode();

		if($hostedMode)
		{
			$baseUri = $hostedMode['hosted_path_base'];
		}
		else
		{
			$baseUri = rtrim(preg_replace('@^https?://@i', '', JURI::root()), '/');
		}

		$visitorTrackingHTMLSnippet = <<<EOF
<span id="ie_ready" style="font-size: 1px; height: 1px; width: 1px;">&nbsp;</span>
<script language="Javascript" type="text/javascript">
(function() {
    var alreadyLoaded = false;
    
    var injectTrackerImgFunc = function () {
	var bodySection = document.getElementsByTagName('body').item(0);

	// These values can be injected dynamically
	var userId = '';
	var fullname = '';
	var username = '';
	var email = '';

	var trackImg = document.createElement('img');
	var trackImgUri = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}/index.php?option=com_jlivechat&no_html=1&tmpl=component&view=popup&task=track_remote_visitor';

	if(userId != '') {
	    trackImgUri += '&user_id='+userId;
	}

	if(fullname != '') {
	    trackImgUri += '&full_name='+encodeURIComponent(fullname);
	}

	if(username != '') {
	    trackImgUri += '&username='+encodeURIComponent(username);
	}

	if(email != '') {
	    trackImgUri += '&email='+encodeURIComponent(email);
	}

	if(document.referrer) {
	    trackImgUri += '&referrer='+encodeURIComponent(document.referrer);
	}

	trackImgUri += '&last_uri='+encodeURIComponent(document.location.href);

	trackImg.src = trackImgUri;
	trackImg.alt = '';
	trackImg.style.width = '1px';
	trackImg.style.height = '1px';
	trackImg.style.border = '0';

	bodySection.appendChild(trackImg);
    };

    var onLoadEvt = function () {
	if(!alreadyLoaded) {
	    alreadyLoaded = true;
	
	    JLiveChat.websiteRoot=('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}';
	    JLiveChat.hostedModeURI=('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}';

	    injectTrackerImgFunc();
	    
	    JLiveChat.initialize();
	}
    };
    
    var jlcCSS1 = document.createElement('link'); 
    jlcCSS1.type = 'text/css';
    jlcCSS1.rel = 'stylesheet';
    jlcCSS1.href = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}/components/com_jlivechat/assets/css/jlivechat.min.css';

    var jlcJS1 = document.createElement('script'); 
    jlcJS1.type = 'text/javascript'; 
    jlcJS1.async = false;
    jlcJS1.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}/components/com_jlivechat/js/lazyload-min.js';
    
    var jlcJS2 = document.createElement('script'); 
    jlcJS2.type = 'text/javascript'; 
    jlcJS2.async = false;
    jlcJS2.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}/components/com_jlivechat/js/jlivechat.min.js';
    jlcJS2.onload=onLoadEvt;
    jlcJS2.onreadystatechange=function () {
	if(this.readyState == 'loaded' || this.readyState == 'complete') {
	    onLoadEvt();
	}
    };
    
    // Inject JLive! Chat scripts into page
    var headSection = document.getElementsByTagName("head")[0];
    headSection.appendChild(jlcCSS1);
    headSection.appendChild(jlcJS1);
    headSection.appendChild(jlcJS2);
})();
</script>
EOF;

		$imgHTMLSnippet = <<<EOF
<script language="Javascript" type="text/javascript">
var curUri = String(document.location.href);
var currentTimestamp = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);

// You can change the following variables if you know what you are doing, otherwise the leave the defaults
var popupOpenMode = 'popup'; // Could be popup or iframe
var imgSize = 'large'; // Could be large or small
var specificOpertors = ''; // Comma seperated list of Operator IDs
var specificDepartment = ''; // Specific department to send visitor to, if any
var specificRouteId = ''; // Specific route id to send visitor to, if any

var livechatImgUri = ('https:' == document.location.protocol ? 'https://' : 'http://') + '{$baseUri}/index.php?option=com_jlivechat&view=popup&task=display_status_img&no_html=1&do_not_log=true&t='+currentTimestamp;

livechatImgUri += '&size='+imgSize;

if(specificOpertors != '') {
	livechatImgUri += '&operators='+specificOpertors;
}

if(specificDepartment != '') {
	livechatImgUri += '&department='+specificDepartment;
}

if(specificRouteId != '') {
	livechatImgUri += '&routeid='+specificRouteId;
}

var livechatImg = '<a href="javascript:void(0);" onclick="requestLiveChat(\'http://{$baseUri}/index.php?option=com_jlivechat&view=popup&tmpl=component&popup_mode='+popupOpenMode+'\', \''+popupOpenMode+'\');"><img src="'+livechatImgUri+'" border="0" alt="" /></a>';

document.writeln(livechatImg);
</script>
EOF;

		$this->assignRef('visitor_tracking_html_snippet', $visitorTrackingHTMLSnippet);
		$this->assignRef('img_html_snippet', $imgHTMLSnippet);

		$this->_addCss();
		$this->_addJs();

		parent::display($tpl);
	}

	function _addCss()
	{
		$document = & JFactory::getDocument();

		// YUI Stuff
		$document->addStyleSheet('components/com_jlivechat/assets/css/fonts-min.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/tabview.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/menu.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/button.css');

		$document->addStyleSheet('components/com_jlivechat/assets/css/styles.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/settings.css');
	}

	function _addJs()
	{
		$document = & JFactory::getDocument();

		(method_exists('JHtmlBehavior', 'mootools')) ? JHTML::_('behavior.mootools') : JHtml::_('behavior.framework');
		JHTML::_('behavior.tooltip');

		// YUI Stuff
		$document->addScript('components/com_jlivechat/js/yahoo-dom-event.js');
		$document->addScript('components/com_jlivechat/js/container_core-min.js');
		$document->addScript('components/com_jlivechat/js/menu-min.js');
		$document->addScript('components/com_jlivechat/js/element-min.js');
		$document->addScript('components/com_jlivechat/js/tabview-min.js');
		$document->addScript('components/com_jlivechat/js/button-min.js');

		$document->addScript('components/com_jlivechat/js/jlivechat.js');
		$document->addScript('components/com_jlivechat/js/settings.js');
	}
}
