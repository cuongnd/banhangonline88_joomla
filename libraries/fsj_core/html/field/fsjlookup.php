<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
jimport('fsj_core.lib.utils.general');
class JFormFieldFSJLookup extends JFormFieldList
{
	protected $type = 'FSJLookup';

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$res = parent::setup($element, $value, $group);
		
		if ((string)$this->element['load_lang'])
		{
			$language = JFactory::getLanguage();
			$language->load((string)$this->element['load_lang']);
		}
		
		return $res;
	}

	protected function getOptions()
	{
		// Initialise variables.
		$lu_table = (string)$this->element['lu_table'];
		$lu_field = (string)$this->element['lu_field'];
		$lu_display = (string)$this->element['lu_display'];
		$lu_nested = (int)$this->element['lu_nested'];
		$lu_sql = $this->element['lu_sql'];
		$use_state = (int)$this->element['use_state'];
		$use_set = (string)$this->element['use_set'];
		$prod_set = (int)$this->element['prod_set'];
		
		$default_sql = (string)$this->element['default_sql'];

		$db = JFactory::getDbo();
		
		if ($default_sql && $this->value == "")
		{
			$cur_value = $this->form->getValue($this->fieldname);
			if ($cur_value == "")
			{
				$db->setQuery($default_sql);
				$item = $db->loadObject();
				$this->form->setValue($this->fieldname, $this->group, $item->id);
				$this->value = $item->id;
			}
		}
		
		$incl_js = $this->element['incl_js'];
		
		if ($incl_js)
		{
			$document = JFactory::getDocument();
			FSJ_Page::Script($incl_js); 
		}
	
		if ($lu_sql && trim($lu_sql) != "")
		{
			$query = $lu_sql;
			$query = FSJ_Helper::ParseDataFields($query, $this->form);
			
		} else {
			
			$options = array();
			if ($use_state)
			{
				$state = $this->element['state'] != "" ? $this->element['state'] : array(0,1);
			} else {
				$state = 0;	
			}
			$name = (string) $this->element['name'];
			
			// Let's get the id for the current item, either category or content item.
			$jinput = JFactory::getApplication()->input;
			
			$query	= $db->getQuery(true);

			$fields = array();
			$fields[] = "a." . $lu_field . " AS value";
			$fields[] = "a." . $lu_display . " AS text";
			if ($lu_nested) $fields[] = "a.level";
			if ($use_state) $fields[] = "a.state";

			$set_group = $use_set;
			if ($set_group == "1")
				$set_group = "";

			//echo "Checking Set : $use_set, $set_group<br>";

			if ($use_set && $this->form->getValue('set_id', $set_group) > 0)
			{
				//$set_id = $this->form->state->get('filter.set_id');
				//echo "Set : " . $this->form->getValue('set_id', $set_group) . "<br>";
				if ($lu_nested)
				{
					$query->where(" (set_id = " . $db->escape($this->form->getValue('set_id', $set_group)) . " OR parent_id = 0 )");
				} else {
					$query->where("set_id = " . $db->escape($this->form->getValue('set_id', $set_group)));
				}
			}

			if ($lu_nested)
			{
				$query->where("level > 0");	
			}

			$query->select(implode(", ", $fields));
			$query->from($lu_table . ' AS a');

			// Filter on the published state
			if ($use_state)
			{
				if (is_numeric($state))
				{
					$query->where('a.state = ' . (int) $state);
				}
				elseif (is_array($state))
				{
					JArrayHelper::toInteger($state);
					$query->where('a.state IN (' . implode(',', $state) . ')');
				}
			}

			$group = array();
			$group[] = "a." . $lu_field;
			$group[] = "a." . $lu_display;
			if ($lu_nested)
			{
				$group[] = "a.level";
				$group[] = "a.lft";
				$group[] = "a.rgt";
				$group[] = "a.parent_id";
			}
			if ($use_state) $group[] = "a.state";
			

			$query->group(implode(", ", $group));
			
			if ($lu_nested)
			{
				$query->order('a.lft ASC');
			}
		}
		
		//echo "Lookup Qry : " . $query . "<br>";
		// Get the options.
		$db->setQuery($query);
		
		$options = $db->loadObjectList();
		
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.	
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			
			if ($lu_nested) // if nested add - prefixes and [] to non published items
			{
				// Translate ROOT
				if ($options[$i]->level == 0)
				{
					$options[$i]->text = JText::_('FSJ_UNCATEGORISED');
				}

				if (!$use_state || $options[$i]->state == 1)
				{
					$options[$i]->text = str_repeat('- ', $options[$i]->level). $options[$i]->text ;
				}
				else
				{
					$options[$i]->text = str_repeat('- ', $options[$i]->level). '[' .$options[$i]->text . ']';
				}
			} else { // non nested add [] to non published items
				if ($use_state && $options[$i]->state != 1)
				{
					$options[$i]->text = '[' .$options[$i]->text . ']';
				}
			}
		}
		
		// PERMISSIONS STUFF - SORT LATER
		// Get the current user object.
		/*$user = JFactory::getUser();

		// For new items we want a list of categories you are allowed to create in.
		if ($oldCat == 0)
		{
			foreach ($options as $i => $option)
			{
				// To take save or create in a category you need to have create rights for that category
				// unless the item is already in that category.
				// Unset the option if the user isn't authorised for it. In this field assets are always categories.
				if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true )
				{
					unset($options[$i]);
				}
			}
		}
		// If you have an existing category id things are more complex.
		else
		{
			// If you are only allowed to edit in this category but not edit.state, you should not get any
			// option to change the category parent for a category or the category for a content item,
			// but you should be able to save in that category.
			foreach ($options as $i => $option)
			{
				if ($user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true && !isset($oldParent))
				{
					if ($option->value != $oldCat  )
					{
						unset($options[$i]);
					}
				}
				if ($user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true
					&& (isset($oldParent)) && $option->value != $oldParent)
				{
					unset($options[$i]);
				}

				// However, if you can edit.state you can also move this to another category for which you have
				// create permission and you should also still be able to save in the current category.
				if (($user->authorise('core.create', $extension . '.category.' . $option->value) != true)
					&& ($option->value != $oldCat && !isset($oldParent)))
				{
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create', $extension . '.category.' . $option->value) != true)
					&& (isset($oldParent)) && $option->value != $oldParent)
				{
					{
						unset($options[$i]);
					}
				}
			}
		}*/
		
		// something to do with parents, uses the $row variable created earlier
		if ($lu_nested && $this->element['parent'] == true && isset($row) && !isset($options[0]) && isset($this->element['show_root']))
		{
			if ($row->parent_id == '1') {
				$parent = new stdClass();
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
		}

		if (!is_array($options))
			$options = array();
		
		if ($this->element['useglobal'] && !$this->element['disable_global'])
		{
			FSJ_Lang_Helper::Load_Library('fsj_core');
			$global_opt = new stdClass();
			$global_opt->value = "";
			$global_opt->text = JText::_("FSJ_FORM_USE_GLOBAL");
			$options = array_merge(array($global_opt), $options);
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
	
	public function AdminDisplay($value, $name, $item)
	{
		$field = $this->lookup->fieldalias;
		$value = $item->$field;
		
		if ($this->lookup->nested && $value == "ROOT")
			$value = JText::_('FSJ_UNCATEGORISED');
		
		if ($this->lookup->jtext)
			$value = JText::_($value);
		
		if ($this->lookup->tmpl)
		{
			$item->template = $value;
			return $this->ParseTmpl($item);	
		} else {
			return $value;	
		}
	}
	
	function ParseTmpl($item)
	{
		// parse template and remove tags that are there, and %FIELD% tags too
		$tmpl = $this->lookup->tmpl;
		
		$tmpl = FSJ_Helper::ParseDataFields($tmpl, $item);
		
		/*if (preg_match_all("/%([a-zA-Z_]+)%/", $tmpl, $matches))
		{
			foreach ($matches[0] as $offset => $search)
			{
				$field = strtolower($matches[1][$offset]);
				if (property_exists($item, $field))
				{
					$replace = $item->$field;
					$tmpl = str_replace($search, $replace, $tmpl);	
				}
			}
		}*/
		
		$tmpl = str_replace("{imgbase}", JURI::root(), $tmpl);
		
		if (preg_match_all("/{url:([a-zA-Z0-9\?\.\=\&\;\_\-]+)}/", $tmpl, $matches))
		{
			foreach ($matches[0] as $offset => $search)
			{
				$link = $matches[1][$offset];
				$replace = JRoute::_($link);
				$tmpl = str_replace($search, $replace, $tmpl);	
			}
		}
		
		return $tmpl;
	}
}
