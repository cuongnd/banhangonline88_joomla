<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TTools {
	
	/**
	 * This function will redirect the current page to the joomla login page
	 * @param URL $returnurl, after login redirect to this url
	 */
	static function redirectToLogin($returnurl="") {
		$app = JFactory::getApplication();
		$returnurl = base64_encode(TRoute::_($returnurl,false));
		if (COMMUNITY_BUILDER == 1) {
			$app->redirect(JRoute::_("index.php?option=com_comprofiler&task=registers"));
		} else {
		if(version_compare(JVERSION,'1.6.0','>=')){
			//joomla 1.6 format
                $app->redirect( JRoute::_("index.php?option=com_users&view=login&return=$returnurl",false));
		} else {
			//joomla 1.5 format
                $app->redirect( JRoute::_("index.php?option=com_user&view=login&return=$returnurl",false));
            }
		}
	}
	
    static function print_popup($url)
	{
		$url .= '&tmpl=component&print=1';
	
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
	
		// checks template image directory for image, if non found default are loaded
		$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
	
		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']		= 'nofollow';
	
		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}
	
	static function print_screen()
	{
		// checks template image directory for image, if non found default are loaded
		$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a><script>jQ(function() {window.print();});</script>';
	}
    
	static function getCatImageUrl($catid,$thumb=false) {
		$extensions = array("jpg","png","gif");
		$image_name = ($thumb == true) ? "cat_t":"cat";
		
		foreach($extensions as $ext) {
			if (file_exists(JPATH_ROOT."/images/com_adsmanager/categories/".$catid."$image_name.$ext"))
				return JURI::root()."images/com_adsmanager/categories/".$catid."$image_name.$ext";
		}
		return JURI::root().'components/com_adsmanager/images/default.gif';
	}
    
    static function loadModule($module, $title, $style = 'none')
	{
        $mods[$module] = '';
        $document	= JFactory::getDocument();
        $renderer	= $document->loadRenderer('module');
        $mod		= JModuleHelper::getModule($module, $title);
        // If the module without the mod_ isn't found, try it with mod_.
        // This allows people to enter it either way in the content
        if (!isset($mod)){
            $name = 'mod_'.$module;
            $mod  = JModuleHelper::getModule($name, $title);
        }
        $params = array('style' => $style);
        ob_start();

        echo $renderer->render($mod, $params);

        $mods[$module] = ob_get_clean();
            
		return $mods[$module];
	}
	
	/**
	 * This method transliterates a string into an URL
	 * safe string or returns a URL safe UTF-8 string
	 * based on the global configuration
	 *
	 * @param   string  $string  String to process
	 * @param   boolean $forcetransliterate set to true to force transliterate
	 * 
	 * @return  string  Processed string
	 *
	 * @since   11.1
	 */
	static public function stringURLSafe($string,$unicodesupport=false)
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			if ($unicodesupport == false && JFactory::getConfig()->get('unicodeslugs') == 1)
			{
				$output = JFilterOutput::stringURLUnicodeSlug($string);
			}
			else
			{
				$output = JFilterOutput::stringURLSafe($string);
			}
		} else {
			$output = JFilterOutput::stringURLSafe($string);
		}
	
		return $output;
	}
	
	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ending if the text is longer than length.
	 *
	 * @param string $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 * @return string Trimmed string.
	 */
	static function truncate($text, $length = 100, $ending = '[...]', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
	
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
	
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
	
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an “empty element'' with or without xhtml-conform closing slash (f.e.)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag (f.e. )
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag (f.e. )
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
	
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
	
					// the number of characters which are left
					$left = $length - $total_length;
	
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
	
				// if the maximum length is reached, get off the loop
				if($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
	
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
	
		// add the defined ending to the text
		$truncate .= $ending;
	
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '';
			}
		}
	
		return $truncate;
	}
	
	
}