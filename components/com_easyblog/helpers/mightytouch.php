<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

class EasyBlogMightyTouchHelper
{
	public function __construct()
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );
	}

	/**
	 * Sets the karma point for the respective user when certain action is performed.
	 *
	 * @param	int $userId The user id
	 * @param	string $action This action should map with the config's action string.
	 */
	public function setKarma( $userId , $action )
	{
		$config		= EasyBlogHelper::getConfig();

		if(!$config->get( 'integrations_mighty_karma_' . $action ) )
		{
			return false;
		}


		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'api.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}
		require_once($file);

		$points		= $config->get( 'integrations_mighty_karma_' . $action . '_points');
		JSCommunityApi::increaseKarma( $userId , $points );
		return true;
	}

	public function getBlogLink( $blog )
	{
		return EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id . $this->getItemId() , false , true );
	}

	public function getBlogTitle( $blog )
	{
		$title		= htmlspecialchars( $blog->title );

		if (strlen($title) > 80)
		{
			$title		= JString::substr( $blog->title , 0 , 80 ) . '...';
		}

		return $title;
	}

	public function getCategoryTitle( $blog )
	{
		$category	= EasyBlogHelper::getTable( 'Category' );
		$category->load( $blog->category_id );

		return $category->title;
	}

	public function getCategoryLink( $blog )
	{
		return EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $blog->category_id . $this->getItemId() , false , true );
	}

	public function getItemId()
	{
		$id		= '';

		$app	= JFactory::getApplication();

		if( $app->isAdmin() )
		{
			$id		= '&Itemid=' . EasyBlogRouter::getItemId( 'latest' );
		}
		return $id;
	}

	public function getFirstImage( $content )
	{
		// Remove all html tags from the content as we want to chop it down.
		$content	= strip_tags( $content );

		$pattern		= '#<img[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );

			return $matches[ 0 ] . '<div style="clear:both;"></div>';
		}

		return false;
	}

	public function addFeaturedActivity( $blog )
	{
		$config	= EasyBlogHelper::getConfig();
		$title	= JString::substr( $blog->title , 0 , 30 ) . '...';


		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'api.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}
		require_once($file);

		$blogLink	= $this->getBlogLink( $blog );
		$my			= JFactory::getUser();

		$content	= $blog->intro	. $blog->content;
		$content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $content );
		$image		= $this->getFirstImage( $content );

		if( JString::strlen($content) > $config->get( 'integrations_jomsocial_blogs_length', 250 ))
		{
			$content = JString::substr($content, 0, $config->get( 'integrations_mighty_blogs_length', 250 ) ) . ' ...';
		}

		$output		= '<div style="background: #f4f4f4;border: 1px solid #eeee;border-radius: 5px;padding: 5px;margin-top:5px;">';

		if( $image !== false )
		{
			$output	= $image . $content;
		}
		else
		{
			$output	.= $content;
		}

		$output	.= '<div style="text-align: right;"><a href="' . $this->getBlogLink( $blog ) . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';
		$output	.= '</div>';

		$title	= JText::sprintf( 'COM_EASYBLOG_MIGHTY_ACTIVITY_FEATURE_BLOG' , $this->getBlogLink( $blog ) , $this->getBlogTitle( $blog ) );

		JSCommunityApi::registerActivity( 0 , $title . $output , $my->id , null , 'user', null , 'com_easyblog' , null , JText::_( 'COM_EASYBLOG_MIGHTYTOUCH_FILTER_BLOGS' ) );

		return true;
	}

	public function addActivity( $blog , $new = false )
	{
		$config		= EasyBlogHelper::getConfig();

		// We do not want to add activities if new blog activity is disabled.
		if( $new && !$config->get( 'integrations_mighty_activity_new_blog' ) )
		{
			return false;
		}

		// We do not want to add activities if update blog activity is disabled.
		if( !$new && !$config->get( 'integrations_mighty_activity_update_blog') )
		{
			return false;
		}

		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'api.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}
		require_once($file);
		$date		= EasyBlogHelper::getDate();
		$priority	= 0;
		$title		= $blog->title;
		$content	= '';

		if($config->get('integrations_mighty_submit_content'))
		{
			$requireVerification = false;

			if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
			{
				$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);
				$requireVerification = true;
			}

			if($requireVerification && !EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
			{
				$theme = new CodeThemes();
				$theme->set('id', $blog->id);
				$theme->set('return', base64_encode( $this->getBlogLink( $blog ) ) );
				$output	= $theme->fetch( 'blog.protected.php' );
			}
			else
			{
				$content    = $blog->intro . $blog->content;

				$content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $content );
				$pattern	= '#<img[^>]*>#i';
				preg_match( $pattern , $content , $matches );

				// Remove all html tags from the content as we want to chop it down.
				$content	= strip_tags( $content );
				$content	= JString::substr($content, 0, $config->get( 'integrations_mighty_blogs_length', 250 ) ) . ' ...';

				$output		= '<div style="background: #f4f4f4;border: 1px solid #eeee;border-radius: 5px;padding: 5px;margin-top:5px;">';
				if( $matches )
				{
					$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );

					$output		.= $matches[0] . $content . '<div style="clear: both;"></div>';
				}
				else
				{
					$output	.= $content;
				}
				$output	.= '<div style="text-align: right;"><a href="' . $this->getBlogLink( $blog ) . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';
				$output	.= '</div>';
			}
		}

		if( $new )
		{
			$title	= JText::sprintf( 'COM_EASYBLOG_MIGHTY_ACTIVITY_BLOG' , $this->getBlogLink( $blog ) , $this->getBlogTitle( $blog ) );
		}
		else
		{
			$title	= JText::sprintf( 'COM_EASYBLOG_MIGHTY_ACTIVITY_BLOG_UPDATE' , $this->getBlogLink( $blog ) , $this->getBlogTitle( $blog ) );
		}

		if($config->get('integrations_mighty_show_category'))
		{
			$title	.= JText::sprintf( 'COM_EASYBLOG_MIGHTY_ACTIVITY_IN_CATEGORY' , $this->getCategoryLink( $blog ) , $this->getCategoryTitle( $blog ) );
		}

		JSCommunityApi::registerActivity( 0 , $title . $output , $blog->created_by , null , 'user', null , 'com_easyblog' , null , JText::_( 'COM_EASYBLOG_MIGHTYTOUCH_FILTER_BLOGS' ) );

		return true;
	}
}
