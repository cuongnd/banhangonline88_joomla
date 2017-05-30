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
<div class="row-fluid">
	<div class="span12">

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS' ); ?></legend>
			<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_DESC' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tr>
					<td class="key" width="300">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_rss' , $this->config->get( 'main_rss' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key" width="300">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_DESC' ); ?></div>
							<select name="main_rss_content" class="inputbox">
								<option value="introtext"<?php echo $this->config->get( 'main_rss_content' ) == 'introtext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_INTROTEXT' ); ?></option>
								<option value="fulltext"<?php echo $this->config->get( 'main_rss_content' ) == 'fulltext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_FULLTEXT' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_TITLE' ); ?></legend>
			<p class="">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/feedburner.png" align="left" style="padding: 0 8px;margin: 0 8px 8px;"/>
				<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_DESC' ); ?>
				<a href="http://feedburner.com" target="_blank">http://feedburner.com</a>
				<br /><br />
				<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_FEED_URL_BELOW' ); ?>
			</p>
			<div style="clear:both;"></div>
			<div class="notice" style="margin-top:5px;">
				<strong><?php echo JText::_( 'COM_EASYBLOG_LATEST_ITEMS_FEED' );?></strong> - <?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=feed&type=rss
			</div>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_feedburner' , $this->config->get( 'main_feedburner' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_feedburnerblogger' , $this->config->get( 'main_feedburnerblogger' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL_DESC' ); ?></div>
							<input type="text" name="main_feedburner_url" class="inputbox full-width" value="<?php echo $this->config->get('main_feedburner_url');?>" size="60" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

	</div>

</div>
