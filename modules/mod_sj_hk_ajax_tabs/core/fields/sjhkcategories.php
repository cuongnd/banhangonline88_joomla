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

jimport('joomla.form.formfield');

if (!class_exists('JFormFieldSjHkCategories')) {
	class JFormFieldSjHkCategories extends JFormField
	{
		protected $type = 'sjhkcategories';

		public function getInput()
		{
			$html = array();
			if ($this->com_hikiashop_installed()) {
				$html[] = $this->getInputHtml();
			} else {
				$html[] = "<div style='clear:both; margin: 0 0 0 150px; font-weight:bold; color:red;'>";
				$html[] = "There are no data table for Zoo.<br>";
				$html[] = "If you have HikaShop component installed.<br>";
				$html[] = "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
				$html[] = "Thank you";
				$html[] = "</div>";
			}
			return implode("\n", $html);
		}

		protected function getInputHtml()
		{
			if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
			if (!include_once(rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS . 'helper.php')) {
				echo 'This module can not work without the Hikashop Component';
				return;
			};
			$_categories = array();
			$categories = array();
			$_children = array();
			$class = hikashop_get('class.category');
			$database = JFactory::getDBO();
			//$query = 'SELECT * FROM '.hikashop_table('category').' as c ORDER BY c.category_left ASC';
			$query = 'SELECT * FROM ' . hikashop_table('category') . ' as c WHERE c.category_type <> "root" ORDER BY c.category_left ASC';
			$database->setQuery($query);
			$categories = $database->loadObjectList();

			$select_all = isset($this->element['selectall']) && $this->element['selectall'] == 'true';
			$is_multiple = isset($this->element['multiple']) && $this->element['multiple'] == 'multiple';
			if (count($categories) > 0) {
				foreach ($categories as $i => $category) {
					$_categories[$category->category_id] = $categories[$i];
				}

				foreach ($categories as $i => $category) {
					$cid = $category->category_id;
					$pid = $category->category_parent_id;
					if (isset($_categories[$pid])) {
						$_categories[$pid]->child[$cid] = $category;
					}
				}

				if (!is_array($this->value)) {
					$this->value = array($this->value);
				}

				$select_attr = "";
				if (isset($this->element['multiple'])) {
					$select_attr .= " multiple=\"multiple\"";
					$size = $this->element['size'] ? (int)$this->element['size'] : 15;
					$select_attr .= " size=\"$size\"";
				}
				if (isset($this->element['css'])) {
					$select_attr .= ' class="' . trim($this->element['css']) . '"';;
				} else {
					$select_attr .= ' class="inputbox"';;
				}
				//	var_dump($_categories);die;
				$html = "<select $select_attr id=\"" . $this->id . '" name="' . $this->name . '">';
				foreach ($_categories as $j => $category) {
					$pid = $category->category_parent_id;
					if (!isset($_categories[$pid])) {
						$_categories[$j]->level = 1;
						$stack = array($_categories[$j]);
						while (count($stack) > 0) {
							$opt = array_pop($stack);
							$option = array(
								'label' => ($opt->level > 1 ? str_repeat(' - - | ', $opt->level - 1) : ' - ') . ucwords($opt->category_name),
								'value' => $opt->category_id
							);
							$selected = in_array($opt->category_id, $this->value) ? 'selected="selected" ' : ' ';
							$html .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
							if (isset($opt->child) && count($opt->child)) {
								foreach (array_reverse($opt->child) as $child) {
									$child->level = $opt->level + 1;
									array_push($stack, $child);
								}

							}
						}
					}
				}
				$html .= '</select>';
			} else {
				$html = "<div style='clear:both; margin: 0 0 0 150px; font-weight:bold; color:red;'>";
				$html .= "Problem on reading <b>{$db->getPrefix()}hikashop_category</b><br>";
				$html .= "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
				$html .= "Thank you";
				$html .= "</div>";
			}
			return $html;
		}

		protected function com_hikiashop_installed()
		{
			$db = JFactory::getDbo();
			$prefix = $db->getPrefix();
			$tables = $db->getTableList();
			return in_array($prefix . 'hikashop_category', $tables);
		}
	}
}