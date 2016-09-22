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
if(!class_exists('Image_Barcode2'))
	include_once HIKASERIAL_INC.'barcode'.DS.'Barcode2.php';

class hikaserialBarcodeInc {
	private $opt_type;
	private $opt_format;
	private $opt_height;
	private $opt_width;
	private $opt_showText;
	private $opt_rotation;

	public function __construct($type = 'int25', $height = 60, $width = 1, $showText = true, $rotation = 0, $format = 'png') {
		$this->setOptions($type, $height, $width, $showText, $rotation, $format);
	}

	public function setOptions($type = 'int25', $height = 60, $width = 1, $showText = true, $rotation = 0, $format = 'png') {
		$types = array(
			'code39' => Image_Barcode2::BARCODE_CODE39,
			'int25' => Image_Barcode2::BARCODE_INT25,
			'ean13' => Image_Barcode2::BARCODE_EAN13,
			'upca' => Image_Barcode2::BARCODE_UPCA,
			'upce' => Image_Barcode2::BARCODE_UPCE,
			'code128' => Image_Barcode2::BARCODE_CODE128,
			'ean8' => Image_Barcode2::BARCODE_EAN8,
			'postnet' => Image_Barcode2::BARCODE_POSTNET,
		);
		$formats = array(
			'png' => Image_Barcode2::IMAGE_PNG,
			'jpg' => Image_Barcode2::IMAGE_JPEG,
			'gif' => Image_Barcode2::IMAGE_GIF
		);
		$rotations = array(
			0 => Image_Barcode2::ROTATE_NONE,
			90 => Image_Barcode2::ROTATE_RIGHT,
			180 => Image_Barcode2::ROTATE_UTURN,
			270 => Image_Barcode2::ROTATE_LEFT
		);

		if(isset($types[$type]))
			$this->opt_type = $types[$type];
		else
			$this->opt_type = $types['int25'];

		$this->opt_height = (int)$height;
		if($this->opt_height <= 0)
			$this->opt_height = 60;

		$this->opt_width = (int)$width;
		if($this->opt_width <= 0)
			$this->opt_width = 1;

		$this->opt_showText = ($showText == true);

		if(isset($rotations[$rotation]))
			$this->opt_rotation = $rotations[$rotation];
		else
			$this->opt_rotation = $rotations[0];

		if(isset($formats[$format]))
			$this->opt_format = $formats[$format];
		else
			$this->opt_format = $formats['png'];
	}

	public function getImage($data) {
		$ret = null;
		try {
			ob_start();
			$ret = Image_Barcode2::draw($data, $this->opt_type, $this->opt_format, false, $this->opt_height, $this->opt_width, $this->opt_showText, $this->opt_rotation);
			ob_end_clean();
		} catch(Exception $e) { }

		return $ret;
	}

	public function saveFile($data, $filename, $format = null) {

	}
}
