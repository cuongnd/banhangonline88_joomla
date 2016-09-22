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

require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

class EasyBlogURLShortenerHelper
{
	function get_short_url($login, $apikey, $url, $provider='bitly')
	{
		$function = "make_".$provider."_url";
		return $this->$function($login, $apikey, $url, 'json');
	}

	function make_bitly_url($login, $apikey, $url, $format='json')
	{
		$restURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$apikey.'&uri='.urlencode($url).'&format='.$format;

		return $this->make_short_url($restURL);
	}

	function make_short_url($restURL)
	{
		$out	 = "GET ".$restURL." HTTP/1.1\r\n";
		$out	.= "Host: api.bit.ly\r\n";
		$out	.= "Content-type: application/x-www-form-urlencoded\r\n";
		$out	.= "Connection: Close\r\n\r\n";

		$handle = @fsockopen ('api.bit.ly', 80 , $errno, $errstr, 30);
		fwrite( $handle , $out );

		$body		= false;
		$contents	= '';
		while( !feof( $handle ) )
		{
			$return	= fgets( $handle , 1024 );

			if( $body )
			{
				$contents	.= $return;
			}

			if( $return == "\r\n" )
			{
				$body	= true;
			}
		}
		fclose($handle);

		$json	= new Services_JSON();
		$data	= $json->decode( $contents );

		//for issues with unable to authenticate error, somehow they return errors instead of error.
		if( !isset( $data->status_code ) )
		{
			return false;
		}

		if($data->status_code!='200')
		{
			return false;
		}

		return $data->data->url;
	}
}
