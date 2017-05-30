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
require_once dirname(__FILE__).'/../Exception.php';

class Image_Barcode2_Driver_Ean8 extends Image_Barcode2_Common implements Image_Barcode2_Driver
{
	private $_codingmap = array(
		'0' => array(
			'A' => array(0,0,0,1,1,0,1),
			'C' => array(1,1,1,0,0,1,0)
		),
		'1' => array(
			'A' => array(0,0,1,1,0,0,1),
			'C' => array(1,1,0,0,1,1,0)
		),
		'2' => array(
			'A' => array(0,0,1,0,0,1,1),
			'C' => array(1,1,0,1,1,0,0)
		),
		'3' => array(
			'A' => array(0,1,1,1,1,0,1),
			'C' => array(1,0,0,0,0,1,0)
		),
		'4' => array(
			'A' => array(0,1,0,0,0,1,1),
			'C' => array(1,0,1,1,1,0,0)
		),
		'5' => array(
			'A' => array(0,1,1,0,0,0,1),
			'C' => array(1,0,0,1,1,1,0)
		),
		'6' => array(
			'A' => array(0,1,0,1,1,1,1),
			'C' => array(1,0,1,0,0,0,0)
		),
		'7' => array(
			'A' => array(0,1,1,1,0,1,1),
			'C' => array(1,0,0,0,1,0,0)
		),
		'8' => array(
			'A' => array(0,1,1,0,1,1,1),
			'C' => array(1,0,0,1,0,0,0)
		),
		'9' => array(
			'A' => array(0,0,0,1,0,1,1),
			'C' => array(1,1,1,0,1,0,0)
		)
	);

	public function __construct(Image_Barcode2_Writer $writer)
	{
		parent::__construct($writer);
		$this->setBarcodeHeight(50);
		$this->setBarcodeWidth(1);
	}


	public function validate()
	{
		if (!preg_match('/^[0-9]{8}$/', $this->getBarcode())) {
			throw new Image_Barcode2_Exception('Invalid barcode');
		}
	}


	public function draw()
	{
		$text     = $this->getBarcode();
		$writer   = $this->getWriter();
		$fontsize = $this->getFontSize();

		$barcodewidth = (strlen($text)) * (7 * $this->getBarcodeWidth())
			+ 3 * $this->getBarcodeWidth() // left
			+ 5 * $this->getBarcodeWidth() // center
			+ 3 * $this->getBarcodeWidth() // right
			;

		$barcodelongheight = (int)($writer->imagefontheight($fontsize) / 2)
			 + $this->getBarcodeHeight();

		$img = $writer->imagecreate(
			$barcodewidth,
			$barcodelongheight + $writer->imagefontheight($fontsize) + 1
		);

		$black = $writer->imagecolorallocate($img, 0, 0, 0);
		$white = $writer->imagecolorallocate($img, 255, 255, 255);

		$writer->imagefill($img, 0, 0, $white);

		$xpos = 0;

		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);
		$xpos += $this->getBarcodeWidth();
		$xpos += $this->getBarcodeWidth();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);
		$xpos += $this->getBarcodeWidth();

		for ($idx = 0; $idx < 4; $idx ++) {
			$value = substr($text, $idx, 1);

			if ($this->showText) {
				$writer->imagestring(
					$img,
					$fontsize,
					$xpos + 1,
					$this->getBarcodeHeight(),
					$value,
					$black
				);
			}

			foreach ($this->_codingmap[$value]['A'] as $bar) {
				if ($bar) {
					$writer->imagefilledrectangle(
						$img,
						$xpos,
						0,
						$xpos + $this->getBarcodeWidth() - 1,
						$this->getBarcodeHeight(),
						$black
					);
				}
				$xpos += $this->getBarcodeWidth();
			}
		}

		$xpos += $this->getBarcodeWidth();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);

		$xpos += $this->getBarcodeWidth();
		$xpos += $this->getBarcodeWidth();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);

		$xpos += $this->getBarcodeWidth();
		$xpos += $this->getBarcodeWidth();


		for ($idx = 4; $idx < 8; $idx ++) {
			$value = substr($text, $idx, 1);

			if ($this->showText) {
				$writer->imagestring(
					$img,
					$fontsize,
					$xpos + 1,
					$this->getBarcodeHeight(),
					$value,
					$black
				);
			}

			foreach ($this->_codingmap[$value]['C'] as $bar) {
				if ($bar) {
					$writer->imagefilledrectangle(
						$img,
						$xpos,
						0,
						$xpos + $this->getBarcodeWidth() - 1,
						$this->getBarcodeHeight(),
						$black
					);
				}
				$xpos += $this->getBarcodeWidth();
			}
		}

		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);
		$xpos += $this->getBarcodeWidth();
		$xpos += $this->getBarcodeWidth();
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);

		return $img;
	} // function create

} // class
