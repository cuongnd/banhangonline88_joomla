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

$tpl_params 	= JFactory::getApplication()->getTemplate(true)->params;

$products_per_row = $viewData['products_per_row'];
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";
echo shopFunctionsF::renderVmSubLayout('askrecomjs');

$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

foreach ($viewData['products'] as $type => $products ) {
	
	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);
	if(!empty($type) and count($products)>0){ ?>

		<div class="<?php echo $type ?>-view vm-product-listing-view">

			<?php $productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>

		  	<h4><?php echo $productTitle ?></h4>  

	  	<?php } //!empty($type) && count
		  	// Start the Output

			// Calculating Products Per Row
			$cellwidth = ' width'.floor ( 100 / $products_per_row );
			$BrowseTotalProducts = count($products);
			$col = 1;
			$nb = 1;
			$row = 1;

			foreach ( $products as $product ) {

				// Show the horizontal seperator
				if ($col == 1 && $nb > $products_per_row) { ?>
				<div class="horizontal-separator"></div>
				<?php }

				// this is an indicator wether a row needs to be opened or not
				if ($col == 1) { ?>
			<div class="row">
			<?php }

			// Show the vertical seperator
			if ($nb == $products_per_row or $nb % $products_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

	    // Show Products ?>
		<div class="product vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator; ?> sp-vmproduct-wrapper">
			<div class="vm-spacer">
				<div class="vm-product-media-container sp-vmproduct-image">
					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false); ?>
					</a>
					<div class="vmproduct-more-action">
						<ul>
							<li>
								<?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
							</li>
							<li><a href="<?php echo $product->link; ?>"><i class="megadeal-icon-eye"></i></a></li>
						</ul>
					</div> <!-- //vmproduct-more-action -->
				</div> <!-- //sp-vmproduct-image -->

				<div class="vm-product-info-container">

					<h2 class="sp-item-title">
						<?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?>
					</h2>

					<?php if($tpl_params->get('vm_show_rating', false)){ ?>
					<div class="vm-product-rating-container sp-vmproduct-info">
						<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product)); ?>
					</div>
					<?php } // show ratings 
					if ($tpl_params->get('vm_show_stock', false)) { ?>
						<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
					<?php } 
						if($tpl_params->get('vm_show_countdown', false)){
						echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
					} ?>
				

					<?php if($tpl_params->get('vm_show_description', false)){ ?>
						<div class="vm-product-descr-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
							<?php if(!empty($rowsHeight[$row]['product_s_desc'])){
							?>
							<p class="product_s_desc">
								<?php // Product Short Description
								if (!empty($product->product_s_desc)) {
									echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
								<?php } ?>
							</p>
							<?php  } ?>
						</div>
					<?php } ?>

					<?php //echo $rowsHeight[$row]['price'] ?>
					<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?> sp-price-box"> <?php
						echo shopFunctionsF::renderVmSubLayout('listing_prices',array('product'=>$product,'currency'=>$currency)); ?>
						<div class="clear"></div>
					</div>
					<?php //echo $rowsHeight[$row]['customs'] ?>
					<div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>"> <?php
						echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'view'=>'list', 'position' => array('ontop', 'addtocart'))); ?>
					</div>

					<?php if($tpl_params->get('vm_show_details_btn', false)){?>
						<div class="vm-details-button">
							<?php // Product Details Button
							$link = empty($product->link)? $product->canonical:$product->link;
							echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
							?>
						</div>
					<?php } ?>
				</div> <!-- /.vm-product-info-container -->

			</div> <!-- /.spacer -->
		</div> <!-- /.product -->

		<?php
	    $nb ++;	      // Do we need to close the current row now?
	      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
	    <div class="clear"></div>
	  </div>
	      <?php
	      	$col = 1;
			$row++;
	    } else {
	      $col ++;
	    }
	  }

	      if(!empty($type)and count($products)>0){
	        // Do we need a final closing row tag?
	        //if ($col != 1) {
	      ?>
	    <div class="clear"></div>
	  </div>
    <?php
    // }
    }
  } // foreach $viewData['products']
