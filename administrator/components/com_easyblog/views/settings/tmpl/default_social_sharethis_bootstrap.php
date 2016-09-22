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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_CODE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_CODE_DESC' ); ?></div>
							<textarea name="social_sharethis_publishers" class="inputbox full-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('social_sharethis_publishers');?></textarea>
							<div class="notice full-width"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_INSTRUCTIONS', 'http://stackideas.com/docs/easyblog/structure/155-settings-configuration-guide');?></div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>
	</div>
</div>
