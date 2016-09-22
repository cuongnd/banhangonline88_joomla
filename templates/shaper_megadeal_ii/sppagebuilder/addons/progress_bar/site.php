<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

AddonParser::addAddon('sp_progress_bar','sp_progress_bar_addon');

function sp_progress_bar_addon($atts, $content){

	extract(spAddonAtts(array(
		"type" 		=> '',
		"progress" 	=> '',
		"text" 		=> '',
		"stripped"	=>'',
		"active"	=>'',
		"class"		=>''
		), $atts));

	$output  = '<div class="sppb-progress ' . $class . '">';
	$output .= '<div class="sppb-progress-bar ' . $type . ' ' . $stripped . ' ' . $active . '" role="progressbar" aria-valuenow="' . (int) $progress . '" aria-valuemin="0" aria-valuemax="100" data-width="' . (int) $progress . '%">';	
	$output .= '</div>';
	$output .= '</div>';
	if( $text || $progress ) {
		$output .= '<div class="sppb-progress-wrap">';
			$output .= '<p class="pull-left">'.$text.'</p>';
			$output .= '<p class="pull-right">'.$progress.'%</p>';
			$output .= '<div class="clearfix"></div>';
		$output .= '</div>';
	}	
	return $output;
	
}