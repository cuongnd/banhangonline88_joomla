<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');
jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class AdsmanagerControllerMails extends TController
{
	var $_view = null;
	var $_model = null;
	
	function __construct($config= array()) {
		parent::__construct($config);
	
		// Apply, Save & New
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}
	
	function init()
	{
		// Set the default view name from the Request
		$this->_view = $this->getView("admin",'html');

		// Push a model into the view
		$this->_model = $this->getModel("mail");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
		
		$confmodel	  = $this->getModel("configuration");
		$this->_view->setModel( $confmodel );
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();
		$this->_view->setLayout("listmails");
		$this->_view->display();
	}
	
	function edit()
	{
		$this->init();
		$this->_view->setLayout("editmail");
		$this->_view->display();
	}
	
	function add()
	{
		$this->init();
		$this->_view->setLayout("editmail");
		$this->_view->display();
	}
	
	function save()
	{
		$app = JFactory::getApplication();
		
		$mail = JTable::getInstance('mail', 'AdsmanagerTable');
        
        $post = JRequest::get( 'post',JREQUEST_ALLOWHTML );
        
		// bind it to the table
		if (!$mail -> bind($post)) {
			return JError::raiseWarning( 500, $mail->getError() );
		}
		// store it in the db
		if (!$mail -> store()) {
			return JError::raiseWarning( 500, $mail->getError() );
		}	
		
		$model = $this->getModel("configuration");
		$conf = $model->getConfiguration();
		
		cleanAdsManagerCache();
	
		// Redirect the user and adjust session state based on the chosen task.
		$task = JRequest::getCmd('task');
		switch ($task)
		{
			case 'apply':
				$app->redirect( 'index.php?option=com_adsmanager&c=mails&task=edit&id='.$mail->id, JText::_('ADSMANAGER_MAIL_SAVED'),'message' );
				break;
		
			case 'save2new':
				$app->redirect( 'index.php?option=com_adsmanager&c=mails&task=add', JText::_('ADSMANAGER_MAIL_SAVED'),'message' );
				break;
		
			default:
				$app->redirect( 'index.php?option=com_adsmanager&c=mails', JText::_('ADSMANAGER_MAIL_SAVED'),'message' );
			break;
		}
		
	}
	
	function remove()
	{
		$app = JFactory::getApplication();

		
		$mail = JTable::getInstance('mail', 'AdsmanagerTable');
		
		$ids = JRequest::getVar( 'cid', array(0));
		if (!is_array($ids)) {
			$table = array();
			$table[0] = $ids;
			$ids = $table;
		}
		
		foreach($ids as $id){
			$mail->deleteContent($id);
		}
		
		cleanAdsManagerCache();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=mails', JText::_('ADSMANAGER_MAIL_REMOVED'),'message' );
	}
	
    function send() {
        $id = JRequest::getInt( 'id', 0);
        
        $app = JFactory::getApplication();
        
        if(!$id)
            $app->redirect('index.php?option=com_adsmanager&c=mails', JText::_('ADSMANAGER_MAIL_SEND_NO_MAIL'), 'error');
        
        $model = $this->getModel("mail");
        $mail = $model->getMail($id);
        
        if (version_compare(JVERSION,'2.5.0','>=')) {
            // Get a JMail instance
            $mailer = JFactory::getMailer();
            //$mail->sendMail("support@juloa.com", "support@juloa.com", "support@juloa.com","je fais un test", "je fais un test", 1);
            $mailer->sendMail($mail->from, $mail->fromname, $mail->recipient, $mail->subject, $mail->body, 1, null, null, null, null, null);
        } else {
            JUtility::sendMail($mail->from, $mail->fromname, $mail->recipient, $mail->subject, $mail->body, 1, null, null, null, null, null);
        }
        
        $mailTable = JTable::getInstance('mail', 'AdsmanagerTable');
        $mail->statut = 1;
        
        if (!$mailTable -> bind($mail)) {
			return JError::raiseWarning( 500, $mailTable->getError() );
		}
		// store it in the db
		if (!$mailTable -> store()) {
			return JError::raiseWarning( 500, $mailTable->getError() );
		}	
        
        $app->redirect('index.php?option=com_adsmanager&c=mails', JText::_('ADSMANAGER_MAIL_SENT'), 'message');
    }
}
