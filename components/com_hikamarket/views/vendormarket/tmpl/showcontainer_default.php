<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikamarket_vendor_page" class="hikamarket_vendor_page">
	<div id="hikamarket_vendor_top" class="hikamarket_vendor_top">
		<h1><?php echo $this->vendor->vendor_name; ?></h1>
		<div class="hikamarket_vendor_image">
			<img src="<?php echo $this->vendor_image->url; ?>" alt=""/>
		</div>
<?php
$pluginsClass = hikamarket::get('shop.class.plugins');
$plugin = $pluginsClass->getByName('system', 'hikashopsocial');
if(!empty($plugin) && (!empty($plugin->published) || !empty($plugin->enabled)))
	echo '<div class="hikamarket_vendor_social">{hikashop_social}</div>';
?>
		<div class="hikamarket_vendor_vote">
<?php
	if($this->config->get('display_vendor_vote',0)) {
		$js = '';
		echo hikamarket::getLayout('shop.vote', 'mini', $this->voteParams, $js);
	}
?>
		</div>
		<div class="hikamarket_vendor_fields">
<?php
	if(!empty($this->extraFields['vendor'])) {
?>
			<table class="table table-striped">
<?php
		foreach($this->extraFields['vendor'] as $fieldName => $oneExtraField) {
?>
				<tr class="hikamarket_vendor_custom_<?php echo $oneExtraField->field_namekey;?>_line">
					<td class="key">
						<span id="hikamarket_vendor_custom_name_<?php echo $oneExtraField->field_id;?>" class="hikamarket_vendor_custom_name"><?php
							echo $this->fieldsClass->getFieldName($oneExtraField);
						?></span>
					</td>
					<td>
						<span id="hikamarket_vendor_custom_value_<?php echo $oneExtraField->field_id;?>" class="hikamarket_vendor_custom_value"><?php
							echo $this->fieldsClass->show($oneExtraField, $this->vendor->$fieldName);
						?></span>
					</td>
				</tr>
<?php
		}
?>
			</table>
<?php
	}
?>
		</div>
		<div id="hikamarket_vendor_description" class="hikamarket_vendor_description"><?php
			if($this->config->get('vendor_description_content_plugins', 0))
				echo $this->secure($this->vendor->vendor_description);
			else
				echo JHTML::_('content.prepare', $this->vendor->vendor_description);
		?></div>
	</div>
<?php if($this->config->get('display_vendor_vote',0)) { ?>
	<div id="hikashop_comment_form" class="hikamarket_vendor_vote">
<?php
	$js = '';
	echo hikamarket::getLayout('shop.vote', 'listing', $this->voteParams, $js);
	echo hikamarket::getLayout('shop.vote', 'form', $this->voteParams, $js);
?>
	</div>
<?php }
	if($this->config->get('display_vendor_contact', 0)) {
		echo $this->popup->display(
			'<span>'.JText::_('CONTACT_VENDOR').'</span>',
			'CONTACT_VENDOR',
			hikamarket::completeLink('shop.product&task=contact&target=vendor&vendor_id='.$this->vendor->vendor_id, true),
			'hikamarket_contactvendor_popup',
			array(
				'width' => 750,
				'height' => 460,
				'attr' => 'class="hikashop_cart_button btn btn-small"',
				'type' => 'link'
			)
		);
	}
?>
	<div style="clear:both"></div>
	<div class="hikamarket_submodules" id="hikamarket_submodules" style="clear:both">
<?php
if(!empty($this->modules)) {
	JRequest::setVar('force_using_filters', 1);
	foreach($this->modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
}
?>
	</div>
</div>
