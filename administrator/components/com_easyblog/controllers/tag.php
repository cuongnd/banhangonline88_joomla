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

class EasyBlogControllerTag extends EasyBlogController
{	
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add' , 'edit' );

		$this->registerTask( 'publish'	 , 'publish' );
		$this->registerTask( 'unpublish' , 'unpublish' );
	}
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		$app 	= JFactory::getApplication();
		$msg 	= '';
		$type	= 'message';
		$url 	= 'index.php?option=com_easyblog&view=tags';
		
		// Redirect to new form if necessary
		$saveNew	= JRequest::getInt( 'savenew' , 0 );

		if( JRequest::getMethod() != 'POST' )
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
			$app->redirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
		}

		$post 	= JRequest::get( 'POST' );

		$user = JFactory::getUser();
		$post['created_by']	= isset($post['created_by']) && !empty($post['created_by']) ? $post['created_by'] : $user->id;
		$tagId				= JRequest::getVar( 'tagid' , '' );
		$isNew				= (empty($tagId)) ? true : false;
		$tag				= EasyBlogHelper::getTable( 'tag', 'Table' );
		$tag->load( $tagId );
		$tag->bind( $post );

		$tag->title			= JString::trim($tag->title);
		$tag->alias			= JString::trim($tag->alias);

		if( !$tag->store() )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=tag' , $tag->getError() , 'error' );
		}

		$message	= JText::_( 'COM_EASYBLOG_TAGS_TAG_SAVED' );

		if( $saveNew )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=tag' , $message , $type );
			$app->close();
		}
		
		$app->redirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' );
		
		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		JRequest::setVar( 'view', 'tag' );
		JRequest::setVar( 'tagid' , JRequest::getVar( 'tagid' , '' , 'REQUEST' ) );
		
		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		$tags	= JRequest::getVar( 'cid' , '' , 'POST' );
		
		$message	= '';
		$type		= 'message';
		
		if( empty( $tags ) )
		{
			$message	= JText::_('Invalid tag id');
			$type		= 'error';
		}
		else
		{
			$table		= EasyBlogHelper::getTable( 'Tag' , 'Table' );
			foreach( $tags as $tag )
			{
				$table->load( $tag );
				
				// AlphaUserPoints
				// since 1.2
				if ( EasyBlogHelper::isAUPEnabled() )
				{
					$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $table->created_by );
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_tag', $aupid, '', JText::sprintf('AUP TAG DELETED', $table->title) );
				}
				
				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYBLOG_TAGS_REMOVE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
					return;
				}
			}
			
			$message	= JText::_('COM_EASYBLOG_TAGS_TAG_REMOVED');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		
		$message	= '';
		$type		= 'message';
		
		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('Invalid tag id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );
			
			if( $model->publish( $tags , 1 ) )
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_PUBLISH_ERROR');
				$type		= 'error';
			}
			
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		
		$message	= '';
		$type		= 'message';
		
		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('Invalid tag id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );
			
			if( $model->publish( $tags , 0 ) )
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_UNPUBLISH_ERROR');
				$type		= 'error';
			}
			
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}
	
	function setDefault()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		
		if (!empty($cid))
		{
			$model	= $this->getModel('Tags');
			JArrayHelper::toInteger($cid);
			
			if (!$model->setDefault($cid))
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_SET_DEFAULT_ERROR');
				$type		= 'error';
			} else {
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_SET_DEFAULT_SUCCESS');
				$type		= 'success';
			}
		}
		
		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}
	
	function unsetDefault()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'tag' );
		
		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		
		if (!empty($cid))
		{
			$model	= $this->getModel('Tags');
			JArrayHelper::toInteger($cid);
			
			if (!$model->unsetDefault($cid))
			{
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_UNSET_DEFAULT_ERROR');
				$type		= 'error';
			} else {
				$message	= JText::_('COM_EASYBLOG_TAGS_TAG_UNSET_DEFAULT_SUCCESS');
				$type		= 'success';
			}
		}
		
		$this->setRedirect( 'index.php?option=com_easyblog&view=tags' , $message , $type );
	}
}