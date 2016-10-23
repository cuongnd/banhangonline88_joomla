<?php
// namespace components\com_jchat\views\attachments;
/**
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage views
 * @subpackage attachments
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Main view class
 *
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage views
 * @subpackage attachments
 * @since 1.0
 */
class JChatViewAttachments extends JChatView {
	/**
	 * Display the form used to upload file attachments
	 * @access public
	 * @return void
	 */
	public function display($tpl = null) {
		$defaultModelReference = $this->getModel();
		
		// View variables
		$this->to = $defaultModelReference->getState('to');
		$this->tologged = $defaultModelReference->getState('tologged');
		$this->option = $defaultModelReference->getState('option');
		$this->liveSite = JURI::base();
		
		// Model message after result, any errors or simply default success result message?
		$this->modelMessage = $defaultModelReference->getError(null, false) ? $defaultModelReference->getError(null, false) : $defaultModelReference->getState('result');
		$this->success = $defaultModelReference->getState('result') ? ' success' : null;
		$this->visibleClass = $this->modelMessage !== null ? 'visible' : '';
		
		$this->joomlaTemplate = $this->app->getTemplate();
		$chosenChatTemplate = $defaultModelReference->getComponentParams()->get('chat_template', 'default.css');
		$this->baseTemplate = $chosenChatTemplate == 'custom.css' ? 'custom.css' : 'default.css';
		
		$this->chatTemplate = null;
		$directTemplates = array('default.css', 'custom.css');
		if(!in_array($chosenChatTemplate, $directTemplates)) {
			$this->chatTemplate = $chosenChatTemplate;
		}
		
		parent::display ( $tpl );
	}
}