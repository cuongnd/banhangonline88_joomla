<?php 
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
    prepYUI();
</script>
<div id="livechat_container">
    <div class="livechat_inner">
	<span class="jlc-popup-title">
	    <span class="jlc-text"><?php echo $this->popup->getWindowTitle($this->current_language); ?></span>
	    <span class="jlc-wnd-btns">
		<?php if(JRequest::getVar('popup_mode', null, 'method') == 'iframe') { ?><a href="javascript:;" onclick="minimizeWnd();" id="minimize-window-button" class="minimize-wnd-icn">&nbsp;</a><?php } ?><a href="javascript:;" onclick="closeWnd();" class="close-wnd-icn">&nbsp;</a>
	    </span>
	    <div class="jlc-clr">&nbsp;</div>
	</span>
	<div class="jlc-clr">&nbsp;</div>
	<div id="jlc_toolbar_container" class="livechat_toolbar">
	    <span>
		<a href="javascript:;" onclick="JLiveChat.toggleSoundNotifications();" id="mute_icon" title="Mute Sound" class="unmute_sound_icon">&nbsp;</a>
	    </span>
	</div>
	<div id="jlc_prechat_container">
	    <span class="intro_wrapper">
		<?php echo $this->popup->getWindowIntro($this->current_language); ?>
	    </span>
	    <div class="jlc-clr">&nbsp;</div>
	    <form id="start_chat_form" action="" method="post" onsubmit="return false;">
		<span class="jlc-name-wrapper">
		    <?php echo JText::_('ENTER_YOUR_NAME_BELOW'); ?>
		    <br />
		    <input type="text" id="chat_name" name="chat_name" value="<?php echo $this->default_name; ?>" maxlength="50" />
		    <br />
		</span>
		<div class="jlc-clr jlc-margbot5">&nbsp;</div>
		<?php if($this->settings->getSetting('routeby') == 'department' && empty($this->specific_operators) && empty($this->specific_department) && empty($this->specific_route)) { ?>
		<label id="route-filter-container">
		    <select id="route_filter" name="route_filter">
			<option value=""><?php echo JText::_('SELECT_DEPARTMENT'); ?></option>
			<?php
			    foreach($this->departments as $department)
			    {
				if(empty($department['department'])) continue;

				$lbl = $department['department'];

				if($this->settings->getSetting('route_display_format') == 'all_w_status')
				{
				    // Display all with status format
				    if($department['last_auth_date'] <= $this->online_timestamp)
				    {
					// Is Offline
					$lbl .= ' ('.JText::_('CURRENTLY_OFFLINE').')';
				    }
				    else
				    {
					// Is Online
					$lbl .= ' ('.JText::_('CURRENTLY_ONLINE').')';
				    }
				}
				elseif($this->settings->getSetting('route_display_format') == 'only_online')
				{
				    // Display only online operators
				    if($department['last_auth_date'] <= $this->online_timestamp)
				    {
					// Is Offline
					continue;
				    }
				}
			?>
			<option value="<?php echo $department['department']; ?>"><?php echo $lbl; ?></option>
			<?php } ?>
		    </select>
		</label>
		<?php } elseif($this->settings->getSetting('routeby') == 'operator' && empty($this->specific_operators) && empty($this->specific_department) && empty($this->specific_route)) { ?>
		<label id="route-filter-container">
		    <select id="route_filter" name="route_filter">
			<option value=""><?php echo JText::_('SELECT_OPERATOR'); ?></option>
			<?php
			foreach($this->operators as $operator)
			{
			    if(empty($operator['operator_name'])) continue;

			    $lbl = $operator['operator_name'];

			    if($this->settings->getSetting('route_display_format') == 'all_w_status')
			    {
				// Display all with status format
				if($operator['last_auth_date'] <= $this->online_timestamp)
				{
				    // Is Offline
				    $lbl .= ' ('.JText::_('CURRENTLY_OFFLINE').')';
				}
				else
				{
				    // Is Online
				    $lbl .= ' ('.JText::_('CURRENTLY_ONLINE').')';
				}
			    }
			    elseif($this->settings->getSetting('route_display_format') == 'only_online')
			    {
				// Display only online operators
				if($operator['last_auth_date'] <= $this->online_timestamp)
				{
				    // Is Offline
				    continue;
				}
			    }
			?>
			<option value="<?php echo $operator['operator_id']; ?>"><?php echo $lbl; ?></option>
			<?php } ?>
		    </select>
		</label>
		<?php } ?>
		<div class="jlc-clr jlc-margbot4">&nbsp;</div>
		<span class="jlc-left msg-wrapper">
		    <span id="connecting_layer"><?php echo JText::_('PLEASE_WAIT_CONNECTING'); ?></span>
		    <span id="error_layer">Please enter a valid name.</span>
		</span>
		<span class="jlc-right start-btn-wrapper">
		    <button id="start_button" name="start_button" type="button"><?php echo JText::_('START'); ?></button>
		</span>
		<div class="jlc-clr">&nbsp;</div>
	    </form>
	</div>
	<div id="jlc_offline_container">
	    <span class="offline_msg_wrapper<?php if($this->settings->getSetting('enable_messaging') == 'n') { ?>_long<?php } ?>">
		<?php echo $this->popup->getWindowOffline($this->current_language); ?>
	    </span>
	    <div class="jlc-clr">&nbsp;</div>
	    <?php if($this->settings->getSetting('enable_messaging') == 'y') { ?>
	    <form action="" method="post" onsubmit="return false;">
		<table cellpadding="0" cellspacing="0" border="0">
		    <tr>
			<td colspan="2" class="message-header"><?php echo JText::_('LEAVE_A_MESSAGE'); ?></td>
		    </tr>

		    <tr>
			<td class="jlc-lbl"><?php echo JText::_('NAME_LBL'); ?></td>
			<td><input size="30" type="text" id="message_name" name="message_name" value="<?php echo $this->default_name; ?>" maxlength="50" /></td>
		    </tr>

		    <tr>
			<td class="jlc-lbl"><?php echo JText::_('EMAIL_LBL'); ?></td>
			<td><input size="30" type="text" id="message_email" name="message_email" value="<?php echo $this->default_email; ?>" maxlength="100" /></td>
		    </tr>
		    <?php if($this->settings->getSetting('ask_phone_number') != 'none') { ?>
		    <tr>
			<td class="jlc-lbl"><?php echo JText::_('PHONE_LBL'); ?></td>
			<td><input size="18" type="text" id="message_phone" name="message_phone" value="" maxlength="20" /></td>
		    </tr>
		    <?php } ?>
		    <tr>
			<td class="jlc-lbl"><?php echo JText::_('MESSAGE_LBL'); ?></td>
			<td><textarea id="message_txt" name="message_txt" rows="2" cols="28"></textarea></td>
		    </tr>

		    <tr>
			<td colspan="2">
			    <span class="jlc-right">
				<button id="send_message_button" name="send_message_button" type="button"><?php echo JText::_('SEND_MSG'); ?></button>
			    </span>
			</td>
		    </tr>
		</table>
	    </form>
	    <div class="jlc-clr close-wnd-btn-space<?php if($this->settings->getSetting('ask_phone_number') == 'none') { ?>-long<?php } ?>">&nbsp;</div>
	    <?php } ?>
	    <button id="close_window_button" name="close_window_button"><?php echo JText::_('CLOSE'); ?></button>
	</div>
	<div id="jlc_inchat_container">
	    <div id="session-content-display"><div id="session-content-display-inner">&nbsp;</div></div>
	    <div id="status-display-outer">
		<div id="status-display">
		    <img src="<?php echo JURI::root(true); ?>/components/com_jlivechat/assets/images/icons/typing.gif" width="16" height="15" border="0" alt="" align="middle" /><?php echo JText::_('OPERATOR_IS_TYPING'); ?>
		</div>
	    </div>
	    <form action="" method="post" onsubmit="return false;">
		<textarea id="msg-input" wrap="hard"></textarea>
		<div class="jlc-clr">&nbsp;</div>
		<span class="send-msg-btn-wrapper">
		    <button id="send_msg_button" name="send_msg_button" type="button"><?php echo JText::_('SEND'); ?></button>
		</span>
		<div class="jlc-clr">&nbsp;</div>
	    </form>
	    <div class="jlc-clr">&nbsp;</div>
	</div>
    </div>
</div>
<script type="text/javascript">
    <?php if(!empty($this->specific_operators)) { ?>
	specificOperators = '<?php echo $this->specific_operators; ?>';
    <?php } ?>

    <?php if(!empty($this->specific_department)) { ?>
	specificDepartment = '<?php echo addslashes($this->specific_department); ?>';
    <?php } ?>

    <?php if(!empty($this->specific_route)) { ?>
	specificRouteId = <?php echo (int)$this->specific_route; ?>;
    <?php } ?>
	
    <?php if($this->settings->getSetting('routeby') != 'none') { ?>
	var routeFilterMenu = new YAHOO.widget.Button({
	    id: "route-filter-menubutton",
	    name: "route-filter-menubutton",
	    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('routeby') == 'department') { echo JText::_('SELECT_DEPARTMENT'); } elseif($this->settings->getSetting('routeby') == 'operator') { echo JText::_('SELECT_OPERATOR'); } ?></em>",
	    type: "menu",
	    menu: "route_filter",
	    container: "route-filter-container"
	});

	<?php if($this->settings->getSetting('routeby') == 'department') { ?>
	routeFilterMenu.on("selectedMenuItemChange", onRouteDepartmentSelectedMenuItemChange);
	<?php } elseif($this->settings->getSetting('routeby') == 'operator') { ?>
	routeFilterMenu.on("selectedMenuItemChange", onRouteOperatorSelectedMenuItemChange);
	<?php } ?>
    <?php } ?>
    
    var oStartChatBtn = new YAHOO.widget.Button("start_button", { type: "push", onclick: { fn: startChatSession } });
    var oSendMsgBtn = new YAHOO.widget.Button("send_msg_button", { type: "push", onclick: { fn: sendMsgAndClearEvent } });
    var oCloseWndBtn = new YAHOO.widget.Button("close_window_button", { onclick: { fn: closeWnd } });

    <?php if($this->settings->getSetting('enable_messaging') == 'y') { ?>
    var oOfflineSendMsgBtn = new YAHOO.widget.Button("send_message_button", { type: "push", onclick: { fn: leaveMessage } });
    <?php } ?>
    
    JLiveChat.callbackFunc = function() {
	<?php if($this->chat_session_active) { ?>
	// Chat is active
	setSessionActive();
	<?php } elseif(!$this->is_online) { ?>
	setOffline();
	<?php } ?>
    };

    JLiveChat.initialize();
</script>
