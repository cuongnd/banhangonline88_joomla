<?php
/**    
 * SocialBacklinks Modules view Header layout
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
<thead>
	<tr>
		<th class="header-l">
			<div class="joo-logo">
				<div class="icon"></div>
				<p class="sub-title"><?php echo JText::_( 'SB_JOOMLA_CONTENT' ) ?></p>
			</div>
		</th>
		<th class="header-c" id="synchronization">
			<?php if ( !empty( $this->with_sync ) ) : ?>
			<div class="sync-bt-wrapper">
				<a href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=sync&task=synchronize&loading=1&tmpl=component&progress=1' ) ?>"
						class="sync-bt modal" rel="{handler:'iframe',size:{x:800,y:600}}">
					<?php echo JText::_( 'SB_SYNC_NOW' ) ?>
				</a>
			</div>
			<?php endif; ?>
		</th>
		<th class="header-r">
			<div class="social-logo">
				<div class="icon"></div>
				<p class="sub-title"><?php echo JText::_( 'SB_ON_SOCIAL_NETWORKS' ) ?></p>
			</div>
		</th>
	</tr>
</thead>
