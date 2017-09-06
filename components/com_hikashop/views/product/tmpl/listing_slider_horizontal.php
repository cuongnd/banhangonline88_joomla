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
$height=$this->newSizes->height;
$width=$this->newSizes->width;
$mainDivName = $this->params->get('main_div_name');
$duration=(int)$this->params->get('product_effect_duration',400)/1000;
$paneHeightCss = '';
if($this->params->get('pane_height') != '')
	 $paneHeightCss = 'height:'.$this->params->get('pane_height').'px';
$transitions = array(
 	'bounce' => 'ease-out',
	'linear' => 'linear',
	'elastic' => 'cubic-bezier(1,0,0,1)',
	'sin' => 'cubic-bezier(.45,.05,.55,.95)',
	'quad' => 'cubic-bezier(.46,.03,.52,.96)',
	'expo' => 'cubic-bezier(.19,1,.22,1)',
	'back' => 'cubic-bezier(.18,.89,.32,1.28)'
);
$productTransition = $transitions[$this->params->get('product_transition_effect','bounce')];
$link = hikashop_contentLink('product&task=show&cid='.$this->row->product_id.'&name='.$this->row->alias.$this->itemid.$this->category_pathway,$this->row);
$htmlLink = "";
$cursor = "";
if($this->params->get('link_to_product_page',1)){
	if(!$this->params->get('add_to_cart') && !$this->params->get('add_to_wishlist')){
		$htmlLink = 'onclick="window.location.href=\''.$link.'\'';
		$cursor = "cursor:pointer;";
	}
}

if(!empty($this->row->extraData->top)) { echo implode("\r\n",$this->row->extraData->top); }
?>
<div class="hikashop_horizontal_slider" id="window_<?php echo $mainDivName; ?>_<?php echo $this->row->product_id;  ?>" <?php echo $htmlLink; ?>" >
 	<div class="hikashop_horizontal_slider_subdiv">
		<table cellspacing="0" cellpadding="0">
			<tr>
			<th valign="top">
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
						if($this->params->get('display_badges',1)){
							$this->classbadge->placeBadges($this->image, $this->row->badges, -10, 0);
						}
						$this->image->main_thumbnail_x = $main_thumb_x;
						$this->image->main_thumbnail_y = $main_thumb_y;

					if($this->params->get('link_to_product_page',1)){ ?>
						</a>
					<?php } ?>
					</div>
				</div>
				<!-- EO PRODUCT IMG -->
				<div class="hikashop_img_pane_panel">
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
			</th>
			<th valign="top" height="<?php echo $height; ?>" width="<?php echo $width; ?>" >

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
				<?php if(!empty($this->row->extraData->afterProductName)) { echo implode("\r\n",$this->row->extraData->afterProductName); } ?>

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

				<!-- ADD TO CART BUTTON AREA -->
				<?php
				if($this->params->get('add_to_cart') || $this->params->get('add_to_wishlist')){
					$this->setLayout('add_to_cart_listing');
					echo $this->loadTemplate();
				}?>
				<!-- EO ADD TO CART BUTTON AREA -->
			</th>
			</tr>
		</table>
	</div>
</div>
<?php
if(!empty($this->row->extraData->bottom)) { echo implode("\r\n",$this->row->extraData->bottom); }

if($this->rows[0]->product_id == $this->row->product_id){
?>
<style>
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider{
		margin: auto;
		<?php echo $cursor; ?>
		height:<?php echo $height; ?>px;
		width:<?php echo $width; ?>px;
		overflow:hidden;
		position:relative;
	}
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider_subdiv{
		height:<?php echo $height; ?>px;
		width:<?php echo $width*2; ?>px;
	}
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider_subdiv table{
		height:<?php echo $height; ?>px;
	}
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider_subdiv table th{
		height:<?php echo $height; ?>px;
		width:<?php echo $width; ?>px;
		padding:0px
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
		<?php echo $paneHeightCss; ?>;
	}
	#<?php echo $mainDivName; ?> .hikashop_product_description{
		text-align:<?php echo $this->align; ?>;
		overflow:hidden
	}
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider_subdiv{
		margin-left: 0px;
		-webkit-transition: margin-left <?php echo $duration.'s '.$productTransition; ?>;
		-moz-transition: margin-left <?php echo $duration.'s '.$productTransition; ?>;
		-o-transition: margin-left <?php echo $duration.'s '.$productTransition; ?>;
		transition: margin-left <?php echo $duration.'s '.$productTransition; ?>;
	}
	#<?php echo $mainDivName; ?> .hikashop_horizontal_slider_subdiv:hover{
		margin-left: -<?php echo (int)$width+1; ?>px;
	}
</style>
<?php
}
?>
