<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaserial::completeLink('config&task=sql'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<table class="admintable" style="width:100%">
		<tr>
			<td valign="top" width="50%">
				<fieldset>
					<legend><?php echo JText::_('HIKA_SQL_REQUEST');?></legend>
					<textarea name="sql_data" rows="15" style="width:100%"><?php echo @$this->sql_data; ?></textarea>
				</fieldset>
			</td>
			<td valign="top" width="50%">
				<fieldset>
					<legend><?php echo JText::_('HIKA_SQL_RESULT');?></legend>
					<?php
					if(!empty($this->query_result)) {
						if(is_array($this->query_result)) {
							echo '<table class="adminlist table table-striped" style="width:100%"><thead>';
							$head = array_keys(get_object_vars(reset($this->query_result)));
							foreach($head as $h) {
								echo '<th>'.$h.'</th>';
							}
							reset($this->query_result);

							echo '</thead><tbody>';
							foreach($this->query_result as $result) {
								echo '<tr>';
								foreach($head as $h) {
									echo '<td>'.nl2br(htmlentities($result->$h)).'</td>';
								}
								echo '</tr>';
							}
							echo '</tbody></table>';
						} else {
							echo $this->query_result;
						}
					} else {
						echo JText::_('HIKA_NO_SQL');
					}
					?>
				</fieldset>
			</td>
		</tr>
	</table>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
