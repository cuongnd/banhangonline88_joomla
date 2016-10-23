<?php
/** 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage views
 * @subpackage form
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');?>
<div class="item-page<?php echo $this->cparams->get('pageclass_sfx', null);?>">
	<?php if ($this->cparams->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h3> <?php echo $this->escape($this->cparams->get('page_heading', $this->menuTitle)); ?> </h3>
		</div>
	<?php endif;?>
</div>

<!-- Private messaging view -->

<div id="jchat_private_messaging" dir="ltr">
	<div id="jchat_left_userscolumn">
		<?php if($this->cparams->get('show_search', 1)):?>
			<input id="jchat_leftusers_search" type="text" placeholder="<?php echo JText::_('COM_JCHAT_SEARCH');?>" value="">
		<?php endif; ?>
		<span id="jchat_messaging_maximizebutton"></span>
		
		<ul id="jchat_userslist">
			<?php if(is_array($this->registeredUsers) && count($this->registeredUsers)) :
				foreach($this->registeredUsers as $registeredUserObject):
					// Get current cycled user avatar
					$userAvatar = JChatHelpersUsers::getAvatar($registeredUserObject->id, $registeredUserObject->id);
					$filteredUserName = JString::strlen($registeredUserObject->username) > 16 ? JString::substr($registeredUserObject->username, 0, 13) . '...' : $registeredUserObject->username;
					$pendingMessages = null;
					if(isset($this->pendingMessages[$registeredUserObject->id]) && is_array($this->pendingMessages[$registeredUserObject->id])) {
						$pendingMessages = '<span class="jchat_newmessages_notifier">' . $this->pendingMessages[$registeredUserObject->id]['newmessages'] . '</span>';
					}
					?>
					<li class="jchat_userbox" data-userid="<?php echo $registeredUserObject->id;?>" data-username="<?php echo $filteredUserName;?>">
						<div class="jchat_usertab_container">
							<span class="jchat_usersbox_name">
								<img class="jchat_usersbox_avatar" width="32px" height="32px" src="<?php echo $userAvatar;?>">
								<span class="jchat_usersbox_textname"><?php echo $filteredUserName;?></span>
								<span class="jchat_usersbox_status jchat_offline" data-userid="<?php echo $registeredUserObject->id;?>"></span>
							</span>
							<?php echo $pendingMessages;?>
						</div>
						<div class="jchat_lastmessage"></div>
					</li>
					<?php 
				endforeach;
			else: ?>
				<li><?php echo JText::_('COM_JCHAT_NOUSERS');?></li>
			<?php endif; ?>
		</ul>
	</div>
	
	<div id="jchat_right_messagescolumn">
		<div id="jchat_loadolder_messages"><?php echo JText::_('COM_JCHAT_LOAD_OLDER_MESSAGES');?></div>
	
		<div id="jchat_usersmessages">
		
		</div>
	</div>
	
	<div class="jchat_fullcolumn_input">
		<div id="jchat_privatemessaging_textarea" contenteditable="true" class="jchat_textarea jchat_noresize"></div>
		<div class="jchat_userslist_ctrls">
			<div class="jchat_trigger_messaging_emoticon" data-trigger="1" data-role="emoticons"></div>
			<div class="jchat_trigger_messaging_fileupload" data-trigger="1" data-role="fileupload"></div>
			<div class="jchat_trigger_messaging_export">
				<a href="javascript:void(0);"></a>
			</div>
			<div class="jchat_trigger_messaging_delete" data-trigger="1" data-role="deleteconversation"></div>
			<div class="jchat_trigger_messaging_openbox" data-role="openbox"></div>
			<div class="jchat_userslist_reply"><?php echo JText::_('COM_JCHAT_REPLY');?></div>
			<span class="jchat_userslist_reply_info"><?php echo JText::_('COM_JCHAT_REPLY_INFO');?></span>
		</div>
	</div>
</div>