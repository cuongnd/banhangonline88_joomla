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


class Image_Barcode2_Driver_Code128 extends Image_Barcode2_Common implements Image_Barcode2_Driver
{
	private $_codingmap = array(
		0 => '212222',  // " "
		1 => '222122',  // "!"
		2 => '222221',  // "{QUOTE}"
		3 => '121223',  // "#"
		4 => '121322',  // "$"
		5 => '131222',  // "%"
		6 => '122213',  // "&"
		7 => '122312',  // "'"
		8 => '132212',  // "("
		9 => '221213',  // ")"
		10 => '221312', // "*"
		11 => '231212', // "+"
		12 => '112232', // ","
		13 => '122132', // "-"
		14 => '122231', // "."
		15 => '113222', // "/"
		16 => '123122', // "0"
		17 => '123221', // "1"
		18 => '223211', // "2"
		19 => '221132', // "3"
		20 => '221231', // "4"
		21 => '213212', // "5"
		22 => '223112', // "6"
		23 => '312131', // "7"
		24 => '311222', // "8"
		25 => '321122', // "9"
		26 => '321221', // ":"
		27 => '312212', // ";"
		28 => '322112', // "<"
		29 => '322211', // "="
		30 => '212123', // ">"
		31 => '212321', // "?"
		32 => '232121', // "@"
		33 => '111323', // "A"
		34 => '131123', // "B"
		35 => '131321', // "C"
		36 => '112313', // "D"
		37 => '132113', // "E"
		38 => '132311', // "F"
		39 => '211313', // "G"
		40 => '231113', // "H"
		41 => '231311', // "I"
		42 => '112133', // "J"
		43 => '112331', // "K"
		44 => '132131', // "L"
		45 => '113123', // "M"
		46 => '113321', // "N"
		47 => '133121', // "O"
		48 => '313121', // "P"
		49 => '211331', // "Q"
		50 => '231131', // "R"
		51 => '213113', // "S"
		52 => '213311', // "T"
		53 => '213131', // "U"
		54 => '311123', // "V"
		55 => '311321', // "W"
		56 => '331121', // "X"
		57 => '312113', // "Y"
		58 => '312311', // "Z"
		59 => '332111', // "["
		60 => '314111', // "\"
		61 => '221411', // "]"
		62 => '431111', // "^"
		63 => '111224', // "_"
		64 => '111422', // "`"
		65 => '121124', // "a"
		66 => '121421', // "b"
		67 => '141122', // "c"
		68 => '141221', // "d"
		69 => '112214', // "e"
		70 => '112412', // "f"
		71 => '122114', // "g"
		72 => '122411', // "h"
		73 => '142112', // "i"
		74 => '142211', // "j"
		75 => '241211', // "k"
		76 => '221114', // "l"
		77 => '413111', // "m"
		78 => '241112', // "n"
		79 => '134111', // "o"
		80 => '111242', // "p"
		81 => '121142', // "q"
		82 => '121241', // "r"
		83 => '114212', // "s"
		84 => '124112', // "t"
		85 => '124211', // "u"
		86 => '411212', // "v"
		87 => '421112', // "w"
		88 => '421211', // "x"
		89 => '212141', // "y"
		90 => '214121', // "z"
		91 => '412121', // "{"
		92 => '111143', // "|"
		93 => '111341', // "}"
		94 => '131141', // "~"
		95 => '114113', // 95
		96 => '114311', // 96
		97 => '411113', // 97
		98 => '411311', // 98
		99 => '113141', // 99
		100 => '114131', // 100
		101 => '311141', // 101
		102 => '411131', // 102
	);

	public function __construct(Image_Barcode2_Writer $writer)
	{
		parent::__construct($writer);
		$this->setBarcodeHeight(60);
		$this->setBarcodeWidth(1);
	}


	public function validate()
	{
	}


	public function draw()
	{
		$startcode = $this->_getStartCode();
		$checksum  = 104;
		$allbars   = $startcode;
		$text      = $this->getBarcode();
		$writer    = $this->getWriter();
		$fontsize  = $this->getFontSize();


		for ($i = 0, $all = strlen($text); $i < $all; ++$i) {
			$char = $text[$i];
			$val = $this->_getCharNumber($char);

			$checksum += ($val * ($i + 1));

			$allbars .= $this->_getCharCode($char);
		}


		$checkdigit = $checksum % 103;
		$bars = $this->_getNumCode($checkdigit);



		$stopcode = $this->_getStopCode();
		$allbars = $allbars . $bars . $stopcode;


		$barcodewidth = 20;



		for ($i = 0, $all = strlen($allbars); $i < $all; ++$i) {
			$nval = $allbars[$i];
			$barcodewidth += ($nval * $this->getBarcodeWidth());
		}

		$barcodelongheight = (int)($writer->imagefontheight($fontsize) / 2)
			+ $this->getBarcodeHeight();



		$img = $writer->imagecreate(
			$barcodewidth,
			$barcodelongheight + $writer->imagefontheight($fontsize) + 1
		);
		$black = $writer->imagecolorallocate($img, 0, 0, 0);
		$white = $writer->imagecolorallocate($img, 255, 255, 255);
		$writer->imagefill($img, 0, 0, $white);




		if ($this->showText) {
			$writer->imagestring(
				$img,
				$fontsize,
				$barcodewidth / 2 - strlen($text) / 2 * ($writer->imagefontwidth($fontsize)),
				$this->getBarcodeHeight() + $writer->imagefontheight($fontsize) / 2,
				$text,
				$black
			);
		}

		$xpos = 10;

		$bar = 1;
		for ($i = 0, $all = strlen($allbars); $i < $all; ++$i) {
			$nval = $allbars[$i];
			$width = $nval * $this->getBarcodeWidth();

			if ($bar == 1) {
				$writer->imagefilledrectangle(
					$img,
					$xpos,
					0,
					$xpos + $width - 1,
					$barcodelongheight,
					$black
				);
				$xpos += $width;
				$bar = 0;
			} else {
				$xpos += $width;
				$bar = 1;
			}
		}

		return $img;
	}


	private function _getCharCode($char)
	{
		return $this->_codingmap[ord($char) - 32];
	}


	private function _getStartCode()
	{
		return '211214';
	}


	private function _getStopCode()
	{
		return '2331112';
	}


	private function _getNumCode($index)
	{
		return $this->_codingmap[$index];
	}


	private function _getCharNumber($char)
	{
		return ord($char) - 32;
	}

} // class
