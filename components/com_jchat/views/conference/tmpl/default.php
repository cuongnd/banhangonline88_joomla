<?php
/** 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage views
 * @subpackage form
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');?>
<div class="item-page<?php echo $this->cparams->get('pageclass_sfx', null);?>">
	<?php if ($this->cparams->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h3> <?php echo $this->escape($this->cparams->get('page_heading', $this->menuTitle)); ?> </h3>
		</div>
	<?php endif;?>
</div>

<!-- Private messaging view -->

<div id="jchat_conference_container" dir="ltr">
	<div id="jchat_left_userscolumn">
		<div class="jchat_userslist_myusername"></div>
		<?php if($this->cparams->get('show_search', 1)):?>
			<input id="jchat_leftusers_search" type="text" placeholder="<?php echo JText::_('COM_JCHAT_SEARCH');?>" value="">
		<?php endif; ?>
		
		<?php if($this->cparams->get('conference_maximized', 1)):?>
			<span id="jchat_conference_maximizebutton"></span>
		<?php endif; ?>
		
		<ul id="jchat_conference_userslist"></ul>
	</div>
	
	<div id="jchat_right_videocolumn">
		<div id="jchat_conference_controls"></div>
		<div id="jchat_conference_remotevideos"></div>
	</div>
</div>