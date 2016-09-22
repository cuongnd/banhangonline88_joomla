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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_PERMALINKS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_UNICODE_ALIAS' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_UNICODE_ALIAS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_sef_unicode' , $this->config->get( 'main_sef_unicode' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key vtop">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_URL_TRANSLATION' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_URL_TRANSLATION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_url_translation' , $this->config->get( 'main_url_translation' ) );?>
							<div style="clear:both"></div>
							<div class="small">
							    <?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_URL_TRANSLATION_NOTE' ); ?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_DESC' ); ?></div>

							<div style="padding:5px 0"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_NOTICE');?></div>
							<div class="radio-wrap">
								<div>
									<input type="radio" class="inputbox" value="default" id="main_sef0" name="main_sef"<?php echo $this->config->get('main_sef') == 'default' ? ' checked="checked"' : '';?>>
									<label for="main_sef0">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_TITLE_TYPE');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="date" id="main_sef1" name="main_sef"<?php echo $this->config->get('main_sef') == 'date' ? ' checked="checked"' : '';?>>
									<label for="main_sef1">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_DATE_TYPE');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="category" id="main_sef2" name="main_sef"<?php echo $this->config->get('main_sef') == 'category' ? ' checked="checked"' : '';?>>
									<label for="main_sef2">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_TYPE');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="datecategory" id="main_sef3" name="main_sef"<?php echo $this->config->get('main_sef') == 'datecategory' ? ' checked="checked"' : '';?>>
									<label for="main_sef3">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_DATE_TYPE');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="simple" id="main_sef4" name="main_sef"<?php echo $this->config->get('main_sef') == 'simple' ? ' checked="checked"' : '';?>>
									<label for="main_sef4">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_SIMPLE_TYPE');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="custom" id="main_sef5" name="main_sef"<?php echo $this->config->get('main_sef') == 'custom' ? ' checked="checked"' : '';?>>
									<label for="main_sef5">
										<strong><?php echo JText::_( 'Custom' ); ?></strong> -
									</label>
									<div>
										<span style="line-height:20px;">http://yoursite.com/menu/view/</span>
										<input type="text" class="inputbox width-full" name="main_sef_custom" value="<?php echo $this->config->get( 'main_sef_custom' );?>" style="width: 200px !important;" />
										<span style="line-height:20px;">/title</span>
									</div>
									<div style="margin-top: 5px" class="notice half-width">
										<?php echo JText::_( 'COM_EASYBLOG_AVAILABLE_VALUES_FOR_SEF' );?>:<br /><br />

										%month% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_MONTH_NAME' );?></span><br />
										%day% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_DAY_NAME' );?></span><br />
										%year_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_YEAR_NUMBER' );?></span><br />
										%month_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_MONTH_NUMBER' );?></span><br />
										%day_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_DAY_NUMBER' );?></span><br />
										%category% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_CATEGORY_NAME' );?></span><br />
										%category_id% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_CATEGORY_ID' );?></span><br /><br />

										<?php echo JText::_( 'COM_EASYBLOG_EXAMPLE' );?>: %year_num%/%title%
									</div>
								</div>
							</div>

						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_CANONICAL_URL' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key vtop">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ENABLE_CANONICAL_URL_ENTRY' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ENABLE_CANONICAL_URL_ENTRY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_canonical_entry' , $this->config->get( 'main_canonical_entry' ) );?>
							<div style="clear:both"></div>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_KEYWORDS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_KEYWORDS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_meta_autofillkeywords' , $this->config->get( 'main_meta_autofillkeywords' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_meta_autofilldescription' , $this->config->get( 'main_meta_autofilldescription' ) );?>
						</div>									
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_CHARACTER_LIMIT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_CHARACTER_LIMIT_DESC' ); ?></div>
							<input type="text" name="main_meta_autofilldescription_length" class="inputbox" style="width: 50px;" maxlength="3" value="<?php echo $this->config->get('main_meta_autofilldescription_length' );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>