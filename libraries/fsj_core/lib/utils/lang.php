<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Class for dealing with language files and loading them etc
 */
if (!class_exists("FSJ_Lang_Helper"))
{
	class FSJ_Lang_Helper
	{
		static function Load_Component($comp)
		{
			$language = JFactory::getLanguage();
			if ($language->load($comp))
				return true;
			
			if ($language->load($comp, JPATH_BASE.DS."components".DS.$comp))
				return true;
			
			return false;	
		}
		
		static function Load_Library($lib)
		{
			$language = JFactory::getLanguage();
			$language->load("lib_".$lib);
		}
		
		static function Load_All_Menu_Langs()
		{
			$qry = "SELECT * FROM #__fsj_plg_plugin WHERE type = 'mainmenu'";
			$db = JFactory::getDBO();
			$db->setQuery($qry);
			$items = $db->loadObjectList();
			
			if (is_array($items))
			{
				foreach ($items as &$item)
				{
					$params = json_decode($item->params, true);
					$com = $params['com'];
					FSJ_Lang_Helper::Load_Component($com);
				}
			}
		}	
		
		static function LoadAdminLangs()
		{
			$qry = "SELECT * FROM #__extensions WHERE  `name` LIKE '%com_fsj_%'";
			$db = JFactory::getDBO();
			$db->setQuery($qry);
			$items = $db->loadObjectList();
			
			if (is_array($items))
			{
				foreach ($items as &$item)
				{
					//echo "Load lang : {$item->name}<br>";
					FSJ_Lang_Helper::Load_Component($item->name.".sys");
				}
			}		
		}
		
		public static function isEnabled()
		{
			static $enabled = null;

			if (!isset($enabled)) {
				$app = JFactory::getApplication();

				if ($app->isSite()) {
					$enabled = $app->getLanguageFilter();
				} else {
					$db = JFactory::getDBO();

					$query = $db->getQuery(true);
					$query->select('enabled');
					$query->from($db->quoteName('#__extensions'));
					$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
					$query->where($db->quoteName('folder') . ' = ' . $db->quote('system'));
					$query->where($db->quoteName('element') . ' = ' . $db->quote('languagefilter'));
					$db->setQuery($query);

					$enabled = $db->loadResult();
				}

				JFactory::getConfig()->set('multilingual_support', $enabled);

				if ($enabled) {
					$enabled = JCommentsFactory::getConfig()->get('multilingual_support', $enabled);
				}
			}

			return $enabled;
		}

		public static function getLanguage()
		{
			static $language = null;

			if (!isset($language)) {
				$language = JFactory::getLanguage()->getTag();
			}

			return $language;
		}

		public static function getLanguages()
		{
			// TODO: JoomFish support
			return JLanguageHelper::getLanguages();
		}
	}
}