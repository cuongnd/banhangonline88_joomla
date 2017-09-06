<?php
// namespace components\com_jchat\views\avatar;
/**
 * @package JCHAT::components::com_jchat
 * @subpackage views
 * @subpackage avatar
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Main view class
 *
 * @package JCHAT::components::com_jchat
 * @subpackage views
 * @subpackage avatar
 * @since 1.0
 */
class JChatViewAvatar extends JChatView {
	/**
	 * Display the avatar view
	 * @access public
	 * @return void
	 */
	public function display($tpl = null) {
		$defaultModelReference = $this->getModel();

		// Controlla se l'avatar è stato caricato per l'utente corrente
		$avatar = $this->get('AvatarThumbnailFileName');
		$avatarDeleteButton = null;
		if (isset($avatar[0]) && file_exists($avatar[0])) {
			$this->userAvatar = $avatar[1] . '?nocache=' . time();
			$this->avatarDeleteButton = '<input id="avatar_delete" type="submit" value="" /><label class="buttonlabel">' . JText::_('COM_JCHAT_AVATAR_DELETE') . '</label>';
		} else {
			$this->userAvatar = 'default_my.png?nocache=' . time();
			$this->avatarDeleteButton = null;
		}
		
		// Controllo presenza GD library
		if (!extension_loaded('gd') || !function_exists('gd_info')) {
			$this->gdMissingAlert = JText::_('COM_JCHAT_GDERROR');
		}
		
		// View variables
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

		parent::display($tpl);
	}
}
