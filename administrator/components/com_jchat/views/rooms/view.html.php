<?php
// namespace administrator\components\com_jchat\views\rooms;
/**
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage rooms
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
 
/**
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage rooms
 * @since 1.0
 */
class JChatViewRooms extends JChatView {
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addEditEntityToolbar() {
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->record->id == 0);
		$checkedOut	= !($this->record->checked_out == 0 || $this->record->checked_out == $userId);
		$toolbarHelperTitle = $isNew ? 'COM_JCHAT_ROOM_NEW' : 'COM_JCHAT_ROOM_EDIT';
	
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_( $toolbarHelperTitle ), 'jchat' );
	
		if ($isNew)  {
			// For new records, check the create permission.
			if ($isNew && ($user->authorise('core.create', 'com_jchat'))) {
				JToolBarHelper::apply( 'rooms.applyEntity', 'JAPPLY');
				JToolBarHelper::save( 'rooms.saveEntity', 'JSAVE');
				JToolBarHelper::save2new( 'rooms.saveEntity2New');
			}
		} else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($user->authorise('core.edit', 'com_jchat')) {
					JToolBarHelper::apply( 'rooms.applyEntity', 'JAPPLY');
					JToolBarHelper::save( 'rooms.saveEntity', 'JSAVE');
					JToolBarHelper::save2new( 'rooms.saveEntity2New');
				}
			}
		}
			
		JToolBarHelper::custom('rooms.cancelEntity', 'cancel', 'cancel', 'JCANCEL', false);
	}
	
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addDisplayToolbar() {
		$user = JFactory::getUser();
		JToolBarHelper::title(  JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_( 'COM_JCHAT_LIST_ROOMS' ), 'jchat' );
		JToolBarHelper::addNew('rooms.editEntity', 'COM_JCHAT_ADD_ROOM');
		JToolBarHelper::editList('rooms.editEntity', 'COM_JCHAT_EDIT_ROOM');
		JToolBarHelper::deleteList(JText::_('COM_JCHAT_DELETE_ROOM'), 'rooms.deleteEntity');
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
		
		$orders = array ();
		$orders ['order'] = $this->getModel ()->getState ( 'order' );
		$orders ['order_Dir'] = $this->getModel ()->getState ( 'order_dir' );
		// Pagination view object model state populated
		$pagination = new JPagination ( $total, $this->getModel ()->getState ( 'limitstart' ), $this->getModel ()->getState ( 'limit' ) );
		
		$this->pagination = $pagination;
		$this->searchword = $this->getModel ()->getState ( 'searchword' );
		$this->lists = $lists;
		$this->orders = $orders;
		$this->items = $rows;
		
		// Aggiunta toolbar
		$this->addDisplayToolbar();
		
		parent::display ($tpl);
	}
	
	/**
	 * Edit entity view
	 *
	 * @access public
	 * @param Object& $row the item to edit
	 * @return void
	 */
	public function editEntity($row) {
		// Sanitize HTML Object2Form
		JFilterOutput::objectHTMLSafe( $row );
		
		// Load JS Client App dependencies
		$doc = JFactory::getDocument();
		$base = JURI::root();
		$this->loadJQuery($doc);
		$this->loadBootstrap($doc);
		$this->loadValidation($doc);
		
		// Inject js translations
		$translations = array();
		$this->injectJsTranslations($translations, $doc);
		
		// Load specific JS App
		$doc->addScriptDeclaration("
					Joomla.submitbutton = function(pressbutton) {
						if(!jQuery.fn.validation) {
							jQuery.extend(jQuery.fn, jchatjQueryBackup.fn);
						}
						jQuery('#adminForm').validation();
				
						if (pressbutton == 'rooms.cancelEntity') {
							jQuery('#adminForm').off();
							Joomla.submitform( pressbutton );
							return true;
						}
				
						if(jQuery('#adminForm').validate()) {
							Joomla.submitform( pressbutton );
							return true;
						}
						return false;
					}
				");
		
		$lists = $this->getModel()->getLists($row);
		$this->record = $row;
		$this->lists = $lists;
		
		// Aggiunta toolbar
		$this->addEditEntityToolbar();
		
		parent::display ( 'edit' );
	}
}