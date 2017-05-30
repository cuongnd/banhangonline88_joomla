<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );

class EasyBlogControllerSettings extends EasyBlogController
{
	function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe	= JFactory::getApplication();

		$result		= $this->_store();
		$layout		= JRequest::getString( 'active' , '' );
		$child		= strtolower(JRequest::getString( 'activechild' , '' ));
		$mainframe->redirect( 'index.php?option=com_easyblog&view=settings&active=' . $layout . '&activechild=' . $child , $result['message'] , $result['type'] );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe	= JFactory::getApplication();

		$result		= $this->_store();
		$mainframe->redirect( 'index.php?option=com_easyblog' , $result['message'] , $result['type'] );
	}

	public function saveApi()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$model		= $this->getModel( 'Settings' );
		$key		= JRequest::getVar( 'apikey' );
		$from		= JRequest::getVar( 'from', '' );

		$mainframe	= JFactory::getApplication();

		$model->save( array( 'apikey' => $key ) );

		if( empty( $from ) )
		{
			$mainframe->redirect( 'index.php?option=com_easyblog' , JText::_( 'COM_EASYBLOG_API_KEY_SAVED' ) );
		}
		else
		{
		    $mainframe->redirect( 'index.php?option=com_easyblog&view=updater' , JText::_( 'COM_EASYBLOG_API_KEY_SAVED' ) );
		}
	}

	function _store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$model		= $this->getModel( 'Settings' );
			//$model->save( $key , $values );

			$postArray	= JRequest::get( 'post' );
			$saveData	= array();

			// Unset unecessary data.
			unset( $postArray['task'] );
			unset( $postArray['option'] );
			unset( $postArray['c'] );

			foreach( $postArray as $index => $temp )
			{
				if(is_array($temp))
				{
					$value = implode('|', $temp);
				}
				else
				{
					$value = $temp;
				}

				if( $index == 'integration_google_adsense_code' )
				{
					$value	= str_ireplace( ';"' , '' , $value );
				}

				if( $index != 'task' );
				{
					$saveData[ $index ]	= $value;
				}

			}

			$db		= EasyBlogHelper::db();

			// @rule: If the privacy integrations with jomsocial is enabled, we need to ensure all blog posts are up to date with the
			// correct privacy values.
			if( $saveData['integrations_easysocial_privacy'] )
			{
				$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
						. 'SET ' . $db->nameQuote( 'private' ) . ' = ' . $db->Quote( 10 ) . ' '
						. 'WHERE ' . $db->nameQuote( 'private' ) . ' = ' . $db->Quote( 1 );
				$db->setQuery( $query );
				$db->query();
			}
			else if( $saveData['main_jomsocial_privacy'] )
			{
				$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
						. 'SET ' . $db->nameQuote( 'private' ) . ' = ' . $db->Quote( 20 ) . ' '
						. 'WHERE ' . $db->nameQuote( 'private' ) . ' = ' . $db->Quote( 1 );
				$db->setQuery( $query );
				$db->query();
			}
			else
			{
				$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
						. 'SET ' . $db->nameQuote( 'private' ) . ' = ' . $db->Quote( 1 ) . ' '
						. 'WHERE ' . $db->nameQuote( 'private' ) . ' >= ' . $db->Quote( 10 );
				$db->setQuery( $query );
				$db->query();
			}
			//overwrite the main blog description value by using getVar to preserve the html tag
			$saveData['main_description']	= JRequest::getVar( 'main_description', '', 'post', 'string', JREQUEST_ALLOWRAW );

			//overwrite the addthis custom code value by using getVar to preserve the html tag
			$saveData['social_addthis_customcode']	= JRequest::getVar( 'social_addthis_customcode', '', 'post', 'string', JREQUEST_ALLOWRAW );

			if( $model->save( $saveData ) )
			{
				$message	= JText::_( 'COM_EASYBLOG_SETTINGS_STORE_SUCCESS' );
			}
			else
			{
				$message	= JText::_( 'COM_EASYBLOG_SETTINGS_STORE_ERROR' );
				$type		= 'error';
			}
		}
		else
		{
			$message	= JText::_('COM_EASYBLOG_SETTINGS_STORE_INVALID_REQUEST');
			$type		= 'error';
		}

		// Clear the component's cache
		$cache = JFactory::getCache('com_easyblog');
		$cache->clean();

		return array( 'message' => $message , 'type' => $type);
	}

	/**
	* Cancels an edit operation
	*/
	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_easyblog');
	}

	public function import()
	{
		$this->checkAccess( 'setting' );

		$mainframe = JFactory::getApplication();

		$file 		= JRequest::getVar( 'file' , array() , 'FILES' );

		if( !isset( $file[ 'tmp_name' ] ) || empty( $file[ 'tmp_name' ]) )
		{
			return $mainframe->redirect('index.php?option=com_easyblog&view=settings' , JText::_( 'COM_EASYBLOG_SETTINGS_IMPORT_ERROR_FILE_INVALID' ) , 'error' );
		}

		$path 		= $file[ 'tmp_name' ];
		$contents	= JFile::read( $path );

		$table 		= EasyBlogHelper::getTable( 'Configs' );
		$table->load( array( 'name' => 'config' ) );

		$table->params 	= $contents;
		$table->store();

		return $mainframe->redirect('index.php?option=com_easyblog&view=settings' , JText::_( 'COM_EASYBLOG_SETTINGS_IMPORT_SUCCESS' ) );
	}

	/**
	* Save the Email Template.
	*/
	function saveEmailTemplate()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe 	= JFactory::getApplication();
		$file 		= JRequest::getVar('file', '', 'POST' );
		$filepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $file;
		$content	= JRequest::getVar( 'content' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );
		$msg		= '';
		$msgType	= '';

		$status 	= JFile::write($filepath, $content);
		if(!empty($status))
		{
			$msg = JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_SUCCESS');
			$msgType = 'info';
		}
		else
		{
			$msg = JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_FAIL');
			$msgType = 'error';
		}

		$mainframe->enqueueMessage($msg);
		$mainframe->redirect('index.php?option=com_easyblog&view=settings&layout=editEmailTemplate&file='.$file.'&msg='.$msg.'&msgtype='.$msgType.'&tmpl=component&browse=1');
	}
}
