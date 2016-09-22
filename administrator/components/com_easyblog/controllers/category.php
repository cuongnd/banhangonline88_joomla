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

class EasyBlogControllerCategory extends EasyBlogController
{
	function __construct()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'publish' , 'publish' );

		// In Joomla 3.0, it seems like we need to explicitly set unpublish
		$this->registerTask( 'unpublish' , 'unpublish' );
		$this->registerTask( 'orderup' , 'orderup' );
		$this->registerTask( 'orderdown' , 'orderdown' );
	}

	function hi()
	{
		var_dump( 'hi' );exit;
	}

	function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    EasyBlogControllerCategory::orderCategory(1);
	}

	function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    EasyBlogControllerCategory::orderCategory(-1);
	}

	function orderCategory( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$mainframe  = JFactory::getApplication();

		// Initialize variables
		$db		= EasyBlogHelper::db();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = EasyBlogHelper::getTable('Category', 'Table');
			$row->load( (int) $cid[0] );

			$row->move($direction);
		}

		$mainframe->redirect( 'index.php?option=com_easyblog&view=categories');
		exit;
	}

	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

	    $mainframe  = JFactory::getApplication();

		$row = EasyBlogHelper::getTable('Category', 'Table');
		$row->rebuildOrdering();

		//now we need to update the ordering.
		$row->updateOrdering();

		$message	= JText::_('COM_EASYBLOG_CATEGORIES_ORDERING_SAVED');
		$type       = 'message';

		$mainframe->redirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
		exit;
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY'), 'error');

				$url  = 'index.php?option=com_easyblog&view=category';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$category			= EasyBlogHelper::getTable( 'Category', 'Table' );
			$user				= JFactory::getUser();

			if( !isset( $post['created_by'] ) || empty( $post['created_by'] ) )
			{
				$post['created_by']	= $user->id;
			}

			$post['description']	= JRequest::getVar( 'description' , '' , 'REQUEST' , 'none' , JREQUEST_ALLOWHTML );
			$catId					= JRequest::getVar( 'catid' , '' );

			$isNew					= (empty($catId)) ? true : false;

			if( !empty( $catId ) )
			{
				$category->load( $catId );
			}

			$category->bind( $post );

			if (!$category->store())
			{
	        	JError::raiseError(500, $category->getError() );
			}
			else
			{
			    //save the category acl
				$category->deleteACL();
				if($category->private == CATEGORY_PRIVACY_ACL)
				{
					$category->saveACL( $post );
				}

				// Set the meta for the category
				$category->createMeta();

				// AlphaUserPoints
				// since 1.2
				if ( $isNew && EasyBlogHelper::isAUPEnabled() )
				{
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_category', '', 'easyblog_add_category_' . $category->id, JText::sprintf('AUP NEW CATEGORY CREATED', $post['title']) );
				}

				$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
				if(! empty($file['name']))
				{
					$newAvatar  		= EasyBlogHelper::uploadCategoryAvatar($category, true);
					$category->avatar   = $newAvatar;
					$category->store(); //now update the avatar.
				}

				$message	= JText::_( 'COM_EASYBLOG_CATEGORIES_SAVED_SUCCESS' );
			}
		}
		else
		{
			$message	= JText::_('COM_EASYBLOG_INVALID_REQUEST');
			$type		= 'error';
		}

		// Redirect to new form once again if necessary
		$saveNew			= JRequest::getInt( 'savenew' , 0 );

		if( $saveNew )
		{
			$mainframe->redirect( 'index.php?option=com_easyblog&view=category' , $message , $type );
			$mainframe->close();
		}
		$mainframe->redirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=categories' );

		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$catId 	= JRequest::getInt( 'catid' );

		if( $catId )
		{
			$catId 	= '&catid=' . $catId;
		}
		else
		{
			$catId = '';
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=category' . $catId );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$categories	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'info';

		if( empty( $categories ) )
		{
			$message	= JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$table		= EasyBlogHelper::getTable( 'Category' , 'Table' );
			foreach( $categories as $category )
			{
				$table->load( $category );

				if($table->getPostCount())
				{
					$message	= JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
					return;
				}

				if($table->getChildCount())
				{
					$message	= JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
					return;
				}

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYBLOG_CATEGORIES_DELETE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
					return;
				}
				else
				{
					// AlphaUserPoints
					// since 1.2
					if ( EasyBlogHelper::isAUPEnabled() )
					{
					    $aupid = AlphaUserPointsHelper::getAnyUserReferreID( $table->created_by );
						AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_category', $aupid, '', JText::sprintf('AUP CATEGORY DELETED', $table->title) );
					}
				}
			}
			$message	= JText::_('COM_EASYBLOG_CATEGORIES_DELETE_SUCCESS');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
	}

	public function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 1 ) )
			{
				$message	= JText::_('COM_EASYBLOG_CATEGORIES_PUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYBLOG_CATEGORIES_PUBLISHED_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
	}

	public function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 0 ) )
			{
				$message	= JText::_('COM_EASYBLOG_CATEGORIES_UNPUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYBLOG_CATEGORIES_UNPUBLISHED_ERROR');
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
	}

	function makeDefault()
	{
		$cid	= JRequest::getVar( 'cid' );
		$message	= '';
		$type		= 'message';

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		if( empty( $cid ) )
		{
			$message	= JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
		    //reset the other prevous defaulted category.
		    $model		= $this->getModel( 'Categories' );
		    $model->resetDefault();


			$category    = EasyBlogHelper::getTable( 'Category' );
			$category->load( $cid );

			$category->default = ( $category->default == '0' ) ? '1' : '0' ;
			$category->store();

			$message	= JText::_('COM_EASYBLOG_CATEGORIES_MARKED_AS_DEFAULT');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=categories' , $message , $type );
	}
}
