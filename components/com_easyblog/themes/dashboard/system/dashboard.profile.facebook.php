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
<?php if( $this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook' )  && $system->config->get('integrations_facebook_centralized_and_own') ){?>
<script type="text/javascript">

EasyBlog.ready(function($){

	var left = (screen.width/2)-( 300 /2);
	var top = (screen.height/2)-( 300 /2);

	$( '#facebook-login' ).bind( 'click' , function(){
		var url = '<?php echo rtrim( JURI::root() , '/' );?>/index.php?option=com_easyblog&controller=oauth&task=request&type=<?php echo EBLOG_OAUTH_FACEBOOK;?>&call=doneLogin';
		window.open(url, '' , 'scrollbars=no,resizable=no, width=300,height=300,left=' + left + ',top=' + top );
	});
});

window.doneLogin = function(){
	window.location.href = '<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false );?>';
}
</script>
<?php
$expires 	= $facebook->getAccessTokenValue( 'expires' );
$created 	= strtotime( $facebook->created );

$expire 	= EasyBlogHelper::getDate( $created + $expires )->toFormat( '%A, %d %B %Y' );
?>

<div class="ui-modbox" id="widget-profile-facebook">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS_DESC'); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label for="facebook_allow_access"><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?> :</label>
        	    <div>
        	    	<?php if( $facebook->id ): ?>
        				<label class="mbs"><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=revoke&type=' . EBLOG_OAUTH_FACEBOOK );?>"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?></a></label>
						<p style="margin:8px 0 8px 0;" class="small">
							<?php echo JText::_( 'COM_EASYBLOG_FACEBOOK_EXPIRE_TOKEN');?> <strong><?php echo $expire; ?></strong>.
						</p>
						<a href="javascript:void(0);" class="buttons" id="facebook-login"><?php echo JText::_( 'COM_EASYBLOG_FACEBOOK_RENEW_YOUR_TOKEN' );?></a>
        			<?php else: ?>
        				<label class="mbs"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_ACCESS_DESC');?></label>
        				<div>
        					<a href="javascript:void(0);" id="facebook-login"><img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/facebook_signon.png" border="0" alt="here" /></a>
        				</div>
        	    	<?php endif; ?>
        		</div>
        	</li>
        	<li>
        	    <label for="integrations_facebook_auto"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?> :</label>
        	    <div>
        			<select name="integrations_facebook_auto" id="integrations_facebook_auto" class="input select">
        				<option value="1" <?php echo ($facebook->auto == true)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_YES'); ?></option>
        				<option value="0" <?php echo ($facebook->auto == false)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_NO'); ?></option>
        			</select>
        		</div>
        	</li>
        </ul>
    </div>
</div>
<?php } ?>