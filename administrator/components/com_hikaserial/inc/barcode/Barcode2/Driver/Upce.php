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

class Image_Barcode2_Driver_Upce extends Image_Barcode2_Common implements Image_Barcode2_Driver
{
	private $_paritypattern = array(
		'0' => array(1,1,1,0,0,0),
		'1' => array(1,1,0,1,0,0),
		'2' => array(1,1,0,0,1,0),
		'3' => array(1,1,0,0,0,1),
		'4' => array(1,0,1,1,0,0),
		'5' => array(1,0,0,1,1,0),
		'6' => array(1,0,0,0,1,1),
		'7' => array(1,0,1,0,1,0),
		'8' => array(1,0,1,0,0,1),
		'9' => array(1,0,0,1,0,1)
	);

	private $_codingmap = array(
		'0' => array(
			'O' => array(0,0,0,1,1,0,1),
			'E' => array(0,1,0,0,1,1,1)
		),
		'1' => array(
			'O' => array(0,0,1,1,0,0,1),
			'E' => array(0,1,1,0,0,1,1)
		),
		'2' => array(
			'O' => array(0,0,1,0,0,1,1),
			'E' => array(0,0,1,1,0,1,1)
		),
		'3' => array(
			'O' => array(0,1,1,1,1,0,1),
			'E' => array(0,1,0,0,0,0,1)
		),
		'4' => array(
			'O' => array(0,1,0,0,0,1,1),
			'E' => array(0,0,1,1,1,0,1)
		),
		'5' => array(
			'O' => array(0,1,1,0,0,0,1),
			'E' => array(0,1,1,1,0,0,1)
		),
		'6' => array(
			'O' => array(0,1,0,1,1,1,1),
			'E' => array(0,0,0,0,1,0,1)
		),
		'7' => array(
			'O' => array(0,1,1,1,0,1,1),
			'E' => array(0,0,1,0,0,0,1)
		),
		'8' => array(
			'O' => array(0,1,1,0,1,1,1),
			'E' => array(0,0,0,1,0,0,1)
		),
		'9' => array(
			'O' => array(0,0,0,1,0,1,1),
			'E' => array(0,0,1,0,1,1,1)
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
			+ $writer->imagefontwidth($fontsize)
			+ $writer->imagefontwidth($fontsize) // check digit padding
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

		$key = substr($text, 0, 1);

		$xpos = 0;

		if ($this->showText) {
			$writer->imagestring(
				$img,
				$fontsize,
				$xpos,
				$this->getBarcodeHeight(),
				$key,
				$black
			);
		}
		$xpos = $writer->imagefontwidth($fontsize) + 1;


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


		$checkdigit = substr($text, 7, 1);
		for ($idx = 1; $idx < 7; $idx ++) {
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

			if ($this->_paritypattern[$checkdigit][$idx-1] == 1) {
				foreach ($this->_codingmap[$value]['E'] as $bar) {
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
			} else {
				foreach ($this->_codingmap[$value]['O'] as $bar) {
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
		$writer->imagefilledrectangle(
			$img,
			$xpos,
			0,
			$xpos + $this->getBarcodeWidth() - 1,
			$barcodelongheight,
			$black
		);

		$xpos += $this->getBarcodeWidth();


		if ($this->showText) {
			$writer->imagestring(
				$img,
				$fontsize,
				$xpos + 1,
				$this->getBarcodeHeight(),
				$checkdigit,
				$black
			);
		}

		return $img;
	}

} // class
