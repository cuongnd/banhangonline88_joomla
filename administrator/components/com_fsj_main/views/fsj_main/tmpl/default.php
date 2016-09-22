<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

?>

<div class="fsj">
	

<?php echo JHtml::_('fsjtabs.start', 'com_fsj_main_overview_tabs', array('useCookie'=>1)); ?>

<?php echo JHtml::_('fsjtabs.panel', JText::_('FSJ_M_HEADER'), 'components'); ?>

	<?php echo $this->loadTemplate('components'); ?>
	<?php echo $this->loadTemplate('global'); ?>

<?php  echo JHtml::_('fsjtabs.panel', JText::_('FSJ_MAIN_VERSION_INFO'), 'versions'); ?>

	<?php echo $this->loadTemplate('versions'); ?>
	
<?php /*echo JHtml::_('fsjtabs.panel', JText::_('FSJ_M_ANNOUNCE'), 'announce'); ?>

	<?php echo $this->loadTemplate('announce'); ?>

<?php echo JHtml::_('fsjtabs.panel', JText::_('FSJ_M_UPDATES'), 'updates'); ?>

	<?php echo $this->loadTemplate('updates');*/ ?>

<?php echo JHtml::_('fsjtabs.end'); ?>
</div>