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

class fsj_transmanViewPickFiles extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.popup.js');
		
		$this->client = JRequest::getVar('client');
		$this->existing = explode(";", JRequest::getVar('existing'));
		$this->files = FSJ_TM_File_Helper::GetFiles(2);
	
		parent::display();
	}
}
