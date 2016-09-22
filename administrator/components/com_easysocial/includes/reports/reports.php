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

class SocialReports
{
	/**
	 * This is the factory method to ensure that this class is always created all the time.
	 * Usage: FD::get( 'Template' );
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public static function factory()
	{
		return new self();
	}

	/**
	 * Generates the report link to allow users to report on an item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getForm( $extension , $type , $uid , $itemTitle , $text , $title = '' , $description = '' ,  $url = '', $icon=false )
	{
		// Determine if the user is a guest
		$my 		= FD::user();
		$config 	= FD::config();

		if( !$my->id )
		{
			if( !$config->get( 'reports.guests' , false ) )
			{
				return;
			}
		}
		else
		{
			// Check if user is allowed to report.
			$access		= FD::access();

			// @access: reports.submit
			// Check if user is allowed to create reports
			if( !$access->allowed( 'reports.submit' ) )
			{
				return;
			}

			$model 	= FD::model( 'Reports' );
			$usage	= $model->getCount( array( 'created_by' => $my->id ) );

			// Check if the current user exceeded the reports limit
			if( $access->exceeded( 'reports.limit' , $usage ) )
			{
				return;
			}
		}

		$theme 	= FD::themes();

		// Set a default text if API wasn't provided with a custom text.
		if( empty( $text ) )
		{
			$text 	= JText::_( 'COM_EASYSOCIAL_REPORTS_REPORT_ITEM' );
		}

		// If url is not provided, use the current URL.
		if( empty( $url ) )
		{
			$url 	= JRequest::getURI();
		}

		// If title is not supplied, we use the text
		if( empty( $title ) )
		{
			$title 	= $text;
		}

		$theme->set( 'url'			, $url );
		$theme->set( 'extension' 	, $extension );
		$theme->set( 'itemTitle' 	, $itemTitle );
		$theme->set( 'title' 		, $title );
		$theme->set( 'text' 		, $text );
		$theme->set( 'type' 		, $type );
		$theme->set( 'uid'			, $uid );
		$theme->set( 'description'	, $description );
		$theme->set( 'icon'         , $icon );

		$contents 	= $theme->output( 'site/reports/default.link' );

		return $contents;
	}
}
