<?php
/**
 * Social Backlinks Dashboard view Histories layout
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

jimport( 'joomla.html.pagination' );
JHtml::_( 'behavior.framework', true );

$pagination = new JPagination( @$this->lists['total'], @$this->lists['limitstart'], @$this->lists['limit'] );

$doc = JFactory::getDocument( );
$doc->addStylesheet( JURI::root( true ) . '/media/com_socialbacklinks/css/' . SBHelpersEnv::getMediaFile( 'socialbacklinks.css' ) );
?>
<form action="index.php" method="post" name="adminForm" class="block-wrapper popup">
	<div class="header-wrapper">
		<div class="header-text">
			<?php echo JText::_( 'SB_HISTORY_TITLE' )
			?>
		</div>
	</div>
	<table class="adminlist">
		<thead>
			<tr>
				<th class="first" width="15"><?php echo JText::_( 'SB_NUM' ); 
				?></th>
				<th width="30"><?php echo JHTML::_( 'grid.sort', 'ID', 'socialbacklinks_history_id', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
				<th><?php echo JHTML::_( 'grid.sort', 'SB_TITLE', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
				<th width="120"><?php echo JHTML::_( 'grid.sort', 'SB_NETWORK', 'network', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
				<th width="75"><?php echo JHTML::_( 'grid.sort', 'SB_EXTENSION', 'extension', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
				<th width="80"><?php echo JHTML::_( 'grid.sort', 'SB_SYNCED', 'created', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
				<th class="last" width="70"><?php echo JHTML::_( 'grid.sort', 'SB_RESULT', 'result', @$this->lists['order_Dir'], @$this->lists['order'] ); 
				?></th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="5"><?php echo $pagination->getListFooter( ); ?></td>
				<td colspan="2" align="center"><?php echo $pagination->getResultsCounter( ); ?></td>
			</tr>
		</tfoot>

		<tbody>
			<?php if ( empty( $this->rows ) ) :
			?>
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
				<td align="center"><?php echo $pagination->getRowOffset( $i ); ?></td>
				<td align="center"><?php echo $row->socialbacklinks_history_id; ?></td>
				<td><?php echo $this->escape( $row->title ); ?></td>
				<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->network ) ); ?></td>
				<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->extension ) . '_EXTENSION_NAME' ); ?></td>
				<td align="center" nowrap="nowrap"><?php echo SBHelpersSync::convertDate( $row->created )->format( 'M d, Y H:i', true ); ?></td>
				<td align="center" class="jgrid"><span class="<?php echo $class ?>" title="<?php echo $alt ?>"></span></td>
			</tr>
			<?php
			$k = 1 - $k;
			}
			endif;
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="view" value="histories" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
