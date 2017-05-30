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

class SocialCrawlerOEmbed
{

	/**
	 * Ruleset to process document opengraph tags
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.
	 */
	public function process( $parser , &$contents , $uri , $absoluteUrl , $originalUrl )
	{
		$oembed 	= new stdClass();

		if (stristr( $uri , 'pastebin.com' ) !== false) {
			return $this->pastebin($oembed, $absoluteUrl);
		}

		if ($uri == 'https://gist.github.com') {
			return $this->gist($oembed, $absoluteUrl);
		}

		if(stristr( $uri , 'soundcloud.com' ) !== false)
		{
			return $this->soundCloud( $oembed , $absoluteUrl );
		}

		if( stristr( $uri , 'mixcloud.com' ) !== false )
		{
			return $this->mixCloud( $parser , $oembed , $absoluteUrl );
		}

		if( stristr( $uri , 'spotify.com' ) !== false )
		{
			return $this->spotify( $oembed , $originalUrl );
		}

		foreach( $parser->find( 'link[type=application/json+oembed]' ) as $node )
		{
			// Get the oembed url
			if( !isset( $node->attr[ 'href' ] ) )
			{
				continue;
			}

			// Get the oembed url from the doc
			$url	= $node->attr[ 'href' ];

			// Load up the connector first.
			$connector 		= FD::get( 'Connector' );
			$connector->addUrl( $url );
			$connector->connect();

			// Get the result and parse them.
			$contents 	= $connector->getResult( $url );

			// We are retrieving json data
			$oembed 		= FD::json()->decode( $contents );

			// Test if thumbnail_url is set so we can standardize this
			if( isset($oembed->thumbnail_url) )
			{
				$oembed->thumbnail 	= $oembed->thumbnail_url;
			}
		}

		return $oembed;
	}

	public function pastebin(&$oembed, $absoluteUrl)
	{
		$segment 		= str_ireplace('http://pastebin.com/', '', $absoluteUrl);

		$oembed->html 	= '<iframe src="http://pastebin.com/embed_iframe.php?i=' . $segment . '" style="border:none;width:100%"></iframe>';

		return $oembed;
	}

	public function gist(&$oembed, $absoluteUrl)
	{
		$oembed->html 	= '<script src="' . $absoluteUrl . '.js"></script>';

		return $oembed;
	}

	public function mixCloud( $parser , &$oembed , $absoluteUrl )
	{
		$url 	= 'http://www.mixcloud.com/oembed/?url=' . urlencode($absoluteUrl) . '&format=json';

		// Load up the connector first.
		$connector 		= FD::get( 'Connector' );
		$connector->addUrl($url);
		$connector->connect();

		// Get the result and parse them.
		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail 		= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function soundCloud( &$oembed , $absoluteUrl )
	{
		$url 		= 'http://soundcloud.com/oembed?format=json&url=' . urlencode( $absoluteUrl );

		$connector 	= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if( isset($oembed->thumbnail_url) )
		{
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function spotify( &$oembed , $absoluteUrl )
	{
		$url 		= 'https://embed.spotify.com/oembed/?url=' . urlencode( $absoluteUrl );

		$connector 	= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if( isset($oembed->thumbnail_url) )
		{
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}
}
