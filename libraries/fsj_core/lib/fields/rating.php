<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Handle ratings 
 * 
 * THIS SHOULD NOT BE IN HERE
 **/

class FSJ_Rating {
	
	static function AdminDisplay($id, $rating, $size = 16, $styles = '')
	{
		$width = 5 * $size;
		
		$ratingpct = $rating / 500;
		
		$ratingwidth = $width * $ratingpct;
		$graywidth = $width - $ratingwidth;
		$html = array();

		$html[] = "<div id='$id' style=\"position:relative; width: {$width}px; height: {$size}px;{$styles}\">";
		
		$html[] = "<div id='{$id}_active' style=\"position: absolute; left: 0px; top:0px; width: {$ratingwidth}px; height: {$size}px; background-image: url('../libraries/fsj_core/assets/images/plugins/rating/star_{$size}.png');\">";
		$html[] = "</div>";
		
		$html[] = "<div id='{$id}_inactive' style=\"position: absolute; left: {$ratingwidth}px; top:0px; width: {$graywidth}px; height: {$size}px; background-position: right top; background-image: url('../libraries/fsj_core/assets/images/plugins/rating/star_gray_{$size}.png');\">";
		$html[] = "</div>";
		
		$html[] = "</div>";

		return implode($html);
	}
	
	static $url_output = false;
	
	static function Display($id, $rating, $can_vote, $component, $item, $include_div = true)
	{
		FSJ_Page::Script("libraries/fsj_core/assets/js/field/field.rating.js");

		$rating_round = round($rating / 100, 0);
		if ($rating > 0)
		{
			$rating_display = sprintf("%01.1f",round($rating / 100, 1));
		} else {
			$rating_display = '';
		}
		
		if ($include_div)
			$output[] = "<div class='fsj_rating' component='$component' item='$item' itemid='$id'>";
		
		$output[] = $rating_display . "&nbsp;";
		$output[] = "<a class='fsjTip' href='#' onclick='return false;' title='". JText::_('FSJ_FAQS_CLICK_TO_RATE')."'>";
		
		$class = "can_rate";
		if (!$can_vote)
			$class = "";
		
		for ($i = 0 ; $i < $rating_round ; $i++)
		{
			$rate = $i+1;
			$output[] = "<span class='icon-star lit rating $class' rating='$rate'></span>";	
		}
		
		for ($i = $rating_round ; $i < 5 ; $i++)
		{
			$rate = $i+1;
			$output[] = "<span class='icon-star unlit rating $class' rating='$rate'></span>";	
		}
		
		$output[] = "</a>";
		
		if ($include_div)
			$output[] = "</div>";
		
		if (!self::$url_output)
		{
			self::$url_output = true;
			$output[] = "<div id='fsj_rating_url' style='display:none;'>" . JRoute::_('index.php?option=com_fsj_main&controller=rating&task=rate') . "</div>";
			$output[] = "<div id='fsj_rating_wait' style='display:none;'>" . JText::_('FSJ_PLEASE_WAIT') . "</div>";
		}	
		return implode($output);
	}
}