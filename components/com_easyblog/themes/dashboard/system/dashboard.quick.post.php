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
<?php if( $this->acl->rules->add_entry ) { ?>

<script type="text/javascript">
// TODO: Port tag form to new layout.
</script>

    <div class="ui-modbox" id="widget-quickpost">
        <div class="ui-modhead">
        	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICK_POST_PAGE_HEADING'); ?></div>
        	<a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
        </div>
        <div class="ui-modbody clearfix">
            <form id="quick-post" name="quick-post" method="post">
                <div id="eblog-message" class="eblog-message"><div></div></div>
                <ul class="list-form tight reset-ul">
                    <li>
                        <label for="title" class="ui-postlabel"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_TITLE'); ?> :</label>
                        <div>
                            <input type="text"  class="input text width-full fwb" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_TITLE_INSTRUCTIONS'); ?>" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_TITLE_INSTRUCTIONS'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_TITLE_INSTRUCTIONS'); ?>';}" name="title" id="title">
                        </div>
                    </li>
                    <li>
                        <label for="category_id" class="ui-postlabel"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_CATEGORY'); ?> :</label>
                        <div>
    						<?php echo $categories; ?>
                        </div>
                    </li>
                    <?php if( $this->acl->rules->enable_privacy ){ ?>
                    <li>
                        <label for="private"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_PRIVACY' ); ?> :</label>
                        <div>
    						<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions() , 'private' , 'size="1" class="input select"' , 'value' , 'text' );?>
                        </div>
                    </li>
                    <?php } ?>
                    <li>
                        <label for="content" class="ui-postlabel"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_CONTENT'); ?> :</label>
                        <div class="ui-inputwrap">
                            <textarea class="input textarea width-full" rows="6" name="content" id="eblog-post-content"></textarea>
                        </div>
                    </li>
                    <li class="write-posttags">
                    	<label for="ui-tags"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_TAGS'); ?> :</label>
    					<div class="ui-inputwrap">
                            // TODO: Port to new tag layout.
    					    <?php if($this->acl->rules->create_tag): ?>
    					    <?php endif; ?>
    					</div>
                    </li>
                </ul>
                <div class="ui-modfoot">
                    <span id="quickpost-loading" class="float-r mts ir"></span>
                    <input type="button" onclick="eblog.dashboard.quickpost.save();" value="<?php echo JText::_('COM_EASYBLOG_PUBLISH_NOW_BUTTON'); ?>" class="button-head tight float-r" name="publish-post" />
                    <div class="ui-highlighter publish-to float-r mrs">

                        <script type="text/javascript">
                            EasyBlog.ready(function($) {
                                $("input[name='socialshare[]']").click(function() {
                                    $(this).parent().toggleClass( 'active' );
                                });
                            });
                        </script>
						<?php if(
								$this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ||
								$this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ||
								$this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' ) ){
						?>
					    <span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_ALSO_PUBLISH_TO' );?> : </span>
					    <?php } ?>
					<?php if( $this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ){?>
                        <span class="ui-span">
    						<input type="checkbox" name="socialshare[]" value="facebook" id="socialshare-facebook"<?php echo EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'FACEBOOK' ) ? ' checked="checked"' : '';?> />
    						<label for="socialshare-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>"><span class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></span></label>
                        </span>
					<?php } ?>
					<?php if( $this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ){?>
                        <span class="ui-span">
    						<input type="checkbox" name="socialshare[]" value="twitter" id="socialshare-twitter"<?php echo EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'TWITTER' ) ? ' checked="checked"' : '';?> />
    						<label for="socialshare-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>"><span class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></span></label>
                        </span>
					<?php } ?>
					<?php if( $this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' ) ){?>
                        <span class="ui-span">
    						<input type="checkbox" name="socialshare[]" value="linkedin" id="socialshare-linkedin"<?php echo EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'LINKEDIN' ) ? ' checked="checked"' : '';?> />
    						<label for="socialshare-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>"><span class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></span></label>
                        </span>
					<?php } ?>
                    </div>

                    <input type="button" onclick="eblog.dashboard.quickpost.draft();" value="<?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON'); ?>" class="button-head tight silver" name="save-draft" />
                    <span id="quickdraft-loading" class="float-l mts ir"></span>
                    <input type="reset" value="<?php echo JText::_('COM_EASYBLOG_RESET_FORM_BUTTON'); ?>" class="button-head tight silver" name="reset-form" id="reset-form" />
                </div>
            </form>
        </div>
    </div>
<?php } ?>
