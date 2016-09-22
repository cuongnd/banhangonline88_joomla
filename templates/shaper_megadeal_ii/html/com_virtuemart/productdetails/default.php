<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 9058 2015-11-10 18:30:54Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$tpl_params 	= JFactory::getApplication()->getTemplate(true)->params;

$this->product->availability_short =  ($this->product->product_in_stock > 0) ? 'in-stock': 'out-of-stock';

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));


if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>

<div class="productdetails-view productdetails">

	<?php if($tpl_params->get('vm_show_back_print', false)){ ?>
		<?php // Back To Category Button
		if ($this->product->virtuemart_category_id) {
			$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
			$categoryName = vmText::_($this->product->category_name) ;
		} else {
			$catURL =  JRoute::_('index.php?option=com_virtuemart');
			$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
		}
		?>
		<div class="back-to-category">
	    	<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
		</div>
	<?php } ?>

    <?php // afterDisplayTitle Event
	    echo $this->product->event->afterDisplayTitle;
	    // Product Edit Link
	    echo $this->edit_link;
    	// Product Edit Link END
    ?>

	<?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
        <div class="icons">
	    <?php

		    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

			echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
		    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
			echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
			$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
		    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>

    <div class="row">
	    <div class="vm-product-container col-sm-6">

			<div class="vm-product-media-container">
				<?php echo $this->loadTemplate('images'); ?>
				<div class="clear"></div>
				<?php $count_images = count ($this->product->images);
					if ($count_images > 1) {
						echo $this->loadTemplate('images_additional');
					} 
				?>
		    </div>	

		</div>

		<div class="vm-product-details-container col-sm-6">
			<!-- Product Title -->
			<h1 itemprop="name"><?php echo $this->product->product_name ?></h1>
			<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product)); ?>

			<p class="vm-product-availability <?php echo $this->product->availability_short; ?>">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABILITY'); ?>: 
				<span class="vm-product-avalability-text">
					<?php if($this->product->product_in_stock > 0){ ?>
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_STOCK'); ?>
						<i class="fa fa-check"></i>
					<?php } else{ ?>
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_OUT_OF_STOCK'); ?>
						<i class="fa fa-times"></i>
					<?php } ?>
				</span>
			</p>
				

			<?php // Product Short Description
			if (!empty($this->product->product_s_desc) && $tpl_params->get('vm_show_product_short_desc', false)) { ?>
				<div class="product-short-description">
					<?php
					/** @todo Test if content plugins modify the product description */
					echo nl2br($this->product->product_s_desc);
					?>
				</div>
			<?php
	    } // Product Short Description END

	    	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
	    ?>

		    <div class="spacer-buy-area">

			<?php
			if (is_array($this->productDisplayShipments)) {
			    foreach ($this->productDisplayShipments as $productDisplayShipment) {
				echo $productDisplayShipment . '<br />';
			    }
			}
			if (is_array($this->productDisplayPayments)) {
			    foreach ($this->productDisplayPayments as $productDisplayPayment) {
				echo $productDisplayPayment . '<br />';
			    }
			}
			?>

			<?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency)); ?>


			<div class="vm-product-countdown-wrapper">
				<?php
				//In case you are not happy using everywhere the same price display fromat, just create your own layout
				//in override /html/fields and use as first parameter the name of your file
				echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product)); ?>

				<div class="vm-price-save">
					<div class="vm-countdown-save">
						<p class="title">
							<?php echo JText::_('COM_VIRTUEMART_PRODUCT_YOU_SAVE'); ?>
						</p>
						<h2 class="vm-discount-ammount">
							<?php echo str_replace('-', '', $this->currency->createPriceDiv ('discountAmount', '', $this->product->prices, FALSE, FALSE, 1.0, TRUE)); ?>
						</h2>
					</div>
				</div><!-- //sp-price-save -->

			</div>

			<div class="clear"></div><?php
				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product, 'view'=>'details'));
			?>

			<?php
			// Manufacturer of the Product
			if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
			    echo $this->loadTemplate('manufacturer');
			} ?>

		    </div>
		</div>
	</div> <!-- /.row -->

	<div class="clear"></div>
	</div> <!-- /.productdetails-view -->

	<div class="productdetails-view vm-product-details-infos">
		<?php
			// event onContentBeforeDisplay
			echo $this->product->event->beforeDisplayContent; 
		?>

		<!-- Nav tabs -->
		<ul id="vm-product-tab" class="nav nav-tabs vm-product-details-tab" role="tablist">
			<?php if (!empty($this->product->product_desc)) {  // Product Description title ?>
			<li role="vm-tab" class="active">
				<a href="#vm-product-description" aria-controls="vm-product-description" role="tab" data-toggle="tab">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?>
				</a>
			</li>
			<?php } // Product Description END ?>

			<li role="vm-tab">
				<a href="#vm-product-review" aria-controls="vm-product-review" role="tab" data-toggle="tab">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_REVIEW_TITLE') ?>
				</a>
			</li>
			<?php // Product Packaging
			    $product_packaging = '';
			    if ($this->product->product_box) { ?>
			?>
			<li role="vm-tab">
				<a href="#vm-product-box" aria-controls="vm-product-box" role="tab" data-toggle="tab">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_BOX_TITLE') ?>
				</a>
			</li>
			<?php } ?>

			<?php if(isset($this->product->customfields) && $this->product->customfields){ ?>
			<li role="vm-tab">
				<a href="#vm-product-custom-field" aria-controls="vm-product-custom-field" role="tab" data-toggle="tab">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ADDITIONAL_TITLE') ?>
				</a>
			</li>
			<?php } ?>

		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<?php if (!empty($this->product->product_desc)) {  // Product Description title ?>
				<div id="vm-product-description" class="tab-pane active" role="tabpanel">
					<?php echo $this->product->product_desc; ?>
				</div>
			<?php } // Product Description END ?>

			<div id="vm-product-review" class="tab-pane" role="tabpanel" >
				<?php echo $this->loadTemplate('reviews'); ?>
			</div>
			
			<?php if ($this->product->product_box) { ?>
				<div id="vm-product-box" class="tab-pane product-box" role="tabpanel">				
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box; ?> 
				</div>
			<?php } // Product Packaging END ?>

			<?php if(isset($this->product->customfields) && $this->product->customfields){ ?>
			<div id="vm-product-custom-field" class="tab-pane" role="tabpanel">
				<?php
					echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

					echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));
				?>
			</div>
			<?php } ?>
		</div>

	</div> <!-- /.productdetails-view -->

	
	<?php if ($this->product->event->afterDisplayContent) { ?>
		<div class="productdetails-view">
			<!-- onContentAfterDisplay event -->
			<?php echo $this->product->event->afterDisplayContent; ?>
		</div> <!-- /.productdetails-view -->
	<?php } ?>
	

	<?php 
		echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal')); 
	?>

	<?php echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true )); ?>


	<?php echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories')); ?>


	<?php if (VmConfig::get('showCategory', 1)) { ?>
		<!-- Show child categories -->
		<?php echo $this->loadTemplate('showcategory'); ?>
	<?php } //showCategory ?>

	<?php
	$j = 'jQuery(document).ready(function($) {
		Virtuemart.product(jQuery("form.product"));

		$("form.js-recalculate").each(function(){
			if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
				var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
				Virtuemart.setproducttype($(this),id);

			}
		});
	});';
	//vmJsApi::addJScript('recalcReady',$j);

	/** GALT
	 * Notice for Template Developers!
	 * Templates must set a Virtuemart.container variable as it takes part in
	 * dynamic content update.
	 * This variable points to a topmost element that holds other content.
	 */
	$j = "Virtuemart.container = jQuery('.productdetails-view');
	Virtuemart.containerSelector = '.productdetails-view';";

	vmJsApi::addJScript('ajaxContent',$j);

	if(VmConfig::get ('jdynupdate', TRUE)){
		$j = "jQuery(document).ready(function($) {
		Virtuemart.stopVmLoading();
		var msg = '';
		jQuery('a[data-dynamic-update=\"1\"]').off('click', Virtuemart.startVmLoading).on('click', {msg:msg}, Virtuemart.startVmLoading);
		jQuery('[data-dynamic-update=\"1\"]').off('change', Virtuemart.startVmLoading).on('change', {msg:msg}, Virtuemart.startVmLoading);
	});";

		vmJsApi::addJScript('vmPreloader',$j);
	}

	echo vmJsApi::writeJS();

	// if ($this->product->prices['salesPrice'] > 0) {
	//   echo shopFunctionsF::renderVmSubLayout('snippets',array('product'=>$this->product, 'currency'=>$this->currency, 'showRating'=>$this->showRating));
	// }

	?>

</div>



