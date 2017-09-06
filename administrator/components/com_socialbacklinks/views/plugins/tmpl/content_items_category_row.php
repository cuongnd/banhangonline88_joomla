<?php
/**    
 * SocialBacklinks Plugins view Content Items layout Category Row sub layout
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

$params = ( object ) $this->params;
$class = ( $params->is_parent ) ? ' parental-row' : '';
?>
<tr class="<?php echo "row{$params->k}" . $class; ?>">
	<td align="center">
		<?php ( in_array( $params->item->id, $params->selected ) ) ? $checked = 'checked="checked"' : $checked = '' ?>
		<input type="checkbox" id="<?php echo $params->item->id ?>" 
			name="categories" value="<?php echo $params->item->id; ?>"
			class="level-<?php echo $params->level ?><?php if ( !is_null( $params->parent ) ) echo " parent-{$params->parent}" ?>" <?php echo $checked ?>
		/>
	</td>
	<td>
		<span>
		<?php
			$prefix = '';
			for ( $i = 0; $i < $params->level - 1; $i++ ) {
				$prefix .= '&nbsp;&nbsp;&nbsp;';
			}
			if ( strlen( $prefix ) ) {
				$prefix = "&nbsp;{$prefix}<sup>|_</sup>&nbsp;";
			}
			echo $prefix . $this->escape( JText::_( $params->item->title ) );
		?>
		</span>
		
		<?php if ( $params->is_parent ) : ?>
			<span class="control-buttons">
				<span class="check-child"><?php echo JText::_( 'SB_CHECK_CHILD' ) ?></span> &nbsp;/&nbsp;
				<span class="uncheck-child"><?php echo JText::_( 'SB_UNCHECK_CHILD' ) ?></span>
			</span>
		<?php endif; ?>
	</td>
	<td align="center">
		<?php echo $params->item->id ?>
	</td>
</tr>
