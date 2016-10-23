<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	25 June 2012
 * @file name	:	modules/mod_jblancestats/tmpl/default.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
 // no direct access
 defined('_JEXEC') or die('Restricted access');
 
 $document = JFactory::getDocument();
 $direction = $document->getDirection();
 $config = JblanceHelper::getConfig();
 
 if($config->loadBootstrap){
 	JHtml::_('bootstrap.loadCss', true, $direction);
 }

 $document->addStyleSheet("modules/mod_jblancestats/css/style.css");
 $document->addStyleSheet("components/com_jblance/css/style.css");
 
 $sh_users 		= $params->get('total_users', 1);
 $sh_active 	= $params->get('active_projects', 1);
 $sh_total 		= $params->get('total_projects', 1);
 $display_type 	= $params->get('display_type', 'vertical');
?>
<?php if($display_type == 'vertical') : ?>
<div class="form-horizontal">
	<?php if($sh_users) : ?>
	<div class="control-group">
		<label class="control-label nopadding"><?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_USERS'); ?>: </label>
		<div class="controls">
			<?php echo $total_users; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if($sh_active) : ?>
	<div class="control-group">
		<label class="control-label nopadding"><?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_OPEN_PROJECTS'); ?>: </label>
		<div class="controls">
			<?php echo $active_projects; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if($sh_total) : ?>
	<div class="control-group">
		<label class="control-label nopadding"><?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_PROJECTS'); ?>: </label>
		<div class="controls">
			<?php echo $total_projects; ?>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php elseif($display_type == 'horizontal') : ?>
<ul class="inline row-fluid statistics">
	<li class="span4">
		<span class="statcount"><?php echo $total_users; ?></span> <?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_USERS'); ?>
	</li>
	<li class="span4">
		<span class="statcount"><?php echo $active_projects; ?></span> <?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_OPEN_PROJECTS'); ?>
	</li>
	<li class="span4">
		<span class="statcount"><?php echo $total_projects; ?></span> <?php echo JText::_('MOD_JBLANCE_LABEL_TOTAL_PROJECTS'); ?>
	</li>
</ul>
<?php endif; ?>