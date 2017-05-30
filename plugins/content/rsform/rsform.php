<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class plgContentRSForm extends JPlugin
{
	var $_deleteCache;
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentRSForm( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
	
	function canRun()
	{
		if (class_exists('RSFormProHelper')) return true;
		
		$helper = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
		if (file_exists($helper))
		{
			require_once($helper);
			return true;
		}
		
		return false;
	}
	
	function onContentBeforeDisplay($context, &$article, &$params, $limitstart=0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		
		if (isset($article->text))
			$this->onPrepareContent($article, $params, $limitstart);
	}
	
	function onContentPrepare($context, &$article, &$params, $limitstart=0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		
		if (isset($article->text))
			$this->onPrepareContent($article, $params, $limitstart);
	}
	
	function onAfterDispatch()
	{
		if (!$this->canRun()) return true;
		
		$app 	 = JFactory::getApplication();
		$cache   = JFactory::getCache('com_content','view');
		$caching = $app->getCfg('caching');
		if ($caching)
			$cache->setCaching(true);
	}
	
	function onPrepareContent($article, $params, $limitstart=0)
	{
		$mainframe = JFactory::getApplication();
		
		$option = JRequest::getVar('option');
		$task 	= JRequest::getVar('task');
		if ($option == 'com_content' && $task == 'edit')
			return true;
		
		if (strpos($article->text, '{rsform ') === false)
			return true;
		
		if (!$this->canRun()) return true;
		
		// expression to search for
		$pattern = '#\{rsform ([0-9]+)\}#i';
		if (preg_match_all($pattern, $article->text, $matches))
		{
			$doc = JFactory::getDocument();
			if ($doc->getType() != 'html') {
				$article->text = preg_replace($pattern, '', $article->text);
				return true;
			}
			
			// 2.5
			if (RSFormProHelper::isJ16())
			{
				$cache = JFactory::getCache('com_content','view');
				$cache->setCaching(false);
			}
			// 1.5
			else
				$this->_deleteCache = true;
			
			$lang = JFactory::getLanguage();
			$lang->load('com_rsform', JPATH_SITE);
			
			foreach ($matches[0] as $i => $match)
			{
				$formId = $matches[1][$i];
				$article->text = str_replace($matches[0][$i], RSFormProHelper::displayForm($formId,true), $article->text);
			}
		}
		
		return true;
	}
}