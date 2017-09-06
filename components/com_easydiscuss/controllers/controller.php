<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

if( !class_exists( 'EasyDiscussControllerParent' ) )
{
	if( DiscussHelper::getJoomlaVersion() >= '3.0' )
	{
		class EasyDiscussControllerParent extends JControllerLegacy
		{

		}
	}
	else
	{
		class EasyDiscussControllerParent extends JController
		{

		}
	}

}

class EasyDiscussController extends EasyDiscussControllerParent
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	public function __construct()
	{
		// Load necessary css and javascript files.
		DiscussHelper::loadHeaders();

		parent::__construct();
	}


	/**
	 * Override parent's display method
	 *
	 * @since 0.1
	 */
	public function display( $cachable = false , $urlparams = false )
	{
		$document	= JFactory::getDocument();

		$viewName	= JRequest::getCmd( 'view'		, 'index' );
		$viewLayout	= JRequest::getCmd( 'layout'	, 'default' );
		$view		= $this->getView( $viewName		, $document->getType() , '' );
		$format		= JRequest::getCmd( 'format'	, 'html' );
		$tmpl		= JRequest::getCmd( 'tmpl'		, 'html' );

		if( !empty( $format ) && $format == 'ajax' )
		{
			if( !JRequest::checkToken() && !JRequest::checkToken( 'get' ) )
			{
				echo 'Invalid token';
				exit;
			}

			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal			= array();
						foreach($value as $val)
						{
							$item		=& $val;
							$item		= stripslashes($item);
							$item		= rawurldecode($item);
							$arrVal[]	= $item;
						}

						$arguments[]	= $arrVal;
					}
					else
					{
						$value			= stripslashes( $value );
						$value			= rawurldecode( $value );
						$arguments[]	= $value;
					}
				}
			}

			if(!method_exists( $view , $viewLayout ) )
			{
				$ajax	= new Disjax();
				$ajax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$ajax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			$config	= DiscussHelper::getConfig();

			// Load theme css
			DiscussHelper::loadThemeCss();

			// Non ajax calls.
			require_once DISCUSS_CLASSES . '/themes.php';
			require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

			// Prepare class names for wrapper
			$cat_id			= JRequest::getInt( 'category_id', '', 'GET' );
			$cat_cls_name	= $cat_id ? ' category-' . $cat_id : '';
			$wrapper_sfx	= htmlspecialchars($config->get( 'layout_wrapper_sfx', '' ));
			$discussView	= ' discuss-view-' . $view->getName();

			// We allow 3rd party to show jomsocial's toolbar even if integrations are disabled.
			$showJomsocial	= JRequest::getBool( 'showJomsocialToolbar' , false );
			$jomsocialClass	= '';

			if( $config->get( 'integration_jomsocial_toolbar' ) && $format != 'pdf' && $format != 'phocapdf' && $tmpl != 'component' || $showJomsocial )
			{
				$jomsocialClass	= ' jomsocial-discuss';
			}

			// Set the wrapper.
			echo '<div id="discuss-wrapper" class="discuss-wrap'.$wrapper_sfx.$cat_cls_name.$jomsocialClass.$discussView.'">';

			$print = JRequest::getBool('print');


			if( $config->get( 'integration_jomsocial_toolbar' ) && $format != 'pdf' && $format != 'phocapdf' && $tmpl != 'component' || $showJomsocial )
			{
				if(JFile::exists( JPATH_ROOT . '/components/com_community/libraries/core.php'))
				{
					require_once JPATH_ROOT . '/components/com_community/libraries/core.php';
					require_once JPATH_ROOT . '/components/com_community/libraries/toolbar.php';

					$appsLib	= CAppPlugins::getInstance();
					$appsLib->loadApplications();

					$appsLib->triggerEvent( 'onSystemStart' , array() );

					if( class_exists( 'CToolbarLibrary' ) )
					{
						echo '<div id="community-wrap">';
						if( method_exists( 'CToolbarLibrary' , 'getInstance' ) )
						{
							$jsToolbar  = CToolbarLibrary::getInstance();
							echo $jsToolbar->getHTML();
						}
						else
						{
							echo CToolbarLibrary::getHTML();
						}
						echo '</div>';
					}
				}
			}

			// Allow 3rd party to hide our headers
			$hideToolbar	= JRequest::getBool( 'hideToolbar' , false );

			if(!$print && $format != 'pdf' && $format != 'feed' && !$hideToolbar )
			{
				echo $this->getToolbar( $view->getName() , $view->getLayout() );
			}

			if( $viewLayout != 'default' )
			{
				if( $cachable )
				{
					$cache	= JFactory::getCache( 'com_easydiscuss' , 'view' );
					$cache->get( $view , $viewLayout );
				}
				else
				{
					if( !method_exists( $view , $viewLayout ) )
					{
						$view->display();
					}
					else
					{
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}

			// Powered by link
			

			echo '<input type="hidden" class="easydiscuss-token" value="' . DiscussHelper::getToken() . '" />';

			// End wrapper.
			echo '</div>';
		}
	}

	public function getToolbar( $currentView )
	{
		$template	= new DiscussThemes();
		$config		= DiscussHelper::getConfig();
		$acl		= DiscussHelper::getHelper( 'ACL' );
		$my			= JFactory::getUser();

		// Set active menu.
		$views	= array( 'index' => '' , 'tags' => '', 'categories'=>'', 'search' => '', 'profile' => '', 'create' => '' , 'users' => '' , 'badges' => '' , 'favourites' => '' , 'conversation' => '');
		$views	= (object) $views;

		// search query
		$query	= JRequest::getString( 'query' , '' );


		// @rule: If a user is viewing a specific category, we need to ensure that it's setting the correct active menu
		if( JRequest::getInt( 'category_id' , 0 ) !== 0 )
		{
			$currentView	= 'categories';
		}

		$views->current 	= $currentView;

		if( isset( $views->$currentView ) )
		{
			if( $currentView == 'profile' )
			{
				if( $my->id == JRequest::getInt( 'id' ) || JRequest::getInt( 'id' , 0 ) == 0 )
				{
					$views->$currentView	= ' active';
				}
				else
				{

					$views->index	= ' active';
				}
			}
			else
			{
				$views->$currentView	= ' active';
			}
		}
		else
		{
			// View does not exist, so we set the default 'latest' to be active.
			if( JRequest::getVar( 'layout' ) == 'submit' && $currentView == 'post' )
			{
				$views->create	= ' active';
			}
			elseif( $currentView == 'tag' )
			{
				$views->tags	= ' active';
			}
			elseif( $currentView == 'categories' )
			{
				$views->tags	= ' active';
			}
			elseif( $currentView == 'users' )
			{
				$views->tags	= ' active';
			}
			else
			{
				$views->index		= ' active';
			}
		}

		// Get category id from request.
		$categoryId		= JRequest::getInt( 'category_id' , 0 );

		if( $currentView == 'post' )
		{
			$postModel 	= DiscussHelper::getModel( 'Posts' );
			$categoryId	= $postModel->getCategoryId( JRequest::getInt( 'id' ) );
			$id			= JRequest::getInt( 'id' );

			if( $id )
			{
				// Clear up any notifications that are visible for the user.
				$notifications	= $this->getModel( 'Notification' );
				$notifications->markRead(	$my->id ,
											$id ,
											array(
													DISCUSS_NOTIFICATIONS_REPLY,
													DISCUSS_NOTIFICATIONS_RESOLVED,
													DISCUSS_NOTIFICATIONS_ACCEPTED,
													DISCUSS_NOTIFICATIONS_FEATURED,
													DISCUSS_NOTIFICATIONS_COMMENT,
													DISCUSS_NOTIFICATIONS_MENTIONED,
													DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION,
													DISCUSS_NOTIFICATIONS_LIKES_REPLIES,
													DISCUSS_NOTIFICATIONS_LOCKED,
													DISCUSS_NOTIFICATIONS_UNLOCKED,
													DISCUSS_NOTIFICATIONS_ON_HOLD,
													DISCUSS_NOTIFICATIONS_WORKING_ON,
													DISCUSS_NOTIFICATIONS_REJECTED,
													DISCUSS_NOTIFICATIONS_NO_STATUS
												)
										);

			}
		}


		$headers		= new JObject();
		$headers->title	= $config->get( 'main_title' );
		$headers->desc	= $config->get( 'main_description' );

		$model			= DiscussHelper::getModel( 'Notification' );
		$notifications	= $model->getTotalNotifications( $my->id );

		$return 		= JRequest::getURI();
		$return			= base64_encode( $return );

		$messageObject	= DiscussHelper::getMessageQueue();

		$template->set( 'messageObject'			, $messageObject );

		// Get new message count.
		$conversationModel 	= DiscussHelper::getModel( 'Conversation' );
		$totalMessages		= $conversationModel->getCount( $my->id , array( 'filter' => 'unread' ) );

		// Minimize the process if user using other template
		$categoriesModel	= DiscussHelper::getModel( 'Categories' );

		// Get all the categories ids
		$options 		= array( 'published' => true );
		$categoryItems 	= $categoriesModel->getData( true , $options );
		$categories 	= array();

		if( $categoryItems )
		{
			foreach( $categoryItems as $row )
			{
				$category 	= DiscussHelper::getTable( 'Category' );
				$category->load( $row->id );

				$categories[]	= $category;
			}
		}

		$postId		= JRequest::getInt('id', 0);
		$post		= JTable::getInstance( 'Posts' , 'Discuss' );
		$post->load($postId);

		$customClass = 'select-searchbar';
		$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $post->category_id, true, true, true, false, $customClass);

		$template->set( 'categories'	, $categories );

		// @TODO: Update with proper message count.
		$template->set( 'totalMessages'			, $totalMessages );
		$template->set( 'totalNotifications' 	, $notifications );
		$template->set( 'return'				, $return );
		$template->set( 'categoryId'			, $categoryId );
		$template->set( 'views' 				, $views );
		$template->set( 'headers'				, $headers );
		$template->set( 'query'					, $query );

		// Deprecated since 3.0. To be removed in 4.0
		$template->set( 'config' , $config );
		$template->set( 'acl'	, $acl );
		$template->set( 'notifications' , $notifications );
		$template->set( 'nestedCategories' , $nestedCategories );

		echo $template->fetch( 'toolbar.php' );
	}
}
