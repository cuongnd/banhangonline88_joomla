<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php if( $this->step - 1 > 0 ){ ?>
<script type="text/javascript">
function previousPage()
{
	window.location.href 	= 'index.php?option=com_easyblog&view=autoposting&layout=twitter&step=<?php echo $this->step - 1;?>';
}
</script>
<?php } ?>
<div class="autoposting-steps">
<div class="pa-15">
	<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/twitter_setup.png" style="float:left;margin-right:20px;" />
	<h3 class="head-3"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER');?> - <?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_STEP_' . $this->step ); ?></h3>
	<div class="clear"></div>
	<?php echo $this->loadTemplate( 'step' . $this->step ); ?>
</div>
</div>
