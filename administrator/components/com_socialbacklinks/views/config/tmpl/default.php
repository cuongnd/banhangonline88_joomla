<?php
/**	
 * Social Backlinks Config view Default layout
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( );

JHtml::_( 'behavior.framework', true );

$doc = JFactory::getDocument( );
$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
$doc->addScriptDeclaration( "
document.addEvent('domready', function()
{
	var config = new SB.Config(
	{
		'parent_block': 'fieldset',
		'single_block': '.text-block',
		
		'ajax_error_msg': '" . JText::_( 'SB_OTHER_ERROR', true ) . "',
		'ajax_success_msg': '" . JText::_( 'SB_SAVED', true ) . "'
	});
});
" );
?>
<form action="index.php" method="post" name="adminForm" class="block-wrapper popup" id="configuration">
	
	<div class="header-wrapper">
		<div class="header-text"><?php echo JText::_( 'SB_SETTINGS') ?></div>
		<div class="options-wrapper">
			<div class="save-button button">
				<span><?php echo JText::_( 'SB_SAVE') ?></span>
			</div>
		</div>
	</div>
	
	<div class="error-block"></div>
	
	<table style="width: 100%">
	<tbody>
	<tr>
		<td style="vertical-align: top; width: 50%;">
			<fieldset>
				<legend><?php echo JText::_( 'SB_SYNCHRONIZATION' ); ?></legend>
				<div class="config-block">
					<span class="text-block section-block">
						<?php echo JText::_( 'SB_SYNC_AUTOMATICALLY' ) ?>:
					</span>
					
					<?php
					if ( $this->sbsynchronizer )
					{
						$class = 'on-button';
						$style = '';
					}
					else {
						$class = 'off-button';
						$style = 'style="display: none; opacity: 0"';
					}
					?>
					<div class="on-off-wrapper">
						<div class="on-off-cell">
							<div id="sbsynchronizer" class="config-on-off <?php echo $class ?>"></div>
						</div>
					</div>
					<div class="clr"></div>
					
					<input type="hidden" name="sbsynchronizer" value="<?php echo ( int ) $this->sbsynchronizer ?>" />
				</div>
				<div class="config-block hidden-block" <?php echo $style ?>>
					<div class="text-block">
						<span class="section-block">
							<?php echo JText::_( 'SB_SYNC_PERIODICITY' ) ?>:
						</span>
						<?php echo JText::_( 'SB_EVERY' ) ?>
						<input type="text" name="sync_periodicity" id="sync_periodicity" 
							size="3" maxlength="4" value="<?php echo ( int ) $this->sync_periodicity ?>" 
						/>
						<?php echo JText::_( 'SB_MIN' ) ?>
					</div>
					<div class="text-block">
						<span class="section-block">
							<?php echo JText::_( 'SB_SEND_ERRORS_NOTIFICATIONS' ) ?>:
						</span><br />
						<span>
							<label class="radiobtn errors_recipient_type" id="errors_recipient_type2-lbl" for="errors_recipient_type2">
								<input type="radio" value="2" class="inputbox" id="errors_recipient_type2" name="errors_recipient_type"
									<?php echo ( $this->errors_recipient_type == 2 ) ? 'checked="checked"' : '' ?> 
								/>
								<?php echo JText::_( 'SB_SYSTEM_ADMINISTRATORS' ); ?>
							</label>
							<label class="radiobtn errors_recipient_type" id="errors_recipient_type1-lbl" for="errors_recipient_type1">
								<input type="radio" value="1" class="inputbox" id="errors_recipient_type1" name="errors_recipient_type" 
									<?php echo ( $this->errors_recipient_type == 1 ) ? 'checked="checked"' : '' ?>
								/>
								<?php echo JText::_( 'SB_ANOTHER_RECIPIENT' ); ?>
							</label>
							<label class="radiobtn errors_recipient_type" id="errors_recipient_type0-lbl" for="errors_recipient_type0">
								<input type="radio" value="0" class="inputbox" id="errors_recipient_type0" name="errors_recipient_type" 
									<?php echo ( $this->errors_recipient_type == 0 ) ? 'checked="checked"' : '' ?>
								/>
								<?php echo JText::_( 'SB_NOBODY' ); ?>
							</label>
						</span>
					</div>
					<?php
					if ( $this->errors_recipient_type == 1 )
					{
						$style = '';
					}
					else {
						$style = 'style="display: none; opacity: 0"';
					}
					?>
					<div class="text-block hidden-block" <?php echo $style ?>>
						<span class="section-block">
							<?php echo JText::_( 'SB_SEND_ERRORS_EMAIL' ) ?>:
						</span>
						<input type="text" name="send_errors_email" id="send_errors_email" size="30" value="<?php echo $this->send_errors_email ?>" />
					</div>
					<div class="text-block">
						<span class="section-block">
							<?php echo JText::_( 'SB_SYNC_DOMAIN' ) ?>:
						</span>
						<input type="text" name="sync_domain" id="sync_domain" 
							size="30" value="<?php echo @$this->sync_domain ?>" 
						/>
					</div>
				</div>
			</fieldset>
		</td>
		<td style="vertical-align: top; width: 50%;">
			<fieldset>
				<legend><?php echo JText::_( 'SB_HISTORY' ); ?></legend>
				<div class="config-block history">
					<div class="text-block">
						<label for="clean_history0" class="radiobtn clean_history">
							<input type="radio" value="0" class="inputbox" id="clean_history0" name="clean_history" 
								<?php echo ( $this->clean_history == 0 ) ? 'checked="checked"' : '' ?>
							/>
							<?php echo JText::_( 'SB_KEEP_HISTORY_FOREVER' ); ?>
						</label>
						<label for="clean_history1" class="radiobtn clean_history">
							<input type="radio" value="1" class="inputbox" id="clean_history1" name="clean_history" 
								<?php echo ( $this->clean_history == 1 ) ? 'checked="checked"' : '' ?>
							/>
							<?php echo JText::_( 'SB_CLEAN_HISTORY' ); ?>
						</label>
					</div>
					<?php
					if ( $this->clean_history )
					{
						$style = '';
					}
					else {
						$style = 'style="display: none; opacity: 0"';
					}
					?>
					<div class="text-block hidden-block" <?php echo $style ?>>
						<?php echo JText::_( 'SB_EVERY' ) ?>
						<input type="text" name="clean_history_periodicity" id="clean_history_periodicity" 
							size="3" maxlength="4" value="<?php echo $this->clean_history_periodicity ?>" 
						/>
						<?php echo JText::_( 'SB_DAYS' ) ?>
					</div>
				</div>
			</fieldset>
		</td>
	</tr>
	</tbody>
	</table>
	
	<div class="ajax-loader"></div>
	<div class="ajax-overlay"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="view" value="config" />
	<input type="hidden" name="task" value="save" />
</form>
