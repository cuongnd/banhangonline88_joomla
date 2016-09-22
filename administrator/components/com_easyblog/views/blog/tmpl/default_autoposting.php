<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$draftAutoPost  			= array();
$draftCentralizedAutoPost  	= array(); 

if( $this->draft->id )
{
	$draftCentralizedAutoPost 	= ( $this->draft->id && $this->draft->autopost_centralized) ? explode( ',', $this->draft->autopost_centralized ) : '';
	$draftAutoPost 				= ( $this->draft->id && $this->draft->autopost) ? explode( ',', $this->draft->autopost ) : '';

	if(! empty($draftAutoPost) )
	{
		$draftAutoPost = array_fill_keys($draftAutoPost, 1);
	}

	if(! empty($draftCentralizedAutoPost) )
	{
		$draftCentralizedAutoPost = array_fill_keys($draftCentralizedAutoPost, 1);
	}
}
?>
<ul class="list-form reset-ul">
	<li>
  	<?php
		if( $this->config->get( 'integrations_linkedin_centralized' ) || $this->config->get( 'integrations_facebook_centralized' ) || $this->config->get( 'integrations_twitter_centralized' )
		|| $this->config->get( 'integrations_linkedin_centralized_and_own') || $this->config->get( 'integrations_facebook_centralized_and_own') || $this->config->get( 'integrations_twitter_centralized_and_own' ) ) { ?>
        	<?php if( $this->config->get( 'integrations_linkedin_centralized' ) || $this->config->get( 'integrations_facebook_centralized' ) || $this->config->get( 'integrations_twitter_centralized') )
        	{
          ?>
        		<div class="option">
        			<b><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS');?></b>
        			<div><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS_DESC' );?></div>
        			<div>
        				<span class="ui-highlighter publish-to in-block mrm">
        				<?php if( $this->config->get( 'integrations_facebook_centralized' ) ){ ?>

							<?php if( $this->draft->id ) { ?>

				                <span class="ui-span<?php echo ( isset( $draftCentralizedAutoPost['facebook'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="facebook" id="centralized-facebook"<?php echo ( isset( $draftCentralizedAutoPost['facebook'] ) ) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>

							<?php } else { ?>

				                <span class="ui-span<?php echo ( $this->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_facebook_centralized_send_updates') && !empty($this->blog->id) ) || isset( $draftCentralizedAutoPost['facebook'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="facebook" id="centralized-facebook"<?php echo ( $this->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_facebook_centralized_send_updates') && !empty($this->blog->id) ) || isset( $draftCentralizedAutoPost['facebook'] )) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>
							<?php } ?>
		                <?php } ?>

		                <?php if( $this->config->get( 'integrations_twitter_centralized' ) ){ ?>

							<?php if( $this->draft->id ) { ?>
				                <span class="ui-span<?php echo (isset( $draftCentralizedAutoPost['twitter'] )) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="twitter" id="centralized-twitter"<?php echo (isset( $draftCentralizedAutoPost['twitter'] )) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>
							<?php } else { ?>
				                <span class="ui-span<?php echo ( $this->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_twitter_centralized_send_updates') && !empty($this->blog->id) ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="twitter" id="centralized-twitter"<?php echo ( $this->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_twitter_centralized_send_updates') && !empty($this->blog->id) )) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>
			                <?php }?>

						<?php }?>

		                <?php if( $this->config->get( 'integrations_linkedin_centralized' ) ){ ?>
							<?php if( $this->draft->id ) { ?>
				                <span class="ui-span<?php echo ( isset( $draftCentralizedAutoPost['linkedin'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="linkedin" id="centralized-linkedin"<?php echo ( isset( $draftCentralizedAutoPost['linkedin'] ) ) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
							<?php } else { ?>
				                <span class="ui-span<?php echo ( $this->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_linkedin_centralized_send_updates') && !empty($this->blog->id) )) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="linkedin" id="centralized-linkedin"<?php echo ( $this->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($this->blog->id) || ( $this->config->get('integrations_linkedin_centralized_send_updates') && !empty($this->blog->id) )) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
							<?php } ?>
		                <?php } ?>
		            </span>

        				<div class="clear"></div>
        			</div>
        		</div>
    		<?php
    		}
    		?>

				<?php if(
						$this->acl->rules->update_facebook && $this->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'FACEBOOK' ) ||
						$this->acl->rules->update_twitter && $this->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'TWITTER' ) ||
						$this->acl->rules->update_linkedin && $this->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'LINKEDIN' ) ){
				?>


        		<div class="option">
        			<b><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS');?></b>
        			<div><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS_DESC' );?></div>
        			<div>
        				<span class="ui-highlighter publish-to in-block mrm">
						<?php if( $this->acl->rules->update_facebook && $this->config->get( 'integrations_facebook' ) ){?>

							<?php if( $this->draft->id ) { ?>
				                <span class="ui-span<?php echo ( isset( $draftAutoPost['facebook'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="facebook" id="socialshare-facebook"<?php echo ( isset( $draftAutoPost['facebook'] ) ) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>
							<?php } else if( EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'FACEBOOK' ) ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'FACEBOOK' ) && empty($this->blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="facebook" id="socialshare-facebook"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'FACEBOOK' ) && empty($this->blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>
			                <?php } ?>

						<?php } ?>


		                <?php if( $this->acl->rules->update_twitter && $this->config->get( 'integrations_twitter' ) ){?>

							<?php if( $this->draft->id ) { ?>

				                <span class="ui-span<?php echo ( isset( $draftAutoPost['twitter'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="twitter" id="socialshare-twitter"<?php echo ( isset( $draftAutoPost['twitter'] ) ) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>

							<?php } else if( EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'TWITTER' ) ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'TWITTER' ) && empty($this->blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="twitter" id="socialshare-twitter"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'TWITTER' ) && empty($this->blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>
			                <?php } ?>
						<?php } ?>

		                <?php if( $this->acl->rules->update_linkedin && $this->config->get( 'integrations_linkedin' ) ){?>
							<?php if( $this->draft->id ) { ?>
				                <span class="ui-span<?php echo ( isset( $draftAutoPost['linkedin'] ) ) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="linkedin" id="socialshare-linkedin"<?php echo ( isset( $draftAutoPost['linkedin'] ) ) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
							<?php } else if( EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $this->my->id , 'LINKEDIN' )  ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'LINKEDIN' ) && empty($this->blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="linkedin" id="socialshare-linkedin"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $this->my->id , 'LINKEDIN' ) && empty($this->blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
			                <?php } ?>
						<?php } ?>

		            	</span>
        				<div class="clear"></div>
        			</div>
        		</div>

				<?php } ?>
        <?php } ?>
		<div>&nbsp;</div>
	</li>
</ul>
