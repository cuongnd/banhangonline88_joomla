<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaauction::completeLink('config'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php
$this->setLayout('main');
echo $this->loadTemplate();
$this->setLayout('languages');
echo $this->loadTemplate();
?>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo HIKAAUCTION_COMPONENT; ?>" />
	<input type="hidden" name="task" id="config_form_task" value="" />
	<input type="hidden" name="ctrl" value="config" />
	<?php echo JHTML::_('form.token'); ?>
</form>
