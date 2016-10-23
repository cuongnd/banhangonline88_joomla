<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	29 March 2012
 * @file name	:	modules/mod_jblancesearch/tmpl/default.php
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
 $document->addStyleSheet("modules/mod_jblancesearch/css/style.css"); 
 
 $currencysym = $config->currencySymbol;

 $set_Itemid	= intval($params->get('set_itemid', 0));
 $Itemid = ($set_Itemid > 0) ? '&Itemid='.$set_Itemid : '';

 $sh_category 	= $params->get('category', 1);
 $sh_status 	= $params->get('status', 1);
 $sh_budget 	= $params->get('budget', 1);
 
 //check of all other three fields are hidden
 $isOnlyKeywords = false;
 if($sh_category == 0 && $sh_status == 0 && $sh_budget == 0)
 	$isOnlyKeywords = true;
?>
<?php if($isOnlyKeywords == false) : ?>
<form action="index.php" method="get" name="userForm" >
	<div class="control-group">
		<label class="control-label" for="keyword"><?php echo JText::_('MOD_JBLANCE_ENTER_KEYWORD'); ?>: </label>
		<div class="controls">
			<input type="text" class="span2" name="keyword" id="keyword" />
		</div>
	</div>
	
	<?php if($sh_status == 1){ ?>
	<div class="control-group">
		<label class="control-label" for="status"><?php echo JText::_('MOD_JBLANCE_STATUS'); ?>: </label>
		<div class="controls">
			<?php $list_categ = ModJblanceSearchHelper::getSelectProjectStatus();	   					   		
			echo $list_categ; ?>
		</div>
	</div>
	<?php } ?>
	
	<?php if($sh_category == 1){ ?>
	<div class="control-group">
		<label class="control-label" for="id_categ"><?php echo JText::_('MOD_JBLANCE_CATEGORY'); ?>: </label>
		<div class="controls">
			<?php $list_categ = ModJblanceSearchHelper::getListJobCateg();	   					   		
			echo $list_categ; ?>
		</div>
	</div>
	<?php } ?>
	
	<?php if($sh_budget == 1){ ?>
	<div class="control-group">
		<label class="control-label" for="min_bud"><?php echo JText::_('MOD_JBLANCE_MIN_BUDGET'); ?>: </label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><?php echo $currencysym; ?></span>
				<input type="text" class="input-small" name="min_bud" />
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="max_bud"><?php echo JText::_('MOD_JBLANCE_MAX_BUDGET'); ?>: </label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><?php echo $currencysym; ?></span>
				<input type="text" class="input-small" name="max_bud" />
			</div>
		</div>
	</div>
	<?php } ?>
	<hr>
	<div class="text-center">
		<input type="submit" class="btn btn-primary" value="<?php echo JText::_('MOD_JBLANCE_SEARCH'); ?>" />
	</div>
	
	<input type="hidden" name="option" value="com_jblance"/>
	<input type="hidden" name="view" value="project"/>
	<input type="hidden" name="layout" value="searchproject"/>
	<input type="hidden" name="Itemid" value="<?php echo $set_Itemid; ?>"/>
</form>
<?php else : ?>
<form class="text-center">
	<div class="input-append">
		<input type="text" class="span6" name="keyword" id="keyword" placeholder="<?php echo JText::_('MOD_JBLANCE_SEARCH_KEYWORD_TIPS'); ?>" />
		<button type="submit" class="btn btn-primary"><?php echo JText::_('MOD_JBLANCE_SEARCH'); ?></button>
	</div>
	
	<input type="hidden" name="option" value="com_jblance"/>
	<input type="hidden" name="view" value="project"/>
	<input type="hidden" name="layout" value="searchproject"/>
	<input type="hidden" name="Itemid" value="<?php echo $set_Itemid; ?>"/>
</form>
<?php endif; ?>