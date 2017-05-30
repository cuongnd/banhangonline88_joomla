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
include_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

class EasyBlogTooltipHelper
{
	/*
	 * Returns a html formatted string for a standard tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public static function getHTML( $content, $options )
	{
		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'content'	, $content );
		$themes->set( 'options'	, $options );

		return $themes->fetch( 'tooltip.php' );
	}

	/*
	 * Returns a html formatted string for the blogger's tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public static function getTeamHTML( $teamId, $options )
	{
		return '';

		$team->load( $teamId );

		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'team'	, $team );
		$themes->set( 'options' , $options );

		return $themes->fetch( 'tooltip.team.php' );
	}

	/*
	 * Returns a html formatted string for the blogger's tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public static function getBloggerHTML( $userId, $options )
	{
		return '';

		$user->load( $userId );

		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'user'	, $user );
		$themes->set( 'options' , $options );

		return $themes->fetch( 'tooltip.blogger.php' );
	}

	/*
	 * Returns a html formatted string for the calendar's tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public static function getCalendarHTML( $data , $date, $options, $itemId )
	{
		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'data'	, $data );
		$themes->set( 'date'	, $date );
		$themes->set( 'options'	, $options );
		$themes->set( 'itemId'	, $itemId );

		return $themes->fetch( 'tooltip.calendar.php' );
	}

	public static function getTagsHTML( $data, $options )
	{
		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'data'	, $data );
		$themes->set( 'options'	, $options );

		return $themes->fetch( 'tooltip.tags.php' );
	}
}
