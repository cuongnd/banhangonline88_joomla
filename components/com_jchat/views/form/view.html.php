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
class JChatViewForm extends JChatView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param string $userData
	 * @return void
	 */
	public function display($tpl = null) {
		$document = JFactory::getDocument();
		$this->menuTitle = null;
		$this->activeMenu = $this->app->getMenu()->getActive ();
		$this->cparams = $this->getModel ()->getState ( 'cparams' );
		if (isset ( $this->activeMenu )) {
			$this->menuTitle = $this->activeMenu->title;
		}
		
		$this->userInfo = $this->get('Data');
		
		// If user is a guest already joined to chat or user logged
		if($this->cparams->get('guestenabled', 0) != 2) {
			$tpl = 'noavailable';
		} elseif($this->user->id) {
			$tpl = 'joined';
		} else {
			$this->loadJQuery($document, false);
			$this->loadValidation($document);
			$document->addScript ( JURI::root(true) . '/components/com_jchat/js/form.js' );
			$document->addStyleSheet ( JURI::root(true) . '/components/com_jchat/css/form.css' );
			
			// Inject js translations
			$translations = array('COM_JCHAT_FORM_JOINEDCHAT',
								  'COM_JCHAT_FORM_JOINED',
								  'COM_JCHAT_VALIDATION_ERROR_ANTISPAM',
								  'COM_JCHAT_VALIDATION_ERROR_MOBILE_EXCLUDED'
			);
			$this->injectJsTranslations($translations, $document);
		}
		
		// Add meta info
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
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
	
		$title = $this->params->get('page_title', 'Chat form');
		$document->setTitle($title);
	
		if ($this->params->get('menu-meta_description')) {
			$document->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->params->get('menu-meta_keywords')) {
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
	
		if ($this->params->get('robots')) {
			$document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}