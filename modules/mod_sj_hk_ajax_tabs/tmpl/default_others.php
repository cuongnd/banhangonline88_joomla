<?php
/**
 * @package SJ Ajax Tabs for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
 
defined('_JEXEC') or die;

/*--------------------------- Start Button ------------------- */
ob_start();

$config = hikashop_config();
	$main_div_name = $item->product_id.'_'.$module->module.'_'.$module->id;
	
	if ($config->get('show_quantity_field')<2) { ?>
	<form action="<?php echo hikashop_completeLink('product&task=updatecart'); ?>" 
		  method="post" 
		  name="hikashop_product_form_<?php echo $main_div_name; ?>" 
		  enctype="multipart/form-data">
	<?php }
	if ( $config->get('show_quantity_field') < 2 ) {
		$module_id = $module->id;
		$_formName = ',\'hikashop_product_form_'.$main_div_name.'\'';
		$ajax='';
		if(!$config->get('ajax_add_to_cart',0)){
			$ajax = 'if(hikashopCheckChangeForm(\'item\',\'hikashop_product_form_'.$main_div_name.'\')){ return hikashopModifyQuantity(\''.$item->product_id.'\',field,1,\'hikashop_product_form_'.$main_div_name.'\',\'cart\','.$module_id.'); } return false;';
		}
		
		/*--------------------------- Start Quantity ------------------- */
		if($config->get('show_quantity_field')==-2){
			$params->set('show_quantity_field',$item->product_display_quantity_field);
		}
		$wishlistEnabled = $config->get('enable_wishlist', 1);
		$hideForGuest = 1;
		if(($config->get('hide_wishlist_guest', 1) && hikashop_loadUser() != null) || !$config->get('hide_wishlist_guest', 1)){
			$hideForGuest = 0;
		}
		if(!isset($cart)) $cart = hikashop_get('helper.cart');
		if(!empty($item->has_options)){
			$pathway_sef_name = $config->get('pathway_sef_name','category_pathway');
			$category_pathway = JRequest::getInt($pathway_sef_name,0);
			if($config->get('simplified_breadcrumbs',1)){
				$category_pathway='';
			}
			$app = JFactory::getApplication();
			global $Itemid;
			$menus	= $app->getMenu();
			$menu	= $menus->getActive();
			if(empty($menu)){
				if(!empty($Itemid)){
					$menus->setActive($Itemid);
					$menu	= $menus->getItem($Itemid);
				}
			}
			$url_itemid='';
			if(!empty($Itemid)){
				$url_itemid='&Itemid='.$Itemid;
			}
			if((int)$params->get('display_add_to_cart',1)) {
				echo '<div class="hikashop_add_to_cart">';
					echo $cart->displayButton(JText::_('CHOOSE_OPTIONS'),'choose_options',$params,hikashop_completeLink('product&task=show&product_id='.$item->product_id.'&name='.$item->alias.$url_itemid.$category_pathway),'window.location = \''.str_replace("'","\'",hikashop_completeLink('product&task=show&product_id='.$item->product_id.'&name='.$item->alias.$url_itemid.$category_pathway)).'\';return false;','');
				echo '</div>';	
			}
		}else{
			$cart = hikashop_get('helper.cart');
			$url = '';
			$module_id = $module->id;
			if(empty($ajax)){
				$ajax = 'return hikashopModifyQuantity(\''.$item->product_id.'\',field,1,0,\'cart\','.$module_id.')';
			}
			$start_date = 0;
			if(@$item->product_sale_start ){
				$start_date = @$item->product_sale_start;
			}
			$end_date = 0;
			if(@$item->product_sale_end ){
				$end_date = @$item->product_sale_end;
			}
			
			$formName = ',0';
			if (!$config->get('ajax_add_to_cart', 0) || ($config->get('show_quantity_field')>=2 && !@$item->product_id)) {
				if(empty($_formName)) {
					if($item->product_id)
						$formName = ',\'hikashop_product_form_'.$main_div_name.'\'';
					else
						$formName = ',\'hikashop_product_form_'.$main_div_name.'\'';
				} else {
					$formName = $_formName;
				}
			}
			if($end_date && $end_date<time()){
				?>
				<span class="hikashop_product_sale_end">
					<?php echo JText::_('ITEM_NOT_SOLD_ANYMORE'); ?>
				</span>
				<?php
			}elseif($start_date && $start_date>time()){
				?>
				<span class="hikashop_product_sale_start">
					<?php
					echo JText::sprintf('ITEM_SOLD_ON_DATE',hikashop_getDate($start_date, $params->get('date_format','%d %B %Y')));
					?>
				</span>
				<?php
			} elseif (!$params->get('catalogue') && ($config->get('display_add_to_cart_for_free_products') || !empty($item->prices))){
				$min = 0;
				if(@$item->product_min_per_order ){
					$min = @$item->product_min_per_order;
				}
				$max = 0;
				if(@$item->product_max_per_order ){
					$max = @$item->product_max_per_order;
				}
				if($min<=0){
					$min=1;
				}
				$wishlistAjax =	'if(hikashopCheckChangeForm(\'item\''.$formName.')){ return hikashopModifyQuantity(\'' . (int)@$item->product_id . '\',field,1' . $formName . ',\'wishlist\','.$module_id.'); } else { return false; }';

				if($item->product_quantity == -1){
					$item->product_quantity = $item->product_quantity;
				}
				$btnType = 'add';
				if($item->product_quantity==-1){
				?>
				<div class="hikashop_product_stock">
				<?php
					if((int)$params->get('display_add_to_cart',1)){
						echo '<div class="hikashop_add_to_cart">';
							echo $cart->displayButton(JText::_('ADD_TO_CART'),'add',$params,$url,$ajax,'',$max,$min);
						echo '</div>';
						$btnType = 'wish';
					}
					if(hikashop_level(1) && (int)$params->get('display_add_to_wishlist',1) && $wishlistEnabled && !$hideForGuest && $config->get('display_add_to_wishlist_for_free_products','1')){
						echo '<div class="hikashop_add_wishlist">';
							echo $cart->displayButton(JText::_('ADD_TO_WISHLIST'),$btnType,$params,$url,$wishlistAjax,'',$max,$min,'',false);
						echo '</div>';
					}
				}elseif($item->product_quantity>0){
				?>
				<div class="hikashop_product_stock">
				<?php
					echo '<span class="hikashop_product_stock_count">'.JText::sprintf('X_ITEMS_IN_STOCK',$item->product_quantity).'<br/></span>';
					if($config->get('button_style','normal')=='css'){
						echo '<br />';
					}
					if($max<=0 || $max>$item->product_quantity) $max = $item->product_quantity;
					if((int)$params->get('display_add_to_cart',1)){
						echo '<div class="hikashop_add_to_cart">';
							echo $cart->displayButton(JText::_('ADD_TO_CART'),'add',$params,$url,$ajax,'',$max,$min);
						echo '</div>';
						$btnType = 'wish';
					}
					if(hikashop_level(1) && (int)$params->get('display_add_to_wishlist',1)  && $wishlistEnabled && !$hideForGuest && $config->get('display_add_to_wishlist_for_free_products','1')){
						echo '<div class="hikashop_add_wishlist">';
							echo $cart->displayButton(JText::_('ADD_TO_WISHLIST'),$btnType,$params,$url,$wishlistAjax,'',$max,$min,'',false);
						echo '</div>';
					}
				}else{
					?>
				<div class="hikashop_product_no_stock">
				<?php
					echo JText::_('NO_STOCK').'<br/>';
					$waitlist = $config->get('product_waitlist',0);
					if(hikashop_level(1) && (int)$params->get('display_add_to_cart',1) && ($waitlist==2 || ($waitlist==1 && (!empty($item->product_waitlist) )))){ ?>
						</div><div id="hikashop_product_waitlist_main" class="hikashop_product_waitlist_main">
						<?php
						$empty='';
						jimport('joomla.html.parameter');
						$params = new HikaParameter($empty);
						echo '<div class="hikashop_add_to_cart">';
							echo $cart->displayButton(JText::_('ADD_ME_WAITLIST'),'add_waitlist',$params,hikashop_completeLink('product&task=waitlist&cid='.$item->product_id),'window.location=\''.str_replace("'","\'",hikashop_completeLink('product&task=waitlist&cid='.$item->product_id)).'\';return false;');
						echo '</div>';
					}
					if(hikashop_level(1) && (int)$params->get('display_add_to_wishlist',1)  && $wishlistEnabled  && !$hideForGuest && $config->get('display_add_to_wishlist_for_free_products','1')){
						echo '<div class="hikashop_add_wishlist">';
							echo $cart->displayButton(JText::_('ADD_TO_WISHLIST'),'add',$params,$url,$wishlistAjax,'',@$item->product_max_per_order,1,'',false);
						echo '</div>';
					}
				}?>
				</div>
			<?php
			} elseif(hikashop_level(1) && $wishlistEnabled && (int)$params->get('display_add_to_wishlist',1) && $config->get('display_add_to_wishlist_for_free_products','1') && !$hideForGuest && !$config->get('display_add_to_cart_for_free_products')){
				$wishlistAjax =	'if(hikashopCheckChangeForm(\'item\''.$formName.')){ return hikashopModifyQuantity(\'' . (int)@$item->product_id . '\',field,1' . $formName . ',\'wishlist\','.$module_id.'); } else { return false; }';
				echo '<div class="hikashop_add_wishlist">';
					echo $cart->displayButton(JText::_('ADD_TO_WISHLIST'),'add',$params,$url,$wishlistAjax,'',@$item->product_max_per_order,1,'',false);
				echo '</div>';
			}
		}
		/*--------------------------- End Quantity ------------------- */
		
		if(!empty($ajax) && $config->get('redirect_url_after_add_cart','stay_if_cart')=='ask_user'){ ?>
			<input type="hidden" name="popup" value="1"/>
		<?php } ?>
		<input type="hidden" name="hikashop_cart_type_<?php echo $item->product_id.'_'.$module_id; ?>" id="hikashop_cart_type_<?php echo $item->product_id.'_'.$module_id; ?>" value="cart"/>
		<input type="hidden" name="product_id" value="<?php echo $item->product_id; ?>" />
		<input type="hidden" name="module_id" value="<?php echo $module_id; ?>" />
		<input type="hidden" name="add" value="1"/>
		<input type="hidden" name="ctrl" value="product"/>
		<input type="hidden" name="task" value="updatecart"/>
		<input type="hidden" name="return_url" value="<?php echo urlencode(base64_encode(HKAjaxtabsHelper::_getCheckOutURL()));?>"/>
	</form>
	<?php } elseif(empty($item->has_options)&& !$config->get('catalogue') && ($config->get('display_add_to_cart_for_free_products') || !empty($item->prices))){
			if($item->product_quantity==-1 || $item->product_quantity>0){ ?>
			 <input id="hikashop_listing_quantity_<?php echo $item->product_id;?>" 
			   type="text" style="width:40px;" 
			   name="data[<?php echo $item->product_id;?>]" 
			   class="hikashop_listing_quantity_field" value="0" />
	<?php } else {
			echo JText::_('NO_STOCK');
		}
	} ?>
<?php 
$btn_add = ob_get_contents();
ob_end_clean();
/*------------- End Button ----------------*/

/*------------- Start Votes ----------------*/
ob_start();
	$doc = JFactory::getDocument();
	$type_item = JRequest::getCmd('ctrl');
	$class = hikashop_get('class.vote');
	$class->loadJS();
	$doc->addScript(HIKASHOP_JS.'vote.js');
	$config = hikashop_config();
	$db = JFactory::getDBO();
	$hikashop_vote_nb_star = $config->get('vote_star_number');
	if($config->get('enable_status_vote',0)=='vote' || $config->get('enable_status_vote',0)=='two' || $config->get('enable_status_vote',0)=='both' ){
		$vote_enabled = 1;
	}else{
		$vote_enabled = 0;
	}
	$row = new stdClass();
	$row->vote_enabled = $vote_enabled;
	if($vote_enabled == 1){
		if(!empty($params)){
			$main_div_name = $config->get('main_div_name');
			$hikashop_vote_ref_id = $config->get('vote_ref_id');
			if(empty($hikashop_vote_ref_id))
				$hikashop_vote_ref_id = $item->product_id;
			$listing_true =$config->get('listing_product');
			$type_item = $config->get('vote_type');
		}
		$hikashop_vote_user_id = hikashop_loadUser();
		if($type_item == 'vendor'){
			$query = 'SELECT vendor_average_score, vendor_total_vote FROM '.hikashop_table('hikamarket_vendor',false).' WHERE vendor_id = '.(int)$hikashop_vote_ref_id;
			$db->setQuery($query);
			$scores = $db->loadObject();
			$hikashop_vote_average_score = $scores->vendor_average_score;
			$hikashop_vote_total_vote = $scores->vendor_total_vote;
		}else{
			$query = 'SELECT product_average_score, product_total_vote FROM '.hikashop_table('product').' WHERE product_id = '.(int)$hikashop_vote_ref_id;
			$db->setQuery($query);
			$scores = $db->loadObject();
			$hikashop_vote_average_score = $scores->product_average_score;
			$hikashop_vote_total_vote = $scores->product_total_vote;
		}

		$hikashop_vote_average_score_rounded = round($hikashop_vote_average_score, 0);
		$row->vote_ref_id = $hikashop_vote_ref_id;
		$row->main_div_name = $main_div_name ;
		$row->listing_true = $listing_true;
		$row->hikashop_vote_average_score_rounded = $hikashop_vote_average_score_rounded;
		JRequest::setVar("rate_rounded",$hikashop_vote_average_score_rounded);
		$row->hikashop_vote_average_score = $hikashop_vote_average_score;
		$row->hikashop_vote_total_vote = $hikashop_vote_total_vote;
		$row->hikashop_vote_nb_star = $hikashop_vote_nb_star;
		$row->type_item = $type_item;
	}
		JHTML::_('behavior.tooltip');
	if ($vote_enabled == 1) {
		$hikashop_vote_average_score = $row->hikashop_vote_average_score;
		$hikashop_vote_average_score_rounded = $row->hikashop_vote_average_score_rounded;
		JRequest::setVar("rate_rounded",$hikashop_vote_average_score_rounded);
		$hikashop_vote_total_vote = $row->hikashop_vote_total_vote;
		$hikashop_vote_nb_star = $row->hikashop_vote_nb_star;
		JRequest::setVar("nb_max_star",$hikashop_vote_nb_star);

		$type_item = ($row->type_item != '') ?  $row->type_item : 'product';
		$hikashop_vote_ref_id = $row->vote_ref_id;

		$main_div_name = ($row->main_div_name != '') ? $row->main_div_name : 'hikashop_vote_ok_'.(int)$hikashop_vote_ref_id.'_'.$module->id ;
		$hikashop_vote_user_id = hikashop_loadUser();
		$listing_true = $row->listing_true;
		$select_id = "select_id_".$hikashop_vote_ref_id;
		if($main_div_name != '' ){
			$select_id .= "_".$main_div_name;
		}else{
			$select_id .= "_hikashop_main_div_name";
		}
		if(empty($main_div_name)){?>
			<input 	type="hidden" id="hikashop_vote_ref_id" value="<?php echo $hikashop_vote_ref_id;?>"/>
	<?php } ?>
		<input 	type="hidden" id="hikashop_vote_ok_<?php echo $hikashop_vote_ref_id;?>" value="0"/>
		<input 	type="hidden" id="vote_type_<?php echo $hikashop_vote_ref_id;?>" value="<?php echo $type_item; ?>"/>
		<input 	type="hidden" id="hikashop_vote_user_id_<?php echo $hikashop_vote_ref_id;?>" value="<?php echo $hikashop_vote_user_id;?>"/>

		<div class="hikashop_vote_stars">
			<select name="hikashop_vote_rating" style="display: none;" class="chzn-done" id="<?php echo $select_id;?>">
				<?php
				for ($g = 1; $g <= $hikashop_vote_nb_star; $g++) {
					echo '<option value="' . $g . '">' . $g . '</option>';
				}
				?>
			</select>
			<script type='text/javascript'>
				window.addEvent('domready', function() {
					var rating = new hikashop_ratings(document.getElementById('<?php echo $select_id;?>'), {
						id : 'hikashop_vote_rating_<?php echo $type_item; ?>_<?php echo $hikashop_vote_ref_id.'_'.$module->id;?>_',
						showSelectBox : false,
						container : null,
						defaultRating :  <?php echo $hikashop_vote_average_score_rounded; ?>
					});
				});
			</script>
			<span class="hikashop_total_vote" >(<?php echo JHTML::tooltip($hikashop_vote_average_score.'/'.$hikashop_vote_nb_star, JText::_('VOTE_AVERAGE'), '', ' '.$hikashop_vote_total_vote.' '); ?>) </span>
			<span id="hikashop_vote_status_<?php echo $hikashop_vote_ref_id;?>" class="hikashop_vote_notification_mini"></span>
		</div>
	<?php
	}

$minivotes = ob_get_contents();
ob_end_clean();
/*------------- Start Votes ----------------*/
?>


