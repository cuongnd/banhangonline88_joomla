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
<?php
if( $system->config->get('main_feedburner') && $system->config->get('main_feedburnerblogger') && $this->acl->rules->allow_feedburner)
{
?>
<div class="ui-modbox" id="widget-profile-feedburner">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FEEDBURNER_TITLE'); ?></div>
        <a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_FEEDBURNER_DESC' ); ?></span>
        </div>
        <ul class="list-form reset-ul">
        	<li>
        	    <label for="adsense_code"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FEEDBURNER_URL'); ?> :</label>
        	    <div><input type="text" id="feedburner_url" name="feedburner_url" class="input text width-half" value="<?php echo $this->escape( $feedburner->url ); ?>" /></div>
        	</li>
        </ul>
    </div>
</div>
<?php
}
?>