<?php
/**
 * SocialBacklinks Config view Twitter layout
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
$doc->addStyleDeclaration( '
.contentpane
{
height: 90%;
}
' );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
$doc->addScriptDeclaration( "
document.addEvent('domready', function()
{
var config = new SB.SocialConfig(
{
'wrapper': 'form',
'single_block': 'form',

'fields': Array( 'consumer_key', 'consumer_secret' ),
'section': 'twitter',

'no_value_error_msg': '" . JText::_( 'SB_EMPTY_VALUE_ERROR', true ) . "',

'ajax_error_msg': '" . JText::_( 'SB_OTHER_ERROR', true ) . "',
'ajax_success_msg': '" . JText::_( 'SB_SAVED', true ) . "'
})
})
" );
?>
<form action="index.php" method="post" name="adminForm" class="popup account-settings-wrapper">
	<div class="header-wrapper">
		<div class="header-text">
			<?php echo JText::sprintf( 'SB_ACCOUNT_SETTINGS', JText::_( 'SB_TWITTER' ) ) ?>
		</div>
		<div class="options-wrapper">
			<div class="save-button button">
				<span><?php echo JText::_( 'SB_SAVE')
					?></span>
			</div>
		</div>
	</div>

	<div class="error-block"></div>

	<fieldset>
		<ul>
			<li>
				<label class="key" for="app_id"> <?php echo JText::_( 'SB_CONSUMER_KEY' ); ?>: </label>
				<div class="value">
					<input type="text" id="consumer_key" name="consumer_key" maxlength="250"
					value="<?php echo $plugin->getOption('consumer_key', '')?>" />
				</div>
			</li>
			<li>
				<label class="key" for="secret"> <?php echo JText::_( 'SB_CONSUMER_SECRET' ); ?>: </label>
				<div class="value">
					<input type="text" id="consumer_secret" name="consumer_secret" maxlength="250"
					value="<?php echo $plugin->getOption('consumer_secret', '') ?>" />
				</div>
				consumer_secret
			</li>
		</ul>
	</fieldset>

	<div class="ajax-loader"></div>
	<div class="ajax-overlay"></div>
</form>
