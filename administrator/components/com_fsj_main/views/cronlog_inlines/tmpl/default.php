<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<div class="fsj fsj_inline">

	<?php 
		$bar = JToolBar::getInstance('toolbar');
		echo $bar->render(); 
	?>
			
	<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inlines'.DS.'snippet'.DS.'_setup.php'; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_fsj_main&view=cronlog_inlines');?>" method="post" name="adminForm" id="adminForm">

		<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inlines'.DS.'snippet'.DS.'_footer.php'; ?>

		<div id="j-main-container" class="span12">
			<div class="js-stools clearfix">
				<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inlines'.DS.'snippet'.DS.'_tools.php'; ?>
			</div>
	
			<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inlines'.DS.'snippet'.DS.'_table.php'; ?>
		</div>

	</form>

	
</div>