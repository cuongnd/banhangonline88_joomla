<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/views/views' );

class EasySocialViewNews extends EasySocialAdminView
{
	/**
	 * Returns an array of news item in JSON format.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	A list of news items.
	 * @return	SocialAjax
	 *
	 */
	public function getNews( $news , $appNews = array() )
	{
		$ajax 	= FD::ajax();

		// Format the output of the news item since we need the following values
		// day,month
		foreach( $news as &$item )
		{
			$date				= explode( '/' , $item->date );

			$item->day 		= $date[ 0 ];
			$item->month	= $date[ 1 ];
		}

		$theme 	= FD::themes();
		$theme->set( 'items' , $news );
		$content	= $theme->output( 'admin/news/items' );

		if( $appNews )
		{
			foreach( $appNews as &$appItem )
			{
				$date			= FD::date( $appItem->updated );

				$appItem->lapsed	= $date->toLapsed();
				$appItem->day 		= $date->format( 'd' );
				$appItem->month		= $date->format( 'M' );
			}
		}

		$theme 	= FD::themes();
		$theme->set( 'items' , $appNews );
		$apps		= $theme->output( 'admin/news/apps' );

		return $ajax->resolve( $content , $apps );
	}
}
