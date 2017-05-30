<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class BouncesController extends acymailingController{
	var $pkey = 'ruleid';
	var $table = 'rules';
	var $groupMap = '';
	var $groupVal = '';

	function process(){
		if(!$this->isAllowed('configuration','manage')) return;
		acymailing_increasePerf();

		$config = acymailing_config();
		$bounceClass = acymailing_get('helper.bounce');
		$bounceClass->report = true;
		if(!$bounceClass->init()) return;
		if(!$bounceClass->connect()){
			acymailing_display($bounceClass->getErrors(),'error');
			return;
		}
		$disp = "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
		$disp .= '<title>'.addslashes(JText::_('BOUNCE_PROCESS')).'</title>'."\n";
		$disp .= "<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;padding-top:30px;}</style>\n</head>\n<body>";
		echo $disp;

		acymailing_display(JText::sprintf('BOUNCE_CONNECT_SUCC',$config->get('bounce_username')),'success');
		$nbMessages = $bounceClass->getNBMessages();
		acymailing_display(JText::sprintf('NB_MAIL_MAILBOX',$nbMessages),'info');

		if(empty($nbMessages)) exit;

		$bounceClass->handleMessages();
		$bounceClass->close();

		$cronHelper = acymailing_get('helper.cron');
		$cronHelper->messages[] = JText::sprintf('NB_MAIL_MAILBOX',$nbMessages);
		$cronHelper->detailMessages = $bounceClass->messages;
		$cronHelper->saveReport();

		if($config->get('bounce_max',0) != 0 && $nbMessages > $config->get('bounce_max',0)){
			$url = 'index.php?option=com_acymailing&ctrl=bounces&task=process&continuebounce=1&tmpl=component';
			if(JRequest::getInt('continuebounce')){
				echo '<script type="text/javascript" language="javascript">document.location.href=\''.$url.'\';</script>';
			}else{
				echo '<div style="padding:20px;"><a href="'.$url.'">'.JText::_('CLICK_HANDLE_ALL_BOUNCES').'</a></div>';
			}
		}

		echo "</body></html>";
		while($bounceClass->obend-- > 0){
			ob_start();
		}
		exit;

	}

	function store(){
		if(!$this->isAllowed('configuration','manage')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$app = JFactory::getApplication();

		$class = acymailing_get('class.rules');
		$status = $class->saveForm();
		if($status){
			$app->enqueueMessage(JText::_( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					$app->enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function remove(){
		if(!$this->isAllowed('configuration','manage')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$cids = JRequest::getVar( 'cid', array(), '', 'array' );

		$class = acymailing_get('class.rules');
		$num = $class->delete($cids);

		if($num){
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS',$num), 'message');
		}

		JRequest::setVar( 'layout', 'listing'  );
		return parent::display();
	}

	function saveconfig(){
		$this->_saveconfig();
		return $this->listing();
	}

	function _saveconfig(){
		if(!$this->isAllowed('configuration','manage')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$config =& acymailing_config();
		$newConfig = JRequest::getVar( 'config', array(), 'POST', 'array' );

		$newConfig['auto_bounce_next'] = min($config->get('auto_bounce_last',time()),time()) + $newConfig['auto_bounce_frequency'];

		$status = $config->save($newConfig);
		$app = JFactory::getApplication();

		if($status){
			$app->enqueueMessage(JText::_( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
		}

		$config->load();
	}

	function chart(){
		JRequest::setVar( 'layout', 'chart'  );
		return parent::display();

	}

	function test(){

		$this->_saveconfig();

		acymailing_increasePerf();
		$config = acymailing_config();
		$bounceClass = acymailing_get('helper.bounce');
		$bounceClass->report = true;

		$app = JFactory::getApplication();
		if($bounceClass->init()){
			if($bounceClass->connect()){
				$nbMessages = $bounceClass->getNBMessages();
				$app->enqueueMessage(JText::sprintf('BOUNCE_CONNECT_SUCC',$config->get('bounce_username')));
				$app->enqueueMessage(JText::sprintf('NB_MAIL_MAILBOX',$nbMessages));
				$bounceClass->close();
				if(!empty($nbMessages)){
					$app->enqueueMessage('<a class="modal" style="text-decoration:blink" rel="{handler: \'iframe\', size: {x: 640, y: 480}}" href="'.acymailing_completeLink("bounces&task=process",true ).'">'.JText::_('CLICK_BOUNCE').'</a>');
				}
			}else{
				$errors = $bounceClass->getErrors();
				if(!empty($errors)){
					acymailing_display($errors,'error');
					$errorString = implode(' ',$errors);
					$port = $config->get('bounce_port','');
					if(preg_match('#certificate#i',$errorString) && !$config->get('bounce_certif',false)){
						acymailing_display('You may need to turn ON the option <i>'.JText::_('BOUNCE_CERTIF').'</i>','warning');
					}elseif(!empty($port) AND !in_array($port,array('993','143','110'))){
						acymailing_display('Are you sure you selected the right port? You can leave it empty if you do not know what to specify','warning');
					}
				}
			}
		}

		return $this->listing();
	}

	function reinstall(){

		$db = JFactory::getDBO();
		$db->setQuery('TRUNCATE TABLE `#__acymailing_rules`');
		$db->query();

		$updateHelper = acymailing_get('helper.update');
		$updateHelper->installBounceRules();

		return $this->listing();
	}

}
