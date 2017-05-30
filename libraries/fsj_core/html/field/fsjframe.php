<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJFrame extends JFormField
{
	protected $type = 'FSJFrame';
	
	function __construct()
	{
		static $loaded = false;

		
		if (!$loaded)
		{
			$document = JFactory::getDocument();
			FSJ_Page::Script('libraries/fsj_core/assets/js/jquery/jquery.iframe-auto-height.js');
			FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.js');

			$loaded = true;
		}

		parent::__construct();
	}

	protected function getInput()
	{
		$only = $this->element->attributes()->fsjframe_only_msg;
		$only_if = $this->element->attributes()->fsjframe_only_if;
		$url = $this->element->attributes()->fsjframe_url;
		$url = FSJ_Helper::ParseDataFields($url, $this->form); 
	
		if ($only_if)
		{
			$only_value = $this->form->getValue($only_if);
			
			if (!$only_value)
			{
				$hide_js = "\n\njQuery(document).ready(function () { jQuery('#fsjframe_".$this->fieldname."_wait').hide(); });\n\n";
				$document = JFactory::getDocument();
				$document->addScriptDeclaration($hide_js); 
				return "<div style='padding:10px'><p class='alert alert-info'>" . JText::_($only) . "</p></div>";
			}
		}
		
		return "<iframe class='fsj_iframe fsj_iframe_dl' data-src='". JRoute::_($url) ."' style='width:100%;' scrolling='no' id='fsjframe_" . $this->fieldname . "' name='fsjframe_" . $this->fieldname . "'></iframe>";
	}
}
