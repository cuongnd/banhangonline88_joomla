<?php
/**
 * @package Sj Ajax Minicart Pro
 * @version 1.1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2009-2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
class plgSystemPlg_Sj_Hk_Ajax_MiniCart_Pro extends JPlugin {
	function onAfterDispatch(){
		$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		$is_ajax_from_minicart_pro = (int)JRequest::getVar('minicart_ajax', 0);
		if ($is_ajax && $is_ajax_from_minicart_pro){
			switch( JRequest::getCmd('minicart_task') ){
				case 'update':
					$cart_hikashop_product_id = JRequest::getVar('cart_hikashop_product_id',array(),'POST', 'array');
					$quantity =  JRequest::getVar('quantity',array(),'POST', 'array');
					$_cart = hikashop_get('class.cart');
					$cart = $_cart->loadFullCart();
					$result = new stdClass();
					if($cart){
						$count1 = 0;
						$count2 = 0;
						for($i=0; $i<count($cart_hikashop_product_id);$i++){
							$update_id = $cart_hikashop_product_id[$i];
							$update_qty = $quantity[$i];
							JRequest::setVar('quantity', $update_qty);
							if( $msg = $_cart->update($update_id,$update_qty) ){
								$count1++;
							} else {
								$count2++;
							}
						}
						$result->status = '1';
						$result->message = $count1.'/'.($count1+$count2).' success update.';
						$result->quantity =$quantity;
					} else {
						$result->status = 0;
						$result->message = 'no cart';
					}
					die(json_encode($result));
					break;
				case 'refresh':
					ob_start();
					$db = JFactory::getDbo();
					$db->setQuery( 'SELECT * FROM #__modules WHERE id='.JRequest::getInt('minicart_modid') );
					$result = $db->loadObject();
					if (isset($result->module)){
						echo JModuleHelper::renderModule($result);
					}
					$list_html = ob_get_contents();
					ob_end_clean();
					$_cart = hikashop_get('class.cart');
					$cart = $_cart->loadFullCart();
					$lang = JFactory::getLanguage();
					$extension = 'com_hikashop';
					$lang->load($extension);
					$result = new stdClass();
					$result->list_html = $list_html;
					if($cart){
						$billTotal = $cart->total;
						$_price = '';
						$currencyHelper = hikashop_get('class.currency');
						if(empty($billTotal->prices)){
							$_price .=  JText::_('FREE_PRICE');
						}else{
							foreach($billTotal->prices as $price){
								$_price .=  $currencyHelper->format($price->price_value,$price->price_currency_id);
							}
						}
						$cart->billTotal = $_price;
					}
					$result->billTotal = $cart->billTotal;
					$result->length = count($cart->products);
					die(json_encode($result));
					break;
				 case 'delete':
					$cart_hikashop_product_id = JRequest::getVar('cart_hikashop_product_id');
					 $_cart = hikashop_get('class.cart');
					 $cart = $_cart->loadFullCart();
					$result = new stdClass();
					if($cart){
						$msg = $_cart->update($cart_hikashop_product_id,0);
						$result->status = 1;
						$result->message = 'success delete';
					} else {
						$result->status = 0;
						$result->message = 'no cart';
					}
					die(json_encode($result));
					break; 
				default:
					die('invalid task');
					break;
			}
			
			die;
		}
	}
}
