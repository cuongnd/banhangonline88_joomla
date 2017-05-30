<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/fsj_transman.php';


class fsj_transmanModelpackage extends JModelAdmin
{
	protected $text_prefix = 'fsj_transman_package';

	protected function canDelete($record)
	{
		$user = JFactory::getUser();
		return $user->authorise('core.delete', 'com_fsj_transman');
	}

	protected function canEditState($record)
	{
		return parent::canEditState('com_fsj_transman');
	}

	protected function prepareTable($table)
	{
		$db = $this->getDbo();



		// Reorder the articles within the category so the new article is first
		/*if (empty($table->id)) {
			$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
		}*/
	}

	public function getTable($type = 'package', $prefix = 'JTablefsj_transman', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {


																		
		}
		

		
      
      // convert old style file lists to the new style ones.
      // when saving, the fields will be cleared
      require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');
      if (!function_exists("addFiles"))
      {
        function addFiles($item, $files, $key)
        {
          $addfiles = explode(";", $files);
          $files = json_decode($item->files, true);
          if (!is_array($files)) $files = array();
          $path_files = FSJ_TM_File_Helper::GetFiles(false, $item->langcode, $key);
          foreach ($addfiles as $file)
          {
            $file = trim($file);
            if ($file == "") continue;
            $file = str_ireplace($item->langcode.".", "", $file);
            if (array_key_exists($file, $path_files))
            {
              $file_data = $path_files[$file];
              //print_p($file_data);
              if (!array_key_exists($key, $files))
                $files[$key] = array();
              $filecat = $file_data->category;
              if ($filecat == "")
                $filecat = "xxx-none-xxx";
              if (!array_key_exists($filecat, $files[$key]))
                $files[$key][$filecat] = array();
              $files[$key][$filecat][] = $file;
            }
          }
          $item->files = json_encode($files);
        }
      }
      if ($item->sitefiles != "")
        addFiles($item, $item->sitefiles, "0|g.general");
      if ($item->adminfiles != "")
        addFiles($item, $item->adminfiles, "1|g.general");
      $item->sitefiles = "";
      $item->adminfiles = "";
      // rebuild category list for all files on loading
      $files = json_decode($item->files, true);
      if (!is_array($files)) $files = array();
      $target = array();
      foreach ($files as $key => $cats)
      {
        $path_files = FSJ_TM_File_Helper::GetFiles(false, $item->langcode, $key);
        foreach ($cats as $cat => $files)
        {
          foreach ($files as $file)
          {
            $cat = "";
            if (array_key_exists($file, $path_files))
            {
              $cat = $path_files[$file]->category;
            }
            if ($cat == "")
              $cat = "xxx-none-xxx";
            $target[$key][$cat][] = $file;
          }
        }
        ksort($target[$key]);
        foreach ($target[$key] as $cat => $files)
        {
          sort($target[$key][$cat]);
        }
      }
      $item->files = json_encode($target);
      
    	
		return $item;
	}

	public function getForm($data = array(), $loadData = true)
	{
	
		// Get the form.
		$form = $this->loadForm('fsj_transman.package', 'package', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id =  $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id =  $jinput->get('id', 0);
		}

		// Determine correct permissions to check.
		/*if ($this->getState('article.id'))
		{
			$id = $this->getState('article.id');
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}*/

		$user = JFactory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		/*if ($id != 0 && (!$user->authorise('core.edit.state', 'com_fsj_transman.article.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_fsj_transman'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}*/

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_fsj_transman.edit.package.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('package.id') == 0) {
				$app = JFactory::getApplication();
				
				// need to load the state of any filters in
				$data->set('langcode', JRequest::getInt('langcode', $app->getUserState('com_fsj_transman.packages.filter.langcode')));

																						// if set_id and no value is found, then we need to lookup the default one from the table
			}
		}

		return $data;
	}

	public function save($data)
	{
		$db = JFactory::getDBO();


																																																																																												
		
      
      // clean out old file lists when saving as they should have been converted to the new style one
      $data['sitefiles'] = '';
      $data['adminfiles'] = '';
      
    
/* PARENT SAVE CODE */
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}


			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}


			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}



			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);
		
/* END PARENT SAVE CODE */

																																																																																												


		return true;
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
				return $condition;
	}

	protected function cleanCache($group = null, $client_id = 0)
	{
		/*parent::cleanCache('com_fsj_transman');
		parent::cleanCache('mod_articles_archive');
		parent::cleanCache('mod_articles_categories');
		parent::cleanCache('mod_articles_category');
		parent::cleanCache('mod_articles_latest');
		parent::cleanCache('mod_articles_news');
		parent::cleanCache('mod_articles_popular');*/
	}
	
	public function canSave($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}
	



	static function allowEdit($faq)
	{
		$user     = JFactory::getUser();
		$userId   = $user->get('id');
		$asset    = 'com_fsj_transman.transman_package.' . $faq->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			// If the owner matches 'me' then do the test.
			if ($faq->created_by == $userId)
			{
				return true;
			}
		}

		return false;
	}

	static function allowState($faq)
	{
		$user     = JFactory::getUser();
		$userId   = $user->get('id');
		$asset    = 'com_fsj_transman.transman_package.' . $faq->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit.state', $asset))
		{
			return true;
		}

		return false;
	}
}
