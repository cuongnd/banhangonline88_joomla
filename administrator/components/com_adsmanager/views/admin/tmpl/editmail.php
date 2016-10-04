<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
//include (JPATH_ROOT.'/libraries/joomla/html/html/access.php');
include_once (JPATH_ROOT.'/components/com_adsmanager/helpers/usergroups.php');
?>
<script type="text/javascript">
<?php if(version_compare(JVERSION,'1.6.0','>=')){ ?>
Joomla.submitbutton = function(pressbutton) {
<?php } else { ?>
function submitbutton(pressbutton) {
<?php } ?>
	   $editor = JFactory::getEditor(); 
	   echo $editor->save( 'description' );
       submitform(pressbutton);
   }
</script>
<?php JText::_('ADSMANAGER_MAIL_EDITION'); ?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_SUBJECT'); ?></td>
<td colspan="2"><input type="text" size="50"  name="subject" value="<?php echo @$this->row->subject; ?>" /></td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_BODY'); ?></td>
<td colspan="2">
<?php
$editor = JFactory::getEditor();
echo $editor->display('body', @$this->row->body, '100%','350', 75, 20);
?>				
</td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_RECIPIENT'); ?></td>
<td colspan="2"><input type="text" size="50"  name="recipient" value="<?php echo @$this->row->recipient; ?>" /></td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_STATUT'); ?></td>
<td colspan="2">
    <select id='statut' name='statut'>
        <option value='0' <?php if (@$this->row->statut == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_MAIL_STATUT_NSENT'); ?></option>
        <option value='1' <?php if (@$this->row->statut == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_MAIL_STATUT_SENT'); ?></option>
    </select>
</td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_FROM'); ?></td>
<td colspan="2"><input type="text" size="50"  name="from" value="<?php echo @$this->row->from; ?>" /></td>
</tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_FROMNAME'); ?></td>
<td colspan="2"><input type="text" size="50"  name="fromname" value="<?php echo @$this->row->fromname; ?>" /></td>
</tr>

</table>
<input type="hidden" name="id" value="<?php echo @$this->row->id; ?>" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="c" value="mails" />
<input type="hidden" name="task" value="" />
</form>