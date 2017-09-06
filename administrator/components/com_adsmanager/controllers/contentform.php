<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class AdsmanagerControllerContentform extends TController
{
	function init()
	{
		// Set the default view name from the Request
		$this->_view = $this->getView("admin",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "position");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();
		
		$fieldmodel	  = $this->getModel("field");
		$this->_view->setModel( $fieldmodel );
		
		$this->_view->setLayout("contentform");
		$this->_view->display();
	}
	
	function save() 
	{
		$app = JFactory::getApplication();
		
		$model = $this->getModel("position");
		
		$model->cleanFieldsByPosition('edit');
		
		$positions = $model->getPositions('edit');
		foreach($positions as $key => $position) {
			$model->setPosition($position->id,
								JRequest::getVar('title_position_'.$position->id),
								JRequest::getVar('listfields_'.$position->id),
								"edit");
		}
		
		$app->redirect( 'index.php?option=com_adsmanager&c=contentform', JText::_('ADSMANAGER_CONTENT_FORM_UPDATED') ,'message');
	}
}
