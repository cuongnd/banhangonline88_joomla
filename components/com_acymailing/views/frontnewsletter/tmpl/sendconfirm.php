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
<?php

if(!empty($this->values->nbqueue)){
	echo acymailing_display(JText::sprintf('ALREADY_QUEUED',$this->values->nbqueue));
}elseif(empty($this->lists)){
	echo acymailing_display(JText::_( 'EMAIL_AFFECT' ),'warning');
}else{ ?>
	<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm" >
	<div>
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'NEWSLETTER_SENT_TO' ); ?></legend>
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
							echo ' ( '.JText::sprintf('SELECTED_USERS',$row->nbsub).' )';
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
		<?php if(!empty($this->values->alreadySent)){
				acymailing_display(JText::sprintf('ALREADY_SENT',$this->values->alreadySent).'<br/>'.JText::_('REMOVE_ALREADY_SENT').'<br/>'.JHTML::_('select.booleanlist', "onlynew",'',1,JText::_('JOOMEXT_YES'),JText::_('SEND_TO_ALL')),'warning');
			}
	?>
	<input type="submit" class="btn btn-primary" value="<?php echo JText::_('SEND'); ?>">
	</div>
	<div class="clr"></div>
	<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>" />
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="send" />
	<input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid'); ?>" />
	<input type="hidden" name="ctrl" value="frontnewsletter" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php } ?>
</div>
