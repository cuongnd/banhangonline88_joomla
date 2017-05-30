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

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
defined('_JEXEC') or die('Restricted access');

class EasySimpleImage
{

   var $image;
   var $image_type;
   var $src_filename;
   var $orientationFixed = false;

   function load($filename)
   {
      $image_info = getimagesize($filename);

      $this->image_type 	= $image_info[2];
	  $this->src_filename   = $filename;

	  if( $this->image_type == IMAGETYPE_JPEG )
	  {
         $this->image = imagecreatefromjpeg($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_GIF )
	  {
         $this->image = imagecreatefromgif($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_PNG )
	  {
         $this->image = imagecreatefrompng($filename);
      }

	  $this->fixOrientation( $filename );
   }

   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null)
   {
   		$contents	= '';

		if( $image_type == IMAGETYPE_JPEG )
		{
			ob_start();
			imagejpeg( $this->image , null , $compression );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_GIF )
		{
			ob_start();
			imagegif( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_PNG )
		{
			ob_start();
			imagepng( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}

		if( !$contents )
		{
			return false;
		}
		jimport( 'joomla.filesystem.file' );
		$status	= @JFile::write( $filename , $contents );

		if( $permissions != null)
		{
			chmod($filename,$permissions);
		}

		return $status;
   }

   function output($image_type=IMAGETYPE_JPEG)
   {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }
   function getWidth()
   {
      return imagesx($this->image);
   }

   function getHeight()
   {
      return imagesy($this->image);
   }

   function resizeToHeight($height)
   {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width)
   {
      $ratio = $width / $this->getWidth();
      $height = $this->getHeight() * $ratio;
      $this->resize($width,$height);
   }

   function scale($scale)
   {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }



   function resize($width, $height)
   {
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
		    $new_image = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy( $new_image , $this->image );
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, 0, 0, $width , $height , $this->getWidth() , $this->getHeight() );
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{
		    $new_image 		= imagecreatetruecolor( $width , $height );
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
		}
		$this->image = $new_image;
	}

	function resizeWithin($maxWidth, $maxHeight)
	{
		//@TODO: resizeWithin should also scale smaller images, with a boolean flag to disable it.

		$sourceWidth  = $this->getWidth();
		$sourceHeight = $this->getHeight();
		$targetWidth  = $sourceWidth;
		$targetHeight = $sourceHeight;

		if (!empty($maxWidth) && $targetWidth > $maxWidth)
		{
			$ratio = $maxWidth / $sourceWidth;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if (!empty($maxHeight) && $targetHeight > $maxHeight)
		{
			$ratio = $maxHeight / $sourceHeight;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		$this->resize($targetWidth, $targetHeight);
	}

	function resizeToFit($maxWidth, $maxHeight)
	{
		$sourceWidth  = $this->getWidth();
		$sourceHeight = $this->getHeight();
		$targetWidth  = $sourceWidth;
		$targetHeight = $sourceHeight;

		$newX = 0;
		$newY = 0;
		$oriX = 0;
		$oriY = 0;

		$newWidth       = $maxWidth;
		$newHeight      = $maxHeight;

		if (!empty($maxWidth) && $targetWidth > $maxWidth)
		{
			$ratio = $maxWidth / $sourceWidth;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if (!empty($maxHeight) && $targetHeight > $maxHeight)
		{
			$ratio = $maxHeight / $sourceHeight;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if( $newWidth > $targetWidth )
		{
			$newX   = intval( ($newWidth - $targetWidth) / 2 );
		}

		if( $newHeight > $targetHeight )
		{
			$newY   = intval( ($newHeight - $targetHeight) / 2 );
		}

		//rebuilding new image
		$new_image = imagecreatetruecolor($newWidth, $newHeight);

		if( $this->image_type == IMAGETYPE_JPEG )
		{
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{

			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, $newX, $newY, $oriX, $oriY, $targetWidth , $targetHeight , $sourceWidth , $sourceHeight );

		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{

			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $newWidth, $newHeight, $transparent);
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
		}

		$this->image = $new_image;
	}

	function fixOrientation( $filename )
	{
		if( !function_exists( 'exif_read_data' ) )
		{
			return false;
		}

		// currently this only support jpeg
		// see http://php.net/manual/en/function.imagerotate.php
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$exif 			= exif_read_data( $filename );
			$orientation	= ( isset( $exif['Orientation'] ) && !empty( $exif['Orientation'] ) ) ? $exif['Orientation'] : 1;

		    switch($orientation)
		    {
		        case 1: // nothing
		        break;

		        case 2: // horizontal flip
		            //$image->flipImage($public,1);
		        break;

		        case 3: // 180 rotate left
		            $this->_rotateImage(180);
		            $this->orientationFixed = true;
		        break;

		        case 4: // vertical flip
		            //$image->flipImage($public,2);
		        break;

		        case 5: // vertical flip + 90 rotate right
		            //$image->flipImage($public, 2);
		        	//$this->_rotateImage(-90);
		        break;

		        case 6: // 90 rotate right
		            $this->_rotateImage(-90);
		            $this->orientationFixed = true;
		        break;

		        case 7: // horizontal flip + 90 rotate right
		            //$image->flipImage($public,1);
		            //$this->_rotateImage(-90);
		        break;

		        case 8:    // 90 rotate left
		            $this->_rotateImage(90);
		            $this->orientationFixed = true;
		        break;
		    }

		}
	}

	function _rotateImage( $degree )
	{
		$this->image = imagerotate($this->image, $degree, 0);
	}

	// TODO: Ability to expand original image source if dimension is smaller.
	function resizeToFill($maxWidth, $maxHeight) {

		$sourceWidth   = $this->getWidth();
		$sourceHeight  = $this->getHeight();
		$targetWidth   = $sourceWidth;
		$targetHeight  = $sourceHeight;

		$ratio = $maxWidth / $sourceWidth;
		$targetWidth = $sourceWidth * $ratio;
		$targetHeight = $sourceHeight * $ratio;

		if ($targetHeight < $maxHeight) {
			$ratio = $maxHeight / $sourceHeight;
			$targetWidth = $sourceWidth * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		$targetTop = $maxHeight - $targetHeight;
		$targetLeft = $maxWidth - $targetWidth;
		$targetWidth = ($targetWidth + $targetLeft) / $ratio;
		$targetHeight = ($targetHeight + $targetTop) / $ratio;

		$targetTop = abs($targetTop / 2) / $ratio;
		$targetLeft = abs($targetLeft / 2) / $ratio;

		//rebuilding new image
		$new_image = imagecreatetruecolor($maxWidth, $maxHeight);

		if( $this->image_type == IMAGETYPE_JPEG ) {

			imagecopyresampled($new_image, $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth, $maxHeight, $targetWidth, $targetHeight);

		} elseif( $this->image_type == IMAGETYPE_GIF ) {

			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth , $maxHeight , $targetWidth , $targetHeight );

		} elseif( $this->image_type == IMAGETYPE_PNG ) {

			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $maxWidth, $maxHeight, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth, $maxHeight, $targetWidth, $targetHeight);
		}

		$this->image = $new_image;
	}

	// TODO: Ability to crop the image from center of the source image.
	function crop($width, $height)
	{
   		$oriHeight  = $this->getHeight();
   		$oriWidth   = $this->getWidth();
   		$newHeight	= $height;
   		$newWidth	= $width;

		$newX = 0;
		$newY = 0;
		$oriX = 0;
		$oriY = 0;

  		$oriX   = intval( ( $oriWidth - $newWidth ) / 2 );
		$oriY   = intval( ( $oriHeight - $newHeight ) / 2 );

		//rebuilding new image
		$new_image = imagecreatetruecolor($newWidth, $newHeight);

		if( $this->image_type == IMAGETYPE_JPEG )
		{
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $newWidth, $newHeight, $newWidth, $newHeight);
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{

			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, $newX, $newY, $oriX, $oriY, $newWidth , $newHeight , $newWidth , $newHeight );

		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{

			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $newWidth, $newHeight, $transparent);
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $newWidth, $newHeight, $newWidth, $newHeight);
		}

		$this->image = $new_image;
	}
}
