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


require_once( dirname( __FILE__ ) . '/lib.php' );

class SocialStorageAmazon implements SocialStorageInterface
{
	private $lib 	= null;
	public function __construct()
	{
		$config 		= FD::config();
		$access			= trim( $config->get( 'storage.amazon.access' ) );
		$secret 		= trim( $config->get( 'storage.amazon.secret' ) );
		$this->bucket	= $config->get( 'storage.amazon.bucket' );
		$this->bucket 	= rtrim( $this->bucket , '/' );
		$this->config 	= $config;

		$region 		= $this->config->get( 'storage.amazon.region' );

		if( $region == 'us' )
		{
			$endpoint 	= 's3.amazonaws.com';
		}
		else
		{
			$endpoint 	= 's3-' . $this->config->get( 'storage.amazon.region' ) . '.amazonaws.com';
		}

		$this->region 	= $endpoint;

		$this->lib 	= new SocialAmazonLibrary( $access , $secret , true , $endpoint );
	}

	public function init()
	{
		$config 	= FD::config();
		$bucket 	= $config->get( 'storage.amazon.bucket' );
		$bucket 	= trim( $bucket );

		// We assume that either the system or the user has already created the bucket.
		if( !empty( $bucket ) )
		{
			return $bucket;
		}

		// Initialize to check if the container exists
		$jConfig 	= FD::config( 'joomla' );
		$bucket 	= str_ireplace( 'http://' , '' , JURI::root() );
		$bucket 	= JFilterOutput::stringURLSafe( $bucket );

		if( !$this->containerExists( $bucket ) )
		{
			$this->createContainer( $bucket );
		}

		return $bucket;
	}

	public function containerExists( $container )
	{
		$containers	= $this->lib->listBuckets();

		return in_array( $container , $containers );
	}

	public function createContainer( $container )
	{
		$config		= FD::config();
		$region		= strtolower( $config->get( 'storage.amazon.region' ) );

		$state		= $this->lib->putBucket( $container	, SocialAmazonLibrary::ACL_PRIVATE , $region );

		return $state;
	}

	/**
	 * Returns the absolute path to the object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The storage id
	 * @return	string	The absolute URI to the object
	 */
	public function getPermalink($relativePath)
	{
		// Ensure that the preceeding / is removed
		$relativePath	= ltrim($relativePath, '/');

		$paths			= explode('/', $this->bucket);

		$subfolder		= false;
		$base			= $paths[ 0 ];

		if (count($paths) > 1)
		{
			unset($paths[0]);
			$subfolder = implode('/', $paths);
		}

		$config = FD::config();
		$url = $config->get('storage.amazon.ssl') ? 'https://' : 'http://';

		if ($url == 'https://' || stristr($base, '_') !== false) {
			$url .= $this->region . '/' . $base . '/';
		} else {
			$url .= $base . '.' . $this->region . '/';	
		}

		if ($subfolder) {
			$url .= rtrim($subfolder, '/') . '/';
		}

		$url .= $relativePath;

		return $url;
	}

	/**
	 * Pushes a file to the remote repository
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function push( $fileName , $source , $dest )
	{
		$file 	= $this->lib->inputFile( $source );

		// Ensure that there is no preceeding / in front of the relative path.
		$dest	= ltrim( $dest , '/' );

		// Try to push the object over now
		$state	= $this->lib->putObject( $file , $this->bucket , $dest , SocialAmazonLibrary::ACL_PUBLIC_READ , array() , array("Content-Type" => "application/octet-stream", "Content-Disposition" => "attachment; filename=" . $fileName ) );

		return $state;
	}

	/**
	 * Pulls a file from the remote repositor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function pull()
	{
	}

	/**
	 * Deletes a file from the remote repository
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function delete( $paths , $folder = false )
	{
		if( is_array( $paths ) )
		{
			// Ensure that all indexes are integers
			$paths 	= array_values( $paths );

			foreach( $paths as $relativePath )
			{
				// Ensure that leading / is removed
				$relativePath	= ltrim( $relativePath , '/' );

				// Finally delete the last item
				$this->lib->deleteObject( $this->bucket , $relativePath );
			}

			return true;
		}

		// Ensure that leading / is removed
		$paths	= ltrim( $paths , '/' );

		if( $folder )
		{
			$objects	= $this->lib->getBucket( $this->bucket , $paths );

			foreach( $objects as $object )
			{
				$this->lib->deleteObject( $this->bucket , $object[ 'name' ] );
			}

			return true;
		}

		$this->lib->deleteObject( $this->bucket , $paths );

		return true;
	}
}
