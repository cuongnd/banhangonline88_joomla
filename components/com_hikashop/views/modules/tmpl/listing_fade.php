<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$height = $this->newSizes->height;
$width = $this->newSizes->width;
$mainDivName = $this->params->get('main_div_name','');
$duration = $this->params->get('product_effect_duration',1000);
$backgroundColor=$this->params->get('background_color',$this->config->get('background_color'));
$link = hikashop_contentLink('product&task=show&cid='.$this->row->product_id.'&name='.$this->row->alias.$this->itemid.$this->category_pathway,$this->row);
$htmlLink = "";
$cursor = "";
if($this->params->get('link_to_product_page',1)){
	$htmlLink='onclick = "window.location.href = \''.$link.'\'';
	$cursor="cursor:pointer;";
}
$paneHeight='';
if(!$this->params->get('pane_height','') != '')
	$paneHeight = 'height:'.$this->params->get('pane_height').'px;';

if(!empty($this->row->extraData->top)) { echo implode("\r\n",$this->row->extraData->top); }
?>
<div class="hikashop_fade_effect" id="div_<?php echo $mainDivName.'_'.$this->row->product_id;  ?>" <?php echo $htmlLink; ?>" >
	<div data-effect="fadeOut" class="hikashop_fade_effect_picture">
		<!-- PRODUCT IMG -->
		<div class="hikashop_product_image">
			<div class="hikashop_product_image_subdiv">
			<?php if($this->params->get('link_to_product_page',1)){ ?>
				<a href="<?php echo $link;?>" title="<?php echo $this->escape($this->row->product_name); ?>">
			<?php } ?><?php
				$image_options = array('default' => true,'forcesize'=>$this->config->get('image_force_size',true),'scale'=>$this->config->get('image_scale_mode','inside'));
				$img = $this->image->getThumbnail(@$this->row->file_path, array('width' => $this->image->main_thumbnail_x, 'height' => $this->image->main_thumbnail_y), $image_options);
				if($img->success) {
					echo '<img class="hikashop_product_listing_image" title="'.$this->escape(@$this->row->file_description).'" alt="'.$this->escape(@$this->row->file_name).'" src="'.$img->url.'"/>';
				}
				$main_thumb_x = $this->image->main_thumbnail_x;
				$main_thumb_y = $this->image->main_thumbnail_y;
				$horizontal = '0';
				$vertical = -'10';
				if($this->params->get('display_badges',1)){
					$this->classbadge->placeBadges($this->image, $this->row->badges, $vertical, $horizontal);
				}
				$this->image->main_thumbnail_x = $main_thumb_x;
				$this->image->main_thumbnail_y = $main_thumb_y;

			if($this->params->get('link_to_product_page',1)){ ?>
				</a>
			<?php } ?>
			</div>
		</div>
		<!-- EO PRODUCT IMG -->

		<div class="hikashop_img_fade_panel">
			<!-- PRODUCT NAME -->
			<span class="hikashop_product_name">
					<?php if($this->params->get('link_to_product_page',1)){ ?>
						<a href="<?php echo $link;?>">
					<?php }
						echo $this->row->product_name;
					if($this->params->get('link_to_product_page',1)){ ?>
						</a>
					<?php } ?>
				</span>
			<!-- EO PRODUCT NAME -->

			<!-- PRODUCT CODE -->
				<span class='hikashop_product_code_list'>
					<?php if ($this->config->get('show_code')) { ?>
						<?php if($this->params->get('link_to_product_page',1)){ ?>
							<a href="<?php echo $link;?>">
						<?php }
						echo $this->row->product_code;
						if($this->params->get('link_to_product_page',1)){ ?>
							</a>
						<?php } ?>
					<?php } ?>
				</span>
			<!-- EO PRODUCT CODE -->

			<!-- PRODUCT PRICE -->
				<?php
					if($this->params->get('show_price','-1')=='-1'){
						$config =& hikashop_config();
						$this->params->set('show_price',$config->get('show_price'));
					}
					if($this->params->get('show_price')){
						$this->setLayout('listing_price');
						echo $this->loadTemplate();
					}
				?>
			<!-- EO PRODUCT PRICE -->
		</div>
	</div>

	<!-- PRODUCT NAME -->
	<span class="hikashop_product_name">
		<?php if($this->params->get('link_to_product_page',1)){ ?>
			<a href="<?php echo $link;?>">
		<?php }
			echo $this->row->product_name;
		if($this->params->get('link_to_product_page',1)){ ?>
			</a>
		<?php } ?>
	</span>
	<!-- EO PRODUCT NAME -->

	<!-- PRODUCT DESCRIPTION -->
	<?php if($this->config->get('show_description_listing',0)){ ?>
	<div class="hikashop_product_description">
		<?php
		echo preg_replace('#<hr *id="system-readmore" */>.*#is','',$this->row->product_description);
		?>
	</div>
	<?php } ?>
	<!-- EO PRODUCT DESCRIPTION -->

	<!-- PRODUCT CUSTOM FIELDS -->
	<?php
		if(!empty($this->productFields)) {
			foreach ($this->productFields as $fieldName => $oneExtraField) {
				if(!empty($this->row->$fieldName) || (isset($this->row->$fieldName) && $this->row->$fieldName === '0')) {
		?>
				<dl class="hikashop_product_custom_<?php echo $oneExtraField->field_namekey;?>_line">
					<dt class="hikashop_product_custom_name">
						<?php echo $this->fieldsClass->getFieldName($oneExtraField);?>
					</dt>
					<dd class="hikashop_product_custom_value">
						<?php echo $this->fieldsClass->show($oneExtraField,$this->row->$fieldName); ?>
					</dd>
				</dl>
		<?php
				}
			}
		}
	?>
	<!-- EO PRODUCT CUSTOM FIELDS -->

	<!-- PRODUCT VOTE -->
	<?php
	if($this->params->get('show_vote')){
		$this->setLayout('listing_vote');
		echo $this->loadTemplate();
	}
	?>
	<!-- EO PRODUCT VOTE -->

	<!-- ADD TO CART BUTTON AREA -->
	<?php
	if($this->params->get('add_to_cart') || $this->params->get('add_to_wishlist')){
		$this->setLayout('add_to_cart_listing');
		echo $this->loadTemplate();
	}?>
	<!-- EO ADD TO CART BUTTON AREA -->

	<!-- COMPARISON AREA -->
	<?php
	if(JRequest::getVar('hikashop_front_end_main',0) && JRequest::getVar('task')=='listing' && $this->params->get('show_compare')) { ?>
		<br/><?php
		if( $this->params->get('show_compare') == 1 ) {
			$js = 'setToCompareList('.$this->row->product_id.',\''.$this->escape($this->row->product_name).'\',this); return false;';
			echo $this->cart->displayButton(JText::_('ADD_TO_COMPARE_LIST'),'compare',$this->params,$link,$js,'',0,1,'hikashop_compare_button');
		 } else { ?>
		<input type="checkbox" class="hikashop_compare_checkbox" id="hikashop_listing_chk_<?php echo $this->row->product_id;?>" onchange="setToCompareList(<?php echo $this->row->product_id;?>,'<?php echo $this->escape($this->row->product_name); ?>',this);"><label for="hikashop_listing_chk_<?php echo $this->row->product_id;?>"><?php echo JText::_('ADD_TO_COMPARE_LIST'); ?></label>
	<?php }
	} ?>
	<!-- EO COMPARISON AREA -->
</div>
<?php
if(!empty($this->row->extraData->bottom)) { echo implode("\r\n",$this->row->extraData->bottom); }

if($this->rows[0]->product_id == $this->row->product_id){
?>
<style>
	#<?php echo $mainDivName; ?> .hikashop_fade_effect{
		margin: auto;
		text-align:<?php echo $this->align; ?>;
		<?php echo $cursor; ?>
		height:<?php echo $height; ?>px;
		width:<?php echo $width; ?>px;
		overflow:hidden;
		position:relative;
	}
	#<?php echo $mainDivName; ?> .hikashop_fade_effect_picture{
		background-color:<?php echo $backgroundColor; ?>;
		position:absolute;
		height:<?php echo $height; ?>px;
		width:<?php echo $width; ?>px;
	}
	#<?php echo $mainDivName; ?> .hikashop_product_image{
		height:<?php echo $this->image->main_thumbnail_y;?>px;
		text-align:center;
		clear:both;
	}
	#<?php echo $mainDivName; ?> .hikashop_product_image_subdiv{
		position:relative;
		text-align:center;
		clear:both;
		width:<?php echo $this->image->main_thumbnail_x;?>px;
		margin: auto;
	}
	#<?php echo $mainDivName; ?> .hikashop_img_pane_panel{
		width:<?php echo $width; ?>px;
		<?php echo $paneHeight; ?>;
	}
	#<?php echo $mainDivName; ?> .hikashop_product_description{
		text-align:<?php echo $this->align; ?>;
		overflow:hidden
	}
	#<?php echo $mainDivName; ?> .hikashop_fade_effect_picture{
		opacity: 1;
		-webkit-transition: opacity <?php echo (int)$duration / 1000; ?>s ease-in;
		-moz-transition: opacity <?php echo (int)$duration / 1000; ?>s ease-in;
		-o-transition: opacity <?php echo (int)$duration / 1000; ?>s ease-in;
		transition: opacity <?php echo (int)$duration / 1000; ?>s ease-in;
	}
	#<?php echo $mainDivName; ?> .hikashop_fade_effect_picture:hover{
		opacity: 0;
	}
</style>
<?php
}
?>
