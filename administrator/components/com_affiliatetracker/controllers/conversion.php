<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - conversions for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

class ConversionsControllerConversion extends ConversionsController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'conversion' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('conversion');

		if ($id = $model->store($post)) {
			$msg = JText::_( 'CONVERSION_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_CONVERSION' );
		}

		$task = JRequest::getCmd( 'task' );
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_affiliatetracker&controller=conversion&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_affiliatetracker&controller=conversions';
				break;
		}
		
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('conversion');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETING_CONVERSIONS' );
		} else {
			$msg = JText::_( 'CONVERSIONS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=conversions', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=conversions', $msg );
	}
	
	function search_account(){
		$mainframe = JFactory::getApplication();
		
		$keywords = JRequest::getVar("searchword");
		
		$db = JFactory::getDBO();
		
		$where_clause[] = ' ( acc.account_name LIKE "%'.$keywords.'%" ) ';
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
		
		$query = ' SELECT acc.* FROM #__affiliate_tracker_accounts as acc ' . $where_clause ;
		$db->setQuery($query);
		$usuaris = $db->loadObjectList();
		
		$return = "";
		
		foreach($usuaris as $u){
			$return .= "<a class='btn'  href='javascript:obtain_account(" .$u->id . ");'>" .$u->account_name . " [".$u->id."]</a> " ;	
		}
		
		echo $return ;
		
		$mainframe->close();
	}
	
	function obtain_account(){
		$mainframe = JFactory::getApplication();
		
		$id = JRequest::getInt("id");
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT acc.* FROM #__affiliate_tracker_accounts as acc WHERE acc.id = ' .$id;
		$db->setQuery($query);
		$usuari = $db->loadObject();
		
		$return = array();
		
		$return[0] = new stdClass();
		$return[0]->key = 'atid' ;
		$return[0]->value = $usuari->id ;
		
		$return[1] = new stdClass();
		$return[1]->key = 'account_name' ;
		$return[1]->value = $usuari->account_name ." [".$usuari->id."]" ;
		
		echo json_encode($return);
		
		$mainframe->close();
	}
	
	function search_user(){
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();
		$keywords = $db->escape(JRequest::getVar("searchword"));
		
		$where_clause[] = ' ( u.username LIKE "%'.$keywords.'%" OR  u.name LIKE "%'.$keywords.'%" ) ';
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
		
		$query = ' SELECT u.* FROM #__users as u ' . $where_clause ;
		$db->setQuery($query);
		$usuaris = $db->loadObjectList();

		$return = "";
		
		foreach($usuaris as $u){
			if (JRequest::getInt("parents")) $return .= "<a class='btn'  href='javascript:obtain_user_parents(" .$u->id . ");'>" .$u->username . " [".$u->id."]</a> " ;
			else $return .= "<a class='btn'  href='javascript:obtain_user(" .$u->id . ");'>" .$u->username . " [".$u->id."]</a> " ;
		}

		echo $return ;

		$mainframe->close();
	}
	
	function obtain_user(){
		$mainframe = JFactory::getApplication();
		
		$id = JRequest::getInt("id");
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT u.* FROM #__users as u WHERE u.id = ' .$id;
		$db->setQuery($query);
		$usuari = $db->loadObject();
		
		$return = array();
		
		$return[0] = new stdClass();
		$return[0]->key = 'user_id' ;
		$return[0]->value = $usuari->id ;
		
		$return[1] = new stdClass();
		$return[1]->key = 'username' ;
		$return[1]->value = $usuari->username ." [".$usuari->id."]" ;
		
		echo json_encode($return);
		
		$mainframe->close();
	}

	function obtain_parent(){
		$mainframe = JFactory::getApplication();

		$id = JRequest::getInt("id");

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query
				->select(array('a.id', 'u.username'))
				->from($db->quoteName('#__affiliate_tracker_accounts', 'a'))
				->join('LEFT', $db->quoteName('#__users', 'u') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('u.id') . ')')
				->where($db->quoteName('u.id') . ' = ' . $db->quote($db->escape($id)));

		$db->setQuery($query);

		$usuari = $db->loadObject();

		$return = array();

		$return[0] = new stdClass();
		$return[0]->key = 'parent_id' ;
		$return[0]->value = $usuari->id ;

		$return[1] = new stdClass();
		$return[1]->key = 'parent_username' ;
		$return[1]->value = $usuari->username ." [".$usuari->id."]" ;

		echo json_encode($return);

		$mainframe->close();
	}
	
	function publish()
	{
		$model = $this->getModel('conversion');
		if(!$model->publish()) {
			$msg = JText::_( 'ERROR_PUBLISHING_CONVERSIONS' );
		} else {
			$msg = JText::_( 'CONVERSIONS_PUBLISHED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=conversions', $msg );
	}
	
	function unpublish()
	{
		$model = $this->getModel('conversion');
		if(!$model->unpublish()) {
			$msg = JText::_( 'ERROR_UNPUBLISHING_CONVERSIONS' );
		} else {
			$msg = JText::_( 'CONVERSIONS_UNPUBLISHED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=conversions', $msg );
	}
	
	
	function getconversionModel()
	{
		if (!class_exists( 'conversionsModelconversion' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'models'.DS.'conversion.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'conversionsModelconversion' )) {
					JError::raiseWarning( 0, 'View class conversionsModelconversion not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'View conversionsModelconversion not supported. File not found.' );
				return $false;
			}
		}

		$model = new conversionsModelconversion(false, false, false);
		return $model;
	}
}