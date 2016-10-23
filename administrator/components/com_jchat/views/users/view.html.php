<?php
// namespace administrator\components\com_jchat\views\users;
/**
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage users
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.utilities.date' );

/**
 * Users view implementation
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage views
 * @since 1.6
 */
class JChatViewUsers extends JChatView {
	/**
	 * Add the page title and toolbar.
	 *
	 * @since 1.6
	 */
	protected function addDisplayToolbar() {
		JToolBarHelper::title ( JText::_ ( 'COM_JCHAT_MAINTITLE_TOOLBAR' ) . JText::_ ( 'COM_JCHAT_LIST_USERS' ), 'jchat' );
		JToolBarHelper::custom ( 'cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false );
	}
	
	/**
	 * Default listEntities
	 *
	 * @access public
	 */
	public function display($tpl = 'list') {
		$doc = JFactory::getDocument ();
		$this->loadJQuery ( $doc );
		$this->loadJQueryUI ( $doc ); // Required for draggable feature
		$this->loadBootstrap ( $doc );

		// Get main records
		$rows = $this->get ( 'Data' );
		$lists = $this->get ( 'Filters' );
		$total = $this->get ( 'Total' );
		
		$orders = array ();
		$orders ['order'] = $this->getModel ()->getState ( 'order' );
		$orders ['order_Dir'] = $this->getModel ()->getState ( 'order_dir' );
		// Pagination view object model state populated
		$pagination = new JPagination ( $total, $this->getModel ()->getState ( 'limitstart' ), $this->getModel ()->getState ( 'limit' ) );
		
		$this->pagination = $pagination;
		$this->order = $this->getModel ()->getState ( 'order' );
		$this->searchword = $this->getModel ()->getState ( 'searchword' );
		$this->lists = $lists;
		$this->orders = $orders;
		$this->items = $rows;
		$this->option = $this->getModel ()->getState ( 'option' );
		
		// Add toolbar
		$this->addDisplayToolbar ();
		
		parent::display ( $tpl );
	}
	
	/**
	 * Class constructor
	 *
	 * @param array $config        	
	 */
	public function __construct($config = array()) {
		// Parent view object
		parent::__construct ( $config );
	}
}