<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_subscriber_form_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
		<td id="acybutton_subscriber_save"><a onclick="javascript:submitbutton('save'); return false;" href="#" ><span class="icon-32-save" title="<?php echo JText::_('ACY_SAVE'); ?>"></span><?php echo JText::_('ACY_SAVE'); ?></a></td>
		<td id="acybutton_subscriber_apply"><a onclick="javascript:submitbutton('apply'); return false;" href="#" ><span class="icon-32-apply" title="<?php echo JText::_('ACY_APPLY'); ?>"></span><?php echo JText::_('ACY_APPLY'); ?></a></td>
		<td id="acybutton_subscriber_cancel"><a onclick="javascript:submitbutton('cancel'); return false;" href="#" ><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL'); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo JText::_('ACY_USER'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'tmpl'.DS.'form.php');
