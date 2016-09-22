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
<div class="ui-modbox" id="widget-profile-account">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS_DESC'); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_USERNAME'); ?> :</label>
        	    <div><input type="text" id="username" name="username" class="input text disabled width-300" value="<?php echo $my->username; ?>" disabled="disabled" /></div>
        	</li>
        	<li>
        	    <label for="fullname"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME'); ?> :</label>
        	    <div><input type="text" id="fullname" name="fullname" class="input text width-200" value="<?php echo $this->escape( $my->name ); ?>" /></div>
        	</li>
        	<li>
        	    <label for="nickname"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_WHAT_OTHERS_CALL_YOU'); ?> :</label>
        	    <div><input type="text" id="nickname" name="nickname" class="input text width-300" value="<?php echo $this->escape( $profile->nickname ); ?>" /></div>
        	</li>
        	<?php if( $system->config->get( 'main_joomlauserparams' ) ) { ?>
        	<li>
        		<label for="email"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_ACCOUNT_EMAIL' ); ?> :</label>
        		<div><input class="input text width-half required validate-email" type="text" id="email" name="email" value="<?php echo $this->escape( $my->email );?>" size="40" /></div>
        	</li>
        	<?php if($my->get('password')) { ?>
        	<li>
        		<label for="password"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD' ); ?> :</label>
        		<div>
        			<input class="half validate-password" type="password" id="password" name="password" value="" class="" />
        		</div>
        	</li>
        	<li>
        		<label for="password2"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_ACCOUNT_RECONFIRM_PASSWORD' ); ?> :</label>
        		<div>
        			<input class="half validate-passverify" type="password" id="password2" name="password2" size="40" />
        		</div>
        	</li>
        	<?php } ?>
        	<?php } ?>
        	<?php if($system->config->get('layout_avatar') && $system->config->get( 'layout_avatarIntegration' ) == 'default' ) { ?>
        	<li>
        	    <label for="file-upload"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE'); ?> :</label>
        	    <div>
                    <div>
                        <img class="avatar-image" class="mts" src="<?php echo $profile->getAvatar(); ?>"/>
                    </div>
                    <?php  if( $this->acl->rules->upload_avatar ) { ?>
                    <div id="avatar-upload-form" class="mts">
                    	<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE_UPLOAD_CONDITION', (float) $system->config->get( 'main_upload_image_size', 0 ) , EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT); ?>
                    	<div class="mts"><input id="file-upload" type="file" name="Filedata" /></div>
                    	<div><span id="upload-clear"></span></div>
                    </div>
                    <?php } ?>
        		</div>
        	</li>
        	<?php } ?>
        </ul>
    </div>
</div>