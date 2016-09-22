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




require_once dirname(__FILE__).'/../Driver.php';
require_once dirname(__FILE__).'/../Common.php';
require_once dirname(__FILE__).'/../DualHeight.php';
require_once dirname(__FILE__).'/../Exception.php';

class Image_Barcode2_Driver_Postnet extends Image_Barcode2_Common implements Image_Barcode2_Driver, Image_Barcode2_DualHeight
{
	private $_barshortheight = 7;

	private $_bartallheight = 15;

	private $_codingmap = array(
		'0' => '11000',
		'1' => '00011',
		'2' => '00101',
		'3' => '00110',
		'4' => '01001',
		'5' => '01010',
		'6' => '01100',
		'7' => '10001',
		'8' => '10010',
		'9' => '10100'
	);

	public function __construct(Image_Barcode2_Writer $writer)
	{
		parent::__construct($writer);
		$this->setBarcodeWidth(2);
	}


	public function validate()
	{
		if (!preg_match('/^[0-9]+$/', $this->getBarcode())) {
			throw new Image_Barcode2_Exception('Invalid barcode');
		}
	}


	public function draw()
	{
		$text   = $this->getBarcode();
		$writer = $this->getWriter();

		$barcodewidth = (strlen($text)) * 2 * 5 * $this->getBarcodeWidth()
			+ $this->getBarcodeWidth() * 3;

		$img = $writer->imagecreate($barcodewidth, $this->_bartallheight);

		$black = $writer->imagecolorallocate($img, 0, 0, 0);
		$white = $writer->imagecolorallocate($img, 255, 255, 255);

		$writer->imagefill($img, 0, 0, $white);

		$xpos = 0;

		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$this->_bartallheight,
			$black
		);

		$xpos += 2 * $this->getBarcodeWidth();

		for ($idx = 0, $all = strlen($text); $idx < $all; $idx++) {
			$char = substr($text, $idx, 1);

			for ($baridx = 0; $baridx < 5; $baridx++) {
				$elementheight = $this->_barshortheight;

				if (substr($this->_codingmap[$char], $baridx, 1)) {
					$elementheight = 0;
				}

				$writer->imagefilledrectangle(
					$img,
					$xpos,
					$elementheight,
					$xpos + $this->getBarcodeWidth() - 1,
					$this->_bartallheight,
					$black
				);

				$xpos += 2 * $this->getBarcodeWidth();
			}
		}

		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$this->_bartallheight,
			$black
		);

		return $img;
	}

} // class
