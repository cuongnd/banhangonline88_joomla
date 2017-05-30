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

class Image_Barcode2_Driver_Code39 extends Image_Barcode2_Common implements Image_Barcode2_Driver, Image_Barcode2_DualWidth
{
	private $_codingmap = array(
		'0' => '000110100',
		'1' => '100100001',
		'2' => '001100001',
		'3' => '101100000',
		'4' => '000110001',
		'5' => '100110000',
		'6' => '001110000',
		'7' => '000100101',
		'8' => '100100100',
		'9' => '001100100',
		'A' => '100001001',
		'B' => '001001001',
		'C' => '101001000',
		'D' => '000011001',
		'E' => '100011000',
		'F' => '001011000',
		'G' => '000001101',
		'H' => '100001100',
		'I' => '001001100',
		'J' => '000011100',
		'K' => '100000011',
		'L' => '001000011',
		'M' => '101000010',
		'N' => '000010011',
		'O' => '100010010',
		'P' => '001010010',
		'Q' => '000000111',
		'R' => '100000110',
		'S' => '001000110',
		'T' => '000010110',
		'U' => '110000001',
		'V' => '011000001',
		'W' => '111000000',
		'X' => '010010001',
		'Y' => '110010000',
		'Z' => '011010000',
		'-' => '010000101',
		'*' => '010010100',
		'+' => '010001010',
		'$' => '010101000',
		'%' => '000101010',
		'/' => '010100010',
		'.' => '110000100',
		' ' => '011000100'
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
		if (preg_match("/[^0-9A-Z\-*+\$%\/. ]/", $this->getBarcode())) {
			throw new Image_Barcode2_Exception('Invalid barcode');
		}
	}


	public function draw()
	{
		$text     = $this->getBarcode();
		$writer   = $this->getWriter();
		$fontsize = $this->getFontSize();

		$final_text = '*' . $text . '*';

		$barcode = '';
		foreach (str_split($final_text) as $character) {
			$barcode .= $this->_dumpCode($this->_codingmap[$character] . '0');
		}

		$barcode_len = strlen($barcode);

		$img = $writer->imagecreate($barcode_len, $this->getBarcodeHeight());

		$black = $writer->imagecolorallocate($img, 0, 0, 0);
		$white = $writer->imagecolorallocate($img, 255, 255, 255);
		$font_height = $writer->imagefontheight($fontsize);
		$font_width = $writer->imagefontwidth($fontsize);

		$writer->imagefill($img, 0, 0, $white);

		$xpos = 0;

		foreach (str_split($barcode) as $character_code) {
			if ($character_code == 0) {
				$writer->imageline(
					$img,
					$xpos,
					0,
					$xpos,
					$this->getBarcodeHeight() - $font_height - 1,
					$white
				);
			} else {
				$writer->imageline(
					$img,
					$xpos,
					0,
					$xpos,
					$this->getBarcodeHeight() - $font_height - 1,
					$black
				);
			}

			$xpos++;
		}

		if ($this->showText) {
			$writer->imagestring(
				$img,
				$fontsize,
				($barcode_len - $font_width * strlen($text)) / 2,
				$this->getBarcodeHeight() - $font_height,
				$text,
				$black
			);
		}


		return $img;
	}


	private function _dumpCode($code)
	{
		$result = '';
		$color = 1; // 1: Black, 0: White

		foreach (str_split($code) as $bit) {
			if ($bit == 1) {
				$result .= str_repeat($color, $this->getBarcodeWidthThick());
			} else {
				$result .= str_repeat($color, $this->getBarcodeWidthThin());
			}

			$color = ($color == 0) ? 1 : 0;
		}

		return $result;
	}
}
