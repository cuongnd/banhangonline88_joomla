<?php
/**
 * SocialBacklinks Config view Block layout
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
  
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<div id="settings" class="block-wrapper">
	<div class="block-header">
		<div class="block-header-inner">
			<div class="block-header-sub">
				<div class="toggle-button"></div>
				<?php echo JText::_( 'SB_SETTINGS' ); ?>
			</div>
		</div>
	</div>
	<div id="block-config" class="content-block">
		<div class="error-block"></div>
		<div class="block">
			<a class="modal button edit-button" rel="{handler:'iframe',size:{x:800,y:250}}"
			href="<?php echo JRoute::_( 'index.php?option=com_socialbacklinks&view=config&tmpl=component' ) ?>"> <span><?php echo JText::_( 'SB_EDIT' )
				?></span> </a>

			<div class="text-block">
				<?php echo $this->config_status; ?>
			</div>

			<div class="success-block"></div>
		</div>
	</div>
</div>
