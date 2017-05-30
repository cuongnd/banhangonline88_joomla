<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldModal_Partner extends JFormField
{
	protected $type = 'Modal_Partner';

	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '    function jSelectPartner_'.$this->id.'(id, title, catid, object) {';
		$script[] = '        document.id("'.$this->id.'_id").value = id;';
		$script[] = '        document.id("'.$this->id.'_name").value = title;';
		$script[] = '        SqueezeBox.close();';
		$script[] = '    }';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html = array();
		$link = 'index.php?option=com_cmgroupbuying&amp;view=partners&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPartner_'.$this->id;

		$db = JFactory::getDBO();
		$query = 'SELECT name FROM #__cmgroupbuying_partners WHERE id = ' . (int) $this->value;
		$db->setQuery($query);
		$title = $db->loadResult();

		if($error = $db->getErrorMsg())
		{
			JError::raiseWarning(500, $error);
		}

		if(empty($title))
		{
			$title = JText::_('COM_CMGROUPBUYING_PARTNER_SELECT_PARTNER_LABEL');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		if(version_compare(JVERSION, '3.0.0', 'ge')):
			// The current user display field.
			$html[] = '<input class="input input-medium" type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';

			// The user select button.
			$html[] = '<a class="modal btn btn-primary" title="'.JText::_('COM_CMGROUPBUYING_CHANGE_PARTNER_BUTTON').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_CMGROUPBUYING_CHANGE_PARTNER_BUTTON').'</a>';
		else:
			// The current user display field.
			$html[] = '<div class="fltlft">';
			$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
			$html[] = '</div>';

			// The user select button.
			$html[] = '<div class="button2-left">';
			$html[] = '  <div class="blank">';
			$html[] = '    <a class="modal" title="'.JText::_('COM_CMGROUPBUYING_CHANGE_PARTNER_BUTTON').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_CMGROUPBUYING_CHANGE_PARTNER_BUTTON').'</a>';
			$html[] = '  </div>';
			$html[] = '</div>';
		endif;

		// The active partner id field.
		if(0 == (int)$this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';

		if($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}