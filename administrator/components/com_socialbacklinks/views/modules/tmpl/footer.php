<?php
/**    
 * SocialBacklinks Modules view Footer layout
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
<tfoot>
	<tr>
		<td>
			<span class="hint">
				<a href="http://player.vimeo.com/video/38697785" class="modal" rel="{handler:'iframe',size:{x:800,y:360}}">
					<?php echo JText::_( 'SB_CHOOSE_CONTENT' ) ?>
				</a>
				<br />
				<a href="http://player.vimeo.com/video/38661344" class="modal" rel="{handler:'iframe',size:{x:800,y:400}}">
					<?php echo JText::_( 'SB_CONFIGURE_NETWORKS' ) ?>
				</a>
			</span>
		</td>
		<td align="center">
			<p class="footer-tip">
				
			</p>
		</td>
		<td class="copyright-wrapper">
			<?php echo JText::_( 'Copyright' ) ?>&#160;&#169;
			<?php 
			$start_y = 2012;
			if ( date( 'Y' ) > $start_y )
			{
				echo $start_y . ' - ' . date( 'Y' );
			}
			else {
				echo date( 'Y' );
			} 
			?>
			<a href="http://joomunited.com/?s=sb_footer" target="_blank">
				<?php echo JText::_( 'JoomUnited' ) ?>
			</a>
			<br />
			<?php echo JText::_( 'Version' ) . ' ' . SBHelpersRequirements::getVersion( ) ?>
			<br />
			<?php 
			$license_link_start = '<a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">';
			$license_link_end = '</a>';
			echo JText::sprintf( 'SB_LICENSE_INFO', $license_link_start, $license_link_end ); 
			?>
		</td>
	</tr>
</tfoot>