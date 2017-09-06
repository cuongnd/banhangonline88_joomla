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
class categoryController extends hikashopController{
	var $modify = array();
	var $delete = array();
	var $modify_views = array();

	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);
		$this->display = array_merge($this->display, array(
			'test','update_remote_category','update_remote_category_taobao','get_sub_category_by_category_id'
		));
	}

	function authorize($task){
		if($this->isIn($task,array('display'))){
			return true;
		}
		return false;
	}
	public function update_remote_category(){
		$app=JFactory::getApplication();
		$vatgia_category_id=$app->input->getInt('vatgia_category_id');
		$category_id=$app->input->getInt('category_id',0);
		$class = hikashop_get('class.category');
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->update('#__hikashop_category')
			->set('vatgia_category_id='.(int)$vatgia_category_id)
			->where('category_id='.(int)$category_id)
		;
		$db->setQuery($query)->execute();
		die;
	}
	public function update_remote_category_taobao(){
		$app=JFactory::getApplication();
		$taobao_category_id=$app->input->getInt('taobao_category_id');
		$category_id=$app->input->getInt('category_id',0);
		$taobao_category_name=$app->input->getString('taobao_category_name','');
		$taobao_link_category=$app->input->getString('taobao_link_category','');
		$class = hikashop_get('class.category');
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->update('#__hikashop_category')
			->set('taobao_category_id='.$taobao_category_id)
			->set('taobao_category_name='.$query->q($taobao_category_name))
			->set('taobao_link_category='.$query->q($taobao_link_category))
			->where('category_id='.(int)$category_id)
		;
		$db->setQuery($query)->execute();
		die;
	}
	public function get_sub_category_by_category_id(){
		$app=JFactory::getApplication();
		$category_id=$app->input->getInt('category_id',0);
		$class_category = hikashop_get('class.category');
		$sub_category_item = $class_category->getChilds($category_id, true, array(), '', 0, 300);
		echo json_encode($sub_category_item);
		die;
	}
}
