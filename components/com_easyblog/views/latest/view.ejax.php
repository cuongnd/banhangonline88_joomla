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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * Mark an item as featured
	 *
	 * @param	string	$type	The type of this item
	 * @param	int		$postId	The unique id of the item
	 *
	 * @return	string	Json string
	 **/
	function makeFeatured($type, $postId)
	{
	    $ajax	= new Ejax();
		$config	= EasyBlogHelper::getConfig();
		$acl 	= EasyBlogACLHelper::getRuleset();

		// Only super admins can feature items
		if( !EasyBlogHelper::isSiteAdmin() && !$acl->rules->feature_entry )
	    {
	    	$ajax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
	    	$ajax->send();
	    	return;
		}

		// Only non protected blog can be feature
		if($config->get('main_password_protect'))
		{
			$blog = EasyBlogHelper::getTable( 'Blog', 'Table' );
			$blog->load($postId);

			if(!empty($blog->blogpassword))
			{
				$ajax->alert( JText::_( 'COM_EASYBLOG_PASSWORD_PROTECTED_CANNOT_BE_FEATURED' ) , '' , '450' );
		    	$ajax->send();
		    	return;
			}
		}

	    EasyBlogHelper::makeFeatured($type, $postId);

	    $idName 	= '';
	    $message    = '';
	    switch($type)
	    {
	        case 'blogger':
	            $idName 	= '#blogger_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOGGER_FEATURED');
	            break;
	        case 'teamblog':
	            $idName 	= '#teamblog_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_TEAMBLOG_FEATURED');
	            break;
	        case 'post':
	        default:
	            $idName 	= '#title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOG_FEATURED');
	            break;
	    }

	    $ajax->alert( $message, JText::_('COM_EASYBLOG_INFO') , '450', 'auto');
	    $ajax->send();
	    return;
	}

	/**
	 * Remove an item as featured
	 *
	 * @param	string	$type	The type of this item
	 * @param	int		$postId	The unique id of the item
	 *
	 * @return	string	Json string
	 **/
	function removeFeatured($type, $postId)
	{
	    $ajax	= new Ejax();
	    $acl 	= EasyBlogACLHelper::getRuleset();

		// Only super admins can feature items
		if( !EasyBlogHelper::isSiteAdmin() && !$acl->rules->feature_entry )
	    {
	    	$ajax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
	    	$ajax->send();
	    	return;
		}

	    EasyBlogHelper::removeFeatured($type, $postId);

	    $idName 	= '';
	    $message    = '';
	    switch($type)
	    {
	        case 'blogger':
	            $idName 	= '#blogger_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOGGER_UNFEATURED');
	            break;
	        case 'teamblog':
	            $idName 	= '#teamblog_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_TEAMBLOG_UNFEATURED');
	            break;
	        case 'post':
	        default:
	            $idName 	= '#title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOG_UNFEATURED');
	            break;
	    }

		$ajax->script('$("' . $idName .'").removeClass("featured-item");');
	    $ajax->alert( $message, JText::_('COM_EASYBLOG_INFO') , '450', 'auto');
	    $ajax->send();
	    return;
	}
}
