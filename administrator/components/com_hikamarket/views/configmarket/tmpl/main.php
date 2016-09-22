<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
echo $this->leftmenu(
	'main',
	array(
		'#main_front' => JText::_('MAIN_OPTIONS'),
		'#main_css' => JText::_('CSS'),
		'#main_editor' => JText::_('HIKA_EDITOR'),
		'#main_email' => JText::_('HIKAM_OPTIONS_EMAIL'),
		'#main_statistics' => JText::_('HIKAM_OPTIONS_STATISTICS'),
	)
);
?>
<div id="page-main" class="rightconfig-container <?php if(HIKASHOP_BACK_RESPONSIVE) echo 'rightconfig-container-j30';?>">
	<fieldset id="main_front" class="adminform">
		<legend><?php echo JText::_('MAIN_OPTIONS');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONTEND_EDITION'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[frontend_edition]','',$this->config->get('frontend_edition',1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_SHOW_EDIT_BTN'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[show_edit_btn]','',$this->config->get('show_edit_btn',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_PRODUCT_EDIT_CANCEL_MODE'); ?></td>
	<td><?php
		$values = array(
			JHTML::_('select.option', 'product', JText::_('HIKAM_PRODUCT_EDIT_CANCEL_MODE_PRODUCT')),
			JHTML::_('select.option', 'current_url', JText::_('HIKAM_PRODUCT_EDIT_CANCEL_MODE_CURRENT_URL')),
			JHTML::_('select.option', 'listing', JText::_('HIKAM_PRODUCT_EDIT_CANCEL_MODE_LISTING')),
		);
		echo JHTML::_('select.genericlist', $values, 'config[product_edit_cancel_url]', '', 'value', 'text', $this->config->get('product_edit_cancel_url', 'product'));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_SHOW_CATEGORY_EXPLORER'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[show_category_explorer]','',$this->config->get('show_category_explorer',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DEFAULT_PRODUCT_TEMPLATE'); ?></td>
	<td><?php
		echo $this->nameboxType->display(
			'config[default_template_id]',
			$this->config->get('default_template_id', 0),
			hikamarketNameboxType::NAMEBOX_SINGLE,
			'product_template',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('AVOID_DUPLICATE_PRODUCT_CODE'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[avoid_duplicate_product_code]",'',$this->config->get('avoid_duplicate_product_code', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_PRODUCT_APPROVAL'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[product_approval]', '', $this->config->get('product_approval',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DISPLAY_CART_LINK'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[product_cart_link]','',$this->config->get('product_cart_link',0));
	?></td>
</tr>
</table>
	</fieldset>

	<fieldset id="main_css" class="adminform">
		<legend><?php echo JText::_('CSS');?></legend>
<table class="admintable table" cellspacing="1">
	<tr>
		<td class="key"><?php echo JText::_('CSS_FRONTEND'); ?></td>
		<td><?php echo $this->cssType->display('config[css_frontend]', 'frontend', $this->config->get('css_frontend','default'));?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('STYLES_FOR_FRONTEND'); ?></td>
		<td><?php echo $this->cssType->display('config[css_style]', 'style', $this->config->get('css_style',''));?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('CSS_BACKEND'); ?></td>
		<td><?php echo $this->cssType->display('config[css_backend]', 'backend', $this->config->get('css_backend','default'));?></td>
	</tr>
</table>
	</fieldset>

	<fieldset id="main_editor" class="adminform">
		<legend><?php echo JText::_('HIKA_EDITOR');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_SMALL_EDITOR'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[front_small_editor]','',$this->config->get('front_small_editor',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKA_EDITOR'); ?></td>
	<td><?php
		echo $this->editorType->display('config[editor]', $this->config->get('editor', ''));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKA_EDITOR_DISABLE_BUTTONS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[editor_disable_buttons]','',$this->config->get('editor_disable_buttons',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKA_UPLOADED_FILE_FREE_DOWNLOAD'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[upload_file_free_download]','',$this->config->get('upload_file_free_download',0));
	?></td>
</tr>
<?php
?>
</table>
	</fieldset>

	<fieldset id="main_email" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_EMAIL');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_NOTIFICATION_STATUSES_FILTER'); ?></td>
	<td><?php
		$order_statuses = explode(',', $this->config->get('vendor_email_order_status_notif_statuses', ''));
		if(!empty($order_statuses)) {
			foreach($order_statuses as &$order_status) {
				$order_status = trim($order_status);
			}
			unset($order_status);
		}
		echo $this->nameboxType->display(
			'config[vendor_email_order_status_notif_statuses]',
			$order_statuses,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'order_status',
			array(
				'delete' => true,
				'sort' => false,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
</table>

<table style="margin-top:5px" class="adminlist table table-striped table-hover">
	<thead>
		<tr>
			<th class="title titlenum"><?php echo JText::_( 'HIKA_NUM' );?></th>
			<th class="title"><?php echo JText::_('HIKA_EMAIL'); ?></th>
			<th class="title titletoggle"><?php echo JText::_('HIKA_PUBLISHED'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
if(!empty($this->emails)) {
	$k = 0;
	foreach($this->emails as $i => $email) {
?>
<tr class="row<?php echo $k ;?>">
	<td style="text-align:center"><?php echo $i+1; ?></td>
	<td><?php
		if($this->emailManage) {
			?><a href="<?php echo hikamarket::completeLink('shop.email&task=edit&mail_name='.$email['file']);?>"><?php
		}
		$key = 'MARKET_' . strtoupper($email['name']);
		echo (JText::_($key) == $key) ? $email['name'] : JText::_($key);
		if($this->emailManage) {
			?></a><?php
		}
	?></td>
	<td style="text-align:center"><?php
		$publishedid = 'config_value-shop.'.$email['file'].'.published';
		echo $this->toggleClass->toggle($publishedid, (int)$email['published'], 'config');
	?></td>
</tr>
<?php
		$k = 1 - $k;
	}
}
?>
	</tbody>
</table>
	</fieldset>

	<fieldset id="main_statistics" class="adminform">
		<legend><?php echo JText::_('STATISTICS');?></legend>
<table style="margin-top:5px" class="adminlist table table-striped table-hover admintable">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('HIKA_NAME'); ?></th>
			<th class="title titletoggle"><?php echo JText::_('HIKAM_STAT_PERIOD'); ?></th>
			<th class="title titletoggle"><?php echo JText::_('HIKAM_SLOT'); ?></th>
			<th class="title titletoggle"><?php echo JText::_('HIKA_ORDER'); ?></th>
			<th class="title titletoggle"><?php echo JText::_('HIKA_PUBLISHED'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
if(!empty($this->statistics)) {
	$statisticsSlots = array(
		JHTML::_('select.option', 0, 0),
		JHTML::_('select.option', 1, 1),
	);
	$statisticsPublished = array(
		JHTML::_('select.option', 0, JText::_('JNO')),
		JHTML::_('select.option', 1, JText::_('JYES')),
	);
	$statisticsPeriods = $this->statisticsClass->getDateRangeList();
	foreach($statisticsPeriods as $key => &$period) {
		$txt = '' . $period;
		$period = JHTML::_('select.option', $key, JText::_($txt));
	}
	unset($period);

	$statisticsGroupPeriods = $this->statisticsClass->getDateRangeList(true);
	foreach($statisticsGroupPeriods as $key => &$period) {
		if(strpos($key,'.day') !== false) {
			unset($statisticsGroupPeriods[$key]);
			continue;
		}
		$txt = '' . $period;
		$period = JHTML::_('select.option', $key, JText::_($txt));
	}
	unset($period);

	$k = 0;
	foreach($this->statistics as $key => $statistic) {
?>		<tr class="row<?php echo $k ;?>">
			<td><?php
				if(!empty($statistic['label']))
					echo $statistic['label'] . ' (<em>'.$key.'</em>)';
				else
					echo $key;
			?></td>
			<td><?php
				if(isset($statistic['vars']['DATE_RANGE']) && $statistic['type'] != 'graph') {
					echo JHTML::_('select.genericlist', $statisticsPeriods, 'config[vendor_statistics]['.$key.'][vars][DATE_RANGE]', '', 'value', 'text', $statistic['vars']['DATE_RANGE']);
				} else if(isset($statistic['vars']['DATE_RANGE'])) {
					echo JHTML::_('select.genericlist', $statisticsGroupPeriods, 'config[vendor_statistics]['.$key.'][vars][DATE_RANGE]', '', 'value', 'text', $statistic['vars']['DATE_RANGE']);
				}
			?></td>
			<td><?php echo JHTML::_('select.genericlist', $statisticsSlots, 'config[vendor_statistics]['.$key.'][slot]', '', 'value', 'text', (int)$statistic['slot']); ?></td>
			<td><input type="text" name="config[vendor_statistics][<?php echo $key; ?>][order]" size="5" class="text_area" style="text-align: center" value="<?php echo (int)$statistic['order']; ?>" /></td>
			<td><?php echo JHTML::_('select.genericlist', $statisticsPublished, 'config[vendor_statistics]['.$key.'][published]', '', 'value', 'text', (int)$statistic['published']); ?></td>
		</tr>
<?php
		$k = 1 - $k;
	}
}
?>
	</tbody>
</table>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VALID_ORDER_STATUSES'); ?></td>
	<td><?php
		$order_statuses = explode(',', $this->config->get('stats_valid_order_statuses', 'confirmed,shipped'));
		if(!empty($order_statuses)) {
			foreach($order_statuses as &$order_status) {
				$order_status = trim($order_status);
			}
			unset($order_status);
		}
		echo $this->nameboxType->display(
			'config[stats_valid_order_statuses]',
			$order_statuses,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'order_status',
			array(
				'delete' => true,
				'sort' => false,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DISPLAY_ORDER_STATISTICS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[display_order_statistics]','',$this->config->get('display_order_statistics',0));
	?></td>
</tr>
</table>
	</fieldset>
</div>
