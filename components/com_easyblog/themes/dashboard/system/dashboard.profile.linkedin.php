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
<?php if( $this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin' ) && $system->config->get('integrations_linkedin_centralized_and_own') ){?>
<div class="ui-modbox" id="widget-profile-linkedin">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_SETTINGS_DESC'); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?> :</label>
        	    <div>
        	    	<?php if( $linkedin->id ): ?>
        				<label><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=revoke&type=' . EBLOG_OAUTH_LINKEDIN );?>"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?></a></label>
        			<?php else: ?>
        				<label><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?></label>
        				<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=request&type=' . EBLOG_OAUTH_LINKEDIN );?>">
        					<img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/linkedin_signon.png" border="0" alt="here" />
        				</a>
        	    	<?php endif; ?>
        		</div>
        	</li>
            <li>
                <label for="integrations_linkedin_message"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_LINKEDIN_MESSAGE' );?> :</label>
                <div>
                    <textarea id="integrations_linkedin_message" name="integrations_linkedin_message" class="input textarea width-full"><?php echo (empty($linkedin->message)) ? $system->config->get('main_linkedin_message' ) : $linkedin->message; ?></textarea>
                </div>
            </li>
        	<li>
        	    <label for="integrations_linkedin_auto"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?> :</label>
        	    <div>
        			<select name="integrations_linkedin_auto" id="integrations_linkedin_auto" class="input select">
        				<option value="1" <?php echo ($linkedin->auto == true)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_YES'); ?></option>
        				<option value="0" <?php echo ($linkedin->auto == false)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_NO'); ?></option>
        			</select>
        		</div>
        	</li>
        	<li>
        	    <label for="integrations_linkedin_private"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE'); ?> :</label>
        	    <div>
        	    	<input type="checkbox" name="integrations_linkedin_private" id="integrations_linkedin_private" value="1"<?php echo $linkedin->private ? ' checked="checked"' : '';?> class="input checkbox" />
        			<label for="integrations_linkedin_private"><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE_DESC' );?></label>
        		</div>
        	</li>
        </ul>
    </div>
</div>
<?php } ?>