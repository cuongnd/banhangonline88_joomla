<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class fsj_transmanControllerfiles extends JControllerAdmin
{
	protected $text_prefix = 'FSJ_TRANSMAN_FILE';

	public function __construct($config = array())
	{
		parent::__construct($config);

		//$this->registerTask('unfeatured',	'featured');
		
		
		$this->createLanguageCache();
	}
	
	function createLanguageCache()
	{
		$conf = JFactory::getConfig();
		$locale = $conf->get('language');
		
		$path = JPATH_BASE . DS . "cache" . DS . 'fsj_lang' . DS . 'language'.  DS . $locale;
		if (!file_exists($path))
			mkdir($path,0755,true);
		
		$language = JFactory::getLanguage();
		$file = $path . DS . $locale . '.fsj_transman_file.ini';
		
		if (!file_exists($file))
		{	
			$d = $language->getDebug();
			$language->setDebug(false);
	
			$itemname = JText::_('COM_fsj_transman_ITEMS_transman_file');
			$itemnamemulti = JText::_('COM_fsj_transman_ITEMS_transman_fileS');
		
			$keys[] = $this->createLangString('fsj_transman_file_NO_ITEM_SELECTED','FSJ_IMB_NO_ITEM_SELECTED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_ARCHIVED','FSJ_IMB_N_ITEMS_ARCHIVED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_ARCHIVED_1','FSJ_IMB_N_ITEMS_ARCHIVED', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_CHECKED_IN_0','FSJ_IMB_N_ITEMS_CHECKED_IN_0', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_CHECKED_IN_1','FSJ_IMB_N_ITEMS_CHECKED_IN_1', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_CHECKED_IN_MORE','FSJ_IMB_N_ITEMS_CHECKED_IN_1', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_DELETED','FSJ_IMB_N_ITEMS_DELETED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_DELETED_1','FSJ_IMB_N_ITEMS_DELETED', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_PUBLISHED','FSJ_IMB_N_ITEMS_PUBLISHED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_PUBLISHED_1','FSJ_IMB_N_ITEMS_PUBLISHED', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_TRASHED','FSJ_IMB_N_ITEMS_TRASHED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_TRASHED_1','FSJ_IMB_N_ITEMS_TRASHED', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_UNPUBLISHED','FSJ_IMB_N_ITEMS_UNPUBLISHED', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_UNPUBLISHED_1','FSJ_IMB_N_ITEMS_UNPUBLISHED', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_SET_DEFAULT','FSJ_IMB_N_ITEMS_SET_DEFAULT', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_SET_DEFAULT_1','FSJ_IMB_N_ITEMS_SET_DEFAULT', $itemname);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_UNSET_DEFAULT','FSJ_IMB_N_ITEMS_UNSET_DEFAULT', $itemnamemulti);
			$keys[] = $this->createLangString('fsj_transman_file_N_ITEMS_UNSET_DEFAULT_1','FSJ_IMB_N_ITEMS_UNSET_DEFAULT', $itemname);
		
			$text = implode("\n", $keys);
			file_put_contents($file, $text);
			$language->setDebug($d);
		}
		
		$language->load('fsj_transman_file', JPATH_BASE . DS . "cache" . DS . 'fsj_lang');
	}
	
	function createLangString($dest, $base, $item)
	{
		$text = strtoupper($dest) . '="';
		$text .= str_replace('$', '%', JText::sprintf($base, $item));
		$text .= '"';
		return $text;
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'file', $prefix = 'fsj_transmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	
		

	public function saveorder()
	{
		$return = parent::saveorder();
		
		return $return;
	}

	public function reorder()
	{
		$return = parent::reorder();
		
		return $return;
	}

	public function delete()
	{
		$return = parent::delete();
		
		return $return;
	}
	
	public function publish()
	{
		$return = parent::publish();
		
		return $return;
	}
	
		
      
      function unpub()
      {
          require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');
          $ids = JRequest::getVar('cid', array(), 'array');
          foreach ($ids as $filename)
          {
              FSJ_TM_File_Helper::unpublish($filename);
          }
          $this->setRedirect(JRoute::_('index.php?option=com_fsj_transman&view=files', false));
      } 
      function pub()
      {
          require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');
          $ids = JRequest::getVar('cid', array(), 'array');
          foreach ($ids as $filename)
          {
              FSJ_TM_File_Helper::publish($filename);
          }
          if (count(FSJ_TM_File_Helper::$error) > 0)
              $this->setMessage(implode("<br />", FSJ_TM_File_Helper::$error), "error");
          $this->setRedirect(JRoute::_('index.php?option=com_fsj_transman&view=files', false));
      }
      function createbase()
      {
          require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');
          $ids = JRequest::getVar('cid', array(), 'array');
          foreach ($ids as $filename)
          {
              FSJ_TM_File_Helper::createbase($filename);  
          }
          if (count(FSJ_TM_File_Helper::$error) > 0)
              $this->setMessage(implode("<br />", FSJ_TM_File_Helper::$error), "error");
          $this->setRedirect(JRoute::_('index.php?option=com_fsj_transman&view=files', false));
     }
      
    	
}


