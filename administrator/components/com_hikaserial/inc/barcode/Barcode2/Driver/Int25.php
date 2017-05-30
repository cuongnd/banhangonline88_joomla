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
require_once dirname(__FILE__).'/../DualWidth.php';
require_once dirname(__FILE__).'/../Exception.php';

class Image_Barcode2_Driver_Int25 extends Image_Barcode2_Common implements Image_Barcode2_Driver, Image_Barcode2_DualWidth
{
	private $_codingmap = array(
		'0' => '00110',
		'1' => '10001',
		'2' => '01001',
		'3' => '11000',
		'4' => '00101',
		'5' => '10100',
		'6' => '01100',
		'7' => '00011',
		'8' => '10010',
		'9' => '01010'
	);

	public function __construct(Image_Barcode2_Writer $writer)
	{
		parent::__construct($writer);
		$this->setBarcodeHeight(50);
		$this->setBarcodeWidthThin(1);
		$this->setBarcodeWidthThick(3);
	}


	public function validate()
	{
		if (!preg_match('/[0-9]/', $this->getBarcode())) {
			throw new Image_Barcode2_Exception('Invalid barcode');
		}
	}


	public function draw()
	{
		$text   = $this->getBarcode();
		$writer = $this->getWriter();

		$text = strlen($text) % 2 ? '0' . $text : $text;

		$barcodewidth = (strlen($text))
			* (3 * $this->getBarcodeWidthThin() + 2 * $this->getBarcodeWidthThick())
			+ (strlen($text))
			* 2.5
			+ (7 * $this->getBarcodeWidthThin() + $this->getBarcodeWidthThick()) + 3;

		$img = $writer->imagecreate($barcodewidth, $this->getBarcodeHeight());

		$black = $writer->imagecolorallocate($img, 0, 0, 0);
		$white = $writer->imagecolorallocate($img, 255, 255, 255);

		$writer->imagefill($img, 0, 0, $white);

		$xpos = 0;

		for ($i = 0; $i < 2; $i++) {
			$elementwidth = $this->getBarcodeWidthThin();
			$writer->imagefilledrectangle(
				$img,
				$xpos,
				0,
				$xpos + $elementwidth - 1,
				$this->getBarcodeHeight(),
				$black
			);
			$xpos += $elementwidth;
			$xpos += $this->getBarcodeWidthThin();
			$xpos ++;
		}

		$all = strlen($text);

		for ($idx = 0; $idx < $all; $idx += 2) {
			$oddchar  = substr($text, $idx, 1);
			$evenchar = substr($text, $idx + 1, 1);

			if(!isset($this->_codingmap[$oddchar]) || !isset($this->_codingmap[$evenchar]))
				continue;

			for ($baridx = 0; $baridx < 5; $baridx++) {
				$elementwidth = $this->getBarcodeWidthThin();
				if (substr($this->_codingmap[$oddchar], $baridx, 1)) {
					$elementwidth = $this->getBarcodeWidthThick();
				}

				$writer->imagefilledrectangle(
					$img,
					$xpos,
					0,
					$xpos + $elementwidth - 1,
					$this->getBarcodeHeight(),
					$black
				);

				$xpos += $elementwidth;

				$elementwidth = $this->getBarcodeWidthThin();
				if (substr($this->_codingmap[$evenchar], $baridx, 1)) {
					$elementwidth = $this->getBarcodeWidthThick();
				}

				$xpos += $elementwidth;
				$xpos ++;
			}
		}


		$elementwidth = $this->getBarcodeWidthThick();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $elementwidth - 1,
			$this->getBarcodeHeight(),
			$black
		);
		$xpos += $elementwidth;
		$xpos += $this->getBarcodeWidthThin();
		$xpos ++;
		$elementwidth = $this->getBarcodeWidthThin();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $elementwidth - 1,
			$this->getBarcodeHeight(),
			$black
		);

		return $img;
	}

} // class
