<?php
/**
 * SocialBacklinks Plugins view Content Items layout Articles sub layout
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

$rows = $this->items;
?>
<div class="select-wrapper">
	<div class="controls-wrapper">
		<div class="select-categories hidden"></div>
	
		<a title="<?php echo JText::_( 'SB_ADD_ARTICLE' ) ?>" class="button select-button select-article" href="#">
			<span><?php echo JText::_( 'SB_ADD_ARTICLE' ); ?></span>
		</a>
		<span class="text"><?php echo JText::_( 'SB_ADD_ARTICLE_INFO' ); ?></span>
		<a href="#" target="_blank" class="info-link"> <span class="info"></span> </a>
	</div>
	<div class="clr"></div>
	
	<?php echo $this->loadTemplate( 'articles_selectbox' ); ?>
</div>

<div class="error-block"></div>

<table class="adminlist">
	<thead>
		<tr>
			<?php foreach ( $this->shown_fields as $field ) : ?>
				<th <?php if ( isset( $field['class'] ) ) echo "class=\"$field[class]\"" ?>
					<?php if ( isset( $field['width'] ) ) echo "width=\"$field[width]\"" ?>
				>
					<?php echo JText::_( $field['title'] ); ?>
				</th>
			<?php endforeach; ?>
			<th class="last" width="30">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( empty( $rows ) ) : ?>
		<tr>
			<td colspan="7" align="center"><?php echo JText::_( 'SB_NO_ITEMS' ); ?></td>
		</tr>
		<?php else : ?>
		<?php
			$k = 0;
			for ( $i = 0, $n = count( $rows ); $i < $n; $i++ ) :
				$row = $rows[$i];
		?>
				<tr id="row-<?php echo $row->id ?>" class="<?php echo "row$k"; ?>">
					<?php foreach ( $this->shown_fields as $field ) : ?>
						<td<?php if ($field['field']=='id') echo ' class="row-id"'?>><?php
							if ( strtolower( $field['title'] ) != 'date' ) {
								echo $this->escape( $row->{$field['field']} );
							}
							else {
								echo JHTML::_( 'date', $row->{$field['field']}, JText::_( 'DATE_FORMAT_LC4' ) );
							}
						?></td>
					<?php endforeach; ?>
					<td align="center"><div class="delete-row-button"></div></td>
				</tr>
		<?php
				$k = 1 - $k;
			endfor;
		?>
		<?php endif; ?>
	</tbody>
</table>
