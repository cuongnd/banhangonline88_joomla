<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
$option = JRequest::getString('option');
$xmlFile = str_replace("com_", "", $option).'.xml';
$xmlElement = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/'.$option.'/'.$xmlFile);
if($xmlElement)
{
    $title = (string) $xmlElement->name;
    $version = (string) $xmlElement->version;
}
?>
<table style="margin-bottom: 5px; padding: 3px; width: 100%; border: 2px solid #BBBBBB; background:#F8F8F8">
	<tbody>
		<tr>
			<td style="text-align: left; width: 25%; padding: 10px 0 0 10px;">
			<a href="http://www.sourcecoast.com" target="_blank"><img src="components/<?php echo $option;?>/assets/footer/SourceCoast_Logo.png" width="250px"/></a>
			</td>
			<td style="text-align: center; width: 49%;">
				<?php echo $title;?>
				<br/>Copyright: 2010-2014 &copy; <a href="http://www.sourcecoast.com" target="_blank">SourceCoast Web Development</a>
				<br/>
				Version: <?php echo $version;?>
			</td>
			<td style="text-align: right; width: 25%;">
				<a href="http://extensions.joomla.org/extensions/owner/sourcecoast" target="_blank"><?php echo JText::_('COM_JFBCONNECT_FOOTER_LEAVE_FEEDBACK');?></a>
				<br/>
				<a href="http://twitter.com/sourcecoast" target="_blank"><?php echo JText::_('COM_JFBCONNECT_FOOTER_FOLLOW_TWITTER');?></a>
				<br/>
				<a href="http://www.sourcecoast.com/forums" target="_blank"><?php echo JText::_('COM_JFBCONNECT_FOOTER_SUPPORT_FORUMS');?></a>
				<br/>
				<a href="http://www.sourcecoast.com/affiliates" target="_blank"><?php echo JText::_('COM_JFBCONNECT_FOOTER_BE_AFFILIATE');?></a>
			</td>
		</tr>
	</tbody>
</table>
