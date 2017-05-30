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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewRatings extends EasyBlogView
{
	public function vote( $value , $uid , $type , $elementId )
	{
		$ajax	= new Ejax();
		$my		= JFactory::getUser();
		$config = EasyBlogHelper::getConfig();

		$blog	= EasyBlogHelper::getTable( 'Blog', 'Table' );
		$blog->load($uid);

		if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
		{
			if(!EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
			{
				echo 'Invalid Access.';
				exit;
			}
		}

		$rating	= EasyBlogHelper::getTable( 'Ratings' , 'Table' );

		// Do not allow guest to vote, or if the voter already voted.
		if( $rating->fill( $my->id , $uid , $type , JFactory::getSession()->getId() ) || ( $my->id < 1 && !$config->get( 'main_ratings_guests') ) )
		{
			// We wouldn't allow user to vote more than once so don't do anything here
			$ajax->send();
		}

		$rating->set( 'created_by'	, $my->id );
		$rating->set( 'type'		, $type );
		$rating->set( 'uid' 		, $uid );
		$rating->set( 'ip'			, @$_SERVER['REMOTE_ADDR'] );
		$rating->set( 'value' 		, (int) $value );
		$rating->set( 'sessionid'	, JFactory::getSession()->getId() );
		$rating->set( 'created'		, EasyBlogHelper::getDate()->toMySQL() );
		$rating->set( 'published'	, 1 );
		$rating->store();

		$model			= EasyBlogHelper::getModel( 'Ratings' );
		$ratingValue	= $model->getRatingValues( $uid , $type );
		$total			= $ratingValue->total;
		$rating			= $ratingValue->ratings;

		// Assign badge for users that report blog post.
		// Only give points if the viewer is viewing another person's blog post.
		EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.rate' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_RATED_BLOG' ) );	

		$ajax->script( 'eblog.loader.doneLoading("' . $elementId . '-command .ratings-text")' );
		$ajax->script( 'eblog.ratings.update("' . $elementId . '", "' . $type . '" , "' . $rating . '" , "' . JText::_( 'COM_EASYBLOG_RATINGS_RATED_THANK_YOU' ) . '");');
		$ajax->assign( $elementId . ' .ratings-value' , '<i></i>' . $total . '<b>&radic;</b>' );

		if ( EasyBlogHelper::isAUPEnabled() )
		{
			$id	= AlphaUserPointsHelper::getAnyUserReferreID( $my->id );
			AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_rate_blog', $id, '', JText::sprintf('COM_EASYBLOG_AUP_BLOG_RATED'), '' );
		} 	

		$ajax->send();
	}

	public function showVoters( $elementId , $type )
	{
		$ajax	= new Ejax();
		$model	= $this->getModel( 'Ratings' );
		$config	= EasyBlogHelper::getConfig();
		$voters	= $model->getRatingUsers( $elementId , $type , $config->get('main_ratings_display_raters_max') );

		$guests	= false;

		$theme	= new CodeThemes();
		$theme->set( 'guests' , $guests );
		$theme->set( 'voters' , $voters );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DIALOG_TITLE_RECENT_VOTERS' );
		$options->content	= $theme->fetch( 'ratings.users.php' );
		$ajax->dialog( $options );

		return $ajax->send();
	}
}
