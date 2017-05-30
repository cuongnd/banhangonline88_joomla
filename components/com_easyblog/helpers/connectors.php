<?php
/*
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( 'JPATH_BASE' ) or die( 'Unauthorized Access' );

class EasyBlogConnectorsHelper
{
	public $defaultOptions	= array(
										CURLOPT_CONNECTTIMEOUT	=> 15,
										CURLOPT_RETURNTRANSFER	=> true,
										CURLOPT_TIMEOUT			=> 60,
										CURLOPT_USERAGENT		=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
										CURLOPT_HEADER			=> true
									);

	var $options	= array();
	var $handles	= array();
	var $handle		= null;
	var $result		= array();
	var $header		= array();

	var $redirects	= array( 300 , 301 , 302 , 303 , 304 , 305 , 306 , 307 );
	var $args		= array();
	var $current	= '';

	public function __construct()
	{
		$this->handle		= curl_multi_init();
	}

	public function addUrl( $url )
	{
		$this->handles[ $url ]	= curl_init( $url );
		$this->current			= $url;
		$this->options[ $url ]	= $this->defaultOptions;
		return curl_multi_add_handle( $this->handle , $this->handles[ $url ] ) === 0;
	}

	public function addQuery( $key , $value )
	{
		$this->args[ $this->current ][ $key ]	= $value;
	}

	public function addLength( $length )
	{
		$this->options[ CURLOPT_RANGE ]	= $length;
		$this->options[ CURLOPT_HEADER ]	= false;
	}

	public function useHeadersOnly()
	{
		$this->options[ CURLOPT_HEADER ]	= true;
		$this->options[ CURLOPT_NOBODY ]	= true;

		return true;
	}

	public function execute()
	{
		$running	= null;

		/**
		 * Get all handles and set the options for all respective handles
		 */
		foreach( $this->handles as $handle )
		{
			$info		= curl_getinfo( $handle );
			$url		= $info[ 'url' ];
			// If this is a post request, then we should add the necessary post data
			if( isset( $this->options[ $url ][ CURLOPT_POST ] ) && $this->options[ $url ][ CURLOPT_POST ] === true )
			{
				$this->options[ $url ][ CURLOPT_POSTFIELDS ] = http_build_query( $this->args[ $url ] );
			}

			// Set options for specific urls.
			curl_setopt_array( $handle , $this->options[ $url ] );
		}
		do
		{
			curl_multi_exec( $this->handle , $running );
		}
		while( $running > 0 );

		foreach( $this->handles as $key => $handle )
  		{
			$code	= curl_getinfo( $handle , CURLINFO_HTTP_CODE );

			if( in_array(  $code , $this->redirects ) )
			{
				// @TODO: Send logging to exceptional to log this curl error
				$error		= curl_error( $handle );

				$content	= curl_multi_getcontent( $handle );
				$headers	= explode( "\r\n\r\n" , $content );

				$this->executeRedirects( $handle , $key , $code , $headers[0] );
			}
			else
			{
			    $content    = curl_multi_getcontent( $handle );
			    $content    = explode( "\r\n\r\n" , $content );

				// we 'throw' the 1st index which we know its a header
				$htmlheader		= array_shift($content);
			    $htmlcontent    = implode("\r\n\r\n", $content);

			    $this->result[ $key ] = $htmlcontent;
			    $this->header[ $key ] = $htmlheader;
			}
			curl_multi_remove_handle( $this->handle , $handle );
		}
		curl_multi_close( $this->handle );
		return true;
	}

	function executeRedirects( $handle , $key ,  $code = '' , $headers = '' )
	{
	    static $curl_loops 		= 0;
	    static $curl_max_loops	= 20;

		if( $curl_loops++ >= $curl_max_loops )
		{
	        $curl_loops = 0;
	        return false;
	    }

		if( $curl_loops > 1 )
		{
			$data		= curl_exec( $handle );

			$res		= explode( "\n\n" , $data , 2 );
			$headers	= isset( $res[ 0 ] ) ? $res[ 0 ] : '';
			$newdata	= isset( $res[ 1 ] ) ? $res[ 1 ] : '';

			if( $curl_loops == 5 )
			{
				echo '5';
				exit;
			}

			$code		= curl_getinfo( $handle , CURLINFO_HTTP_CODE );
		}

	    if( $code == 301 || $code == 302)
		{
        	$matches = array();
        	preg_match('/Location:(.*?)\n/', $headers, $matches);

        	$url 		= @parse_url(trim(array_pop($matches)));

        	$new_url    = '';

        	if( isset( $url['host'] ) )
        	{
        	    if( $url['scheme'] == 'http' || $url['scheme'] == 'https' )
        	    {
        	    	//$new_url	= $url['scheme' ] . '://' . $url['host'] . $url['path'];
					$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ( isset( $url['query'] ) ?'?'.$url['query']:'');
        	    }
        	}

        	if( empty( $new_url ) )
        	{
		        if (!$url)
				{
		        	$curl_loops = 0;
		        	return $newdata;
		        }

			    $last_url = parse_url( curl_getinfo( $handle , CURLINFO_EFFECTIVE_URL) );

			    if ( ( isset( $url[ 'scheme'] ) && !$url['scheme'] ) || ( ! isset($url[ 'scheme']) ) )
			    {
			        $url['scheme'] = $last_url['scheme'];
			    }

			    if( ( isset( $url[ 'host'] ) && !$url['host'] ) || ( ! isset($url[ 'host']) ) )
			    {
			    	$url['host'] = $last_url['host'];
				}

			    if( ( isset( $url[ 'path' ] ) && !$url['path'] ) || ( ! isset($url[ 'path']) ) )
			    {
				    $url['path'] = $last_url['path'];
				}


			    $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ( isset( $url['query'] ) ?'?'.$url['query']:'');
		    }

		    // @rule: Refresh with a new curl resource to avoid multi init issues.
			curl_setopt( $handle , CURLOPT_URL , $new_url );
			curl_setopt( $handle , CURLOPT_RETURNTRANSFER , true );
			curl_setopt( $handle , CURLOPT_HEADER , true );
			$data 		= curl_exec( $handle );
			$content	= curl_multi_getcontent( $handle );
			$headers	= explode( "\r\n\r\n" , $content );

			$code	= curl_getinfo( $handle , CURLINFO_HTTP_CODE );

			if( $code == 301 || $code == 302)
			{
				return self::executeRedirects( $handle , $key , $code , $headers );
			}

			$curl_loops				= 0;
			$this->result[ $key ]	= $data;
		}
		else
		{
			$curl_loops				= 0;
			$this->result[ $key ]	= $data;
		}
	}

	public function getResult( $url )
	{

		if( !isset( $this->result[ $url ] ) )
		{
			return false;
		}
		return $this->result[ $url ];
	}

	public function getResultHeader( $url )
	{

		if( !isset( $this->header[ $url ] ) )
		{
			return false;
		}
		return $this->header[ $url ];
	}

	public function getResults()
	{
		return $this->result;
	}

	public function addOption( $key , $value )
	{
		$this->options[$this->current][ $key ]	= $value;
	}

	public function addFile( $resource , $size )
	{
		$this->addOption( CURLOPT_INFILE , $resource );
		$this->addOption( CURLOPT_INFILESIZE , $size );

		return true;
	}

	public function setMethod( $method = 'GET' )
	{
		switch( $method )
		{
			case 'GET':
				$this->addOption( CURLOPT_HTTPGET , true );
			break;
			case 'POST':
				$this->addOption( CURLOPT_POST , true );
			break;
			case 'PUT':
				$this->addOption( CURLOPT_PUT , true );
			break;
		}

		return true;
	}
}