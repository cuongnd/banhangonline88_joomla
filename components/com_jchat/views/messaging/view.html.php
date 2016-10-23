<?php 
// namespace administrator\components\com_jchat\views\stream;

/**
 * @author Joomla! Extensions Store
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage views
 * @subpackage form
 * @Copyright (C) 2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * View for JS client
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage views
 * @subpackage form
 * @since 1.0
 */
class JChatViewMessaging extends JChatView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param string $userData
	 * @return void
	 */
	public function display($tpl = null) {
		$this->menuTitle = null;
		$this->activeMenu = $this->app->getMenu()->getActive ();
		if(!$this->activeMenu) {
			$pmItemid = $this->app->getMenu()->getDefault()->id;
		} else {
			$pmItemid = $this->activeMenu->id;
		}
		$this->cparams = $this->getModel ()->getComponentParams();
		if (isset ( $this->activeMenu )) {
			$this->menuTitle = $this->activeMenu->title;
		}
		
		$this->registeredUsers = $this->get('Data');
		$this->pendingMessages = $this->get('PendingMessages');
		
		$this->loadJQuery($this->doc, false);
		$this->loadFrontendValidation($this->doc);
		
		// Inject js vars
		$this->doc->addScriptDeclaration("var jchat_show_search=" . $this->cparams->get('show_search', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_hide_sidebar=" . $this->cparams->get('hide_sidebar', 0) . ";");
		$this->doc->addScriptDeclaration("var jchat_allow_media_objects=" . $this->cparams->get('allow_media_objects', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_auto_close_popups=" . $this->cparams->get('auto_close_popups', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_messaging_debug=" . $this->cparams->get('enable_debug', 0) . ";");
		$this->doc->addScriptDeclaration("var jchat_privatemessaging_maximized=" . $this->cparams->get('privatemessaging_maximized', 0) . ";");
		$this->doc->addScriptDeclaration("var jchat_auto_open_msgspopup=" . $this->cparams->get('auto_open_msgspopup', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_avatarenable=" . $this->cparams->get('avatarenable', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_my_avatar='" . JChatHelpersUsers::getAvatar(session_id()) . "';");
		$this->doc->addScriptDeclaration("var jchat_my_userid=" . $this->user->id . ";");
		$this->doc->addScriptDeclaration("var jchat_itemid=" . $pmItemid . ";");
		$this->doc->addScriptDeclaration("var jchat_form_token='" . JSession::getFormToken() . "';");
		$userFieldName = $this->cparams->get('usefullname', 'username');
		$this->doc->addScriptDeclaration("var jchat_my_username='" . $this->user->{$userFieldName} . "';");
		
		$this->doc->addScript ( JURI::root(true) . '/components/com_jchat/js/messaging.js' );
		$this->doc->addStyleSheet ( JURI::root(true) . '/components/com_jchat/css/messaging.css' );
		
		// Inject js translations
		$translations = array('COM_JCHAT_NOMESSAGES_FOUND',
							  'COM_JCHAT_LOAD_OLDER_MESSAGES',
							  'COM_JCHAT_LOAD_NOFOUND_OLDER_MESSAGES',
							  'COM_JCHAT_OLDER_MESSAGES_LOADING',
							  'COM_JCHAT_CONFIRM_DELETE_MESSAGE',
							  'COM_JCHAT_CONFIRM_DELETE',
							  'COM_JCHAT_DISCARD_DELETE',
							  'COM_JCHAT_CONFIRM_DELETE_SUBMESSAGE',
							  'COM_JCHAT_USER_NOT_CONNECTED');
		$this->injectJsTranslations($translations, $this->doc);
		
		// Add meta info
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;
	
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if(is_null($menu)) {
			return;
		}
	
		$this->params = new JRegistry;
		$this->params->loadString($menu->params);
	
		$title = $this->params->get('page_title', JText::_('COM_JCHAT_PRIVATE_MESSAGING'));
		$this->doc->setTitle($title);
	
		if ($this->params->get('menu-meta_description')) {
			$this->doc->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->params->get('menu-meta_keywords')) {
			$this->doc->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
	
		if ($this->params->get('robots')) {
			$this->doc->setMetadata('robots', $this->params->get('robots'));
		}
	}
}