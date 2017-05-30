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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

/**
 * @package		Joomla
 * @subpackage	Media
 */
class EasyImageHelper
{
	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	public static function isImage( $fileName )
	{
		static $imageTypes = 'gif|jpg|jpeg|png';
		return preg_match("/$imageTypes/i",$fileName);
	}

	public static function getFileName( $imageURL )
	{
		if( empty($imageURL) )
			return '';

		$filename = basename($imageURL);
		return $filename;
	}

	public static function getFileExtension( $filename )
	{
		if( empty( $filename ) )
			return '';

		// it will return with the leading dot e.g .jpg .png
		$file_extension = substr($filename , strrpos($filename , '.') +1);
		return $file_extension;
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return file type
	 */
	public static function getTypeIcon( $fileName )
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	 */
	public static function canUpload( $file, &$err )
	{
		//$params = &JComponentHelper::getParams( 'com_media' );
		$params = EasyBlogHelper::getConfig();

		if(empty($file['name'])) {
			$err = 'COM_EASYBLOG_WARNEMPTYFILE';
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = 'COM_EASYBLOG_WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		if(! EasyImageHelper::isImage($file['name']) )
		{
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		$maxWidth	= 160;
		$maxHeight	= 160;

		// maxsize should get from eblog config
		//$maxSize	= 2000000; //2MB
		//$maxSize	= 200000; //200KB

		// 1 megabyte == 1048576 byte
		$byte   		= 1048576;
		$uploadMaxsize  = (float) $params->get( 'main_upload_image_size', 0 );
		$maxSize 		= $uploadMaxsize * $byte;

		if ($maxSize > 0 && (float) $file['size'] > $maxSize)
		{
			$err = 'COM_EASYBLOG_WARNFILETOOLARGE';
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;

		if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		return true;
	}

	public static function canUploadFile( $file , &$msgObj = false )
	{
		$config		= EasyBlogHelper::getConfig();

		if(empty($file['name']))
		{
			return JText::_( 'COM_EASYBLOG_IMAGE_UPLOADER_PLEASE_INPUT_A_FILE_FOR_UPLOAD' );
		}

		jimport('joomla.filesystem.file');

		// if ($file['name'] !== JFile::makesafe($file['name']))
		// {
		// 	if( $msgObj )
		// 	{
		// 		$msgObj->code		= EBLOG_MEDIA_FILE_EXTENSION_ERROR;
		// 		$msgObj->message	= JText::_( 'COM_EASYBLOG_WARNFILENAME' );
		// 	}
		// 	return JText::_( 'COM_EASYBLOG_WARNFILENAME' );
		// }

		$format		= strtolower(JFile::getExt($file['name']));
		$allowed	= explode( ',' , $config->get( 'main_media_extensions' ) );

		if( !in_array( $format , $allowed ) )
		{
			if( $msgObj )
			{
				$msgObj->code		= EBLOG_MEDIA_FILE_EXTENSION_ERROR;
				$msgObj->message	= JText::_( 'COM_EASYBLOG_FILE_NOT_ALLOWED' );
			}

			return JText::_( 'COM_EASYBLOG_FILE_NOT_ALLOWED' );
		}

		$byte   		= 1048576;
		$uploadMaxsize  = (float) $config->get( 'main_upload_image_size', 0 );
		$maxSize 		= $uploadMaxsize * $byte;

		if( $maxSize > 0 && (int) $file['size'] > $maxSize )
		{
			if( $msgObj )
			{
				$msgObj->code		= EBLOG_MEDIA_FILE_TOO_LARGE;
				$msgObj->message	= JText::_( 'COM_EASYBLOG_WARNFILETOOLARGE' );
			}
			return JText::_( 'COM_EASYBLOG_WARNFILETOOLARGE' );
		}

		// Sanitize the content of the files
		$content	= @JFile::read( $file[ 'tmp_name' ] , false , 256 );
		$tags		= array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');

		if( $content )
		{
			foreach( $tags as $tag )
			{
				if( stristr( $content , '<' . $tag . ' ') || stristr( $content , '<' . $tag . '>' ) || stristr( $content , '<?php' ) || stristr( $content , '?\>' ) )
				{
					if( $msgObj )
					{
						$msgObj->code		= EBLOG_MEDIA_SECURITY_ERROR;
						$msgObj->message	= JText::_( 'COM_EASYBLOG_FILE_CONTAIN_XSS' );
					}
					return JText::_( 'COM_EASYBLOG_FILE_CONTAIN_XSS' );
				}
			}
		}

		return true;
	}

	function parseSize($size)
	{
		if ($size < 1024) {
			return $size . ' bytes';
		}
		else
		{
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	public static function imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	public static function countFiles( $dir )
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}

	public static function getAvatarDimension($avatar)
	{
		$config			= EasyBlogHelper::getConfig();

		//resize the avatar image
		$avatar	= JPath::clean( JPATH_ROOT . DIRECTORY_SEPARATOR . $avatar );
		$info	= @getimagesize($avatar);
		if(! $info === false)
		{
			$thumb	= EasyImageHelper::imageResize($info[0], $info[1], 60);
		}
		else
		{
			$thumb  = array( EBLOG_AVATAR_THUMB_WIDTH, EBLOG_AVATAR_THUMB_HEIGHT);
		}

		return $thumb;
	}

	public static function getAvatarRelativePath($type = 'profile')
	{
		$config			= EasyBlogHelper::getConfig();
		$avatar_config_path = '';

		if($type == 'category')
		{
			$avatar_config_path = $config->get('main_categoryavatarpath');
		}
		else if($type == 'team')
		{
			$avatar_config_path = $config->get('main_teamavatarpath');
		}
		else
		{
			$avatar_config_path = $config->get('main_avatarpath');
		}
		$avatar_config_path = rtrim($avatar_config_path, '/');
		//$avatar_config_path = str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		return $avatar_config_path;
	}


	public static function rel2abs($rel, $base)
	{
		return EasyBlogHelper::getHelper('string')->rel2abs( $rel, $base );
	}

	private function getMessageObj( $code = '' , $message = '', $item = false )
	{
		$obj			= new stdClass();
		$obj->code		= $code;
		$obj->message	= $message;

		if( $item )
		{
			$obj->item	= $item;
		}

		return $obj;
	}

	public function upload( $folder , $filename , $file , $baseUri , $storagePath , $subfolder = '' )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

		$config			= EasyBlogHelper::getConfig();
		$user           = JFactory::getUser();

		if (isset($file['name']))
		{
			if($config->get('main_resize_original_image'))
			{
				$maxWidth	= $config->get( 'main_original_image_width' );
				$maxHeight	= $config->get( 'main_original_image_height' );

				$image = new EasySimpleImage();
				$image->load($file['tmp_name']);
				$image->resizeWithin( $maxWidth , $maxHeight );

				$uploadStatus = $image->save( $storagePath , $image->image_type , $config->get( 'main_original_image_quality' ) );
			}
			else
			{
				$uploadStatus = JFile::upload($file['tmp_name'], $storagePath);
			}

			if( $uploadStatus )
			{
				// $activity   = new stdClass();
				// $activity->actor_id		= $user->id;
				// $activity->target_id	= '0';
				// $activity->context_type	= 'photo';
				// $activity->context_id	= '0';
				// $activity->verb         = 'upload';
				// EasyBlogHelper::activityLog( $activity );
			}

			// @task: thumbnail's file name
			$storagePathThumb	= JPath::clean( $folder . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $filename );

			// @rule: Generate a thumbnail for each uploaded images
			$image = new EasySimpleImage();
			$image->load( $storagePath );

			$image->resizeWithin( $config->get( 'main_thumbnail_width' ) , $config->get( 'main_thumbnail_height' ) );
			$image->save( $storagePathThumb , $image->image_type , $config->get( 'main_thumbnail_quality' ) );

			if( !$uploadStatus )
			{
				return $this->getMessageObj( EBLOG_MEDIA_PERMISSION_ERROR , JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' ) );
			}
			else
			{
			    // file uploaded. Now we test if the index.html was there or not.
			    // if not, copy from easyblog root into this folder
			    if(! JFile::exists( $folder . DIRECTORY_SEPARATOR . 'index.html' ) )
			    {
			        $targetFile = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
			        $destFile   = $folder . DIRECTORY_SEPARATOR .'index.html';

			        if( JFile::exists( $targetFile ) )
			        {
			        	JFile::copy( $targetFile, $destFile );
			        }
			    }

				return self::getMessageObj( EBLOG_MEDIA_UPLOAD_SUCCESS ,
											JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_SUCCESS' ) ,
											EasyBlogHelper::getHelper( 'ImageData' )->getObject( $folder , $filename , $baseUri , $subfolder )
					);
			}
		}
		else
		{
			return self::getMessageObj( EBLOG_MEDIA_TRANSPORT_ERROR , JText::_( 'COM_EASYBLOG_MEDIA_MANAGER_NO_UPLOAD_FILE' ) );
		}

		return $response;
	}
}
