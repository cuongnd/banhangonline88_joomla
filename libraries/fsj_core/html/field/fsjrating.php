<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport('fsj_core.lib.fields.rating');

class JFormFieldFSJRating extends JFormFieldText
{
	protected $type = 'FSJRating';

	protected function getInput()
	{
		$document = JFactory::getDocument();
		//print_p($this);
		
		$rating = $this->form->getValue('rating');
		$rating_votes = $this->form->getValue('rating_votes');
		$rating_score = $this->form->getValue('rating_score');
	
		FSJ_Page::Script("libraries/fsj_core/assets/js/jquery/jquery.slider.js");
		FSJ_Page::Style("libraries/fsj_core/assets/css/jquery/jquery.slider.less");
		
		$rating_obj = new FSJ_Rating();
		
		$output = array();
		$output[] = "<div>";

		$output[] = "	<div>";
		$output[] = "		" . $rating_obj->AdminDisplay("fsj_rating", $rating, 24, 'float: left;');
		$output[] = "		&nbsp;<span id='fsj_rating_value' style='font-weight: bold'>" . $rating / 100 . "</span> from <span id='fsj_rating_votes'  style='font-weight: bold'>" . $rating_votes . "</span> votes";
		$output[] = "		<a class='btn' href='#' onclick='jQuery(\"#fsj_rating_edit\").toggle();'>";
		$output[] = "			Edit";
		$output[] = "		</a>";
		$output[] = "	</div>";
		
		// Edit form:
		$output[] = "	<div id='fsj_rating_edit' class='form-horizontal form-condensed' style='display:none;margin-top:8px;'>";
		
		$output[] = '		<div class="control-group">';
		$output[] = '			<label class="control-label">';
		$output[] = "				Vote Count:";
		$output[] = '			</label>';
		$output[] = '			<div class="controls" style="width:350px">';
		$output[] =	'				<input type="text" name="jform[rating_votes]" id="jform_rating_votes" value="' . htmlspecialchars($rating_votes, ENT_COMPAT, 'UTF-8') . '"/>';
		$output[] = "			</div>";
		$output[] = "		</div>";
		
		$output[] = '		<div class="control-group">';
		$output[] = '			<label class="control-label">';
		$output[] = "				Rating:";
		$output[] = '			</label>';
		$output[] = '			<div class="controls">';
		$output[] = '				<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($rating, ENT_COMPAT, 'UTF-8') . '" data-slider="true" data-slider-range="0,500" data-slider-step="1"/>';
		$output[] = "			</div>";
		$output[] = "		</div>";

		$output[] = "	</div>";
		
		$output[] = '	<input type="hidden" name="jform[rating_score]" id="jform_rating_score" value="' . htmlspecialchars($rating_score, ENT_COMPAT, 'UTF-8') . '"/>';
		$output[] = "</div>";
		
		$js = "
		jQuery(document).ready(function () {
			 jQuery('[data-slider]').bind('slider:ready slider:changed', function (event, data) {
					var size = 24;
					var width = size * 5;
					
					var votes = jQuery('#jform_rating_votes').val();
					if (votes == 0)
					{
						jQuery('#jform_rating_votes').val(1);
						votes = 1;
					}

					var score = Math.round(data.value / 100 * votes);
										
					var rating = Math.round(score / votes * 100);					
					var disprating = score / votes;
					jQuery('#fsj_rating_value').html(disprating.toFixed(2));
					jQuery('#fsj_rating_votes').html(votes);
					jQuery('#jform_rating').val(rating);
					jQuery('#jform_rating_score').val(score);

					var ratingpct = rating / 500;
					var ratwid = parseInt(width * ratingpct);
					var graywid = width - ratwid;
					
					jQuery('#fsj_rating_active').css('width', ratwid + 'px');
					jQuery('#fsj_rating_inactive').css('left', ratwid + 'px');
					jQuery('#fsj_rating_inactive').css('width', graywid + 'px');
				});
		});";
		
		$document->addScriptDeclaration($js);
		
		return implode($output);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$rating_obj = new FSJ_Rating();
	
		return $rating_obj->AdminDisplay("fsj_rating", $value, 16) . "<div style='white-space: nowrap;'>".round($value / 100, 2) . " (" . $item->rating_votes . " votes)</div>";	
	}
}
