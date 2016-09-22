<?php
/**
* sublayout products
*
* @package	VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle'])? $viewData['customTitle']: false;
if(isset($viewData['class'])){
	$class = $viewData['class'];
} else {
	$class = 'product-fields';
}


if (!empty($product->customfieldsSorted[$position])) {?>

	<?php if ($position =='related_products' || $position =='related_categories'){ ?>
		<div class="productdetails-view c">
	<?php } ?>

	<div class="<?php echo $class?>">
		<?php
		if($customTitle and isset($product->customfieldsSorted[$position][0])){
			$field = $product->customfieldsSorted[$position][0]; ?>
		<div class="product-fields-title-wrapper"><span class="product-fields-title"><?php echo vmText::_ ($field->custom_title) ?></span>
			<?php if ($field->custom_tip) {
				echo JHtml::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
			} ?>
		</div> 
		<?php } ?>

		

			<?php if(isset($product->customfieldsSorted[$position][0])){
				$custom_title = null; 
				$item_info = $product->customfieldsSorted[$position][0];
			?>

				<?php if (!$customTitle and $item_info->custom_title != $custom_title and $item_info->show_title) { ?>
					<div class="product-fields-title-wrapper">
						<span class="product-fields-title">
							<?php echo vmText::_ ($item_info->custom_title) ?>
						</span>
						<?php if ($item_info->custom_tip) {
							echo JHtml::tooltip (vmText::_($item_info->custom_tip), vmText::_ ($item_info->custom_title), 'tooltip.png');
						} ?></div>
				<?php } ?>

			<?php } // isset item ?>

			<div class="row">
			<?php foreach ($product->customfieldsSorted[$position] as $field) {
				if ( $field->is_hidden || empty($field->display)) continue; //OSP http://forum.virtuemart.net/index.php?topic=99320.0 ?>

				<div class="product-field col-sm-4 vm-product-field-type-<?php echo $field->field_type ?>">
					<?php if (!empty($field->display)){ ?>						
						<div class="product-field-display"><?php echo $field->display; ?></div>
					<?php
					} ?>
				</div>
			<?php
				$custom_title = $field->custom_title;
			} ?>
		</div> <!-- /.row -->
      <div class="clear"></div>
	</div>

	<?php if ($position =='related_products' || $position =='related_categories'){ ?>
		</div>
	<?php } ?>

<?php
} ?>