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
<?php if( $system->config->get( 'main_google_profiles' ) ){?>
<div class="ui-modbox" id="widget-profile-google">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLE_SETTINGS_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLE_SETTINGS_DESC'); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label for="google_profile_url"><?php echo JText::_('COM_EASYBLOG_GOOGLE_PROFILE_URL'); ?> :</label>
    	    	<div>
    	    		<input type="text" class="input text width-full" name="google_profile_url" id="google_profile_url" value="<?php echo $google_profile_url;?>" />
    	    	</div>
        	</li>
        	<li>
        	    <label for="show_google_profile_url"><?php echo JText::_('COM_EASYBLOG_GOOGLE_PROFILE_URL_DISPLAY'); ?> :</label>
        	    <div>
        			<select name="show_google_profile_url" id="show_google_profile_url" class="input select">
        				<option value="1"<?php echo ( $show_google_profile_url )? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_YES'); ?></option>
        				<option value="0"<?php echo ( !$show_google_profile_url )? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_NO'); ?></option>
        			</select>
        		</div>
        	</li>
        </ul>
    </div>
</div>
<?php } ?>