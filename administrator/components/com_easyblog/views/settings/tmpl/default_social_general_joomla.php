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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SETTINGS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_POSITIONS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_POSITIONS_DESC' ); ?></div>
							<?php
								$position = array();
								$position[] = JHTML::_('select.option', 'top', JText::_( 'COM_EASYBLOG_TOP_OPTION' ) );
								$position[] = JHTML::_('select.option', 'left', JText::_( 'COM_EASYBLOG_LEFT_OPTION' ) );
								$position[] = JHTML::_('select.option', 'right', JText::_( 'COM_EASYBLOG_RIGHT_OPTION' ) );
								$position[] = JHTML::_('select.option', 'bottom', JText::_( 'COM_EASYBLOG_BOTTOM_OPTION' ) );
								$position = JHTML::_('select.genericlist', $position, 'main_socialbutton_position', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_socialbutton_position' ) );
								echo $position;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_ENABLE_RTL' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_ENABLE_RTL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'social_rtl' , $this->config->get( 'social_rtl' ) );?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_ORDER' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_ORDER_DESC' ); ?></div>
							<table class="social-buttons-preview" style="width:50% !important;">
								<?php foreach( $this->socialButtonsOrder as $key => $val ) { ?>
								<tr>
									<td width="60%">
										<div>
											<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_' . strtoupper( $key ) );?></label>
										</div>
									</td>
									<td width="40%">
										<div>
											<input type="text" name="<?php echo 'integrations_order_' . $key;?>" id="<?php echo 'integrations_order_' . $key;?>" value="<?php echo $val; ?>" />
										</div>
									</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_PRINT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_PRINT_DESC' ); ?></div>
							<?php
								$printButton = array();
								$printButton[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_DISABLE' ) );
								$printButton[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_ENABLE' ) );
								$printButton[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_REGISTERED' ) );
								$showdet = JHTML::_('select.genericlist', $printButton, 'layout_enableprint', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_enableprint' , '1' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_PDF' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_PDF_DESC' ); ?></div>
							<?php
								$pdfButton = array();
								$pdfButton[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_DISABLE' ) );
								$pdfButton[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_ENABLE' ) );
								$pdfButton[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_REGISTERED' ) );
								$showdet = JHTML::_('select.genericlist', $pdfButton, 'layout_enablepdf', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_enablepdf' , '1' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_BOOKMARK' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_BOOKMARK_DESC' ); ?></div>
							<?php
								$bookmarkButton = array();
								$bookmarkButton[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_DISABLE' ) );
								$bookmarkButton[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_ENABLE' ) );
								$bookmarkButton[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PUBLISHING_TOOL_REGISTERED' ) );
								$showdet = JHTML::_('select.genericlist', $bookmarkButton, 'layout_enablebookmark', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_enablebookmark' , '1' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_PROVIDER' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_PROVIDER_DESC' ); ?></div>
								<?php
									$provider = array();
									$provider[] = JHTML::_('select.option', 'addthis', JText::_( 'COM_EASYBLOG_ADDTHIS_OPTION' ) );
									$provider[] = JHTML::_('select.option', 'sharethis', JText::_( 'COM_EASYBLOG_SHARETHIS_OPTION' ) );
									$showOption = JHTML::_('select.genericlist', $provider, 'social_provider', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('social_provider' , 'addthis' ) );
									echo $showOption;
								?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>