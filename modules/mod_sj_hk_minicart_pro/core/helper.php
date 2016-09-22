<?php
/**
 * @package SJ Minicart Pro for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
 
defined('_JEXEC') or die;

include_once dirname(__FILE__).'/helper_base.php';

abstract class HKMinicartproHelper extends HKMinicartproBaseHelper {
	public static function getList( $params ){
		$cart = hikashop_get('class.cart');
		$_cart = $cart->loadFullCart();
		$products = $_cart->products;
		$url_itemid='';
		if(!empty($Itemid)){
			$url_itemid='&Itemid='.$Itemid;
		}
		$Productclass = hikashop_get('class.product');
		if(empty($products)) return $_cart ;
		foreach($products as $product){
			$Productclass->addAlias($product);
			$config = hikashop_config();
			$pathway_sef_name = $config->get('pathway_sef_name','category_pathway');
			$category_pathway = JRequest::getInt($pathway_sef_name,0);
			$product->id = $product->product_id;
			$image = self::_getProductImage($product->product_id);
			$product->title = $product->product_name;
			$product->description = self::_cleanText($product->product_description);
			$product->_image = $image ?  $image : '';
			$product->link = hikashop_completeLink('product&task=show&cid='.$product->product_id.'&name='.$product->alias.$url_itemid.$category_pathway);
			$product->_price = self::_processPrice($product);
		}
		return $_cart;
	}

	private static function _displayBtn(){
		$config = hikashop_config();
		$cart = hikashop_get('helper.cart');

		$cart->getJS(self::_getCheckOutURL(),true);
		if($config->get('redirect_url_after_add_cart') == 'stay_if_cart' && JRequest::getInt('popup') && JRequest::getVar('tmpl')!='component'){
			JHTML::script('system/modal.js',false,true,true);
			$app = JFactory::getApplication();
			if($app->getUserState( HIKASHOP_COMPONENT.'.popup','0')){
				$js = '
				window.addEvent(\'domready\', function() {
					SqueezeBox.fromElement(\'hikashop_notice_box_trigger_link\',{parse: \'rel\'});
				});
				';

				$app->setUserState( HIKASHOP_COMPONENT.'.popup','0');
			}else{
				$js = '';
			}
			if(!empty($js)){
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration("\n<!--\n".$js."\n//-->\n");
			}
		}
		return true;
	}

	public static function _getCheckOutURL($cart = false){
		global $Itemid;
		$url_itemid='';
		if(!empty($Itemid)){
			$url_itemid='&Itemid='.$Itemid;
		}

		$config = hikashop_config();
		$url = $config->get('redirect_url_after_add_cart','stay_if_cart');
		switch($url){
			case 'checkout':
				$url = hikashop_completeLink('checkout'.$url_itemid,false,true);
				break;
			case 'stay_if_cart':
				$url='';
				if(!$cart){
					$url = hikashop_completeLink('checkout'.$url_itemid,false,true);
					break;
				}
			case 'ask_user':
			case 'stay':
				$url='';
			case '':
			default:
				if(empty($url)){
					$url = hikashop_currentURL('return_url',false);
				}
				break;
		}
		
		return urldecode($url);
	}

	private static function _getCatInfor($catids, $params){
		!is_array( $catids ) && settype( $catids, 'array' );
		if(empty($catids)) return;
		$class = hikashop_get('class.category');
		$list = array();
		foreach($catids as $cat){
			$category = $class->get($cat, true);
			$category->count = self::_countProducts($cat,$params);
			$list[] = $category;
			$cat_order_by = $params->get('cat_order_by',null);
			if($cat_order_by != null)
			{
				switch ($cat_order_by) {
					default:
					case 'category_name':
						usort($list, create_function('$a, $b', 'return strnatcasecmp( $a->category_name, $b->category_name);'));
						break;
					case 'category_ordering':
						usort($list, create_function('$a, $b', 'return $a->category_ordering < $b->category_ordering;'));
						break;
					case 'random':
						shuffle($list);
						break;
				}
			}
		}
		if(empty($list)) return;
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
		foreach($list as $cat){
			$cat->id = $cat->category_id;
			$class->addAlias($cat);
			$cat->title = $cat->category_name;
			$cat->description = self::_cleanText($cat->category_description);
			$cat->link = hikashop_completeLink('category&task=listing&cid='.(int)$cat->category_id.'&name='.$cat->alias.$url_itemid);
		}
		return $list;
	}

	/*Check Category Published*/
	private static function _checkCatPublic( $catids )
	{
		$list = array();
		$_catids = array();
		!is_array( $catids ) && settype( $catids, 'array' );
		$class = hikashop_get('class.category');
		foreach($catids as $cat){
			$list[] = $class->get($cat);
		}
		if(empty($list)) return;
		//var_dump($list); die;
		foreach($list as $category){
			if($category->category_published == 1 && ($category->category_access == 'all' || strpos($category->category_access,13) )) {
				$_catids[] = $category->category_id;
			}
		}
		return $_catids;
	}

	private static function _getChildCategory( $element , $params, $allcat = true )
	{
		!is_array( $element ) && settype( $element, 'array' );
		if(empty($element)) return;
		$catids = array();
		$depth = (int)$params->get('depth',1);
		$start = 0;
		$value = $params->get('count_cat', 0);
		$order = '' ; // ORDER BY a.category_name ASC || ORDER BY a.category_ordering DESC || ORDER BY rand() ...
		$category_image = false; // Allow get Image of category
		$select = 'a.*';
		if($depth > 0) 
		{
			$class = hikashop_get('class.category');
			$additional_catids = array();
			$list = $class->getChilds ( $element , true, array(), $order, $start, $value, $category_image, $select);
			foreach ($element as $catid) {
				if ($list) 
				{
					foreach ($list as $category) 
					{
						$condition = (($category->category_depth - $class->get($catid)->category_depth) <= $depth);
						if ($condition) 
						{
							$additional_catids[] = $category->category_id;
						}
					}
				}
		  }	
		  $catids = array_unique($additional_catids);
		}
		if ($allcat) {
			$catids = array_unique(array_merge($element, $catids));
		}

		return $catids;
	}
	
	private static function _countProducts ($catids, $params)
	{
		!is_array($catids) && settype($catids, 'array');
		if(empty($catids)) return;
		$ordering_direction = $params->get('product_ordering_direction','ASC');
		$orderby = $params->get('product_order','ordering');
		if($orderby == 'rand()')
		{
			$orderby = 'ORDER BY RAND()';
			
		}
		else if($orderby == 'ordering')
		{
			$orderby = 'ORDER BY a.'. $orderby .' '. $ordering_direction;
		}
		else
		{
			$orderby = 'ORDER BY b.'. $orderby .' '. $ordering_direction;
		}
		$database	= JFactory::getDBO();
		$filters=array(" AND a.category_id IN(".implode(", ", $catids).")");
		hikashop_addACLFilters($filters,'product_access','b');
		$query = "
				SELECT count( DISTINCT b.product_id) 
				FROM ".hikashop_table('product_category')." AS a 
				LEFT JOIN ".hikashop_table('product')." AS b 
				ON a.product_id=b.product_id 
				WHERE b.product_published=1 
				AND b.product_type = 'main'  
				".implode(' AND ',$filters)."
				". $orderby ." 
			";
		$database->setQuery($query);
		$count = $database->loadResult();
		return $count;
	}

	private static function _getProduct($catids, $params){
		!is_array($catids) && settype($catids, 'array');
		if(empty($catids)) return;
		$ordering_direction = $params->get('product_ordering_direction','ASC');
		$orderby = $params->get('product_order','ordering');
		if($orderby == 'rand()')
		{
			$orderby = 'ORDER BY RAND()';
			
		}
		else if($orderby == 'ordering')
		{
			$orderby = 'ORDER BY a.'. $orderby .' '. $ordering_direction;
		}
		else
		{
			$orderby = 'ORDER BY b.'. $orderby .' '. $ordering_direction;
		}
		$database	= JFactory::getDBO();
		$filters=array(" AND a.category_id IN(".implode(", ", $catids).")");
		hikashop_addACLFilters($filters,'product_access','b');
		$start = 0;
		$limit = (int)$params->get('count',5);
		
		$_limit = $limit > 0 ?  " LIMIT ".$start.", ".$limit." " : " ";
		$query = "  
		  SELECT DISTINCT b.* 
		  FROM ".hikashop_table('product_category')." AS a 
		  LEFT JOIN ".hikashop_table('product')." AS b 
		  ON a.product_id=b.product_id 
		  WHERE b.product_published=1 
		  AND b.product_type = 'main'
		  ".implode(' AND ',$filters)."
		  ". $orderby . $_limit." ";
		  
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if(empty($rows)) return;
			$ids = array();
			$productClass = hikashop_get('class.product');
			foreach($rows as $key => $row){
				$ids[]=$row->product_id;
				$productClass->addAlias($rows[$key]);
			}
			$queryImage = 'SELECT * FROM '.hikashop_table('file').' WHERE file_ref_id IN ('.implode(',',$ids).') AND file_type=\'product\' ORDER BY file_ref_id ASC, file_ordering ASC, file_id ASC';
			$database->setQuery($queryImage);
			$images = $database->loadObjectList();
			foreach($rows as $k=>$row){
				if(!empty($images)){
					foreach($images as $image){
						if($row->product_id==$image->file_ref_id){
							if(!isset($row->file_ref_id)){
								foreach(get_object_vars($image) as $key => $name){
									$rows[$k]->$key = $name;
								}
							}
							break;
						}
					}
				}
				if(!isset($rows[$k]->file_name)){
					$rows[$k]->file_name = $row->product_name;
				}
			}
		
			$database->setQuery('SELECT variant_product_id FROM '.hikashop_table('variant').' WHERE variant_product_id IN ('.implode(',',$ids).')');
			$variants = $database->loadObjectList();
			if(!empty($variants)){
				foreach($rows as $k => $product){
					foreach($variants as $variant){
						if($product->product_id==$variant->variant_product_id){
							$rows[$k]->has_options = true;
							break;
						}
					}
				}
			}
			$database->setQuery('SELECT product_id FROM '.hikashop_table('product_related').' WHERE product_related_type = '.$database->quote('options').' AND product_id IN ('.implode(',',$ids).')');
			$options = $database->loadObjectList();
			if(!empty($options)){
				foreach($rows as $k => $product){
					foreach($options as $option){
						if($product->product_id==$option->product_id){
							$rows[$k]->has_options = true;
							break;
						}
					}
				}
			}

			$config = hikashop_config();
			 if($config->get('tax_zone_type','shipping')=='billing'){
				$zone_id = hikashop_getZone('billing');
			}else{
				$zone_id = hikashop_getZone('shipping');
			}
			$currencyClass = hikashop_get('class.currency');
			$currency_id = hikashop_getCurrency();
			$currencyClass->getListingPrices($rows,$zone_id,$currency_id,'all');

			$classbadge=hikashop_get('class.badge');
			if(!empty($rows)){
				foreach($rows as $k => $row){
					$classbadge->loadBadges($rows[$k]);
				}
			}

			//var_dump($rows); die;
		return $rows;
	}

	public static function _getProductInfor($catids, $params){
		$items = self::_getProduct($catids, $params);
		if(empty($items)) return;
			$config = hikashop_config();
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

			$Productclass = hikashop_get('class.product');
			foreach($items as $item){
				$Productclass->addAlias($item);
				$item->id = $item->product_id;
				$image = self::_getProductImage($item->product_id);
				$item->title = $item->product_name;
				$item->description = self::_cleanText($item->product_description);
				$item->_image = $image ?  $image : '';
				$item->link = hikashop_completeLink('product&task=show&cid='.$item->product_id.'&name='.$item->alias.$url_itemid.$category_pathway);
				$item->_price = self::_processPrice($item);
			}
			//var_dump($items); die;
		return $items;
	}

	private static function _processPrice($item){
		$_price = '';
		$currencyHelper = hikashop_get('class.currency');
		$config = hikashop_config();
		if(empty($item->prices)){
			$_price .=  JText::_('FREE_PRICE');
		}else{
			foreach($item->prices as $price){
				$_price .=  $currencyHelper->format($price->price_value,$price->price_currency_id);
			}
		}
		return $_price;
	}

	private static function _getProductImage($product_id){
		$query = 'SELECT * FROM ' . hikashop_table('file') . ' AS file WHERE file.file_type = \'product\' AND file_ref_id = '.(int)$product_id.' ORDER BY file_ordering ASC';
		$database	= JFactory::getDBO();
		$database->setQuery($query);
		$images = $database->loadObjectList();
		return $images;
	}
}
