<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<script type="text/javascript">
	window.hikashop.ready(function(){window.hikashop.dlTitle('adminForm');});
</script>
<form action="<?php echo hikaserial::completeLink('config'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="hikashop_backend_tile_edition">
<?php
$this->setLayout('main');
echo $this->loadTemplate();

$this->setLayout('languages');
echo $this->loadTemplate();
?>
</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" id="config_form_task" value="" />
	<input type="hidden" name="ctrl" value="config" />
	<?php echo JHTML::_('form.token'); ?>
</form>
