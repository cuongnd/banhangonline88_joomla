<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><style type="text/css">
table#printstats{
	background-color: white;
	border-collapse: collapse;
}
#printstats th, #printstats td{
	border: 1px solid #CCCCCC;
	padding: 4px;
}

#printstats thead{
	background-color: #5471B5;
	color: white;
}

</style>
<div id="acy_content" >
<div id="iframedoc"></div>
<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=diagram" method="post" name="adminForm" id="adminForm" >
	<table width="100%" class="donotprint">
		<tr>
			<td width="50%" valign="top">
				<fieldset class="adminform">
					<legend><?php echo JText::_('DISPLAY'); ?></legend>
					<table>
						<tr>
							<td class="fieldkey"><?php echo JText::_('CHART_TYPE'); ?></td>
							<td>
								<?php
									$value = (empty($this->display['charttype']) ? 'ColumnChart' : $this->display['charttype']);
									$values = array(
										JHTML::_('select.option', 'ColumnChart', JText::_('COLUMN_CHART')),
										JHTML::_('select.option', 'LineChart', JText::_('LINE_CHART'))
									);
									echo JHTML::_('acyselect.radiolist',  $values, 'display[charttype]', '', 'value', 'text', $value );
								?>
							</td>
						</tr>
						<tr>
							<td class="fieldkey"><?php echo JText::_('ACY_PERIOD'); ?></td>
							<td>
								<?php echo JText::_('ACY_FROM_DATE').' '; echo JHTML::_('calendar', @$this->display['datemin'], 'display[datemin]','display_datemin','%Y-%m-%d',array('style'=>'width:120px')); ?>
								<?php echo JText::_('ACY_TO_DATE').' ';  echo JHTML::_('calendar', @$this->display['datemax'], 'display[datemax]','display_datemax','%Y-%m-%d',array('style'=>'width:120px')); ?>
							</td>
						</tr>
						<tr>
							<td class="fieldkey"><?php echo JText::_('ACY_INTERVAL'); ?></td>
							<td>
								<?php
									$value = (empty($this->display['interval']) ? 'month' : $this->display['interval']);
									$values = array(
										JHTML::_('select.option', 'day', JText::_('ACY_DAY')),
										JHTML::_('select.option', 'month', JText::_('ACY_MONTH')),
										JHTML::_('select.option', 'year', JText::_('ACY_YEAR'))
									);
									echo JHTML::_('acyselect.radiolist',  $values, 'display[interval]', '', 'value', 'text', $value );
								?>
							</td>
						</tr>
						<tr>
							<td class="fieldkey"><?php echo JText::_('ACY_STATS_ADDUP'); ?></td>
							<td>
								<?php
									$value = (empty($this->display['sumup']) ? '0' : $this->display['sumup']);
									$values = array(
										JHTML::_('select.option', '0', JText::_('JOOMEXT_NO')),
										JHTML::_('select.option', '1', JText::_('JOOMEXT_YES')),
									);
									echo JHTML::_('acyselect.radiolist',  $values, 'display[sumup]', '', 'value', 'text', $value );
								?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td valign="top">
				<fieldset class="adminform">
					<legend><?php echo JText::_('ACY_COMPARE'); ?></legend>
					<span><input onclick="document.getElementById('alllists').style.display = 'none'; if(this.checked){document.getElementById('alllists').style.display = 'block'}" <?php if(!empty($this->compares['lists'])) echo 'checked="checked"'; ?> type="checkbox" value="lists" name="compares[lists]" id="compares_lists"/> <label for="compares_lists"><?php echo JText::_('LISTS'); ?></label></span>
					<fieldset id="alllists" <?php if(empty($this->compares['lists'])) echo 'style="display:none"' ?> >
					<?php
						if(!empty($this->lists)){
							foreach($this->lists as $oneList){
								echo '<span style="display:bock;float:left;margin-right:15px;"><input type="checkbox" '.(empty($this->filterlists) || !in_array($oneList->listid,$this->filterlists) ? '' : 'checked="checked"').' value="'.$oneList->listid.'" name="filterlists[]" id="list_'.$oneList->listid.'" style="margin:3px;padding:0px;"/><label style="margin:3px;padding:0px;" for="list_'.$oneList->listid.'">'.$oneList->name.'</label></span>';
							}
						}
					?>
					</fieldset>
					<br/><span><input <?php if(!empty($this->compares['years'])) echo 'checked="checked"'; ?> type="checkbox" value="years" name="compares[years]" id="compares_years"/> <label for="compares_years"><?php echo JText::_('ACY_YEARS'); ?></label></span>
				</fieldset>
			</td>
		</tr>
	</table>
	<p style="text-align:center" class="donotprint">
		<input type="submit" class="button btn btn-primary" onclick="document.adminForm.task.value='';" style="padding:10px 20px;font-size:16px" value="<?php echo JText::_('GENERATE_CHART'); ?>"/>
	</p>
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
</form>
<div id="chart" class="printarea" style="margin-top:30px;width:100%"></div>
<?php if(!empty($this->export)){ ?>
	<img class="donotprint" style="position:relative;top:-45px;left:5px;cursor:pointer;" onclick="exportData();" src="<?php echo ACYMAILING_IMAGES.'smallexport.png'; ?>" alt="<?php echo JText::_('ACY_EXPORT',true)?>" title="<?php echo JText::_('ACY_EXPORT',true)?>" />
	<textarea cols="100" rows="10" id="exporteddata" style="display:none;position:absolute;margin-top:-150px;"><?php echo implode("\n",$this->export); ?></textarea>
	<?php
		$lists = explode(',',$this->export[0]);
		echo '<table id="printstats" class="onlyprint"><thead><tr>';
		for($i=0;$i<count($lists);$i++){
					echo '<th>'.$lists[$i].'</th>';
		}
		echo '</tr></thead><tbody>';
		foreach($this->export as $exportNumber => $oneExport){
			if($exportNumber == '0') continue;
			$total = explode(',', $this->export[$exportNumber]);
			echo '<tr>';
			for($i=0;$i<count($total);$i++){
				echo '<td align="center">'.$total[$i].'</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
?>
<br style="clear:both" />
</div>
