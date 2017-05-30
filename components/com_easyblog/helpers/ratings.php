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

class EasyBlogRatingsHelper
{
	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function getHTML( $uid , $type , $command , $elementId , $disabled = false )
	{
		$config		= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_ratings') )
		{
			return false;
		}

		$language	= JFactory::getLanguage();
		$language->load( 'com_easyblog' , JPATH_ROOT );

		// Add ratings to the page
		$document	= JFactory::getDocument();

		$rating	= EasyBlogHelper::getTable( 'Ratings' , 'Table' );
		$my		= JFactory::getUser();

		$hash	= $my->id > 0 ? '' :  JFactory::getSession()->getId();
		$voted	= $rating->fill( $my->id , $uid , $type , $hash );
		$locked	= $voted || ( $my->id < 1 && !$config->get( 'main_ratings_guests' ) ) || $disabled ;

		$model 			= EasyBlogHelper::getModel( 'Ratings' );
		$ratingValue	= $model->getRatingValues( $uid , $type );

		$theme			= new CodeThemes();

		// Only display ratings on entry view
		$entry 			= JRequest::getVar('view') == 'entry' ? true : false;

		$theme->set('entry'		, $entry);
		$theme->set( 'voted'	, $voted );
		$theme->set( 'elementId', $elementId );
		$theme->set( 'rating'	, $ratingValue->ratings );
		$theme->set( 'total'	, $ratingValue->total );
		$theme->set( 'locked'	, $locked );
		$theme->set( 'command'	, $command );
		$theme->set( 'uid' 	, $uid );
		$theme->set( 'type'	, $type );

		return $theme->fetch( 'ratings.form.php' );
	}
}
