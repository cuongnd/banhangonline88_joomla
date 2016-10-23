<?php
// namespace administrator\components\com_jchat\views\recorder;
/** 
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage views
 * @subpackage recorder
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * User messages view implementation
 *
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage views
 * @since 2.9
 */
class JChatViewRecorder extends JChatView {
	/**
	 * Add the page title and toolbar.
	 */
	protected function addDisplayToolbar() {
		// Model state cparams
		$cParams = $this->getModel()->getState('cparams');
		$keepDays = $cParams->get('keep_latest_msgs', 7);
		
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_('COM_JCHAT_RECORDED_MEDIAS_LIST' ), 'jchat' );
		JToolBarHelper::deleteList('COM_JCHAT_DELETE_MEDIAS', 'recorder.deleteEntity');
		JToolBarHelper::custom('cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false);
	}

	/**
	 * Default listEntities
	 * @access public
	 */
	public function display($tpl = 'list') {
		$doc = JFactory::getDocument ();
		$this->loadJQuery($doc);
		$this->loadJQueryUI($doc); // Required for draggable feature
		$this->loadBootstrap($doc);

		// Get main records
		$rows = $this->get('Data');
		$lists = $this->get('Filters');
		$total = $this->get('Total');
		
		$orders = array();
		$orders['order'] = $this->getModel()->getState('order');
		$orders['order_Dir'] = $this->getModel()->getState('order_dir');
		// Pagination view object model state populated
		$pagination = new JPagination( $total, $this->getModel()->getState('limitstart'), $this->getModel()->getState('limit') );
		$dates = array('start'=>$this->getModel()->getState('fromPeriod'), 'to'=>$this->getModel()->getState('toPeriod'));
		 
		$this->pagination = $pagination;
		$this->order = $this->getModel()->getState('order');
		$this->searchword = $this->getModel()->getState('searchword');
		$this->lists = $lists;
		$this->orders = $orders;
		$this->items = $rows;
		$this->option = $this->getModel()->getState('option');
		$this->dates = $dates; 
		$this->componentParams = $this->getModel()->getComponentParams();
		
		// Add toolbar
		$this->addDisplayToolbar();
		
		parent::display($tpl);
	}
}