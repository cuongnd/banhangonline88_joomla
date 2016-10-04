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
class CartController extends hikashopController{
	var $type='cart';
	var $pkey = 'cart_id';
	var $table = 'cart';
	var $orderingMap ='cart_modified';

	function __construct($config = array()) {
		parent::__construct($config);
		$this->modify_views = array_merge($this->modify_views, array(
			'customer_set','customer_save'
		));
	}

	function store($new = false){

		$app = JFactory::getApplication();
		$cartClass = hikashop_get('class.cart');
		$data = JRequest::getVar('data','0');

		$cart_id = hikashop_getCID('cart_id');
		$cart_type = $data['cart']['cart_type'];
		$cart_name = $data['cart']['cart_name'];
		$cart_user = 0;
		if(!empty($data['user']['user_id']))
			$cart_user = (int)$data['user']['user_id'];

		$cart= new stdClass();
		$cart->cart_id = $cart_id;
		if(!empty($cart_user)){
			$userClass = hikashop_get('class.user');
			$user = $userClass->get($cart_user);
			$cart->user_id = $user->user_cms_id;
		}
		$cart->cart_modified = time();
		$cart->cart_type = $cart_type;
		$cart->cart_name = $cart_name;
		if(isset($data['cart']['cart_coupon']))
			$cart->cart_coupon = $data['cart']['cart_coupon'];
		$status = $cartClass->save($cart);
		$formData = JRequest::getVar( 'item', array(), '', 'array' );
		if($status){
			if(!empty($formData)){
				JRequest::setVar($cart_type.'_id',$cart_id);
				JRequest::setVar('cart_type',$cart_type);
				$cartClass->update($formData,0,0,'item');
			}
		}

		if($status){
			if(!HIKASHOP_J30)
				$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ), 'success');
			else
				$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ));
			JRequest::setVar( 'cid', $status  );
			JRequest::setVar( 'fail', null  );
		}else{
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					$app->enqueueMessage($oneError, 'error');
				}
			}
		}
		return $status;
	}

	public function customer_set() {
		JRequest::setVar('layout', 'customer_set');
		return parent::display();
	}

	public function customer_save() {
		$class = hikashop_get('class.cart');
		if( $class === null )
			return false;

		$cart = new stdClass();
		$cart->cart_id = JRequest::getVar('cart_id','0');
		$cart->user_id = JRequest::getVar('user_id','0');
		$cart->session_id = JRequest::getVar('session_id','0');
		$cart->cart_type = JRequest::getVar('cart_type','cart');
		$status = $class->save($cart);
		if($status){
			JRequest::setVar('cid',$status);
			$js = 'parent.window.location.href=\''.hikashop_completeLink('cart&task=edit&cart_type='.$cart->cart_type.'&cid[]='.@$status,false,true).'\';';
		}else{
			$js = 'parent.window.location.reload();';
		}
		if(!headers_sent()){
			header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
		}
		echo '<html><head><script type="text/javascript">'.$js.'</script></head><body></body></html>';
		exit;
	}
}
