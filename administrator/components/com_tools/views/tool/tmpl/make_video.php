<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tools
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_tools/css/form.css');
$frame = 10;
$movie = JPATH_ROOT.DS.'images/videos/Wildlife.wmv';
$thumbnail = JPATH_ROOT.DS.'images/videos/thumbnail.png';
require_once JPATH_ROOT.DS.'libraries/ffmpeg-php-master/FFmpegAutoloader.php';
$mov = new ffmpeg_movie($movie);
$frame = $mov->getFrame($frame);
if ($frame) {
	$gd_image = $frame->toGDImage();
	if ($gd_image) {
		imagepng($gd_image, $thumbnail);
		imagedestroy($gd_image);
		echo '<img src="'.$thumbnail.'">';
	}
}
?>
