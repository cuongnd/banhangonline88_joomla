<?php 
/** 
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage eventstats
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm"> 
	<div class="accordion-group">
		<div class="accordion-heading opened">
			<div class="accordion-toggle noaccordion">
				<h4><span class="icon-pencil"></span><?php echo JText::_( 'COM_JCHAT_ROOM_DETAILS' ); ?></h4>
			</div>
		</div>
		<div id="details" class="accordion-body collapse in">
	      	<div class="accordion-inner">
				<table class="admintable">
				<tbody>
					<tr>
						<td class="key left_title">
							<label for="name">
								<?php echo JText::_('COM_JCHAT_NAME' ); ?>:
							</label>
						</td>
						<td class="right_details">
							<input class="inputbox" type="text" name="name" id="name" data-validation="required" size="50" value="<?php echo $this->record->name;?>" />
						</td>
					</tr>
					<tr>
						<td class="key left_title">
							<label for="description">
								<?php echo JText::_('COM_JCHAT_DESCRIPTION' ); ?>:
							</label>
						</td>
						<td class="right_details">
							<textarea class="inputbox" type="text" name="description" id="description" rows="5" cols="80" ><?php echo $this->record->description;?></textarea>
						</td>
					</tr> 
					<tr>
						<td class="key left_title">
							<label>
								<?php echo JText::_('COM_JCHAT_PUBLISHED' ); ?>:
							</label>
						</td>
						<td class="right_details">
							<fieldset class="radio btn-group">
								<?php echo $this->lists['published']; ?>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td class="key left_title">
							<label>
								<?php echo JText::_('COM_JCHAT_ACCESSLEVEL' ); ?>:
							</label>
						</td>
						<td class="right_details">
							<fieldset class="radio btn-group">
								<?php echo $this->lists['access']; ?>
							</fieldset>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="option" value="<?php echo $this->option?>" />
	<input type="hidden" name="id" value="<?php echo $this->record->id; ?>" />
	<input type="hidden" name="ordering" value="<?php echo $this->record->ordering; ?>" />
	<input type="hidden" name="task" value="" />
</form>