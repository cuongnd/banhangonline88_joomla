<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
use MatthiasMullie\Minify;
use zz\Html\HTMLMinify;
use Joomla\Image\Image;
defined('JPATH_PLATFORM') or die;

/**
 * JUtility is a utility functions class
 *
 * @since  11.1
 */
class JUtility
{
	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 *
	 * @return  array  Key/Value pairs for the attributes
	 *
	 * @since   11.1
	 */
	public static function printDebugBacktrace($title = 'Debug Backtrace:')
	{
		$output = "";
		$output .= "<hr /><div>" . $title . '<br /><table border="1" cellpadding="2" cellspacing="2">';

		$stacks = debug_backtrace();

		$output .= "<thead><tr><th><strong>File</strong></th><th><strong>Line</strong></th><th><strong>Function</strong></th>" .
			"</tr></thead>";
		foreach ($stacks as $_stack) {
			if (!isset($_stack['file'])) $_stack['file'] = '[PHP Kernel]';
			if (!isset($_stack['line'])) $_stack['line'] = '';

			$output .= "<tr><td>{$_stack["file"]}</td><td>{$_stack["line"]}</td>" .
				"<td>{$_stack["function"]}</td></tr>";
		}
		$output .= "</table></div><hr /></p>";
		return $output;
	}
	public static function write_compress_js($file_js, $compress_file)
	{
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/Minify.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/JS.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/Exception.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/path-converter-master/src/Converter.php';
		$minifier = new Minify\JS(JPATH_ROOT.DS.$file_js);
		JFile::write(JPATH_ROOT.DS.$compress_file,$minifier->minify());
	}

	public static function write_compress_css($file_css, $compress_css_file)
	{
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/Minify.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/Exceptions/FileImportException.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/CSS.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/minify-master/src/Exception.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
		require_once JPATH_ROOT.DS.'libraries/minifyjscss/path-converter-master/src/Converter.php';


		require_once JPATH_ROOT.DS.'libraries/minifyjscss/path-converter-master/src/NoConverter.php';
		$minifier = new Minify\CSS($file_css);
		JFile::write(JPATH_ROOT.DS.$compress_css_file,$minifier->minify());
	}
	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 *
	 * @return  array  Key/Value pairs for the attributes
	 *
	 * @since   11.1
	 */
	public static function parseAttributes($string)
	{
		$attr = array();
		$retarray = array();

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);

			for ($i = 0; $i < $numPairs; $i++)
			{
				$retarray[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $retarray;
	}
	public static function gen_random_string($length=8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$base = strlen($salt);
		$makepass = '';

		/*
         * Start with a cryptographic strength random string, then convert it to
         * a string with the numeric base of the salt.
         * Shift the base conversion on each character so the character
         * distribution is even, and randomize the start shift so it's not
         * predictable.
         */
		$random = JCrypt::genRandomBytes($length + 1);
		$shift = ord($random[0]);

		for ($i = 1; $i <= $length; ++$i)
		{
			$makepass .= $salt[($shift + ord($random[$i])) % $base];
			$shift += ord($random[$i]);
		}

		return $makepass;
	}

	public static function remove_string_javascript($str)
	{
		preg_match_all('/<script type=\"text\/javascript">(.*?)<\/script>/s', $str, $estimates);
		return $estimates[1][0];

	}
	public static function remove_string_style_sheet($str)
	{
		preg_match_all('/<style type=\"text\/css">(.*?)<\/style>/s', $str, $estimates);
		return $estimates[1][0];

	}

	public static function write_log_time($title)
	{
		$end_end = microtime(true);
		$total_time = number_format($end_end-TIME_START,3);
		JLog::add("$title:$total_time second");
	}
	public static function html_minify($data)
	{
		require_once JPATH_ROOT.DS.'libraries/html-minifier-master/src/zz/Html/HTMLMinify.php';
		require_once JPATH_ROOT.DS.'libraries/html-minifier-master/src/zz/Html/SegmentedString.php';
		require_once JPATH_ROOT.DS.'libraries/html-minifier-master/src/zz/Html/HTMLTokenizer.php';
		require_once JPATH_ROOT.DS.'libraries/html-minifier-master/src/zz/Html/HTMLToken.php';
		require_once JPATH_ROOT.DS.'libraries/html-minifier-master/src/zz/Html/HTMLNames.php';
		$data = zz\Html\HTMLMinify::minify($data);
		return $data;

	}
	function compress($source, $destination, $quality) {

		$info = getimagesize($source);

		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($source);

		elseif ($info['mime'] == 'image/gif')
			$image = imagecreatefromgif($source);

		elseif ($info['mime'] == 'image/png')
			$image = imagecreatefrompng($source);

		imagejpeg($image, $destination, $quality);

		return $destination;
	}
	public static function create_thumb($source, $width=600,$height=250) {
		require_once JPATH_ROOT.DS.'libraries/joomla/image-master/src/Image.php';
		$source_info = pathinfo($source);
		$image = new Image();

		$temp_image_path= "/tmp/".$source_info['basename'];
		$image->loadFile( $source);
		$image->crop($width, $height);
		$image->toFile(JPATH_ROOT .$temp_image_path);
		return $temp_image_path;
	}
	public static function resize_image($source, $width=600,$height=250) {
		require_once JPATH_ROOT.DS.'libraries/joomla/image-master/src/Image.php';
		$source_info = pathinfo($source);
		$image = new Image();

		$temp_image_path= "/tmp/".$source_info['basename'];
		$image->loadFile( $source);
		$image->resize($width, $height,true, Image::SCALE_FILL);
		$image->toFile(JPATH_ROOT .$temp_image_path);
		return $temp_image_path;
	}
	public static function createThumbs_image($source, $sizes=array('300x300', '64x64', '250x125')) {
		$source=JPATH_ROOT.DS.'images/com_hikashop/upload/thumbnail_25x25/thoitrang_phukien-1336232718.png';
		require_once JPATH_ROOT.DS.'libraries/joomla/image-master/src/Image.php';
		$source_info = pathinfo($source);
		$image = new Image();

		$temp_image_path= "/tmp/".$source_info['basename'];
		$image->loadFile( $source);
		$image->createThumbs($sizes, Image::SCALE_FILL);
		$image->toFile(JPATH_ROOT .$temp_image_path,IMAGETYPE_PNG,array('options' => 0));
		return $temp_image_path;
	}
}
