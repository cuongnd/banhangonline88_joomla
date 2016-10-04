<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT. '/libraries/tcpdf/config/tcpdf_config_cmgroupbuying.php';
require_once JPATH_COMPONENT. '/libraries/tcpdf/tcpdf.php';

// Create new PDF document.
$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$tcpdf->SetTitle($this->pageTitle);

// remove default header/footer
$tcpdf->setPrintHeader(false);
$tcpdf->setPrintFooter(false);

// Add a page.
$tcpdf->AddPage();

if($displayBackground)
{
	$tcpdf->Image($couponBackground, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
}

if($displayQRCode)
{
	// Set style for barcode.
	$style = array(
		'border'		=> 0,
		'vpadding'		=> 'auto',
		'hpadding'		=> 'auto',
		'fgcolor'		=> array(0,0,0),
		'bgcolor'		=> array(255,255,255),
		'module_width'	=> 1, // width of a single module in points
		'module_height'	=> 1 // height of a single module in points
	);

	// QRCODE,L: QR-CODE Low error correction.
	$tcpdf->write2DBarcode($couponCodeString, 'QRCODE,L', $qrCode->left, $qrCode->top, $qrCode->size, $qrCode->size, $style, 'N');
}

if($displayCouponCode)
{
	displayElement($tcpdf, $couponCode->fontsize, $couponCode->width, $couponCode->height, $couponCode->align, $couponCode->left, $couponCode->top, $couponCodeString);
}

if($displayRecipient)
{
	displayElement($tcpdf, $recipient->fontsize, $recipient->width, $recipient->height, $recipient->align, $recipient->left, $recipient->top, $recipientString);
}

if($displayOption)
{
	displayElement($tcpdf, $option->fontsize, $option->width, $option->height, $option->align, $option->left, $option->top, $optionString);
}

if($displayShortDesc)
{
	displayElement($tcpdf, $shortDesc->fontsize, $shortDesc->width, $shortDesc->height, $shortDesc->align, $shortDesc->left, $shortDesc->top, $shortDescString);
}

if($displayHighlights)
{
	displayElement($tcpdf, $highlights->fontsize, $highlights->width, $highlights->height, $highlights->align, $highlights->left, $highlights->top, $highlightsString);
}

if($displayTerms)
{
	displayElement($tcpdf, $terms->fontsize, $terms->width, $terms->height, $terms->align, $terms->left, $terms->top, $termsString);}

if($displayCouponExp)
{
	displayElement($tcpdf, $couponExp->fontsize, $couponExp->width, $couponExp->height, $couponExp->align, $couponExp->left, $couponExp->top, $couponExpString);
}

if($displayPrice)
{
	displayElement($tcpdf, $price->fontsize, $price->width, $price->height, $price->align, $price->left, $price->top, $priceString);
}

if($displayOriginalPrice)
{
	displayElement($tcpdf, $originalPrice->fontsize, $originalPrice->width, $originalPrice->height, $originalPrice->align, $originalPrice->left, $originalPrice->top, $originalPriceString);
}

if($displayAdvancePrice)
{
	displayElement($tcpdf, $advancePrice->fontsize, $advancePrice->width, $advancePrice->height, $advancePrice->align, $advancePrice->left, $advancePrice->top, $advancePriceString);
}

if($displayRemainPrice)
{
	displayElement($tcpdf, $recipient->fontsize, $recipient->width, $recipient->height, $recipient->align, $recipient->left, $recipient->top, $recipientString);
}

//Close and output PDF document
$tcpdf->Output('coupon.pdf', 'I');

function displayElement($tcpdf, $fontSize, $width, $height, $alignment, $left, $top, $text)
{
	$tcpdf->SetFont(PDF_FONT_NAME_MAIN, '', $fontSize);

	switch($alignment)
	{
		case 'center':
			$align = 'C';
			break;
		case 'right':
			$align = 'R';
			break;
		default:
			$align = 'L';
			break;
	}

	$tcpdf->writeHTMLCell($width, $height, $left, $top, $text, 0, 0, false, true, $align, false);
}
