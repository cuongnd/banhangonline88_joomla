<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>						<tr id="hikashop_min_order">
							<td class="key">
									<?php echo JText::_( 'MINIMUM_ORDER_VALUE' ); ?>
							</td>
							<td>
								<input type="text" name="data[discount][discount_minimum_order]" value="<?php echo @$this->element->discount_minimum_order; ?>" />
							</td>
						</tr>
						<tr id="hikashop_min_products">
							<td class="key">
									<?php echo JText::_( 'MINIMUM_NUMBER_OF_PRODUCTS' ); ?>
							</td>
							<td>
								<input type="text" name="data[discount][discount_minimum_products]" value="<?php echo (int)@$this->element->discount_minimum_products; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
									<?php echo JText::_( 'DISCOUNT_QUOTA' ); ?>
							</td>
							<td>
								<input type="text" name="data[discount][discount_quota]" value="<?php echo @$this->element->discount_quota; ?>" />
							</td>
						</tr>
						<tr id="hikashop_quota_per_user">
							<td class="key">
									<?php echo JText::_( 'DISCOUNT_QUOTA_PER_USER' ); ?>
							</td>
							<td>
								<input type="text" name="data[discount][discount_quota_per_user]" value="<?php echo @$this->element->discount_quota_per_user; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
									<?php echo JText::_( 'PRODUCT' ); ?>
							</td>
							<td><?php
		echo $this->nameboxType->display(
			'data[discount][discount_product_id]',
			explode(',', trim(@$this->element->discount_product_id, ',')),
			hikashopNameboxType::NAMEBOX_MULTIPLE,
			'product',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
							?></td>
						</tr>
						<tr>
							<td class="key">
									<?php echo JText::_( 'CATEGORY' ); ?>
							</td>
							<td><?php
		echo $this->nameboxType->display(
			'data[discount][discount_category_id]',
			explode(',', trim(@$this->element->discount_category_id, ',')),
			hikashopNameboxType::NAMEBOX_MULTIPLE,
			'category',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
							?></td>
						</tr>
						<tr>
							<td class="key">
									<?php echo JText::_( 'INCLUDING_SUB_CATEGORIES' ); ?>
							</td>
							<td>
								<?php echo JHTML::_('hikaselect.booleanlist', "data[discount][discount_category_childs]" , '',@$this->element->discount_category_childs	); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
									<?php echo JText::_( 'ZONE' ); ?>
							</td>
							<td><?php
		echo $this->nameboxType->display(
			'data[discount][discount_zone_id]',
			explode(',', trim(@$this->element->discount_zone_id, ',')),
			hikashopNameboxType::NAMEBOX_MULTIPLE,
			'zone',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
							?></td>
						</tr>
						<tr id="hikashop_auto_load">
							<td class="key">
									<?php echo JText::_( 'COUPON_AUTO_LOAD' ); ?>
							</td>
							<td>
								<?php echo JHTML::_('hikaselect.booleanlist', "data[discount][discount_auto_load]" , '',@$this->element->discount_auto_load	); ?>
							</td>
						</tr>
<?php



?>
						<tr id="hikashop_discount_coupon_product_only">
							<td class="key">
									<?php echo JText::_('COUPON_APPLIES_TO_PRODUCT_ONLY'); ?>
							</td>
							<td>
								<?php echo JHTML::_('hikaselect.booleanlist', "data[discount][discount_coupon_product_only]" , '',@$this->element->discount_coupon_product_only	); ?>
							</td>
						</tr>
						<?php
						?>
						<tr id="hikashop_discount_coupon_nodoubling">
							<td class="key">
									<?php echo JText::_('COUPON_HANDLING_OF_DISCOUNTED_PRODUCTS'); ?>
							</td>
							<td>
								<?php

									$options = array();
									$options[] = JHTML::_('select.option', 0, JText::_('STANDARD_BEHAVIOR'));
									$options[] = JHTML::_('select.option', 1, JText::_('IGNORE_DISCOUNTED_PRODUCTS'));
									$options[] = JHTML::_('select.option', 2, JText::_('OVERRIDE_DISCOUNTED_PRODUCTS'));
									echo JHTML::_('hikaselect.genericlist', $options, "data[discount][discount_coupon_nodoubling]" , '', 'value', 'text', @$this->element->discount_coupon_nodoubling );
								?>
							</td>
						</tr>
<?php




	if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multisites'.DS.'helpers'.DS.'utils.php')) {
		$db = JFactory::getDBO();
		$db->setQuery('SHOW CREATE table ' . $db->quoteName( hikashop_table('discount')));
		$discount_descr = $db->loadObject();
		if( !empty( $discount_descr->View)) {
			if ( empty( $this->element->discount_site_id) || $this->element->discount_site_id == '[unselected]') {
				$this->element->discount_site_id = defined( 'MULTISITES_ID') ? MULTISITES_ID : null;
?>
						<tr style="display:none">
								<td colspan="2">
									<input type="hidden" name="data[discount][discount_site_id]" value="<?php echo @$this->element->discount_site_id; ?>" />
								</td>
						</tr>
<?php
				}
		}
		else {
			include_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_multisites'.DS.'helpers'.DS.'utils.php');
			if ( class_exists( 'MultisitesHelperUtils') && method_exists( 'MultisitesHelperUtils', 'getComboSiteIDs')) {
				$comboSiteIDs = MultisitesHelperUtils::getComboSiteIDs( @$this->element->discount_site_id, 'data[discount][discount_site_id]', JText::_( 'SELECT_A_SITE'));
				if( !empty( $comboSiteIDs)){
?>
						<tr>
							<td class="key">
								 <?php echo JText::_( 'SITE_ID' ); ?>
							</td>
							<td>
								<?php echo $comboSiteIDs; ?>
							</td>
						</tr>
<?php
				}
			}
		}
	}

JPluginHelper::importPlugin('hikashop');
$dispatcher = JDispatcher::getInstance();
$html = array();
$dispatcher->trigger('onDiscountBlocksDisplay', array(&$this->element, &$html));
if(!empty($html)) {
	echo implode("\r\n", $html);
}
?>
						<tr>
							<td colspan="2">
								<fieldset class="adminform">
									<legend><?php echo JText::_('ACCESS_LEVEL'); ?></legend>
									<?php
									if(hikashop_level(2)){
										$acltype = hikashop_get('type.acl');
										echo $acltype->display('discount_access',@$this->element->discount_access);
									}else{
										echo hikashop_getUpgradeLink('business');
									} ?>
								</fieldset>
							</td>
						</tr>
