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
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'item.php' );

class EasyBlogMediaManagerFile extends EasyBlogMediaManagerItem
{
	public $file	 	= null;
	public $type 		= 'file';

	public function __construct( $file , $baseURI , $relativePath = '' )
	{
		$this->file 	= $file;

		$this->baseURI	= $baseURI;
		$this->relativePath	= $relativePath;
	}

	/**
	 * Get a list of variations for the particular image item.
	 *
	 * @access	public
	 * @param	null
	 * @return 	Array	An array of variation objects.
	 */
	public function inject( &$obj )
	{
		$info 	= pathinfo( $this->file );

		$icon	= $this->getIconMap( $info[ 'extension' ] );

		$obj->icon			= new stdClass();
		$obj->icon->url		= JURI::root() . 'components/com_easyblog/themes/dashboard/system/images/media/' . $icon . '.png';
	}


	public function getSize()
	{
		return $this->formatSize( filesize( $this->file ) );
	}
}
