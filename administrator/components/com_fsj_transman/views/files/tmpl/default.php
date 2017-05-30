<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'files'.DS.'snippet'.DS.'_setup.php'; ?>

<form class="fsj" action="<?php echo JRoute::_('index.php?option=com_fsj_transman&view=files');?>" method="post" name="adminForm" id="adminForm">

	<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'files'.DS.'snippet'.DS.'_footer.php'; ?>

	<div id="j-sidebar-container" class="span2">
		<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'files'.DS.'snippet'.DS.'_sidebar.php'; ?>
	</div>

	<div id="j-main-container" class="span10">
		<div class="js-stools clearfix">
			<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'files'.DS.'snippet'.DS.'_tools.php'; ?>
		</div>
	
		<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'files'.DS.'snippet'.DS.'_table.php'; ?>
	</div>

</form>

