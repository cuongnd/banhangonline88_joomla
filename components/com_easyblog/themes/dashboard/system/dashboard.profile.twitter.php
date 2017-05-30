<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if( $this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter' ) && $system->config->get('integrations_twitter_centralized_and_own') ){?>
<div class="ui-modbox" id="widget-profile-twitter">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TWITTER_SETTINGS_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TWITTER_SETTINGS_DESC'); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label for="twitter_allow_access"><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?> :</label>
        	    <div>
        			<?php if( $twitter->id && $twitter->request_token && $twitter->access_token): ?>
        			<label>
        				<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=revoke&type=' . EBLOG_OAUTH_TWITTER );?>"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?></a>
        				<div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_REVOKE')?></div>
        			</label>
        			<?php else: ?>
        			<label class="mbl"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></label>
        			<div class="mtm">
						<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=request&type=' . EBLOG_OAUTH_TWITTER );?>">
							<img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/twitter_signon.png" border="0" alt="here" />
						</a>
					</div>
            		<?php endif; ?>
        		</div>
        	</li>
        	<li>
        	    <label for="integrations_twitter_message"><?php echo JText::_('MESSAGE'); ?> :</label>
        	    <div>
        			<textarea id="integrations_twitter_message" name="integrations_twitter_message" class="input textarea width-full"><?php echo (empty($twitter->message)) ? $system->config->get('main_twitter_message', JText::_('COM_EASYBLOG_EASYBLOG_TWITTER_AUTOPOST_MESSAGE') ) : $twitter->message; ?></textarea>
        			<div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_CHAR_LIMIT')?></div>
        		</div>
        	</li>
        	<li>
        	    <label for="twitter_auto"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?> :</label>
        	    <div>
        			<select name="integrations_twitter_auto" id="integrations_twitter_auto" class="input select">
        				<option value="1" <?php echo ($twitter->auto == true)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_YES'); ?></option>
        				<option value="0" <?php echo ($twitter->auto == false)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_NO'); ?></option>
        			</select>
        		</div>
        	</li>
        </ul>
    </div>
</div>
<?php } ?>