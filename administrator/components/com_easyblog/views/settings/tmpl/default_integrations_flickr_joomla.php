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
?>
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_FLICKR_TITLE' ); ?></legend>
			<table border="0">
				<tr>
					<td valign="top" width="25%"><img src="http://l.yimg.com/g/images/en-us/flickr-yahoo-logo.png.v4" align="right"/></td>
					<td valign="top">
						<p>
							<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FLICKR_INFO');?><br />
						</p>
						<div style="line-height:28px;height:28px;">
							<a href="http://www.flickr.com/services/apps/create/" target="_blank" class="buttons"><?php echo JText::_( 'COM_EASYBLOG_FLICKR_CREATE_APP' );?></a>

							<?php echo JText::_( 'COM_EASYBLOG_OR' ); ?>
							<a href="http://stackideas.com/docs/easyblog/integrations/299-integrating-with-flickr" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_FLICKR_VIEW_DOC' );?></a>
						</div>
					</td>
				</tr>
			</table>
			<div style="clear:both;margin-top:20px;"></div>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_FLICKR' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_FLICKR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_media_flickr' , $this->config->get( 'layout_media_flickr' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY_DESC' ); ?></div>
							<input type="text" name="integrations_flickr_api_key" class="inputbox" style="width: 300px;" value="<?php echo $this->config->get('integrations_flickr_api_key' );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY_DESC' ); ?></div>
							<input type="text" name="integrations_flickr_secret_key" class="inputbox" style="width: 300px;" value="<?php echo $this->config->get('integrations_flickr_secret_key' );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>