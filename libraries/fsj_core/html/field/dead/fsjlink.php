<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJLink extends JFormField
{
	protected $type = 'FSJCount';

	static $counts = array();
	
	protected function getInput()
	{
		return "";
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$link = $this->fsjlink->link;
		
		if (preg_match_all("/%([A-Z]+)%/", $link, $matches))
		{
			foreach ($matches[1] as $match)
			{
				$lcmatch = strtolower($match);
				if (property_exists($item, $lcmatch))
				{
					$link = str_replace("%{$match}%", $item->$lcmatch, $link);
				}
			}
		}	
		$link = JRoute::_($link);
		
		$image = "<img style='position:relative;top:3px;' src='" . JURI::root() . $this->fsjlink->image . "'>&nbsp;";
		
		if (isset($this->fsjlink->class))
		{
			return "<a href='$link' class='{$this->fsjlink->class}'>" . $image . $this->fsjlink->text . "</a>";		
		} else {
			return "<a href='$link'>" . $image . $this->fsjlink->text . "</a>";		
		}
	}
}
