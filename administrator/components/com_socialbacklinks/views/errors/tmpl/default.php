<?php
/**    
 * SocialBacklinks Errors view default layout
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
	var errors = new SB.Errors(
	{
		'wrapper': '.block-wrapper',
		
		'no_item_error_msg': '" . JText::_( 'SB_NO_ITEM_SELECTED', true ) . "',
		'empty_table_msg': '" . JText::_( 'SB_NO_ITEMS', true ) . "',
		'ajax_error_msg': '" . JText::_( 'SB_OTHER_ERROR', true ) . "'
	})
});
" );
?>
<div id="errors" class="block-wrapper popup">
	<div class="error-block"></div>
	<div class="header-wrapper">
		<div class="header-text"><?php echo JText::_( 'SB_ERRORS_TITLE' ) ?></div>
		<div class="options-wrapper">
			<?php if ( !empty( $this->rows ) ) : ?>
			<div class="button sync-all-button">
				<span><?php echo JText::_( 'SB_SYNCHRONIZE_ALL' ) ?></span>
			</div>
			<?php endif; ?>
			<?php
			if ( empty( $this->rows ) )
			{
				$class = ' disabled';
			} 
			else {
				$class = '';
			}
			?>
			<div class="button delete-button<?php echo $class; ?>">
				<span><?php echo JText::_( 'SB_REMOVE_SELECTED' ) ?></span>
			</div>
		</div>
	</div>
	
	<form action="" method="post" name="adminForm" id="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th class="first" width="15"><?php echo JText::_( 'SB_NUM' ); ?></th>
				<th width="5">
		            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(<?php echo count( $this->rows ) ?>);" />
		        </th>
				<th class="title"><?php echo JText::_( 'SB_TITLE' ); ?></th>
				<th class="title" width="110"><?php echo JText::_( 'SB_NETWORK' ); ?></th>
				<th width="75"><?php echo JText::_( 'SB_EXTENSION' ); ?></th>
				<th width="80"><?php echo JText::_( 'SB_SYNCED' ); ?></th>
				<th class="last" width="200"><?php echo JText::_( 'SB_MESSAGE' ); ?></th>
			</tr>
		</thead>
			<tbody>
			<?php if ( empty( $this->rows ) ) : ?>
				<tr>
					<td colspan="7" align="center"><?php echo JText::_( 'SB_NO_ITEMS' ); ?></td>
				</tr>
			<?php
			else :
				$rows = $this->rows;
				$k = 0;
				for ( $i = 0, $n = count( $rows ); $i < $n; $i++ )
				{
					$row = &$rows[$i];
			?>
					<tr class="<?php echo "row$k"; ?>" id="row-<?php echo $row->socialbacklinks_error_id ?>">
						<td><?php echo $i + 1; ?></td>
						<td><?php echo JHTML::_( 'grid.id', $i, $row->socialbacklinks_error_id ); ?></td>
						<td><?php echo $this->escape( $row->title ); ?></td>
						<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->network ) ); ?></td>
						<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->extension ) . '_EXTENSION_NAME' ); ?></td>
						<td align="center" nowrap="nowrap"><?php echo SBHelpersSync::convertDate( $row->created )->format( 'M d, Y H:i', true ); ?></td>
						<td><?php echo $this->escape( $row->message ); ?></td>
					</tr>
					<?php
							$k = 1 - $k;
						}
					endif;
					?>
			</tbody>
		</table>
	<input type="hidden" name="boxchecked" value="0" />
	</form>
	
	<div class="ajax-loader"></div>
    <div class="ajax-overlay"></div>
</div>
