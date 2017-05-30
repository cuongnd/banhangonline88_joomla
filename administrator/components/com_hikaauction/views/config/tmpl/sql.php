<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaauction::completeLink('config&task=sql'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo JText::_('HIKA_SQL_REQUEST');?></legend>
		<textarea name="sql_data" rows="15" style="width:100%"><?php echo @$this->sql_data; ?></textarea>
	</fieldset>
	<div style="overflow-x:auto;max-height:300px;">
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
	</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo HIKAAUCTION_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
