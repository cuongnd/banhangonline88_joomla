<?php
/**	
 * SocialBacklinks Dashboard view Default layout
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

JHTML::_( 'behavior.modal' );
JHtml::_( 'behavior.framework', true );

$doc = JFactory::getDocument( );

$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
$doc->addScript( JURI::root( true ) . '/media/com_socialbacklinks/js/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.js' ) );
$doc->addScriptDeclaration( "
var my_cookie = Cookie.write( 'sb_history', '{$this->last_history_id}' );

var showSuccessMsg = function() { }
	
document.addEvent('domready', function()
{
	var dashboard = new SB.Dashboard(
	{
		wrapper: '.block-wrapper',
		header: '.block-header',
		single_block: '.block',
		social_connect: '.connect-link',
		
		ajax_error_msg: '" . JText::_( 'SB_OTHER_ERROR', true ) . "',
		ajax_success_msg: '" . JText::_( 'SB_SAVED', true ) . "'
	});
	
	showSuccessMsg = dashboard.showSuccessMsg;
})
" );
?>
<form class="com-main-wrapper" action="index.php" method="get" name="adminForm">
<table width="100%">
<?php echo $this->getHeader(); ?>
<tbody>
	<tr>
		<td valign="top" width="30%">
			<?php 
				SBDispatcher::getInstance()->runController( 'plugin', array( 'task' => 'renderSettings', 'plugintype' => 'content' ) );
				
				SBDispatcher::getInstance()->runController( 'config', array( 'task' => 'renderSettings' ) );
			?>
		</td>
		<td valign="top" width="40%">
			
			
			<div id="statistics">
			
			<div class="block-wrapper">
				<div class="statistics-header">
					<div class="last-updated">
						<div class="last-updated-text">
							<?php echo JText::_( 'SB_LAST_SYNC' ) ?>:
						</div>
						<div class="update-time">
							<?php if ( !empty( $this->last_sync ) ) : ?>
								<span class="sp-highlighter-data day"><?php echo $this->last_sync->format( 'D', true ); ?></span>
								&#160;
								<span class="sp-highlighter-text date"><?php echo $this->last_sync->format( 'd M Y', true ); ?></span>
								&#160;
								<?php echo JText::_( 'AT' ) ?>
								&#160;
								<span class="sp-highlighter-text time"><?php echo $this->last_sync->format( 'h:i a', true ); ?></span>
							<?php else : ?>
								<span class="sp-highlighter-text"><?php echo JText::_( 'SB_NEVER' ); ?></span>
							<?php endif; ?>
						</div>
					</div> 
					<div class="ajax-loader"></div>
					<div class="clr"></div>
				</div>
				<div class="statistics-block">
					
					<div class="text-wrapper">
						<?php SBDispatcher::getInstance()->runController( 'histories', array( 'layout' => 'sync_statistics' ) ); ?>
					</div>
					
					<div class="statistic-bt-wrapper">
						<a class="history-bt modal" rel="{handler:'iframe',size:{x:800,y:600}}"
							href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=histories&tmpl=component' ) ?>">
					 		<span><?php echo JText::_( 'SB_SEE_HISTORY' ) ?></span>
					 	</a>
						<a class="log-bt modal" rel="{handler:'iframe',size:{x:800,y:600}}"
							href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=errors&tmpl=component' ) ?>">
					 		<span><?php echo JText::_( 'SB_ERRORS_LOG' ) ?></span>
					 	</a>
					 </div>
					 <div class="clr"></div>
				</div>
			</div>
			
			</div>
		</td>
		<td valign="top" width="30%">
			<div id="social-wrapper">
				<div class="block-wrapper">
					<div class="error-block"></div>
					
					<?php SBDispatcher::getInstance()->runController( 'plugin', array( 'task' => 'renderSettings', 'plugintype' => 'network' ) ); ?>
				</div>
			</div>
		</td>
	</tr>
</tbody>
<?php echo $this->getFooter( ); ?>
</table>
<input type="hidden" name="option" value="com_socialbacklinks" />
<input type="hidden" name="view" value="dashboard" />
<input type="hidden" name="task" value="" />
</form> 
