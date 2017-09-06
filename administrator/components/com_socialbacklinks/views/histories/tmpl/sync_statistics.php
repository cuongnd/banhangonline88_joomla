<?php
/**	
 * SocialBacklinks Histories view Sync Statistics layout
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
<?php if ( empty( $this->statistics ) ) : ?>
	<div class="block">
		<div class="text-block">
			<?php echo JText::_( 'SB_NO_STATISTICS_ITEMS' ) ?>
		</div>
	</div>
<?php else : ?>
<ul class="statistic-list">
	<?php
		if ( !empty( $this->statistics['1'] ) ) :
			foreach ( $this->statistics['1'] AS $extension => $networks ) :
				foreach ( $networks AS $network => $count ) :
	?>
	<li class="success">
		<?php echo JText::plural( 'SB_N_' .strtoupper( $extension ). '_POST_SUCCESS', $count, JText::_( 'SB_' . strtoupper( $network ) ) ); ?>
	</li>
	<?php
				endforeach;
			endforeach;
		endif;
	?>
	
	<?php
		if ( !empty( $this->statistics['0'] ) ) :
			foreach ( $this->statistics['0'] AS $extension => $networks ) :
				foreach ( $networks AS $network => $count ) :
	?>
	<li class="error">
		<?php echo JText::plural( 'SB_N_' .strtoupper( $extension ). '_POST_ERROR', $count, JText::_( 'SB_' . strtoupper( $network ) ) ); ?>
	</li>
	<?php
				endforeach;
			endforeach;
		endif;
	?>
</ul>
<?php endif; ?>
