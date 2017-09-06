<?php
/**    
 * Social Backlinks Errors view Mail layout
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
<div>
	<?php echo JText::sprintf( 'SB_ERRORS_EMAIL_DEAR', $username ) ?><br />
	<br />
	<?php echo JText::_( 'SB_ERRORS_EMAIL_REPORT' ) ?>
</div>
<br />
<table border="1" style="border-collapse: collapse">
	<thead>
		<tr>
			<th width="80"><?php echo JText::_( 'SB_TIME' ); ?></th>
			<th width="110"><?php echo JText::_( 'SB_NETWORK' ); ?></th>
			<th width="75"><?php echo JText::_( 'SB_EXTENSION' ); ?></th>
			<th width="30"><?php echo JText::_( 'ID' ); ?></th>
			<th style="padding: 5px"><?php echo JText::_( 'SB_TITLE' ); ?></th>
			<th width="200"><?php echo JText::_( 'SB_MESSAGE' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $rows AS $row ) : ?>
			<tr>
				<td align="center" nowrap="nowrap"><?php echo SBHelpersSync::convertDate( $row->created )->format( 'M d, Y H:i', true ); ?></td>
				<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->network ) ); ?></td>
				<td align="center"><?php echo JText::_( 'SB_' . strtoupper( $row->extension ) . '_EXTENSION_NAME' ); ?></td>
				<td align="center"><?php echo $row->item_id; ?></td>
				<td style="padding: 5px"><?php echo htmlentities( $row->title ); ?></td>
				<td style="padding: 5px"><?php echo htmlentities( $row->message ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<br />
<div>
	<?php echo JText::sprintf( 'SB_ERRROS_EMAIL_FIX_ISSUES', $siteadmin_link_start, $siteadmin_link_end ); ?><br />
	<?php echo JText::_( 'SB_ERRORS_EMAIL_DONT_RESPOND' ); ?><br />
	<br />
	--<br />
	<?php echo $sitename; ?><br />
	<?php echo $siteurl; ?>
</div>
