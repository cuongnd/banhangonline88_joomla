<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php



require_once dirname(__FILE__).'/Barcode2/Writer.php';
require_once dirname(__FILE__).'/Barcode2/Driver.php';
require_once dirname(__FILE__).'/Barcode2/Exception.php';

class Image_Barcode2
{
	const IMAGE_PNG     = 'png';
	const IMAGE_GIF     = 'gif';
	const IMAGE_JPEG    = 'jpg';

	const BARCODE_CODE39    = 'code39';
	const BARCODE_INT25     = 'int25';
	const BARCODE_EAN13     = 'ean13';
	const BARCODE_UPCA      = 'upca';
	const BARCODE_UPCE      = 'upce';
	const BARCODE_CODE128   = 'code128';
	const BARCODE_EAN8      = 'ean8';
	const BARCODE_POSTNET   = 'postnet';

	const ROTATE_NONE     = 0;
	const ROTATE_RIGHT    = 90;
	const ROTATE_UTURN    = 180;
	const ROTATE_LEFT     = 270;


	public static function draw($text,
		$type = Image_Barcode2::BARCODE_INT25,
		$imgtype = Image_Barcode2::IMAGE_PNG,
		$bSendToBrowser = true,
		$height = 60,
		$width = 1,
		$showText = true,
		$rotation = Image_Barcode2::ROTATE_NONE
	) {
		if (!preg_match('/^[a-zA-Z0-9]+$/', $type)) {
			throw new Image_Barcode2_Exception('Invalid barcode type ' . $type);
		}

		if (!include_once dirname(__FILE__).'/Barcode2/Driver/' . ucfirst($type) . '.php') {
			throw new Image_Barcode2_Exception($type . ' barcode is not supported');
		}

		$classname = 'Image_Barcode2_Driver_' . ucfirst($type);

		$obj = new $classname(new Image_Barcode2_Writer());

		if (!$obj instanceof Image_Barcode2_Driver) {
			throw new Image_Barcode2_Exception(
				"'$classname' does not implement Image_Barcode2_Driver"
			);
		}

		if (!$obj instanceof Image_Barcode2_DualWidth) {
			$obj->setBarcodeWidth($width);
		} elseif ($width > 1) {
			$thin = $obj->getBarcodeWidthThin() * $width;
			$obj->setBarcodeWidthThin($thin);

			$thick = $obj->getBarcodeWidthThick() * $width;
			$obj->setBarcodeWidthThick($thick);
		}

		if (!$obj instanceof Image_Barcode2_DualHeight) {
			$obj->setBarcodeHeight($height);
		}

		$obj->setBarcode($text);
		$obj->setShowText($showText);

		if ($showText && $width > 1) {
			$fontSize = $obj->getFontSize() * $width;
			$obj->setFontSize($fontSize);
		}

		$obj->validate();
		$img = $obj->draw();

		if ($rotation !== self::ROTATE_NONE) {
			$img = imagerotate($img, $rotation, 0);
		}

		if ($bSendToBrowser) {
			switch ($imgtype) {
			case self::IMAGE_GIF:
				header('Content-type: image/gif');
				imagegif($img);
				imagedestroy($img);
				break;

			case self::IMAGE_JPEG:
				header('Content-type: image/jpg');
				imagejpeg($img);
				imagedestroy($img);
				break;

			default:
				header('Content-type: image/png');
				imagepng($img);
				imagedestroy($img);
				break;
			}
		}

		return $img;
	}
}
