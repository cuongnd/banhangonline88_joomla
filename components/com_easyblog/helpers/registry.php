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

class EasyBlogRegistryHelper
{	
	/**
	 * Merge a JRegistry object into this one
	 *
	 * @param   JRegistry  &$source  Source JRegistry object to merge.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function extend(&$source , &$extend )
	{
		$sourceObj	= $source->toObject();

		foreach( $extend->toArray() as $index => $value )
		{
			$source->set( $index , $value );
		}
	}
}