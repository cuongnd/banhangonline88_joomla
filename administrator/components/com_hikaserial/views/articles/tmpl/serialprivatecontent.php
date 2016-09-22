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

<div class="well">
	<div class="row">
		<div class="pull-right">
			<button class="btn btn-primary" type="button" onclick="window.hikashop.submitform('serialprivatecontent', 'adminForm');"><?php echo JText::_('HIKA_INSERT'); ?></button>
			<button class="btn" type="button" onclick="window.parent.jModalClose();"><?php echo JText::_('JCANCEL'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo hikaserial::completeLink('articles'); ?>" method="post" name="adminForm" id="adminForm">

<dl class="hika_options large">
	<dt><?php echo JText::_('PACK'); ?> *</dt>
	<dd><?php
		echo $this->nameboxType->display(
			'data[hikaserial][pack]',
			'',
			hikashopNameboxType::NAMEBOX_MULTIPLE,
			'plg.hikaserial.pack',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
	?></dd>

	<dt><?php echo JText::_('DISPLAY_CONSUME_MODULE'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', 'data[hikaserial][module]', '', false);
	?></dd>

	<dt><?php echo JText::_('MESSAGE'); ?></dt>
	<dd>
		<input type="text" name="data[hikaserial][text]" value="" style="width: 90%; margin:0px;"/>
	</dd>

	<dt><?php echo JText::_('PRODUCT'); ?></dt>
	<dd><?php
		echo $this->nameboxType->display(
			'data[hikaserial][product]',
			'',
			hikashopNameboxType::NAMEBOX_SINGLE,
			'product',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
	?></dd>

	<dt><?php echo JText::_('PRIVATE_CONTENT_DELIMITER'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', 'data[hikaserial][delimiter]', '', true);
	?></dd>
</dl>
	<input type="hidden" name="ed_name" value="<?php echo JRequest::getString('ed_name', ''); ?>" />

	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" id="config_form_task" value="" />
	<input type="hidden" name="ctrl" value="articles" />
	<input type="hidden" name="tmpl" value="<?php echo JRequest::getCmd('tmpl'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
