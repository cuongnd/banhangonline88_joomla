<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'fsj_core.lib.fields.rating');

class FSJ_MainControllerRating extends JControllerLegacy
{
	
	public function rate()
	{
		$component = JRequest::getCmd('component');
		$item = JRequest::getCmd('item');
		
		$itemid = JRequest::getInt('itemid');
		$rating = JRequest::getInt('rating');
		
		$xml_file = JPATH_SITE.DS.'components'.DS.'com_fsj_'.$component.DS.'plugins'.DS.'rating'.DS.$item.'.xml';
		if (!file_exists($xml_file))
			exit;
		
		$xml = simplexml_load_file($xml_file);
		
		if (!$xml)
			exit;
		
		$table = (string)$xml->table;
		
		$db = JFactory::getDBO();
		
		$qry = "UPDATE $table SET rating_votes = rating_votes + 1, rating_score = rating_score + $rating WHERE id = $itemid";
		$db->setQuery($qry);
		$db->Query();
		
		$qry = "UPDATE $table SET rating = (rating_score / (rating_votes * 5)) * 500 WHERE id = $itemid";
		$db->setQuery($qry);
		$db->Query();
		
		$qry = "SELECT rating FROM $table WHERE id = $itemid";
		$db->setQuery($qry);
		$rating = $db->loadObject();
		
		FSJ_Rating::$url_output = true;
		echo FSJ_Rating::Display($itemid, $rating->rating, false, $component, $item, false);	
		echo "<div class='fsj_thanks'>";
		echo JText::_('FSJ_THANKS_FOR_FEEDBACK');
		echo "</div>";
		exit;
	}
}