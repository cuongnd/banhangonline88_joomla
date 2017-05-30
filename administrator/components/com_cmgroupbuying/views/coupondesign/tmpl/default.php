<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet('components/com_cmgroupbuying/assets/css/jquery.ui.all.css');

$jinput = JFactory::getApplication()->input;

$couponElementsJSON = $jinput->get('elements','', 'string');
$couponElementsJSON = str_replace('f:', 'fontsize:', $couponElementsJSON);
$couponElementsJSON = str_replace('s:', 'size:', $couponElementsJSON);
$couponElementsJSON = str_replace('t:', 'top:', $couponElementsJSON);
$couponElementsJSON = str_replace('h:', 'height:', $couponElementsJSON);
$couponElementsJSON = str_replace('w:', 'width:', $couponElementsJSON);
$couponElementsJSON = str_replace('l:', 'left:', $couponElementsJSON);
$couponElementsJSON = str_replace('a:', 'align:', $couponElementsJSON);
$couponElementsJSON = str_replace('v:', 'visible:', $couponElementsJSON);

$elements = explode('+', $couponElementsJSON);

$couponElementsObj = new stdClass();


$elementNames = array('couponcode', 'recipient', 'shortdesc', 'couponexp', 'highlights', 'terms', 'option', 'price', 'originalprice', 'advanceprice', 'remainprice');

if(!empty($elements))
{
	foreach($elements as $element)
	{
		$tmp = explode('--', $element);

		if(isset($tmp[0]))
			$elementName = $tmp[0];
		else
			$elementName = '';

		if(isset($tmp[1]))
		{
			$elementAttr = explode(',', $tmp[1]);
			$attribute = new stdClass();

			foreach($elementAttr as $attr)
			{
				$tmp2 = explode(':', $attr);
				$attribute->{$tmp2[0]} = $tmp2[1];
			}

			$couponElementsObj->{$elementName} = $attribute;
		}
	}
}

if(isset($couponElementsObj->qrcode))
{
	$qrcode = $couponElementsObj->qrcode;
}
else
{
	$qrcode = new stdClass();
	$qrcode->left = 0;
	$qrcode->top = 0;
	$qrcode->visible = 'false';
	$qrcode->size = 150;
	$couponElementsObj->qrcode = $qrcode;
}

foreach($elementNames as $elementName)
{
	if(isset($couponElementsObj->$elementName))
	{
		$$elementName = $couponElementsObj->$elementName;
	}
	else
	{
		$$elementName = new stdClass();
		$$elementName->width = 300;
		$$elementName->height = 50;
		$$elementName->left = 0;
		$$elementName->top = 0;
		$$elementName->fontsize = '20px';
		$$elementName->align = 'center';
		$$elementName->visible = 'false';
		$couponElementsObj->$elementName = $$elementName;
	}
}

// Fix issue if upgade to 2.8.0
if(!isset($couponElementsObj->highlights->fontsize))
	$couponElementsObj->highlights->fontsize = '15px';

if(!isset($couponElementsObj->highlights->align))
	$couponElementsObj->highlights->align = 'left';

if(!isset($couponElementsObj->terms->fontsize))
	$couponElementsObj->terms->fontsize = '15px';

if(!isset($couponElementsObj->terms->align))
	$couponElementsObj->terms->align = 'left';

$sampleCode				= "SAMPLECODE12345";
$sampleRecipient		= "Recipient name";
$sampleOption			= "Option name";
$sampleShortDesc		= "Short description";
$sampleHighlights		= "Highlights";
$sampleTerms			= "Terms";
$sampleCouponExp		= "Expires on 30 Feb 2020";
$samplePrice			= "Price";
$sampleOriginalPrice	= "Original price";
$sampleAdvancePrice		= "Advance price";
$sampleRemainPrice		= "Remaining price";

$coupon = $jinput->get('coupon', '', 'raw');

if($coupon != '' && file_exists(JPATH_ROOT . "/" . $coupon))
{
	list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . "/" . $coupon);
	$displayBackground = true;
}
else
{
	$width = 0;
	$height = 0;
	$displayBackground = false;
}

?>
<?php if(version_compare(JVERSION, '3.0.0', 'lt')): ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="../media/system/js/mootools-core-uncompressed.js"></script>
<style type="text/css">
div#coupon {
	position: relative;
	top: 0px;
	left: 0px;
	width: <?php echo $width; ?>px;
	height: <?php echo $height; ?>px;
}
div#coupon .text {
	position: absolute;
}
body {
	margin: 0;
	padding: 0;
}
</style>
<div id="coupon">
	<div class="text">
		<?php if($displayBackground): ?>
		<img src="<?php echo JURI::root() . $_REQUEST['coupon']; ?>" />
		<?php endif; ?>
		<div id="qrcode" class="ui-widget-content" style="cursor: move; width: <?php echo $qrcode->size; ?>px; height: <?php echo $qrcode->size; ?>px; left: <?php echo $qrcode->left; ?>px; top: <?php echo $qrcode->top; ?>px; position: absolute; <?php if($qrcode->visible=="false") echo "visibility: hidden;"; ?>">
			<img id="qrcode_image" src="<?php echo JURI::base() . 'index.php?option=com_cmgroupbuying&task=coupon.generate&size=' . $qrcode->size . '&code=' . base64_encode($sampleCode) . '&' . JSession::getFormToken() . '=1'; ?>" />
		</div>
		<div class="ui-widget-content" id="couponcode" style="cursor: move; font-size: <?php echo $couponcode->fontsize; ?>; text-align: <?php echo $couponcode->align; ?>; width: <?php echo $couponcode->width; ?>px; height: <?php echo $couponcode->height; ?>px; left: <?php echo $couponcode->left; ?>px; top: <?php echo $couponcode->top; ?>px;  position: absolute; <?php if($couponcode->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleCode; ?>
		</div>
		<div class="ui-widget-content" id="recipient" style="cursor: move; font-size: <?php echo $recipient->fontsize; ?>; text-align: <?php echo $recipient->align; ?>; width: <?php echo $recipient->width; ?>px; height: <?php echo $recipient->height; ?>px; left: <?php echo $recipient->left; ?>px; top: <?php echo $recipient->top; ?>px;  position: absolute; <?php if($recipient->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleRecipient; ?>
		</div>
		<div class="ui-widget-content" id="option" style="cursor: move; font-size: <?php echo $option->fontsize; ?>; text-align: <?php echo $option->align; ?>; width: <?php echo $option->width; ?>px; height: <?php echo $option->height; ?>px; left: <?php echo $option->left; ?>px; top: <?php echo $option->top; ?>px;  position: absolute; <?php if($option->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleOption; ?>
		</div>
		<div class="ui-widget-content" id="shortdesc" style="cursor: move; font-size: <?php echo $shortdesc->fontsize; ?>; text-align: <?php echo $shortdesc->align; ?>; width: <?php echo $shortdesc->width; ?>px; height: <?php echo $shortdesc->height; ?>px; left: <?php echo $shortdesc->left; ?>px; top: <?php echo $shortdesc->top; ?>px;  position: absolute; <?php if($shortdesc->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleShortDesc; ?>
		</div>
		<div class="ui-widget-content" id="highlights" style="cursor: move; font-size: <?php echo $highlights->fontsize; ?>; text-align: <?php echo $highlights->align; ?>; width: <?php echo $highlights->width; ?>px; height: <?php echo $highlights->height; ?>px; left: <?php echo $highlights->left; ?>px; top: <?php echo $highlights->top; ?>px;  position: absolute; <?php if($highlights->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleHighlights; ?>
		</div>
		<div class="ui-widget-content" id="terms" style="cursor: move; font-size: <?php echo $terms->fontsize; ?>; text-align: <?php echo $terms->align; ?>; width: <?php echo $terms->width; ?>px; height: <?php echo $terms->height; ?>px; left: <?php echo $terms->left; ?>px; top: <?php echo $terms->top; ?>px;  position: absolute; <?php if($terms->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleTerms; ?>
		</div>
		<div class="ui-widget-content" id="couponexp" style="cursor: move; font-size: <?php echo $couponexp->fontsize; ?>; text-align: <?php echo $couponexp->align; ?>; width: <?php echo $couponexp->width; ?>px; height: <?php echo $couponexp->height; ?>px; left: <?php echo $couponexp->left; ?>px; top: <?php echo $couponexp->top; ?>px;  position: absolute; <?php if($couponexp->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleCouponExp; ?>
		</div>
		<div class="ui-widget-content" id="price" style="cursor: move; font-size: <?php echo $price->fontsize; ?>; text-align: <?php echo $price->align; ?>; width: <?php echo $price->width; ?>px; height: <?php echo $price->height; ?>px; left: <?php echo $price->left; ?>px; top: <?php echo $price->top; ?>px;  position: absolute; <?php if($price->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $samplePrice; ?>
		</div>
		<div class="ui-widget-content" id="originalprice" style="cursor: move; font-size: <?php echo $originalprice->fontsize; ?>; text-align: <?php echo $originalprice->align; ?>; width: <?php echo $originalprice->width; ?>px; height: <?php echo $originalprice->height; ?>px; left: <?php echo $originalprice->left; ?>px; top: <?php echo $originalprice->top; ?>px;  position: absolute; <?php if($originalprice->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleOriginalPrice; ?>
		</div>
		<div class="ui-widget-content" id="advanceprice" style="cursor: move; font-size: <?php echo $advanceprice->fontsize; ?>; text-align: <?php echo $advanceprice->align; ?>; width: <?php echo $advanceprice->width; ?>px; height: <?php echo $advanceprice->height; ?>px; left: <?php echo $advanceprice->left; ?>px; top: <?php echo $advanceprice->top; ?>px;  position: absolute; <?php if($advanceprice->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleAdvancePrice; ?>
		</div>
		<div class="ui-widget-content" id="remainprice" style="cursor: move; font-size: <?php echo $remainprice->fontsize; ?>; text-align: <?php echo $remainprice->align; ?>; width: <?php echo $remainprice->width; ?>px; height: <?php echo $remainprice->height; ?>px; left: <?php echo $remainprice->left; ?>px; top: <?php echo $remainprice->top; ?>px;  position: absolute; <?php if($remainprice->visible=="false") echo "visibility: hidden;"; ?>">
			<?php echo $sampleRemainPrice; ?>
		</div>
	</div>
</div>
<br />
<?php
if($qrcode->visible == "true")
{
	$show = ' checked=""';
	$hide = '';
}
else
{
	$show = '';
	$hide = ' checked=""';
}

echo '<p>';
echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_QR_CODE') . ': <input type="radio" class="control" name="qrcode_control" value="true"' . $show . '/> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_SHOW') . ' <input type="radio" class="control" name="qrcode_control" value="false"' . $hide . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_HIDE');
echo '<br />';
echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_QR_SIZE') . ': <input type="text" name="qrcode_size" value="' . $qrcode->size . '" />';
echo '</p>';

foreach($elementNames as $elementName)
{
	$left	= ($$elementName->align == "left") ? ' checked=""' : '';
	$center	= ($$elementName->align == "center") ? ' checked=""' : '';
	$right	= ($$elementName->align == "right") ? ' checked=""' : '';
	$show	= ($$elementName->visible == "true") ? ' checked=""' : '';
	$hide	= ($$elementName->visible == "false") ? ' checked=""' : '';

	echo '<hr /><p>';
	echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_' . strtoupper($elementName) . '_NAME') . ': ';
	echo '<input type="radio" class="control" name="' . $elementName . '_control" value="true"' . $show . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_SHOW') . ' ';
	echo '<input type="radio" class="control" name="' . $elementName . '_control" value="false"' . $hide . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_HIDE');
	echo '<br />';
	echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_' . strtoupper($elementName) . '_SIZE') . ': ';
	echo '<input type="text" class="size" name="' . $elementName . '_size" value="' . $$elementName->fontsize . '" />';
	echo '<br />';
	echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_' . strtoupper($elementName) . '_ALIGNMENT') . ': ';
	echo '<input type="radio" class="alignment" name="' . $elementName . '_alignment" value="left"' . $left . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_LEFT') . ' ';
	echo '<input type="radio" class="alignment" name="' . $elementName . '_alignment" value="center"' . $center . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_CENTER') . ' ';
	echo '<input type="radio" class="alignment" name="' . $elementName . '_alignment" value="right"' . $right . ' /> ' . JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_RIGHT');
	echo '</p>';
}
?>
<input id="json" value="" type="hidden" />
<button type="button" onclick="generateResult(); window.parent.jInsertFieldValue(document.getElementById('json').value,'jform_coupon_elements'); window.parent.SqueezeBox.close();"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_SAVE'); ?></button> 
<input type="hidden" name="parent" value="deal" />
<script type="text/javascript" src="components/com_cmgroupbuying/assets/js/coupon.design.js" type="text/javascript"></script>
