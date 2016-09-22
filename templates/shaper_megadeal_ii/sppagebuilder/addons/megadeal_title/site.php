<?php
/**
* @package SP Page Builder
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2016 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

AddonParser::addAddon('sp_megadeal_title','sp_megadeal_title_addon');

function sp_megadeal_title_addon($atts){

	extract(spAddonAtts(array(
		"title" 				=> '',
		"icon" 					=> '',
		"icon_color" 			=> '',
		"class"					=> '',
		), $atts));

	if($title) {
		$output  = '<div class="sppb-addon-megadeal-title clearfix">';

		if($icon) {
			$style = ($icon_color) ? ' style="color: '. $icon_color .'"' : ''; 
			$output .= '<i class="fa ' . $icon . ' pull-left"' . $style . '></i>';
		}

		$output .= '<h3 class="sppb-addon-title pull-left">' . $title . '</h3>';

		$output .= '</div>';

		return $output;
	}
}