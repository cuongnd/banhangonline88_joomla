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

class DiscussUrlHelper
{
	public static function replace( $tmp , $text )
	{
		$pattern		= '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all( $pattern , $tmp , $matches );

		$config			= DiscussHelper::getConfig();
		$targetBlank	= $config->get( 'main_link_new_window' ) ? ' target="_blank"' : '';

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			// to avoid infinite loop, unique the matches
			$uniques = array_unique($matches[ 0 ]);

			foreach( $uniques as $match )
			{
				$matchProtocol 	= $match;

				if( stristr( $matchProtocol , 'http://' ) === false && stristr( $matchProtocol , 'https://' ) === false && stristr( $matchProtocol , 'ftp://' ) === false )
				{
					$matchProtocol	= 'http://' . $matchProtocol;
				}

				$text   = str_ireplace( $match , '<a href="' . $matchProtocol . '"' . $targetBlank . '>' . $match . '</a>' , $text );
			}
		}

		$text	= str_ireplace( '&quot;' , '"', $text );
		return $text;
	}

	public static function clean( $url )
	{
		$juri	= JFactory::getURI($url);
		$juri->parse($url);
		$scheme = $juri->getScheme() ? $juri->getScheme() : 'http';
		$juri->setScheme( $scheme );

		return $juri->toString();
	}
}
