<?php
/**
 * SocialBacklinks Plugins view Content Items layout Articles Selectbox sub layout
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
<div class="selectbox-wrapper closed">
	<div class="selectbox hidden">
		<div class="categories-wrapper">
			<table class="categories">
				<tbody>
					<?php
					$this->tree = (array) $this->tree;
					if ( !empty( $this->tree ) ) {
						$k = 1;
						foreach ($this->tree as $id => $value) {
							$item_params = array( 'parent' => null, 'item' => $value, 'selected' => array( ) );
							$this->_renderCategory( $item_params, 'articles_selectbox_category_row', false, 1, $k );
						}
					}
					else {
					?>
					<tr><td>&nbsp;</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="articles-wrapper">
			<div class="search-wrapper">
				<input type="text" value="Begin typing here.." name="item" class="filter">
				<div class="save-button button">
					<span><?php echo JText::_('Add'); ?></span>
				</div>
			</div>
			<table>
				<thead>
					<tr>
						<th></th>
						<th class="title"> <?php echo JText::_( 'SB_TITLE' ); ?> </th>
						<th class="idTitle"> <?php echo JText::_( 'ID' ); ?> </th>
					</tr>
				</thead>
				<tbody>
					<tr class="hidden">
						<td><input type="checkbox" value="" class="id" /></td>
						<td class="title"> <?php echo JText::_( 'SB_TITLE' ); ?> </td>
						<td class="idTitle"> <?php echo JText::_( 'ID' ); ?> </td>
					</tr>
					<tr class="info">
						<td colspan="3"> <?php echo JText::_( 'SB_CHOOSE_CATEGORY' ); ?> </td>
					</tr>
					<tr class="empty hidden">
						<td colspan="3"> <?php echo JText::_( 'SB_NO_ITEMS' ); ?> </td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="clr"></div>
	</div>
</div>
