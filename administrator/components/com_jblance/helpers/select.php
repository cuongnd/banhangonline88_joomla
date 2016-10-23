<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	12 March 2012
 * @file name	:	helpers/select.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
 defined('_JEXEC') or die('Restricted access');

class SelectHelper {
	
	function getSelectLocationTree($var, $default, $oldId, $title = 'JGLOBAL_ROOT_PARENT'){
		
		// Let's get the id for the current item, either category or content item.
		$app	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);
		
		// Load the category options for a given extension.
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.title AS text, a.level, a.published, a.lft');
		$subQuery = $db->getQuery(true)
			->select('id,title,level,published,parent_id,extension,lft,rgt')
			->from('#__jblance_location');
		$subQuery->where('(extension = ' . $db->quote('') . ' OR parent_id = 0)');
		$subQuery->where('published = 1');
		$query->from('(' . $subQuery->__toString() . ') AS a')
			->join('LEFT', $db->quoteName('#__jblance_location') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->order('a.lft ASC');
		
		if($oldId != 0){
			// Prevent parenting to children of this item.
			// To rearrange parents and children move the children up, not the parents down.
			$query->join('LEFT', $db->quoteName('#__jblance_location') . ' AS p ON p.id = ' . (int) $oldId)
				->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		
			$rowQuery = $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id')
				->from('#__jblance_location AS a')
				->where('a.id = ' . (int) $oldId);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}
		
		// Get the options.
		$db->setQuery($query);//echo $query;
		
		try {
			$options = $db->loadObjectList();
		}
		catch(RuntimeException $e){
			JError::raiseWarning(500, $e->getMessage());
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for($i = 0, $n = count($options); $i < $n; $i++){
			// Translate ROOT
			if($options[$i]->level == 0){
				$options[$i]->text = JText::_($title);
			}
		
			if($options[$i]->published == 1){
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			}
			else {
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		}
		
		$lists 	= JHtml::_('select.genericlist', $options, $var, '', 'value', 'text', $default);
		return $lists;
	}
	
	function getSelectLocationCascade($var, $default, $title, $attribs, $idTag){
		$db	= JFactory::getDbo();
		
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = 'class="input-large" size="1"';
		
		$query = 'SELECT id AS value, title AS text FROM `#__jblance_location` WHERE published=1 AND level=1 ORDER BY lft';
		$db->setQuery($query);
		$locs = $db->loadObjectList();
		
		$types[] = JHtml::_('select.option', '', '- '.JText::_($title).' -');
		foreach($locs as $item){
			$types[] = JHtml::_('select.option', $item->value, JText::_($item->text));
		}
		
		$lists 	= JHtml::_('select.genericlist', $types, $var, $attribs, 'value', 'text', $default, $idTag);
		return $lists;
	}
	
	function getSelectCategoryTree($var, $default, $title, $attribs, $event = '', $group = false, $rootOnly = false){
		$db	= JFactory::getDbo();
	
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = "class='inputbox' size='10'";
	
		$query = 'SELECT * FROM #__jblance_category WHERE parent=0 AND published=1 ORDER BY ordering';
		$db->setQuery($query);
		$categs = $db->loadObjectList();
	
		if(!empty($title))
			$types[] = JHtml::_('select.option', '', '-- '.JText::_($title).' --');
	
		if(!$group){
			foreach($categs as $categ) {
				$indent = '';
				$types[] = JHtml::_('select.option', $categ->id, $categ->category);
			
				if(!$rootOnly){
					$subs = $this->getSubcategories($categ->id, $indent, '', 0);
					foreach($subs as $sub) {
						$types[] = JHtml::_('select.option', $sub->id, $sub->category);
					}
				}
			}
		}
		else {
			foreach($categs as $categ) {
				$indent = '';
				$types[] = JHtml::_('select.optgroup', $categ->category);	// Start group:
			
				$subs = $this->getSubcategories($categ->id, $indent, '', 0);
				foreach($subs as $sub) {
					$types[] = JHtml::_('select.option', $sub->id, $sub->category);
				}
				$types[] = JHtml::_('select.optgroup', $categ->category);	// Finish group:
			}
		}
		
		$lists 	= JHtml::_('select.genericlist', $types, $var, "$attribs $event", 'value', 'text', $default);
		return $lists;
	}
	
	function getCheckCategoryTree($var, $default, $attribs){
		$db	= JFactory::getDbo();
		$i = 0;
		$query = 'SELECT * FROM #__jblance_category WHERE parent=0 AND published=1 ORDER BY ordering';
		$db->setQuery($query);
		$categs = $db->loadObjectList();
	
		echo JHtml::_('bootstrap.startAccordion', 'categoryOptions', array('active' => 'collapse0'));
		foreach($categs as $categ) {
			echo JHtml::_('bootstrap.addSlide', 'categoryOptions', JText::_($categ->category), 'collapse'.$i++);
			$indent = '';
	
			$subs = $this->getSubcategories($categ->id, $indent, '', 0);
			echo '<div class="row-fluid" style="padding: 0 15px;"><div class="span12">';
			foreach($subs as $sub) {
				echo self::checkboxlist($sub, $default, $var);
			}
			
			echo '</div></div>';
			echo JHtml::_('bootstrap.endSlide');
		}
		echo JHtml::_('bootstrap.endAccordion');
	}
	
	/*custom checkbox*/
	function checkboxlist($categ, $default, $var){
		$html = '';
	
		$checked = (in_array($categ->id, $default)) ? 'checked' : '';
		$html .= "<div class=\"test\"><div class=\"span4\"><label for=\"id_category_$categ->id\" class=\"checkbox\">";
		$html .= "<input type=\"checkbox\" name=\"$var\" id=\"id_category_$categ->id\" value=\"$categ->id\" class='jb-checkboxes' $checked>";
		$html .= $categ->category;
		$html .= "</label></div></div>";
		$html .= "\n";
		return $html;
	}	
	
	// list subcats as tree
	function getSubcategories($parent, $indent, $init, $type = 1){
		$db =JFactory::getDbo();
		
		if($init)
			$tree = $init;
		else
			$tree = array();
	
		$db->setQuery("SELECT * FROM #__jblance_category WHERE parent =".$parent." ORDER BY ordering");
		$rows = $db->loadObjectList();
		
		foreach($rows as $v){
			if($type){
				$pre 	= '<span class="gi">|&mdash;</span>';
				$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			else {
				$pre 	= '- ';
				$spacer = '.  ';
			}
			$v->category = $indent.$pre.$v->category;
			$tree[] = $v;
			$tree = $this->getSubcategories($v->id, $indent.$spacer, $tree, $type);
		}
		return $tree;
	}
	
	function getSelectPrivacy($var, $default, $visible, $field_for){
		
		if($field_for == 'profile'){
			if($visible == 'all'){
				$put[] = JHtml::_('select.option',  0, JText::_('COM_JBLANCE_PUBLIC'));
				$put[] = JHtml::_('select.option',  10, JText::_('COM_JBLANCE_SITE_MEMBERS'));
				$put[] = JHtml::_('select.option',  20, JText::_('COM_JBLANCE_ONLY_ME'));
				$privacy = JHtml::_('select.genericlist', $put, $var, 'class=input-medium', 'value', 'text', $default);
			}
			elseif($visible == 'personal'){
				$privacy = '<input type="hidden" id="'.$var.'" name="'.$var.'" value="20" /><span class="label label-info">'.JText::_('COM_JBLANCE_ONLY_ME').'</span>';
			}
		}
		elseif($field_for == 'project'){
			$privacy = '<input type="hidden" id="'.$var.'" name="'.$var.'" value="0" />';
		}
		
		return $privacy;
	}
	
	//1.YesNo - boolean
	function YesNoBool($name, $value = null){
		
		$arr[] = JHtml::_('select.option', '1', JText::_('JYES'));
		$arr[] = JHtml::_('select.option', '0', JText::_('JNO'));
		
		$attribs = array('class'=>'radio btn-group btn-group-yesno');
		
		$list = self::radiolist($arr, $name, $attribs, 'value', 'text', (int) $value);
		
		return $list;
	}
	
	public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,	$translate = false){
		$html = array();
	
		if(is_array($attribs)){
			$attribs = JArrayHelper::toString($attribs);
		}
		
		$id_text = $idtag ? $idtag : $name;
	
		// Start the radio field output.
		$html[] = '<fieldset id="' . $name . '"' . $attribs . ' >';
	
		// Build the radio field output.
		foreach ($data as $i => $option){
			$id = (isset($option->id) ? $option->id : null);
			$id = $id ? $option->id : $id_text . $i;
			$id = str_replace(array('[', ']'), '', $id);
			
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $selected ? ' checked="checked" ' : '');
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
	
			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';
	
			$html[] = '<input type="radio" id="' . $id . '" name="' . $name . '" value="'
			. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . ' />';
	
			$html[] = '<label for="' . $id . '"' . $class . ' >'
			. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $option->text)) . '</label>';
		}
	
		// End the radio field output.
		$html[] = '</fieldset>';
	
		return implode($html);
	}
	
	//20.getSelectUserGroups
	function getSelectUserGroups($var, $default, $title, $attribs, $event){
		$db	= JFactory::getDbo();
	
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = 'class="input-large" size="1"';
	
		$query = 'SELECT id AS value, name AS text FROM `#__jblance_usergroup` WHERE published=1 ORDER BY ordering';
		$db->setQuery($query);
		$groups = $db->loadObjectList();
	
		$types[] = JHtml::_('select.option', '', '- '.JText::_($title).' -');
		foreach($groups as $item){
			$types[] = JHtml::_('select.option', $item->value, JText::_($item->text));
		}
	
		$lists 	= JHtml::_('select.genericlist', $types, $var, "$attribs $event", 'value', 'text', $default);
		return $lists;
	}
	
	//14.getSearchPhrase
	function getRadioSearchPhrase($var, $default){
	
		$searchphrases 	 = array();
		$searchphrases[] = JHtml::_('select.option', 'any', JText::_('COM_JBLANCE_ANY_WORDS'));
		$searchphrases[] = JHtml::_('select.option', 'all', JText::_('COM_JBLANCE_ALL_WORDS'));
		$searchphrases[] = JHtml::_('select.option', 'exact', JText::_('COM_JBLANCE_EXACT_PHRASE'));
		//$lists = JHtml::_('select.radiolist',  $searchphrases, $var, '', 'value', 'text', $default);
		$attribs = array('class'=>'radio btn-group');
		$lists = self::radiolist($searchphrases, $var, $attribs, 'value', 'text', $default);
	
		return $lists;
	}
	
	//16.getCheckJobCategory
	function getCheckCategory($default){
		$app = JFactory::getApplication();
		$db	= JFactory::getDbo();
		$doc = JFactory::getDocument();
	
		$query = 'SELECT * FROM #__jblance_category WHERE parent=0 AND published=1 ORDER BY ordering';
		$db->setQuery($query);
		$categs = $db->loadObjectList();
	
		$html = "<div class='project_search_category'>";
		foreach($categs as $categ){
			$indent = '';
			$checked = (in_array($categ->id, $default)) ? 'checked' : '';
			$html .= "\n\t<label class='checkbox boldfont'><input type='checkbox' onclick=\"checkUncheck(this, 'cat')\" class='cat-parent-$categ->parent' alt='$categ->id' id='category_$categ->id' name='id_categ[]' value='$categ->id' $checked>&nbsp;$categ->category</label>";
			
			$subs = $this->getSubcategories($categ->id, $indent, '', 0);
			
			foreach($subs as $sub) {
				$checked = (in_array($sub->id, $default)) ? 'checked' : '';
				$html .= "\n\t<label class='checkbox'><input type='checkbox' onclick=\"checkUncheck(this, 'cat')\" class='cat-parent-$sub->parent' alt='$sub->id' id='category_$sub->id' name='id_categ[]' value='$sub->id' $checked>&nbsp;$sub->category</label>";
				
			}
		}
		$html .="</div>";
	
		return $html;
	}
	
	function getSelectProjectStatus($var, $default, $title, $attribs, $event){
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = "class='inputbox' size='1'";
		
		$types[] = JHtml::_('select.option', 'any', JText::_($title));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_OPEN', JText::_('COM_JBLANCE_OPEN'));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_FROZEN', JText::_('COM_JBLANCE_FROZEN'));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_CLOSED', JText::_('COM_JBLANCE_CLOSED'));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_EXPIRED', JText::_('COM_JBLANCE_EXPIRED'));
	
		$lists 	 = JHtml::_('select.genericlist', $types, $var, "$attribs $event", 'value', 'text', $default);
		return $lists;
	}

	function getSelectBudgetRange($var, $default, $title, $attribs, $event, $project_type = 'COM_JBLANCE_FIXED'){
		$db	= JFactory::getDbo();
		
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = "class='input-large' size='1'";
		
		$query = "SELECT id, CONCAT_WS('-', budgetmin, budgetmax) AS value, title, budgetmin, budgetmax FROM `#__jblance_budget` ".
				 "WHERE published=1 AND project_type=".$db->quote($project_type)."ORDER BY ordering";
		$db->setQuery($query);
		$budgets = $db->loadObjectList();
		
		$types[] = JHtml::_('select.option', '', '- '.JText::_($title).' -');
		foreach($budgets as $item){
			$perhr = ($project_type == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HOUR') : '';
			$types[] = JHtml::_('select.option', $item->value, sprintf("%s (%s - %s) $perhr", $item->title, JblanceHelper::formatCurrency($item->budgetmin, true, false, 0), JblanceHelper::formatCurrency($item->budgetmax, true, false, 0)));
		}
		
		$lists 	= JHtml::_('select.genericlist', $types, $var, "$attribs $event", 'value', 'text', $default);
		return $lists;
	}
	
	function getSelectProjectDuration($var, $default, $title, $attribs, $event){
		$db	= JFactory::getDbo();
		
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = "class='inputbox' size='1'";
		
		$query = "SELECT * FROM `#__jblance_duration` WHERE published=1 ORDER BY ordering";
		$db->setQuery($query);
		$budgets = $db->loadObjectList();
		
		$types[] = JHtml::_('select.option', '', '- '.JText::_($title).' -');
		foreach($budgets as $item){
			$types[] = JHtml::_('select.option', $item->id, JblanceHelper::formatProjectDuration($item->duration_from, $item->duration_from_type, $item->duration_to, $item->duration_to_type, $item->less_great));
		}
		
		$lists 	= JHtml::_('select.genericlist', $types, $var, "$attribs $event", 'value', 'text', $default);
		return $lists;
	}
	
	//14.getSearchPhrase
	function getRadioProjectType($var, $default){
	
		$searchphrases 	 = array();
		$searchphrases[] = JHtml::_('select.option', 'COM_JBLANCE_FIXED', JText::_('COM_JBLANCE_FIXED'));
		$searchphrases[] = JHtml::_('select.option', 'COM_JBLANCE_HOURLY', JText::_('COM_JBLANCE_HOURLY'));
		
		$attribs = array('class'=>'radio btn-group');
		$lists = self::radiolist($searchphrases, $var, $attribs, 'value', 'text', $default);
	
		return $lists;
	}
	
	function getSelectProgressStatus($var, $default, $title, $attribs){
		//if attribs is empty, then set a default one.
		if(empty($attribs))
			$attribs = "class='input-medium' size='1'";
	
		$types[] = JHtml::_('select.option', '', '- '.JText::_($title).' -');
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_INITIATED', JText::_('COM_JBLANCE_INITIATED'));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_IN_PROGRESS', JText::_('COM_JBLANCE_IN_PROGRESS'));
		$types[] = JHtml::_('select.option', 'COM_JBLANCE_COMPLETED', JText::_('COM_JBLANCE_COMPLETED'));
	
		$lists 	 = JHtml::_('select.genericlist', $types, $var, "$attribs", 'value', 'text', $default);
		return $lists;
	}

}