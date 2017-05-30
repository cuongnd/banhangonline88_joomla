<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<div id="iframedoc"></div>
<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=send" method="post" name="adminForm"  id="adminForm" autocomplete="off">
	<div>
	<?php if(!empty($this->lists)){?>
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'NEWSLETTER_SENT_TO'); ?></legend>
			<table class="adminlist table table-striped" cellspacing="1" align="center">
				<tbody>
					<?php
					$k = 0;
					foreach($this->lists as $row){
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td>
							<?php
							echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
							echo ' ( '.$row->nbsub.' )';
							 ?>
						</td>
					</tr>
					<?php
						$k = 1 - $k;
					} ?>
				</tbody>
			</table>
			<?php
			if(!empty($this->mail->filter)){
				$filterClass = acymailing_get('class.filter');
				$resultFilters = $filterClass->displayFilters($this->mail->filter);
				if(!empty($resultFilters)){
					echo '<br/>'.JText::_('RECEIVER_LISTS').'<br/>'.JText::_('FILTER_ONLY_IF');
					echo '<ul><li>'.implode('</li><li>',$resultFilters).'</li></ul>';
				}
			}?>
		</fieldset>
		<table class="adminform">
			<tr>
				<td class="key">
					<?php echo JText::_('SEND_DATE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('calendar', acymailing_getDate(time(),'%Y-%m-%d'), 'senddate','senddate','%Y-%m-%d',array('style'=>'width:80px'));
					 echo '&nbsp; @ ' . $this->hours . ' : ' . $this->minutes; ?>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<button class="btn btn-primary" type="submit"><?php echo JText::_('SCHEDULE'); ?></button>
				</td>
			</tr>
		</table>

	<?php }else{echo acymailing_display(JText::_( 'EMAIL_AFFECT' ),'warning');}?>

	</div>
	<div class="clr"></div>
	<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>" />
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="schedule" />
	<input type="hidden" name="ctrl" value="send" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
