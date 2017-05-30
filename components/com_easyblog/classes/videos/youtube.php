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

class EasyBlogVideoYoutube
{
	private function getCode( $url )
	{
		// Some content plugins tries to replace & with &amp; in the content. We need to ensure that the URL doesn't contain &amp;
		$url	= str_ireplace( '&amp;' , '&' , $url );

		/* match http://www.youtube.com/watch?v=TB4loah_sXw&feature=fvst */
		preg_match( '/youtube.com\/watch\?v=(.*)(?=&feature)(?=&)/is' , $url , $matches );

		if( !empty( $matches ) )
		{
			// Double test to ensure that the codes doesn't contain any '&'
			$code 	= explode( '&' , $matches[ 1 ] );

			if( count( $code ) > 1 )
			{
				return $code[0];
			}

			return $matches[1];
		}


		/* New format: http://www.youtube.com/user/ToughMudder?v=w1PhUWGz_xw */
		preg_match( '/youtube.com\/user\/(.*)\?v=(.*)/is' , $url , $matches );

		if( !empty( $matches ) )
		{
			// Double test to ensure that the codes doesn't contain any '&'
			$code 	= explode( '&' , $matches[ 2 ] );


			if( count( $code ) > 1 )
			{
				return $code[0];
			}

			return $matches[1];
		}

		/* match http://www.youtube.com/watch?v=sr1eb3ngYko */
		preg_match( '/youtube.com\/watch\?v=(.*)/is' , $url , $matches );
		if( !empty( $matches ) )
		{
			// @task: Replace any '&' in the way.
			$matches[1]	= str_ireplace( '&' , '' , $matches[1] );

			return $matches[1];
		}

		/* match http://www.youtube.com/watch?feature=player_embedded&v=XUaTQKeDw4E */
		preg_match( '/youtube.com\/watch\?.*v=(.*)/is' , $url , $matches );

		if( !empty( $matches ) )
		{
			return $matches[1];
		}

		preg_match( '/youtu.be\/(.*)/is' , $url , $matches );

		if( !empty( $matches ) )
		{
			return $matches[1];
		}

		return false;
	}

	public function getEmbedHTML( $url , $width , $height )
	{
		$code	= $this->getCode( $url );

		if( $code )
		{
			return '<div class="video-container"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $code . '?wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}
		else
		{
		    // this video do not have a code. so include the url directly.
			return '<div class="video-container"><iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="' . $url . '&wmode=transparent" frameborder="0" allowfullscreen></iframe></div>';
		}
		return false;
	}
}
