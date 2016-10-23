<?php
// namespace administrator\components\com_jchat\views\cpanel;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage views
 * @subpackage config
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

/**
 * Config view
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage views
 * @since 1.0
 */
class JChatViewConfig extends JChatView {

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addDisplayToolbar() {
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_('COM_JCHAT_MAINTITLE_TOOLBAR') . JText::_('COM_JCHAT_CONFIG' ), 'jchat' );
		JToolBarHelper::save('config.saveentity', 'COM_JCHAT_SAVECONFIG');
		JToolBarHelper::custom('cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false);
	}
	
	/**
	 * Effettua il rendering dei tabs di configurazione del componente
	 * @access public
	 * @return void
	 */
	public function display($tpl = null) {
		$doc = JFactory::getDocument();
		$this->loadJQuery($doc);
		$this->loadBootstrap($doc);
		
		/*$doc->addScriptDeclaration("Joomla.submitbutton = function(task) {
										if (document.formvalidator.isValid(document.getElementById('adminForm'))) {
											Joomla.submitform(task, document.getElementById('adminForm'));
										}
									}");*/
		
		$params = $this->get('Data');
		$form = $this->get('form');
		
		// Bind the form to the data.
		if ($form && $params) {
			$form->bind($params);
		}
		
		$this->params_form = $form;
		$this->params = $params;
		$this->fieldset = $this->getModel()->getState('fieldset');
		
		// Aggiunta toolbar
		$this->addDisplayToolbar();
		
		// Output del template
		parent::display();
	}
}
?>