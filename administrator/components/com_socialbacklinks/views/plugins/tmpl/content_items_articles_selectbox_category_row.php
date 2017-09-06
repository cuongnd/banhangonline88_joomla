<?php
/**    
 * SocialBacklinks Plugins view Content Items layout Articles Selectbox Category Row sub layout
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
?>
<tr class="<?php echo "row{$params->k}"; ?>">
	<td class="level-<?php echo $params->level; ?>" rel=" <?php echo $params->item->id; ?> ">
		<?php
			$prefix = '';
			for ( $i = 0; $i < $params->level - 1; $i++ ) {
				$prefix .= '&nbsp;';
			}
			if ( strlen( $prefix ) ) {
			    $prefix = $prefix . '<sup>|_</sup>&nbsp;';
			}
			echo $prefix . $this->escape( JText::_( $params->item->title ) );
		?>
	</td>
</tr>
