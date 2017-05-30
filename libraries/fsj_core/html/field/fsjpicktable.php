<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJPickTable extends JFormField
{
	protected $type = 'FSJPickTable';
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function __get($name)
	{
		$res = parent::__get($name);
		
		if ($res)
			return $res;
		
		return $this->$name;		
	}

	protected function getInput()
	{
		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function Add'.$this->id.'Items(table, ids, titles) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = ids[0];';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = titles[0];';

		if ($allowClear)
		{
			$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		}

		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Clear button script
		static $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function fsj_Clear_PickTable(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars(JText::_($this->element['pt_title'], true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		//$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=fsj_picktable_'.$this->id;

		$link = 'index.php?option=com_fsj_main&amp;task=picktable.display&amp;tmpl=component&amp;table=' . $this->element['pt_table'] . '&amp;com=' . $this->element['pt_com'] . '&amp;id=' . $this->id;// . '&amp;function=fsj_picktable_'.$this->id;

		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage='.$this->element['language'];
		}

		$db	= JFactory::getDbo();
		$db->setQuery(
			'SELECT title' .
			' FROM ' . $this->element['pt_dbtable'] .
			' WHERE id = '.(int) $this->value
			);

		try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		if (empty($title))
		{
			$title = JText::_((string)$this->element['pt_title']);
		}
		
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active article id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// The current article display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn hasTooltip" href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';

		// Clear article button
		if ($allowClear)
		{
			$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return fsj_Clear_PickTable(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		}

		$html[] = '</span>';

		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		/*if (isset($this->fsjstring->tmpl) && $this->fsjstring->tmpl)
			return FSJ_Helper::ParseDataFields($this->fsjstring->tmpl, $item);*/

		return $value;	
	}
}
