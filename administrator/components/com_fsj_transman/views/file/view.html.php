<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'trans_helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'general_helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');

class fsj_transmanViewfile extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		FSJ_Settings::LoadBaseSettings("com_fsj_transman");
		
		$file = JRequest::getVar('file');
		$file = FSJ_TM_Helper::SanitizeFilename($file);
		
		//FSJ_Page::IncludeModal();
		
		list($this->client, $this->file, $this->tag, $this->component) = FSJ_TM_Helper::ParseFileName($file);

		$this->basetag = FSJ_TM_Helper::GetBaseLanguage();

		// load in base file
		$this->loadBaseFile();
		
		if (!$this->strings)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage("Unable to load language file {$this->file}", "error");
			$app->redirect(JRoute::_('index.php?option=com_fsj_transman&view=files', false));
			return;	
		}
		// load in translated file
		$this->loadTranslatedFile();
		
		//print_p($this->strings);
		
		// add toolbar to display
		$this->addToolbar();
		
		$this->setLayout("edit");
	
		FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.less");
		$document = JFactory::getDocument();
		$document->addScript( JURI::root().'administrator/components/com_fsj_transman/assets/js/translate.js' );
		$document->addScript( JURI::root() .'libraries/fsj_core/assets/js/jquery/jquery.textarea.resize.js');
				
		if (FSJ_Helper::IsJ3())
		{
			//if (count($this->strings->lines) < 100)
				JHtml::_('bootstrap.tooltip');
		} else {
			FSJ_Page::Style("libraries/fsj_core/assets/css/bootstrap/bootstrap_fsjonly.less");
			FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.j25.less");
		}

		// display
		parent::display();
	}
	
	protected function loadBaseFile()
	{
		$base_file = $this->file;
		$base_file = str_replace($this->tag, $this->basetag, $base_file);
		
		$this->strings = FSJ_TM_Trans_Helper::GetBaseFile($base_file, $this->client, $this->tag, $this->component);
	}
	
	protected function loadTranslatedFile()
	{
		$this->current_file = FSJ_TM_Trans_Helper::AddLangFile($this->strings, $this->file, $this->tag, $this->client, $this->component);
		$this->current_file = str_replace(JPATH_ROOT, "", $this->current_file);
		$this->current_file = trim($this->current_file, "/\\");
	}

	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
	
		$text = JText::_('FSJ_EDIT') . " " . JText::_('COM_fsj_transman_ITEMS_transman_file');
		
		$this->main_section_text = $text;
		
				
		$icon = 'file';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-48.png); }";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		JToolBarHelper::title( JText::_('COM_FSJ_TRANSMAN_SHORT' ).': ' . $text, $icon);
		
		// Built the actions for new and existing records.
		JToolBarHelper::apply('file.apply');
		JToolBarHelper::save('file.save');
		if (!$this->strings->published)	
		{
			JToolBarHelper::custom('file.publish', 'publish', 'publish', 'FSJ_TM_SAVE__PUBLISH', false);
			JToolBarHelper::custom('file.publishclose', 'publishclose', 'publishclose', 'FSJ_TM_SAVE_CLOSE_PUBLISH', false);
		}
		JToolBarHelper::cancel('file.cancel');
	}
}
