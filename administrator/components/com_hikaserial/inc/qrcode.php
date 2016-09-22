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
if(!class_exists('QRencode'))
	include_once HIKASERIAL_INC.'phpqrcode'.DS.'qrlib.php';

class hikaserialQrcodeInc {
	private $factory = null;
	private $bgcolor = null;
	private $fgcolor = null;
	private $outerFrame = 2;

	public function __construct($level = 0, $pixelPerPoint = 3, $outerFrame = 2, $fgcolor = null, $bgcolor = null) {
		$this->setOptions($level, $pixelPerPoint, $outerFrame, $fgcolor, $bgcolor);
	}

	public function setOptions($level = 0, $pixelPerPoint = 3, $outerFrame = 2, $fgcolor = null, $bgcolor = null) {
		$levels = array(
			'l' => 0,
			'm' => 1,
			'q' => 2,
			'h' => 3
		);
		if(!is_numeric($level)) {
			if(is_string($level) && isset($levels[ strtolower($level) ])) {
				$level = $levels[ strtolower($level) ];
			} else {
				$level = 0;
			}
		}
		$this->factory = QRencode::factory($level, $pixelPerPoint, $outerFrame);
		$this->bgcolor = $bgcolor;
		$this->fgcolor = $fgcolor;
		$this->outerFrame = $outerFrame;
	}

	public function encode($data) {
		$ret = null;
		try {
			ob_start();
			$ret = $this->factory->encode($data, false);
			ob_end_clean();
		} catch(Exception $e) { }

		return $ret;
	}

	public function getImage($data) {
		$pngData = $this->encode($data);

		if($pngData == null)
			return null;

		$h = count($pngData);
		$w = strlen($pngData[0]);
		$imgW = $w + 2 * $this->outerFrame;
		$imgH = $h + 2 * $this->outerFrame;

		$qrcode_image = imagecreatetruecolor($imgW, $imgH);
		imagealphablending($qrcode_image, false);
		$qrBackColor = imagecolorallocatealpha($qrcode_image, 255, 255, 255, 127);
		imagefill($qrcode_image, 0, 0, $qrBackColor);
		imagesavealpha($qrcode_image, true);

		if(!empty($this->fgcolor) && ((substr($this->fgcolor,0,1) == '#' && strlen(trim($this->fgcolor)) == 7) || strlen(trim($this->fgcolor)) == 6)) {
			$rgb = str_split(ltrim($this->fgcolor, '#'), 2);
			$qrColor = imagecolorallocatealpha($qrcode_image, hexdec($rgb[0]), hexdec($rgb[1]), hexdec($rgb[2]), 0);
		} else {
			$qrColor = imagecolorallocatealpha($qrcode_image, 0, 0, 0, 0);
		}

		for($y = 0; $y < $h; $y++) {
			for($x = 0; $x < $w; $x++) {
				if ($pngData[$y][$x] == '1') {
					imagesetpixel($qrcode_image, $x + $this->outerFrame, $y + $this->outerFrame, $qrColor);
				}
			}
		}

		return $qrcode_image;
	}

	public function saveFile($text, $filename, $format = null) {
		$ret = null;
		try {
			ob_start();
			$ret = $this->factory->encode($text, $filename);
			ob_end_clean();
		} catch(Exception $e) { }
		return $ret;
	}
}
