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


class Image_Barcode2_Common
{
	protected $barcodeheight;
	protected $barcodewidth;
	protected $barcodethinwidth;
	protected $barcodethickwidth;
	protected $fontsize = 2;
	protected $showText;

	protected $writer;

	protected $barcode;


	public function __construct(Image_Barcode2_Writer $writer)
	{
		$this->setWriter($writer);
	}

	public function setWriter(Image_Barcode2_Writer $writer)
	{
		$this->writer = $writer;
	}

	public function getWriter()
	{
		return $this->writer;
	}

	public function setBarcode($barcode)
	{
		$this->barcode = trim($barcode);
	}

	public function getBarcode()
	{
		return $this->barcode;
	}

	public function setShowText($showText)
	{
		$this->showText = $showText;
	}

	public function getShowText()
	{
		return $this->showText;
	}

	public function setFontSize($size)
	{
		$this->fontsize = $size;
	}

	public function getFontSize()
	{
		return $this->fontsize;
	}

	public function setBarcodeHeight($height)
	{
		$this->barcodeheight = $height;
	}

	public function getBarcodeHeight()
	{
		return $this->barcodeheight;
	}

	public function setBarcodeWidth($width)
	{
		$this->barcodewidth = $width;
	}

	public function getBarcodeWidth()
	{
		return $this->barcodewidth;
	}

	public function setBarcodeWidthThick($width)
	{
		$this->barcodethickwidth = $width;
	}

	public function getBarcodeWidthThick()
	{
		return $this->barcodethickwidth;
	}

	public function setBarcodeWidthThin($width)
	{
		$this->barcodethinwidth = $width;
	}

	public function getBarcodeWidthThin()
	{
		return $this->barcodethinwidth;
	}
}
