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

jimport( 'joomla.application.component.view');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogViewEntry extends EasyBlogView
{
	function display( $tmpl = null )
	{


		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config = EasyBlogHelper::getConfig();

		//for trigger
		$params		= $mainframe->getParams('com_easyblog');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$joomlaVersion = EasyBlogHelper::getJoomlaVersion();

	    $blogId = JRequest::getVar('id');
	    if( empty($blogId) )
		{
			return JError::raiseError( 404, JText::_('COM_EASYBLOG_BLOG_NOT_FOUND') );
		}

	    $my 	= JFactory::getUser();
	    $blog	= EasyBlogHelper::getTable( 'Blog', 'Table' );
	    $blog->load($blogId);

		//check if blog is password protected.
		if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
		{
			if(!EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
			{
				echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_PDF_ERROR');

				return false;
			}
		}

		$blog->intro	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blog->intro );
		$blog->content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blog->content );


		//onEasyBlogPrepareContent trigger start
		$dispatcher->trigger('onEasyBlogPrepareContent', array (& $blog, & $params, $limitstart));
		//onEasyBlogPrepareContent trigger end

	    //onPrepareContent trigger start
		$blog->introtext	= $blog->intro;
		$blog->text			= $blog->content;
		if($joomlaVersion >= '1.6'){
			$dispatcher->trigger('onContentPrepare', array('easyblog.blog', &$blog, &$params, $limitstart));
		} else {
			$dispatcher->trigger('onPrepareContent', array (&$blog, &$params, $limitstart));
		}
		$blog->intro		= $blog->introtext;
		$blog->content		= $blog->text;
	    //onPrepareContent trigger end

	    // @task: Retrieve tags output.
	    $modelPT		= $this->getModel( 'PostTag' );
	    $blogTags		= $modelPT->getBlogTags($blog->id);

		$theme	= new CodeThemes();
		$theme->set( 'tags' , $blogTags );
		$tags 			= $theme->fetch( 'tags.item.php' );


		//page setup
	    $blogHtml		= '';
	    $commentHtml	= '';
	    $blogHeader		= '';
	    $blogFooter		= '';
	    $adsenseHtml	= '';
	    $trackbackHtml  = '';


	    $blogger	= null;
	    if($blog->created_by != 0)
	    {
	    	$blogger 	= EasyBlogHelper::getTable( 'Profile', 'Table' );
	    	$blogger->load( $blog->created_by );

			$blogger->displayName   = $blogger->getName();
	    }

	    //onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
		$blog->event = new stdClass();

		if($joomlaVersion >= '1.6')
		{
			$results = $dispatcher->trigger('onContentAfterTitle', array ('easyblog.blog', &$blog, &$params, $limitstart));
			$blog->event->afterDisplayTitle = JString::trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentBeforeDisplay', array ('easyblog.blog', &$blog, &$params, $limitstart));
			$blog->event->beforeDisplayContent = JString::trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentAfterDisplay', array ('easyblog.blog', &$blog, &$params, $limitstart));
			$blog->event->afterDisplayContent = JString::trim(implode("\n", $results));
		} else {
			$results = $dispatcher->trigger('onAfterDisplayTitle', array (&$blog, &$params, $limitstart));
			$blog->event->afterDisplayTitle = JString::trim(implode("\n", $results));

			$results = $dispatcher->trigger('onBeforeDisplayContent', array (&$blog, &$params, $limitstart));
			$blog->event->beforeDisplayContent = JString::trim(implode("\n", $results));

			$results = $dispatcher->trigger('onAfterDisplayContent', array (&$blog, &$params, $limitstart));
			$blog->event->afterDisplayContent = JString::trim(implode("\n", $results));
		}
		//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger end



	    $tplB = new CodeThemes();

	    $tplB->set('blog', $blog );
	    $tplB->set('tags', $tags );
	    $tplB->set('config'	, $config );
	    $tplB->set('blogger', $blogger );

		$blogHtml	= $tplB->fetch( 'blog.read.pdf.php' );

		//pdf page setup

		$pageTitle	= EasyBlogHelper::getPageTitle($config->get('main_title'));
	    $document->setTitle( $blog->title . $pageTitle );
		$document->setName($blog->permalink);

		// Fix phoca pdf plugin.
		if( method_exists( $document , 'setArticleText' ) )
		{
			$document->setArticleText( $blogHtml );
		}

		echo $blogHtml;
	}
}
