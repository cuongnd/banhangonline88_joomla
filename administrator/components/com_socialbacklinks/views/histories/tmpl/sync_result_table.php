<?php
/**    
 * Social Backlinks Histories view Table layout
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
?>
<table class="adminlist">
	<thead>
		<tr>
			<th class="first" width="30">
				<?php echo JText::_( 'ID' ) ?>
			</th>
			<th>
				<?php echo JText::_( 'SB_TITLE' ) ?>
			</th>
			<th width="110">
				<?php echo JText::_( 'SB_NETWORK' ) ?>
			</th>
			<th width="75">
				<?php echo JText::_( 'SB_EXTENSION' ) ?>
			</th>
			<th width="80">
				<?php echo JText::_( 'SB_SYNCED' ) ?>
			</th>
			<th class="last" width="60">
				<?php echo JText::_( 'SB_RESULT' ) ?>
			</th>
		</tr>
	</thead>
	
	<tbody>
	<?php if ( empty( $this->rows ) ) : ?>
		<tr>
			<td colspan="6" align="center"><?php echo JText::_( 'SB_NO_SYNC_ITEMS' ) ?></td>
		</tr>
	<?php
	else :
		$rows = $this->rows;
		$k = 0;
		for ( $i = 0, $n = count( $rows ); $i < $n; $i++ )
		{
			$row = &$rows[$i];
			
			if ( $row->result )
			{
				$class = 'success';
				$alt = JText::_( 'SB_SUCCESS' );
			}
			else {
				$class = 'fail';
				$alt = JText::_( 'SB_FAIL' );
			}
	?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
		            <?php echo $row->socialbacklinks_history_id ?>
		        </td>
				<td>
					<?php echo $this->escape( $row->title ); ?>
				</td>
				<td align="center">
					<?php echo JText::_( 'SB_' . strtoupper( $row->network ) ) ?>
				</td>
				<td align="center">
					<?php echo JText::_( 'SB_' . strtoupper( $row->extension ) . '_EXTENSION_NAME' ); ?>
				</td>
				<td align="center" nowrap="nowrap">
					<?php echo SBHelpersSync::convertDate( $row->created )->format( 'M d, Y H:i', true ); ?>
				</td>
				<td align="center">
					<span class="<?php echo $class ?>" title="<?php echo $alt ?>"></span>
				</td>
			</tr>
	<?php
			$k = 1 - $k;
		}
	endif;
	?>
	</tbody>
</table>
