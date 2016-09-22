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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );

class EasyBlogGoogleBuzz
{
	function getHTML( $row )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get('main_googlebuzz') )
		{
			return '';
		}

		$position	= $config->get('main_googlebuzz_position' , 'left' );

		$html		= '<div class="social-button google-buzz">
						<a href="http://www.google.com/buzz/post" class="google-buzz-button" title="Google Buzz" data-message="' . $row->title . '" data-button-style="normal-count"></a>
						<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>
						</div>';

		return $html;
	}
}
