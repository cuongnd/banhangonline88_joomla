<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.settings');

class FSJ_AdminHelper
{
	static function GetTemplates()
	{
		$db = JFactory::getDBO();
		
		$qry = "SELECT concat(component, \".\", type) as id, component, type, xmlfile, title, description FROM #__fsj_tpl_type";
		$db->setQuery($qry);
		
		return $db->loadObjectList("id");
	}
		
	static function Item($title, $link, $com, $icon, $help)
	{
		if (strtoupper($title) == $title) // If we are all uppercase, needs translating
			$title = JText::_($title);
		?>
		<li>
			<a href="<?php echo JRoute::_($link); ?>">
				<img src="<?php echo JURI::root( true ); ?>/administrator/components/<?php echo $com; ?>/assets/images/<?php echo $icon;?>-48.png" width="24" height="24">
				<?php echo $title; ?>
			</a>
		</li>
		<?php
	}
	
	static function GetOverview($name)
	{	
		$file = JPATH_ADMINISTRATOR.DS.'components'.DS."com_{$name}".DS.'helpers'.DS.'overview.php'; 
		$overview = null;

		if (file_exists($file))
		{
			require_once($file);
			$class = "{$name}_overview";
			if (class_exists($class))
				$overview = new $class();
		}
		
		return $overview;
	}
}