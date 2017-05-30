<?php
/**    
 * SocialBacklinks Config view Facebook layout
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
	.popup .nonactive
		{
		display: none;
		}
' );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );

// Get configuration fields
if ( !$plugin->getOption('post_target', '') )
{
	$fields = "'app_id', 'secret', 'post_target'";
}
else {
	$fields = "'app_id', 'secret', 'post_target', 'page_id', 'post_as_admin'";
}
$doc->addScriptDeclaration( "
document.addEvent('domready', function()
{
	var config = new SB.SocialConfig(
	{
		'wrapper': 'form',
		'single_block': 'form',
		
		'fields': Array( " . $fields . " ),
		'section': 'facebook',
		
		'no_value_error_msg': '" . JText::_( 'SB_EMPTY_VALUE_ERROR', true ) . "',
		
		'ajax_error_msg': '" . JText::_( 'SB_OTHER_ERROR', true ) . "',
		'ajax_success_msg': '" . JText::_( 'SB_SAVED', true ) . "'
	});
	
	document.getElements('.post_target')
		.addEvent('click', function()
		{
			var radio_btn = this.getElement('input');
			if ( !radio_btn || ( radio_btn.get('type') != 'radio' ) )
			{
				return true;
			}
			if ( radio_btn.value > 0 )
			{
				config.fields = Array( 'app_id', 'secret', 'post_target', 'page_id', 'post_as_admin' );
				
				$$('.advanced').removeClass('nonactive')
					.addClass('active');
			}
			else {
				config.fields = Array( 'app_id', 'secret', 'post_target' );
				
				$$('.advanced').removeClass('active')
					.addClass('nonactive');
			}
		});
})
" );
?>
<div class="clr"></div>
<form action="index.php" method="post" name="adminForm" class="popup account-settings-wrapper">
	<div class="header-wrapper">
		<div class="header-text">
			<?php echo JText::sprintf( 'SB_ACCOUNT_SETTINGS', JText::_( 'SB_FACEBOOK' ) ) ?>
		</div>
		<div class="options-wrapper">
			<div class="save-button button">
				<span><?php echo JText::_( 'SB_SAVE') ?></span>
			</div>
		</div>
	</div>
	
	<div class="error-block"></div>
	
	<fieldset>
		<ul>
			<li>
				<label class="key" for="app_id">
	                <?php echo JText::_( 'SB_APP_ID' ); ?>:
	            </label>
	            <div class="value">
					<input type="text" id="app_id" name="app_id" maxlength="250"
						value="<?php echo ( !$plugin->getOption('app_id', false) ) ? '' : $plugin->getOption('app_id', null); ?>" />
				</div>
			</li>
			<li>
                <label class="key" for="secret">
                    <?php echo JText::_( 'SB_APP_SECRET' ); ?>:
                </label>
	            <div class="value">
					 <input type="text" id="secret" name="secret" maxlength="250" 
					 	value="<?php echo ( !$plugin->getOption('secret', false) ) ? '' : $plugin->getOption('secret', null); ?>" />
	            </div>
	        </li>
	        <li>
	            <label class="key" for="post_target">
	                <?php echo JText::_( 'SB_POST_TARGET' ); ?>:
	            </label>
	            <div class="value radio-list">
	            	<?php $post_target = ( !$plugin->getOption('post_target', false) ) ? 0 : $plugin->getOption('post_target', null); ?>
	            	<label for="post_target0" class="radiobtn post_target">
	            		<input type="radio" value="0" id="post_target0" name="post_target" 
							<?php echo ( $post_target == 0 ) ? 'checked="checked"' : '' ?>
						/>
	            		<?php echo JText::_( 'SB_TO_MY_PROFILE' ); ?>
	            	</label>
					<label for="post_target1" class="radiobtn post_target">
						<input type="radio" value="1" id="post_target1" name="post_target" 
							<?php echo ( $post_target == 1 ) ? 'checked="checked"' : '' ?>
						/>
						<?php echo JText::_( 'SB_TO_SELECTED_PAGE' ); ?>
					</label>
					<label for="post_target2" class="radiobtn post_target">
						<input type="radio" value="2" id="post_target2" name="post_target" 
							<?php echo ( $post_target == 2 ) ? 'checked="checked"' : '' ?>
						/>
						<?php echo JText::_( 'SB_TO_BOTH' ); ?>
					</label>
	            </div>
	         </li>
        <?php
        if ( !$post_target )
		{
			$class = 'nonactive';
		}
		else {
			$class = 'active';
		}
        ?>
	        <li class="advanced <?php echo $class ?>">
                <label class="key" for="page_id">
                    <?php echo JText::_( 'SB_PAGE_ID' ); ?>:
                </label>
	           <div class="value">
					 <input type="text" id="page_id" name="page_id" maxlength="250"
					 		value="<?php echo ( !$plugin->getOption('page_id', false) ) ? '' : $plugin->getOption('page_id', null); ?>" />
	            </div>
	        </li>
	        <li class="advanced <?php echo $class ?>">
                <label class="key" for="post_as_admin">
                    <?php echo JText::_( 'SB_POST_AS_ADMIN' ); ?>:
                </label>
	            <div class="value radio-list">
	            	<?php echo JHTML::_ ( 'select.booleanlist', 'post_as_admin', '', 
	            			( !$plugin->getOption('post_as_admin', false) ) ? '' : $plugin->getOption('post_as_admin', null),
	            			JText::_( 'SB_YES_AS_ADMIN' ), JText::_( 'SB_NO_AS_USER' ) ) ?>
	            </div>
	         </li>
        </ul>
    </fieldset>
	
    <div class="ajax-loader"></div>
    <div class="ajax-overlay"></div>
</form>
