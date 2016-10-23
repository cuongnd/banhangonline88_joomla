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
 * @since 2.5
 */
class JChatViewConference extends JChatView {
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
		
		$this->loadJQuery($this->doc, false);
		$this->loadFrontendValidation($this->doc);
		
		// Inject js vars
		$this->doc->addScriptDeclaration("var jchat_conference_chain=" . $this->cparams->get('conference_chain', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_show_search=" . $this->cparams->get('show_search', 1) . ";");
		$this->doc->addScriptDeclaration("var jchat_hide_sidebar=" . $this->cparams->get('hide_sidebar', 0) . ";");
		$this->doc->addScriptDeclaration("var jchat_conference_access_guest=" . $this->cparams->get('conference_access_guest', 0) . ";");
		$this->doc->addScriptDeclaration("var jchat_hide_own_webcam_mode=" . $this->cparams->get('hide_own_webcam_mode', 0) . ";");
		
		$userFieldName = $this->cparams->get('usefullname', 'username');
		$this->myUsername =  $this->user->{$userFieldName};
		
		$this->doc->addScript ( JURI::root(true) . '/components/com_jchat/js/conference.js' );
		$this->doc->addStyleSheet ( JURI::root(true) . '/components/com_jchat/css/conference.css' );
		
		// Inject js translations
		$translations = array('COM_JCHAT_END_VIDEOCONFERENCE',
							  'COM_JCHAT_NOACCESS_VIDEOCONFERENCE',
							  'COM_JCHAT_SAFARI_WEBRTC_SHORT_SUPPORT');
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
	
		$title = $this->params->get('page_title', JText::_('COM_JCHAT_CONFERENCE_VIEW'));
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