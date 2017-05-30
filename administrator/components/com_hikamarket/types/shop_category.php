<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikamarketShop_categoryType {

	public function display($map, $value, $type = 'category', $field = 'category_id', $form = true, $none = true) {
		$categoryType = hikamarket::get('shop.type.categorysub');
		$categoryType->type = $type;
		$categoryType->field = $field;
		return $categoryType->display($map, $value, $form, $none);
	}

	public function displaySingle($map, $value, $type = '', $root = 0, $delete = false) {

		if(empty($this->nameboxType))
			$this->nameboxType = hikamarket::get('type.namebox');

		return $this->nameboxType->display(
			$map,
			$value,
			hikamarketNameboxType::NAMEBOX_SINGLE,
			'category',
			array(
				'delete' => $delete,
				'root' => $root,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
	}

	public function displayMultiple($map, $values, $type = '', $root = 0) {

		if(empty($this->nameboxType))
			$this->nameboxType = hikamarket::get('type.namebox');

		$first_element = reset($values);
		if(is_object($first_element))
			$values = array_keys($values);

		return $this->nameboxType->display(
			$map,
			$values,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'category',
			array(
				'delete' => true,
				'root' => $root,
				'sort' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
			)
		);
	}

	public function displayTree($id, $root = 0, $type = null, $displayRoot = false, $selectRoot = false) {
		hikamarket::loadJslib('otree');
		if(empty($type))
			$type = array('product','manufacturer','vendor');
		$ret = '';

		$ret .= '<div id="'.$id.'_otree" class="oTree"></div>
<script type="text/javascript">
var options = {rootImg:"'.HIKAMARKET_IMAGES.'otree/", showLoading:false};
var data = '.$this->getData($type, $root, $displayRoot, $selectRoot).';
var '.$id.' = new window.oTree("'.$id.'",options,null,data,false);
'.$id.'.addIcon("world","world.png");
'.$id.'.render(true);
</script>';
		return $ret;
	}

	private function getData($type = 'product', $root = 0, $displayRoot = false, $selectRoot = false) {
		$marketCategory = hikamarket::get('class.category');
		if($root == 1)
			$root = 0;
		$elements = $marketCategory->getList($type, $root, $displayRoot);

		$d = null;
		foreach($elements as $k => $element) {
			if($d !== null && $element->category_depth > ($d + 1)) {
				unset($elements[$k]);
			} else {
				$d = (int)$element->category_depth;
			}
		}

		$ret = '[';
		$cpt = count($elements)-1;
		$sep = '';
		$rootDepth = 0;
		foreach($elements as $k => $element) {
			$next = null;
			if($k < $cpt)
				$next = $elements[$k+1];

			$status = 4;
			if(!empty($next) && $next->category_parent_id == $element->category_id)
				$status = 2;
			if($element->category_type == 'root') {
				$status = 5;
				$rootDepth = (int)$element->category_depth + 1;
			}
			if(($element->category_id == $root) || ($root == 0 && !$displayRoot && $rootDepth == 0))
				$rootDepth = (int)$element->category_depth;

			$ret .= $sep.'{"status":'.$status.',"name":"'.str_replace('"','&quot;',$element->category_name).'"';

			if($element->category_type == 'root') {
				$ret .= ',"icon":"world"';
				if(!$selectRoot)
					$ret .= ',"noselection":1';
				else
					$ret .= ',"value":'.$element->category_id;
			} else {
				$ret .= ',"value":'.$element->category_id;
			}

			$sep = '';
			if(!empty($next)) {
				if($next->category_depth > $element->category_depth && $element->category_type != 'root') {
					$ret .= ',"data":[';
				} else if($next->category_depth < $element->category_depth) {
					$ret .= '}'.str_repeat(']}', $element->category_depth - $next->category_depth);
					$sep = ',';
				} else {
					$ret .= '}';
					$sep = ',';
				}
			} else {
				$ret .= '}';
				if($element->category_depth >= $rootDepth)
					$ret .= str_repeat(']}', $element->category_depth - $rootDepth);
			}
		}
		$ret .= ']';

		return $ret;
	}
}
