<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.controller');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

if( !class_exists( 'EasyBlogController' ) )
{

if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
{
	class EasyBlogParentController extends JControllerLegacy
	{

	}
}
else
{
	class EasyBlogParentController extends JController
	{

	}
}




class EasyBlogController extends EasyBlogParentController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct($config = array())
	{
		// Load necessary css and javascript files.
		EasyBlogHelper::loadHeaders();

		// By default, we use the tables specified at the back end.
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_easyblog/tables' );

		//load the content plugins so that the content trigger will work.
		JPluginHelper::importPlugin('content');

		parent::__construct($config);
	}

	/**
	 * Override parent's display method
	 *
	 * @since 0.1
	 */
	function display( $cachable = false, $urlparams = false )
	{
		$document	= JFactory::getDocument();

		$viewName	= JRequest::getCmd( 'view', $this->getName() );

		if( $viewName == 'easyblog' )
		{
			$viewName	= 'latest';
		}

		if($viewName != 'entry')
		{
			EasyBlogHelper::clearSession('EASYBLOG_TEAMBLOG_ID');
		}

		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view		= $this->getView( $viewName, $document->getType() , '' );
		$format		= JRequest::getCmd( 'format' , 'html' );
		$tmpl		= JRequest::getCmd( 'tmpl' , 'html' );

		// We do not want to display any html codes for trackbacks
		if( $viewName == 'trackback' )
		{
			// Other tasks should not display any html output
			if( !method_exists( $view , $viewLayout ) )
			{
				$view->display();
			}
			else
			{
				$view->$viewLayout();
			}
			return;
		}

		// @rule: Skip processing for feed views
		if( in_array( $format , array( 'feed' , 'weever' ) ) )
		{
			if( $viewLayout != 'default' )
			{
				if( $cachable )
				{
					$cache	= JFactory::getCache( 'com_easyblog' , 'view' );
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

			return;
		}

		if( !empty( $format ) && $format == 'ejax' )
		{
			if( ob_get_length() !== false )
			{
				while (@ ob_end_clean());
				if( function_exists( 'ob_clean' ) )
				{
					@ob_clean();
				}
			}

			$data		= JRequest::get( 'POST' , JREQUEST_ALLOWHTML );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal    = array();
						foreach($value as $val)
						{
							$item   = $val;
							$item   = stripslashes($item);
							// $item   = rawurldecode($item);
							$arrVal[]   = $item;
						}
						$arrVal			= EasyBlogStringHelper::ejaxPostToArray( $arrVal );
						$arguments[]	= $arrVal;
					}
					else
					{
						$val			= stripslashes( $value );
						$val			= rawurldecode( $val );
						$arguments[]	= $val;
					}
				}
			}

			if(!method_exists( $view , $viewLayout ) )
			{
				$ejax	= new Ejax();
				$ejax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$ejax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			$config 	= EasyBlogHelper::getConfig();
			$theme 		= $config->get( 'layout_theme' );
			$dashboard	= $viewName == 'dashboard';

			EasyBlogHelper::loadThemeCss( 'styles.css' , $dashboard );

			jimport( 'joomla.filesystem.file' );
			$mainframe = JFactory::getApplication();

			// If there is any override through the query string, we need to set it here.
			$overrides	= JRequest::getString( 'theme' , '' );

			if( !empty( $overrides ) )
			{
				$theme	= $overrides;
			}

			$themeName 	= $theme;

			// @rule: Use Google fonts if necessary.
			$headingFont	= $config->get( 'layout_googlefont' );
			if(  $headingFont != 'site' )
			{
				$headingFont	= explode( ' ' , $headingFont );
				$headingFont	= JString::strtolower( $headingFont[0] );
			}

			$menu 	= JFactory::getApplication()->getMenu()->getActive();
			$suffix = '';

			if( is_object( $menu ) )
			{
				$menuparams 	= EasyBlogHelper::getRegistry( $menu->params );

				$suffix	= ' ' . $menuparams->get( 'pageclass_sfx' );
			}


			$print 			= JRequest::getBool('print');

			ob_start();

			if( $viewLayout != 'default' )
			{
				if( $cachable )
				{
					$cache	= JFactory::getCache( 'com_easyblog' , 'view' );
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

			// Add token variable.
			echo '<span id="easyblog-token" style="display:none;"><input type="hidden" name="' . EasyBlogHelper::getToken() . '" value="1" /></span>';

			$contents 	= ob_get_contents();
			ob_end_clean();

			ob_start();
			// @task: Set additional wrapper for dashboard views
			if( JRequest::getVar( 'view' ) == 'dashboard' )
			{
				$theme		= new CodeThemes( true );
				$theme->set( 'content' , $contents );
				echo $theme->fetch( 'dashboard.wrapper.php' );

			}
			else
			{
				echo $contents;
			}
			$contents 	= ob_get_contents();
			ob_end_clean();


			$isDashboard	= JRequest::getVar( 'view' ) == 'dashboard';
			$output 		= '';

			$template 	= new CodeThemes();

			// Get JomSocial toolbar
			$jsToolbar 	= $this->getJSToolbar( $format , $tmpl );

			// Get any messages
			$messages 	= $this->getMessages();

			// Get EasyBlog's own toolbar
			// Allow 3rd party to hide our headers
			$toolbar 	= '';

			if( !$print && $format != 'pdf' && $format != 'phocapdf' && $tmpl != 'component' && JRequest::getBool( 'hideToolbar' , false ) == false )
			{
				if( $view->getName() != 'dashboard' )
				{
					$toolbar 	= $this->getToolbar( $view->getName() );
				}
			}

			$template->set( 'jsToolbar' , $jsToolbar );
			$template->set( 'messages' , $messages );
			$template->set( 'toolbar' , $toolbar );

			// Determine if this is using bootstrap or not.
			$bootstrap 	= EasyBlogHelper::getJoomlaVersion() >= '3.0' ? ' eblog-bootstrap' : ' eblog-joomla';

			$template->set( 'contents' , $contents );
			$template->set( 'themeName' , $themeName );
			$template->set( 'headingFont' , $headingFont );
			$template->set( 'suffix' , $suffix );
			$template->set( 'bootstrap' , $bootstrap );

			// @task: Set the wrapper.
			if( JRequest::getVar( 'tmpl' ) != 'component')
			{
				$file 		= $isDashboard ? 'structure.dashboard.php' : 'structure.php';

				$contents 	= $template->fetch( $file );
			}
			else
			{
				$contents 	= $template->fetch( 'structure.tmpl.php' );
			}

			echo $contents;
		}

		//print powered by start
		/* [EBLOG_POWERED_BY_LINK] */
		//print powered by end
	}

	public function getEasySocialToolbar( $themeName )
	{
		$config 	= EasyBlogHelper::getConfig();
		$easysocial = EasyBlogHelper::getHelper( 'EasySocial' );

		if( $easysocial->exists() && $config->get( 'integrations_easysocial_toolbar' ) )
		{
			$easysocial->init();

			ob_start();

			if( $themeName != 'wireframe' )
			{
				echo '<div id="es-wrap" class="es-main es-responsive">';
			}

			echo $easysocial->getToolbar();

			if( $themeName != 'wireframe' )
			{
				echo '</div>';
			}


			$contents 	= ob_get_contents();
			ob_end_clean();

			return $contents;
		}

		return;
	}

	public function getJSToolbar( $format , $tmpl )
	{
		$config 		= EasyBlogHelper::getConfig();

		// We allow 3rd party to show jomsocial's toolbar even if integrations are disabled.
		$showJomsocial	= JRequest::getBool( 'showJomsocialToolbar' , false );

		if( $config->get( 'integrations_jomsocial_toolbar' ) && $format != 'pdf' && $format != 'phocapdf' && $tmpl != 'component' || $showJomsocial )
		{
			$file 	= JPATH_ROOT . '/components/com_community/libraries/core.php';

			jimport( 'joomla.filesystem.file' );

			$exists = JFile::exists( $file );

			if( !$exists )
			{
				return;
			}


			require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );
			require_once( JPATH_ROOT . '/components/com_community/libraries/toolbar.php' );

			ob_start();
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

			$html 	= ob_get_contents();
			ob_end_clean();

			return $html;
		}

		return;
	}

	public function getMessages()
	{
		$messages	= EasyBlogHelper::getMessageQueue();

		$html 		= '';

		ob_start();
		if( !empty( $messages ) )
		{
		?>
		<div class="eblog-message <?php echo $messages->type;?>">
			<p><?php echo $messages->message;?></p>
		</div>
		<?php
		}

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	function getToolbar( $viewName )
	{
		static $toolbar = null;

		// We want to skip this when doing the search redirection.
		if( JRequest::getVar( 'view' ) == 'search' && JRequest::getVar( 'layout' ) == 'parsequery' )
		{
			return;
		}

		require_once( EBLOG_HELPERS . '/helper.php' );

		if( is_null( $toolbar ) )
		{
			// Set active menu.
			$views	= array( 'home' => 'item' , 'blogger' => 'item', 'blogroll' => 'item', 'tags' => 'item', 'categories' => 'item', 'dashboard' => 'item', 'teamblog' => 'item' , 'archive' => 'item' , 'search' => 'item' );
			$views	= (object) $views;

			if( isset( $views->$viewName ) )
			{
				$views->$viewName	= 'item-active';
			}
			else
			{
				// View does not exist, so we set the default 'latest' to be active.
				$views->home		= 'item-active';
			}

			$config			= EasyBlogHelper::getConfig();

			$tpl	= new CodeThemes();

			$title	= $config->get( 'main_title' );
			$desc	= $config->get( 'main_description' );
			$desc	= nl2br($desc);
			$bloggerId	= JRequest::getInt( 'id' , '' );
			$blogger	= JRequest::getVar( 'blogger' , '' );

			$isBloggerMode	= EasyBlogRouter::isBloggerMode();

			if( !empty( $blogger ) )
			{
				$bloggerId	= $blogger;
			}

			if( $isBloggerMode !== false)
			{
				$bloggerId	= $isBloggerMode;
			}

			if( ( ($viewName == 'blogger' || !empty($blogger) ) && !empty( $bloggerId ) ) ||  ($isBloggerMode !== false) )
			{
				$blogger	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
				$blogger->load( $bloggerId );

				$title	= !empty($blogger->title)? $blogger->title : $title ;
				$desc	= $blogger->getDescription() ? $blogger->getDescription() : $desc;
			}

			// @task: If this is currently in entry view, switch the blog heading to the author's configured heading.
			if( $viewName == 'entry' && $config->get( 'layout_headers_respect_author' ) )
			{
				$id 	= $bloggerId;
				$blog	= EasyBlogHelper::getTable( 'Blog' );
				$blog->load( $id );

				$bloggerId	= $blog->created_by;

				if( $bloggerId )
				{
					$blogger	= EasyBlogHelper::getTable( 'Profile' );
					$blogger->load( $bloggerId );

					$title	= !empty($blogger->title)? $blogger->title : $title ;
					$desc	= $blogger->getDescription() ? $blogger->getDescription() : $desc;
				}
			}

			$teamId		= JRequest::getInt( 'id' , 0 );
			if( $viewName == 'teamblog' && !empty( $teamId ) )
			{
				$team   = EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
				$team->load( $teamId );

				$title	= !empty($team->title)? $team->title : $title ;
				$desc	= $team->getDescription() ? $team->getDescription() : $desc;
			}

			$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
			$logoutURL		= base64_encode( EasyBlogRouter::_('index.php?option=com_easyblog&view=latest' , false ) );

			$isTeamAdmin    	= EasyBlogHelper::isTeamAdmin();
			$totalTeamRequest   = 0;

			if($isTeamAdmin)
			{
				$teamModel = $this->getModel('TeamBlogs');
				$totalTeamRequest	= $teamModel->getTotalRequest();
			}

			$my	= JFactory::getUser();
			$totalModComment    = 0;
			if( $my->id > 0)
			{
				$commentModel 		= $this->getModel('Comment');
				$totalModComment   	= $commentModel->getUserModerateCommentCount($my->id);
			}

			$jConfig		= EasyBlogHelper::getJConfig();
			$Itemid     = JRequest::getInt( 'Itemid' );

			$tpl->set( 'title'		, $title );
			$tpl->set( 'desc'		, $desc );
			$tpl->set( 'views' 		, $views );
			$tpl->set( 'isLoggedIn' , EasyBlogHelper::isLoggedIn() );
			$tpl->set( 'logoutURL' 			, $logoutURL );
			$tpl->set( 'isTeamAdmin' 		, $isTeamAdmin );
			$tpl->set( 'totalTeamRequest'	, $totalTeamRequest );
			$tpl->set( 'totalModComment'	, $totalModComment );
			$tpl->set( 'jConfig'		, $jConfig );
			$tpl->set( 'Itemid'	, $Itemid );
			$returnURL	= base64_encode( JRequest::getURI() );
			$tpl->set( 'return'				, $returnURL );
			$toolbar	= $tpl->fetch( 'toolbar.php' );

			unset( $tpl );
		}
		return $toolbar;
	}

	/**
	 * Overrides parent method
	 **/
	public static function getInstance( $controllerName, $config = array() )
	{
		static $instances;

		if( !$instances )
		{
			$instances	= array();
		}

		// Set the controller name
		$className	= 'EasyBlogController' . ucfirst( $controllerName );

		if( !isset( $instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= EBLOG_CONTROLLERS . DIRECTORY_SEPARATOR . JString::strtolower( $controllerName ) . '.php';

				if( JFile::exists( $controllerFile ) )
				{
					require_once( $controllerFile );

					if( !class_exists( $className ) )
					{
						// Controller does not exists, throw some error.
						JError::raiseError( '500' , JText::sprintf('Controller %1$s not found' , $className ) );
					}
				}
				else
				{
					// File does not exists, throw some error.
					JError::raiseError( '500' , JText::sprintf('Controller %1$s.php not found' , $controllerName ) );
				}
			}

			$instances[ $className ]	= new $className($config);
		}
		return $instances[ $className ];
	}

}

}
