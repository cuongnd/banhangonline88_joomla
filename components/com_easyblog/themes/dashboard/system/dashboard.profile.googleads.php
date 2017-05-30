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
<?php if( $this->acl->rules->add_adsense && $config->get( 'integration_google_adsense_enable' ) ){?>
<div class="ui-modbox" id="widget-profile-seo">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_GOOGLEADS_DESC' );?></span>
        </div>
        <ul class="list-form reset-ul">
    		<li>
    		    <label for="adsense_published"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_ENABLE'); ?> :</label>
    		    <div>
    				<select name="adsense_published" id="adsense_published" class="input select">
    					<option value="1" <?php echo ($adsense->published)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_YES'); ?></option>
    					<option value="0" <?php echo empty($adsense->published)? 'SELECTED' : ''; ?>><?php echo JText::_('COM_EASYBLOG_NO'); ?></option>
    				</select>
    			</div>
    		</li>
    		<li>
    		    <label for="adsense_code"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE'); ?> :</label>
    		    <div>
    				<textarea id="adsense_code" name="adsense_code" class="input textarea width-full" style="height: 80px;"><?php echo $adsense->code; ?></textarea>
    				<div class="mts"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE_HELP'); ?></div>
    			</div>
    		</li>
    		<li>
    		    <label for="adsense_display"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_APPEARANCE'); ?> :</label>
    		    <div>
    				<select name="adsense_display" class="input select">
    					<option value="both"<?php echo ($adsense->display == 'both')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_HEADER_AND_FOOTER'); ?></option>
    					<option value="header"<?php echo ($adsense->display == 'header')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_HEADER'); ?></option>
    					<option value="footer"<?php echo ($adsense->display == 'footer')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_FOOTER'); ?></option>
    					<option value="beforecomments"<?php echo ($adsense->display == 'beforecomments')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_BEFORE_COMMENTS'); ?></option>
    					<option value="userspecified"<?php echo ($adsense->display == 'userspecified')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_USER_SPECIFIED'); ?></option>
    				</select>
    				<div class="mts"><?php echo JText::_('COM_EASYBLOG_ADSENSE_DISPLAY_NOTE'); ?></div>
    			</div>
    		</li>
        </ul>
        </div>
</div>
<?php } ?>