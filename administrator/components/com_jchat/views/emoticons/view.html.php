<?php
// namespace administrator\components\com_jchat\views\emoticons;
/**
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage emoticons
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
 
/**
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage emoticons
 * @since 3.2
 */
class JChatViewEmoticons extends JChatView {
	/**
	 * Add the page title and toolbar.
	 */
	protected function addDisplayToolbar() {
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration('.icon-48-jchat{background-image:url("components/com_jchat/images/icon-48-data.png")}');
		JToolBarHelper::title(  JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_( 'COM_JCHAT_EMOTICONS' ), 'jchat' );
		
		JToolBarHelper::custom('cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false);
	}
	
	/**
	 * Default display listEntities
	 *        	
	 * @access public
	 * @param string $tpl
	 * @return void
	 */
	public function display($tpl = 'list') {
		// Get main records
		$rows = $this->get ( 'Data' );
		$lists = $this->get ( 'Filters' );
		$total = $this->get ( 'Total' );
		
		$doc = JFactory::getDocument();
		$this->loadJQuery($doc);
		$this->loadBootstrap($doc);
		$doc->addScript ( JURI::root ( true ) . '/administrator/components/com_jchat/js/emoticons.js' );
		$doc->addStylesheet ( JURI::root ( true ) . '/administrator/components/com_jchat/css/emoticons.css' );
		$doc->addScriptDeclaration('var jchat_livesite="' . JUri::root(false) . '";');
		
		// Evaluate the preserve emoticon size
		if($this->getModel()->getComponentParams()->get('emoticons_original_size', 0)) {
			$doc->addStyleDeclaration("td img[data-mediapreview]{max-width:100%!important;}");
		}
		
		// Inject js translations
		$translations = array (
				'COM_JCHAT_EMOTICON_SAVED',
				'COM_JCHAT_INVALID_KEYCODE',
				'COM_JCHAT_INVALID_KEYCODE_DESC',
				'COM_JCHAT_INVALID_LINKURL',
				'COM_JCHAT_INVALID_LINKURL_DESC'
		);
		$this->injectJsTranslations($translations, $doc);
						
		$orders = array ();
		$orders ['order'] = $this->getModel ()->getState ( 'order' );
		$orders ['order_Dir'] = $this->getModel ()->getState ( 'order_dir' );
		// Pagination view object model state populated
		$pagination = new JPagination ( $total, $this->getModel ()->getState ( 'limitstart' ), $this->getModel ()->getState ( 'limit' ) );
		
		$this->user = JFactory::getUser ();
		$this->pagination = $pagination;
		$this->searchword = $this->getModel ()->getState ( 'searchword' );
		$this->lists = $lists;
		$this->orders = $orders;
		$this->items = $rows;
		$this->document = $doc;
		
		// Manage different emoticons media buttons for Joomla 3.5 -/+
		if (version_compare ( JVERSION, '3.5', '<' )) {
			$this->mediaField = new JChatHtmlEmoticons();
		} else {
			$jForm = new JForm('jchat_emoticon');
			$jForm->setValue('asset_id', null, 'com_jchat');
			$jForm->setValue('authorId', 'jchat');
			$this->mediaField = new JFormFieldMedia();
			$this->mediaField->setForm($jForm);
		}
		$element = new SimpleXMLElement('<field/>');
		$element->addAttribute('class', 'mediaimagefield');
		$element->addAttribute('default', null);
		$this->mediaField->setup($element, null);
		
		// Aggiunta toolbar
		$this->addDisplayToolbar();
		
		parent::display ( $tpl );
	}
}