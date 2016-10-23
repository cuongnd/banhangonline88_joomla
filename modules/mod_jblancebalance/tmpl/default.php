<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	04 March 2013
 * @file name	:	modules/mod_jblancebalance/tmpl/default.php
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

 $user = JFactory::getUser();
 $hasJBProfile = JblanceHelper::hasJBProfile($user->id);
?>
<?php if($hasJBProfile) : ?>
<ul class="inline">
	<li><?php echo JText::_('MOD_JBLANCE_CURRENT_BALANCE'); ?>:</li>
	<li><strong><?php echo JblanceHelper::formatCurrency($total_fund); ?></strong></li>
</ul>
<?php endif; ?>