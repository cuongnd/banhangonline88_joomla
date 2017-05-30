<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="fsj" id="fsj_modal_container">
	<div class="modal fsj_modal hide" id="fsj_modal">
	</div>

	<div class="modal fsj_modal hide" id="fsj_modal_base">
	  <div class="modal-header">
		<button class="close" data-dismiss="modal">&times;</button>
		<h3><?php echo JText::_('FSJ_PLEASE_WAIT'); ?></h3>
	  </div>
	  <div class="modal-body">
		<p class="center">
			<img src="<?php echo JURI::root(true); ?>/libraries/fsj_core/assets/images/misc/ajax-loader.gif">	 
		</p>
	  </div>
	  <div class="modal-footer">
		<a href="#" class="btn" onclick="fsj_modal_hide(); return false;"><?php echo JText::_('JCANCEL'); ?></a>
	  </div>
	</div>
</div>
