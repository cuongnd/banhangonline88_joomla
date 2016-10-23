<?php
// namespace administrator\components\com_jchat\views\lamessages;
/** 
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage lamessages
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );  
define ( 'INDEX_WORKED', 'worked');
define ( 'INDEX_CLOSED', 'closed_ticket');

/**
 * User leaved messages view implementation
 *
 * @package FBCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @since 1.0
 */
class JChatViewLamessages extends JChatView {
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addEditEntityToolbar() {
		JToolBarHelper::title(JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_( 'COM_JCHAT_TICKET_DETAILS' ), 'jchat' );
		JToolBarHelper::apply('lamessages.applyEntity', 'JAPPLY');
		JToolBarHelper::save('lamessages.saveEntity', 'JSAVE');
		JToolBarHelper::custom('lamessages.responsemessage', 'upload', 'upload', 'COM_JCHAT_RESPONSE_LEAVED_MESSAGES', false);
		JToolBarHelper::custom('lamessages.cancelEntity', 'cancel', 'cancel', 'JCANCEL', false);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addDisplayToolbar() {
		JToolBarHelper::title( JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_( 'COM_JCHAT_LIST_TICKETS' ), 'jchat' );
		JToolBarHelper::editList('lamessages.editentity', 'COM_JCHAT_TICKET_DETAILS');
		JToolBarHelper::deleteList(JText::_('COM_JCHAT_DELETE_TICKETS'), 'lamessages.deleteentity');
		JToolBarHelper::custom('lamessages.exportMessages', 'download', 'download', 'COM_JCHAT_EXPORT_TICKETS', false);
		JToolBarHelper::custom('cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false);
	}
	
	/**
	 * Default listEntities
	 * @access public
	 */
	public function display($tpl = 'list') {
		$doc = JFactory::getDocument ();
		$this->loadJQuery($doc);
		$this->loadJQueryUI($doc);
		$this->loadBootstrap($doc);
		$doc->addScriptDeclaration("
						Joomla.submitbutton = function(pressbutton) {
							Joomla.submitform( pressbutton );
							if (pressbutton == 'lamessages.exportMessages') {
								jQuery('#adminForm input[name=task]').val('lamessages.display');
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
		
		// Add toolbar
		$this->addDisplayToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Mostra la visualizzazione dettaglio del record singolo
	 * @param Object& $row
	 * @access public
	 */
	public function editEntity(&$row) {
		// Sanitize HTML Object2Form
		JFilterOutput::objectHTMLSafe( $row );
		
		// Load JS Client App dependencies
		$doc = JFactory::getDocument();
		$base = JURI::root();
		$this->loadJQuery($doc);
		$this->loadJQueryUI($doc);
		$this->loadBootstrap($doc);
		$this->loadValidation($doc);
		
		// Load specific JS code
		$doc->addScriptDeclaration("
						jQuery(function($) {
							$('input[data-role=calendar]').datepicker({
								dateFormat:'yy-mm-dd'
							}).prev('span').on('click', function(){
								$(this).datepicker('show');
							});
						});
					");
		
		// Inject js translations
		$translations = array( 'COM_JCHAT_VALIDATION_ERROR', 
							   'COM_JCHAT_VALIDATION_ERROR_SUBJECT' );
		$this->injectJsTranslations($translations, $doc);
		
		// Load specific JS code
		$doc->addScriptDeclaration("
					Joomla.submitbutton = function(pressbutton) {
						if(!jQuery.fn.validation) {
							jQuery.extend(jQuery.fn, jchatjQueryBackup.fn);
						}
						jQuery('#adminForm').validation();
				
						if (pressbutton == 'lamessages.cancelEntity') {
							Joomla.submitform( pressbutton );
							return true;
						}
						
						if (pressbutton != 'lamessages.responsemessage') {
							jQuery('input[name=email_subject]').attr('data-validation', '');
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
		$this->editor = JFactory::getEditor();
		$this->nameOfUser = $this->user->name;
		
		// Aggiunta toolbar
		$this->addEditEntityToolbar();
		
		parent::display ( 'edit' );
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
		// Clean dirty buffer
		ob_end_clean();
		// Open buffer
		ob_start();
		// Open out stream
		$outstream = fopen("php://output", "w");
		// Funzione di scrittura nell'output stream
		function __outputCSV(&$vals, $key, $userData) {
			// Fields value transformations 
			if(isset($vals[INDEX_WORKED])) {
				$vals[INDEX_WORKED] = $vals[INDEX_WORKED] == 1 ? JText::_('COM_JCHAT_YESWORKED') : JText::_('COM_JCHAT_NOWORKED');
			}
			if(isset($vals[INDEX_CLOSED])) {
				$vals[INDEX_CLOSED] = $vals[INDEX_CLOSED] == 1 ? JText::_('JYES') : JText::_('JNO');
			}
			
			fputcsv($userData[0], $vals, $userData[1], $userData[2]); // add parameters if you want
		}
		// Echo delle intestazioni
		__outputCSV($fieldsFunctionTransformation, null, array($outstream, $delimiter, $enclosure));
		// Output di tutti i records
		array_walk($data, "__outputCSV", array($outstream, $delimiter, $enclosure));
		fclose($outstream);
		// Recupero output buffer content
		$contents = ob_get_clean();
		$size = strlen($contents);
	
	
		header ( 'Pragma: public' );
		header ( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header ( 'Expires: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' );
		header ( 'Content-Disposition: attachment; filename="tickets.csv"' );
		header ( 'Content-Type: text/plain' );
		header ( "Content-Length: " . $size );
		echo $contents;
			
		exit ();
	}
	
	/**
	 * Class constructor
	 *
	 * @param array $config
	 */
	public function __construct($config = array()) {
		// Parent view object
		parent::__construct ( $config );
	
		$joomlaConfig = JFactory::getConfig ();
		$this->joomlaConfig = $this->user->getParam ( 'timezone', $joomlaConfig->get ( 'offset' ) );
	}
}