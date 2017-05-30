<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License
* @website 		http://stackideas.com
* @author 		StackIdeas
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<table class="noshow">
	<tr>
		<td width="50%" valign="top">

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_LAYOUT_TITLE' ); ?></legend>

			<p class="small mt-5 mb-10">
				<img style="margin: 0 15px 0 10px;width: 64px;float:left;" class="float-l" src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/easysocial.png" />
				<?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_INFO' ); ?><br><br>
				<a class="button-blue" target="_blank" href="http://stackideas.com/easysocial?from=easyblog"><?php echo JText::_( 'COM_EASYBLOG_SIGNUP_WITH_EASYSOCIAL' );?></a>
			</p>

			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_TOOLBAR' ); ?>
					</span>
					</td>
					<td valign="top">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TOOLBAR_EASYSOCIAL_USE_MODULE_INFO' ); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ACHIEVEMENTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ACHIEVEMENTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_badges' , $this->config->get( 'integrations_easysocial_badges' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_CONVERSATIONS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_CONVERSATIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_conversations' , $this->config->get( 'integrations_easysocial_conversations' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_POINTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_POINTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_points' , $this->config->get( 'integrations_easysocial_points' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FRIENDS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FRIENDS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_friends' , $this->config->get( 'integrations_easysocial_friends' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FOLLOW' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FOLLOW_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_followers' , $this->config->get( 'integrations_easysocial_followers' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_TITLE' ); ?></legend>

				<p class="small">
					<?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_STREAM_INFO' ); ?>
				</p>

				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_MODIFY_EDIT_PROFILE_LINK' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_MODIFY_EDIT_PROFILE_LINK_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_editprofile' , $this->config->get( 'integrations_easysocial_editprofile' ) );?>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>


			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_TITLE' ); ?></legend>

				<p class="small">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_INFO' ); ?>
				</p>

				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_INTEGRATION' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_INTEGRATION_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_privacy' , $this->config->get( 'integrations_easysocial_privacy' ) );?>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>


		</td>

		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_TITLE' ); ?></legend>
				<p class="small"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_INFO' );?></p>
				<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ENABLE_MEDIA' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ENABLE_MEDIA_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easysocial_album' , $this->config->get( 'integrations_easysocial_album' ) );?>
						</div>
					</td>
				</tr>
				</table>	
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_TITLE' ); ?></legend>

				<p class="small">
					<?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_STREAM_INFO' ); ?>
				</p>

				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_stream_newpost' , $this->config->get( 'integrations_easysocial_stream_newpost' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_FEATURE_POST' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_FEATURE_POST_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_stream_featurepost' , $this->config->get( 'integrations_easysocial_stream_featurepost' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_UPDATED_POST' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_UPDATED_POST_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_stream_updatepost' , $this->config->get( 'integrations_easysocial_stream_updatepost' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_DESC' ); ?></div>
								<select name="integrations_easysocial_stream_newpost_source">
									<option value="intro"<?php echo $this->config->get( 'integrations_easysocial_stream_newpost_source') == 'intro' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_INTRO' ); ?></option>
									<option value="content"<?php echo $this->config->get( 'integrations_easysocial_stream_newpost_source') == 'content' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_CONTENT' ); ?></option>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_COMMENT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_COMMENT_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_stream_newcomment' , $this->config->get( 'integrations_easysocial_stream_newcomment' ) );?>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_TITLE' ); ?></legend>

				<p class="small">
					<?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_NOTIFICATIONS_INFO' ); ?>
				</p>

				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_POST' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_notifications_newpost' , $this->config->get( 'integrations_easysocial_notifications_newpost' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_COMMENT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_COMMENT_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_notifications_newcomment' , $this->config->get( 'integrations_easysocial_notifications_newcomment' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_RATING' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_RATING_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_notifications_ratings' , $this->config->get( 'integrations_easysocial_notifications_ratings' ) );?>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>


			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_TITLE' ); ?></legend>

				<p class="small">
					<?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_INDEXER_INFO' ); ?>
				</p>

				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_easysocial_indexer_newpost' , $this->config->get( 'integrations_easysocial_indexer_newpost' ) );?>
							</div>
						</td>
					</tr>

					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH_DESC' ); ?></div>
								<input type="text" name="integrations_easysocial_indexer_newpost_length" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('integrations_easysocial_indexer_newpost_length');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
							</div>
						</td>
					</tr>

				</table>
			</fieldset>

		</td>
	</tr>
</table>
