<?php
/**    
 * SocialBacklinks Plugins view Content Items layout Categories sub layout
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

$tree = ( array ) $this->tree;
?>
<table class="adminlist">
<thead>
	<tr>
		<th class="first" width="20">&nbsp;</th>
		<th>
			<?php echo JText::_( 'SB_TITLE' ) ?>
		</th>
		<th class="last" width="10">
			<?php echo JText::_( 'ID' ) ?>
		</th>
	</tr>
</thead>
<tbody>
	<?php if ( empty( $tree ) ) { ?>
		<tr>
			<td colspan="3" align="center"><?php echo JText::_( 'SB_NO_ITEMS' ); ?></td>
		</tr>
	<?php
	}
	else {
		$k = 1;
		foreach ( $tree as $id => $value ) {
			$item_params = array( 'parent' => null, 'item' => $value, 'selected' => ( array ) $this->selected_categories );
			$this->_renderCategory( $item_params, 'category_row', $this->use_plugin_category_row_tmpl, 1, $k );
		}
	}
	?>
</tbody>
</table>	
