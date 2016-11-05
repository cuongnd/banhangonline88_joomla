<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tools
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Tool controller class.
 *
 * @since  1.6
 */
class ToolsControllerTool extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'tools';
		parent::__construct();
	}
	public function ajax_get_alias(){
		$app=JFactory::getApplication();
		$post = file_get_contents('php://input');
		$post = json_decode($post);
		$title=$post->title;
		$title = str_replace(' ', '-', $title);
		$title=JString::vn_str_filter($title);
		$title=JString::clean($title);
		$title=strtolower($title);
		echo $title;
		die;

	}
	public function remove_unicode_product_alias(){
		die("da lam xong roi");
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('product_id,product_name')
			->from('#__hikashop_product')
			;
		$list_product=$db->setQuery($query)->loadObjectList();
		$query->clear();
		jimport('joomla.string.string');
		foreach($list_product as $product)
		{
			$product_name=$product->product_name;
			$product_alias = str_replace(' ', '-', $product_name);
			$product_alias=JString::vn_str_filter($product_alias);
			$product_alias=JString::clean($product_alias);
			$product_alias=strtolower($product_alias);
			$query->clear();
			$query->update('#__hikashop_product')
				->set('product_alias='.$query->q($product_alias))
				->where('product_id='.(int)$product->product_id)
				;
			$db->setQuery($query);
			$db->execute();
		}
		die;
	}
	public function update_product_code(){
		die("da lam xong roi");
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('product_id,product_name')
			->from('#__hikashop_product')
			;
		$list_product=$db->setQuery($query)->loadObjectList();
		$query->clear();
		jimport('joomla.string.string');
		foreach($list_product as $product)
		{
			$product_name=$product->product_name;
			$product_code = str_replace(' ', '-', $product_name);
			$product_code=JString::vn_str_filter($product_code);
			$product_code=JString::clean($product_code);
			$product_code=strtolower($product_code);
			$product_code=explode('-',$product_code);
			$product_code=array_slice($product_code, 0, 3);
			foreach($product_code as &$item)
			{
				$item=substr($item,0,2);
			}
			$product_code=implode('_',$product_code)."_$product->product_id";
			echo $product_code;
			echo "<br/>";
			$query->clear();
			$query->update('#__hikashop_product')
				->set('product_code='.$query->q($product_code))
				->where('product_id='.(int)$product->product_id)
				;
			$db->setQuery($query);
			$db->execute();
		}
		die;
	}
	public function remove_unicode_product_category_alias(){
		die("da lam xong roi");
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('category_id,category_name')
			->from('#__hikashop_category')
			->where('category_id!=1')
			;
		$list_category=$db->setQuery($query)->loadObjectList();
		$query->clear();
		jimport('joomla.string.string');
		foreach($list_category as $category)
		{
			$category_name=$category->category_name;
			$category_alias = str_replace(' ', '-', $category_name);
			$category_alias=JString::vn_str_filter($category_alias);
			$category_alias=JString::clean($category_alias);
			$category_alias=strtolower($category_alias);
			$query->clear();
			$query->update('#__hikashop_category')
				->set('category_alias='.$query->q($category_alias))
				->where('category_id='.(int)$category->category_id)
				;
			$db->setQuery($query);
			$db->execute();
		}
		die;
	}
	public function remove_unicode_menu_item(){
		die("da lam xong roi");
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id,title')
			->from('#__menu')
			->where('client_id=0 AND id!=1')
			;
		$list_menu_item=$db->setQuery($query)->loadObjectList();
		$query->clear();
		jimport('joomla.string.string');
		jimport('joomla.user.helper');
		foreach($list_menu_item as $menu_item)
		{
			$menu_item_title=$menu_item->title;
			$menu_item_alias = str_replace(' ', '-', $menu_item_title);
			$menu_item_alias=JString::vn_str_filter($menu_item_alias);
			$menu_item_alias=JString::clean($menu_item_alias);
			$menu_item_alias=strtolower($menu_item_alias);

			$query->clear();
			$query->from('#__menu')
				->select('id')
				->where('alias='.$query->q($menu_item_alias))
			;
			$db->setQuery($query);
			$list_alias=$db->loadObjectList();
			if(count($list_alias))
			{
				$menu_item_alias.='_'.JUserHelper::genRandomPassword(3);

			}
			$query->clear();
			$query->update('#__menu')
				->set('alias='.$query->q($menu_item_alias))
				->where('id='.(int)$menu_item->id)
				;
			$db->setQuery($query);
			$db->execute();
		}
		die;
	}

}
