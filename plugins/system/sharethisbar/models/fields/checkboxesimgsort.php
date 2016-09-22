<?php

/** 
 * @package ShareThisBar Plugin for Joomla! 2.5
 * @subpackage Form Field Checkboxesimgsort
 * @version $Id: sharethisbar.php 3.5 2012-12-29 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2012 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
**/

defined('JPATH_PLATFORM') or die;

/**
 * JFormFieldCheckboxesimgsort form field class for the ShareThisBar plugin.
 * Displays options as a list of check boxes with images which are sortable(mouse dragged).
 * Based on element checkboxes.
 * @author Dusanka Ilic
 * @extension  ShareThisBar Plugin for Joomla! 2.5
 * @since         3.5
 */
class JFormFieldCheckboxesimgsort extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Checkboxesimgsort';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 */
	protected $forceMultiple = true;
	
	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = ($this->element['class']) ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
		
		// Get the field options.
		$options = $this->getOptions();
		
		// Build the checkbox field output. 
		$html[] = '<ul  id="stbul">';
		
		if (is_array($options)) {

		foreach ($options as $i => $option)
		{
		
		 // Initialize some option attributes.
			$checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
			$class = (!empty($option->class)) ? ' class="' . $option->class . '"' : '';
			$disabled = (!empty($option->disable)) ? ' disabled="disabled"' : '';
			
			// img html element
			$simg = (!empty($option->simg)) ? '<img style="" src="'.$option->simg.'" alt="'.$option->simgalt.'" title="'.$option->simgtitle.'"> ' : '';
			
			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
		
		        $html[] = '<li style="display:block;height:45px">';
			$html[] = '<input style="height:36px" type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';

			if ($option->showlabel) {
			  $html[] = '<label  style="height:36px;position:relative;top:10px" for="' . $this->id . $i . '"' . '>' . JText::_($option->text) . '</label>';
			}
			$html[] = $simg;
			$html[] = '</li>';
		
		}   // foreach
				
		$html[] = '</ul>';

		// End the checkbox field output.
		$html[] = '</fieldset>';
		
		// Sorting script - mootools - sort element options with mouse movement.
		$html[] = '<script type="text/javascript">
    
                window.addEvent("domready", function() {
		
		new Sortables("fieldset ul#stbul", {
                  clone: true,
                  revert: true,
                  opacity: 0.7
                });
		
		}); ';   
                
		$html[] = ' </script> ';

		return implode($html);
		
		} else {
		
		return "";
		
		}  // is_array($options)
		
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 */
	protected function getOptions()
	{
	
            // Initialize variables.
	    $options = array();
			
	    // Get values of attr. @value from all element's child <option /> elements which have attr. value - from xml file.  
	    $opts = $this->element->xpath('option[@value]/@value');
		
	    //  Cast the object to a string with strval( ) - in this case all JXMLEelement objects contained in array $opts.
	    //  $optsVals = Array ( [0] => fb [1] => tw [2] => li ) 
	    $optsVals = array_map('strval', $opts ? $opts : array());  

	    // Sorted checked options from params.
	    if (isset($this->value) && is_array($this->value)) {
	      $nizChecked = $this->value;
	    } 
	 
	    // Array will contain all options for the element - contains JXMLElements which will be displayed in admin form. 
	    $optsIncluded = array();
	 
	     // Only if there are checked options.
	     if ($nizChecked) {
		
		  foreach($nizChecked as $elChecked)
		  {
			
		      // Give me those JXMLElement objects(<options />) from xml file which has attr. value=$elChecked. 
		      $optsIncluded = array_merge($optsIncluded, $this->element->xpath('option[@value="'.$elChecked.'"]') );
									
		   }
		
	      }

	      // $optsVals contains attr. value from all <option /> elements from xml file. 
	      // $nizChecked contains only checked values.  If $nizChecked does not exist - nothnig is checked.
	      // $diffArr array will contain rest of <options /> elements not contained in $nizChecked  or all <options /> elements from xml file.

	      // Only if there are checked options.
	      if ($nizChecked) {
  	        $diffArr = array_diff($optsVals, $nizChecked);
	       } else {
		 $diffArr = $optsVals;
	       }
		
		// Add the rest of <options /> element from xml file which are not checked to the array $optsIncluded.
		foreach($diffArr as $elUnchecked)
		{
		  $optsIncluded = array_merge($optsIncluded, $this->element->xpath('option[@value="'.$elUnchecked.'"]') ); 	 
		}
		
	      if (count($optsIncluded) > 0) {
		
		foreach ($optsIncluded as $option)
		{

			// Create a new option object based on the <option /> element. 
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			
			// img attribute.
			$tmp->simg = (string) $option['simg'];
			
			// img alt attribute.
			$tmp->simgalt = (string) $option['simgalt'];
			
			// img title attribute.
			$tmp->simgtitle = (string) $option['simgtitle'];
			
			// to show or not show element <label />.
			$tmp->showlabel = (int) $option['showlabel'];
			
			// Add the option object to the result set.
			$options[] = $tmp;
			
		}  // foreach
		
		reset($options);
		
		return $options;
		
	} else {
		 
		 return true;
		 
	}   // optsChecked
	
	}  // getOptions
	
} // class
