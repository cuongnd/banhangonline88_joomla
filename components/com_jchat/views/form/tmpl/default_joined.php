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
<style type="text/css">
	div.jchat_joined_chat {
		height: 50px;
		width: 220px;
		font-size: 24px;
		position: relative;
		background-color: #67AE62;
		color: #FFF;
		border-radius: 6px;
		padding: 6px;
		float: left;
		text-align: center;
		line-height: 45px;
	}
</style>
<div class="item-page<?php echo $this->cparams->get('pageclass_sfx', null);?>">
	<?php if ($this->cparams->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h3> <?php echo $this->escape($this->cparams->get('page_heading', $this->menuTitle)); ?> </h3>
		</div>
	<?php endif;?>
</div>
	
<div class="jchat_joined_chat"><?php echo JText::_('COM_JCHAT_FORM_JOINED');?></div>