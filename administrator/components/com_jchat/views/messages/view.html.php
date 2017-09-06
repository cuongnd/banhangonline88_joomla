<?php
// namespace administrator\components\com_jchat\views\messages;
/** 
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage messages
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
define ( 'INDEX_TO', 'receiver_name');
define ( 'INDEX_SENT', 'sent');
define ( 'INDEX_READ', 'read');
define ( 'INDEX_MESSAGE', 'message');

/**
 * User messages view implementation
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @since 1.0
 */
class JChatViewMessages extends JChatView {
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addShowEntityToolbar() {
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_('COM_JCHAT_MESSAGE_DETAILS' ), 'jchat' );
		JToolBarHelper::custom('messages.display', 'arrow-left-2', 'arrow-left-2', 'COM_JCHAT_BACK_TO_LIST_MESSAGES', false);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addDisplayToolbar() {
		// Model state cparams
		$cParams = $this->getModel()->getState('cparams');
		$keepDays = $cParams->get('keep_latest_msgs', 7);
		
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_('COM_JCHAT_LIST_MESSAGES' ), 'jchat' );
		JToolBarHelper::editList('messages.showentity', 'COM_JCHAT_VIEW_MESSAGE_DETAILS');
		JToolBarHelper::deleteList('COM_JCHAT_DELETE_MESSAGES', 'messages.deleteEntity');
		JToolBarHelper::custom('messages.exportMessages', 'download', 'download', 'COM_JCHAT_EXPORT_MSG', false);
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Confirm', 'COM_JCHAT_DELETE_ALL_MESSAGES_CONFIRM', 'trash', 'COM_JCHAT_DELETE_ALL_MESSAGES', 'messages.deleteEntities', false);
		$bar->appendButton('Confirm', JText::sprintf('COM_JCHAT_DELETE_ALL_OLDEST_MESSAGES_CONFIRM', $keepDays), 'trash', 'COM_JCHAT_DELETE_ALL_OLDEST_MESSAGES', 'messages.deleteOldestEntities', false);
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
		$doc->addScriptDeclaration("
						Joomla.submitbutton = function(pressbutton) {
							Joomla.submitform( pressbutton );
							if (pressbutton == 'messages.exportMessages') {
								jQuery('#adminForm input[name=task]').val('messages.display');
							}
							return true;
						}
					");
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
		$this->defaultFlagsPath = 'media/mod_languages/images/';
		$this->defaultTranslateToLanguage = $this->componentParams->get('default_to_language', 'en');
		if(in_array($this->defaultTranslateToLanguage, array('ha', 'ig', 'yo'))) {
			$this->defaultFlagsPath = 'components/com_jchat/images/flags/';
		}
		
		// Add toolbar
		$this->addDisplayToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Mostra la visualizzazione dettaglio del record singolo
	 * @param Object& $row
	 * @access public
	 */
	public function showEntity($row) {
		// Sanitize HTML Object2Form
		JFilterOutput::objectHTMLSafe( $row );
		
		// Add toolbar
		$this->addShowEntityToolbar();
		
		$doc = JFactory::getDocument ();
		$this->loadJQuery($doc);
		$this->loadBootstrap($doc);
		
		$this->option = $this->getModel()->getState('option');
		$this->record = $row;
		
		parent::display('edit');
	}
	 
	/**
	 * Effettua l'output view del file in attachment al browser
	 * 
	 * @access public
	 * @param string $contents
	 * @param int $size
	 * @param array& $fieldsFunctionTransformation
	 * @return void
	 */
public function sendCSVMessages($data, &$fieldsFunctionTransformation) {
		$delimiter = ';';
		$enclosure = '"';
		$componentConfig = $this->getModel()->getState('cparams');
		// Clean dirty buffer
		ob_end_clean();
		// Open buffer
		ob_start();
		// Open out stream
		$outstream = fopen("php://output", "w");
		// Funzione di scrittura nell'output stream
		function __outputCSV(&$vals, $key, $userData) {
			// Fields value transformations 
			if(isset($vals[INDEX_SENT]) && (int)$vals[INDEX_SENT]) {
				$vals[INDEX_SENT] = date('Y-m-d H:i:s', $vals[INDEX_SENT]);
			}
			if(isset($vals[INDEX_READ]) && (int)$vals[INDEX_READ]) {
				$vals[INDEX_READ] = JText::_('COM_JCHAT_YESREAD');
			} else {
				if(!$userData[3]) {
					$vals[INDEX_READ] = $vals[INDEX_TO] ? JText::_('COM_JCHAT_NOREAD') : JText::_('COM_JCHAT_MULTIPLE_RECEIVER_USERS');
				}
			}
			if(isset($vals[INDEX_MESSAGE])) {
				$vals[INDEX_MESSAGE] = JChatHelpersMessages::purifyMessage($vals[INDEX_MESSAGE], $userData[4]);
			}
			fputcsv($userData[0], $vals, $userData[1], $userData[2]); // add parameters if you want
		}
		// Echo delle intestazioni
		__outputCSV($fieldsFunctionTransformation, null, array($outstream, $delimiter, $enclosure, true, $componentConfig));
		// Output di tutti i records
		array_walk($data, "__outputCSV", array($outstream, $delimiter, $enclosure, false, $componentConfig));
		fclose($outstream);
		// Recupero output buffer content
		$contents = ob_get_clean();
		$size = strlen($contents);
		
		
		header ( 'Pragma: public' );
		header ( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header ( 'Expires: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' );
		header ( 'Content-Disposition: attachment; filename="messages.csv"' );
		header ( 'Content-Type: text/plain' );
		header ( "Content-Length: " . $size );
		echo $contents;
			
		exit ();
	}
}
?>