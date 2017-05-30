<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
	
<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'plugintype'.DS.'snippet'.DS.'_setup.php'; ?>

<form class="fsj" action="<?php echo JRoute::_('index.php?option=com_fsj_main&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'plugintype'.DS.'snippet'.DS.'_form.php'; ?>

	<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'plugintype'.DS.'snippet'.DS.'_footer.php'; ?>

</form>

<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'plugintype'.DS.'snippet'.DS.'_scripts.php'; ?>

