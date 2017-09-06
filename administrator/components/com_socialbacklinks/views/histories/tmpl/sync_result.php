<?php
/**	
 * Social Backlinks Histories view Sync Result layout
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
$doc->addStyleDeclaration( "
	#statistics
		{
		margin: 0;
		}
" );
if ( !empty($this->loading) ) :
	$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
	$doc->addScriptDeclaration( "
document.addEvent('domready', function()
{
	var sync = new SB.Synchronization(
	{
		wrapper: '.block-wrapper',
		single_block: '.block-wrapper',
		
		ajax_error_msg: '" . JText::_( 'SB_OTHER_ERROR', true ) . "'
	});
	
	sync.startSynchronization( document.id('synchronization').getElement('.error-block'), 
				{ 
					view: 'sync', 
					task: 'synchronize',
					progress: 1 
				}
				, sync.showSyncResult );
	
	parent.sync_check_interval = setInterval( function()
		{
			sync.checkProgress( document.id('synchronization').getElement('.progress-bar'),
				{
					view: 'sync', 
					task: 'checkStatus' 
				}, sync.updateProgressBar )
		}, 1000 )
})
" );
?>
<div id="synchronization">
	<div class="block-wrapper">
		<div class="error-block"></div>
		
		<div class="loading-text">
			<?php echo JText::_( 'SB_SYNCHRONIZATION' ) ?>...
		</div>
		<div class="ajax-loader">
			<div class="progress-bar"></div>
			<div class="progress-text"><?php echo JText::_( '0%' ) ?></div>
		</div>
		<div class="ajax-overlay"></div>
	</div>
</div>
<?php else : ?>
<div id="statistics" class="popup">
	<div class="block-wrapper">
		<div class="header-wrapper">
			<div class="header-text"><?php echo JText::_( 'SB_SYNC_RESULTS' ) ?></div>
			<div class="options-wrapper">
				<a class="modal button" rel="{handler:'iframe',size:{x:800,y:600}}"
					href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=histories&tmpl=component' ) ?>">
			 		<span><?php echo JText::_( 'SB_VIEW_HISTORY' ) ?></span>
			 	</a>
			 	<?php if ( $this->show_errors_button ) : ?>
				<a class="modal button" rel="{handler:'iframe',size:{x:800,y:600}}"
					href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=errors&tmpl=component' ) ?>">
			 		<span><?php echo JText::_( 'SB_VIEW_ERRORS' ) ?></span>
			 	</a>
			 	<?php endif; ?>
			</div>
		</div>
		
		<div class="statistics-block">
			
			<div class="text-wrapper">
				<?php echo $this->loadTemplate( 'table' ) ?>
			</div>
			<div class="clr"></div>
		</div>
	</div>
</div>
<?php endif; ?>
