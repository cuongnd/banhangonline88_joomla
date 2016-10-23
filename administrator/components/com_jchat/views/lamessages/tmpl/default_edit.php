<?php 
/** 
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage lamessages
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
				<h4><span class="icon-pencil"></span><?php echo JText::_( 'COM_JCHAT_TICKET_DETAILS' ); ?></h4>
			</div>
		</div>
		<div id="details" class="accordion-body collapse in">
	      	<div class="accordion-inner">
				<table class="admintable">
				<tbody>
					<tr>
						<td width="20%" class="left_title"> 
							<?php echo JText::_( 'ID' ); ?>: 
						</td>
						<td width="80%" class="right_details">
							<label class="label label-info">
								<?php echo $this->record->id;?> 
							</label>
						</td>
					</tr>
					<tr>
						<td width="20%" class="left_title"> 
							<?php echo JText::_( 'COM_JCHAT_LAMESSAGE_NAME' ); ?>: 
						</td>
						<td width="80%" class="right_details">
					 		<input class="inputbox" type="text" name="name" id="name" data-validation="required" size="50" value="<?php echo $this->record->name;?>" />
						</td>
					</tr>
					<tr>
						<td class="left_title"> 
							<?php echo JText::_( 'COM_JCHAT_LAMESSAGE_EMAIL' ); ?>:  
						</td>
						<td class="right_details">
							<input class="inputbox" type="text" name="email" id="email" data-validation="required email" size="50" value="<?php echo $this->record->email;?>" />
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_( 'COM_JCHAT_MESSAGE' ); ?>:
						</td>
						<td class="right_details">
							<textarea class="inputbox" type="text" name="message" id="message" rows="5" cols="80" ><?php echo $this->record->message;?></textarea>
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_( 'COM_JCHAT_SENT' ); ?>:
						</td>
						<td class="right_details">
							<div class="input-prepend active">
								<span class="add-on"><span class="icon-calendar"></span></span>
								<input class="inputbox" type="text" name="sentdate" id="sentdate" data-validation="required" data-role="calendar" size="50" value="<?php echo $this->record->sentdate;?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_( 'COM_JCHAT_LAMESSAGE_USERID' ); ?>:
						</td>
						<td class="right_details">
							<label class="label label-info">
								<?php echo isset($this->record->_username_logged) ? $this->record->_username_logged : JText::_('COM_JCHAT_ANONYMOUS');?> 
							</label>
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_( 'COM_JCHAT_WORKED' ); ?>:
						</td>
						<td class="right_details">
							<fieldset class="radio btn-group">
								<?php echo $this->lists['worked']; ?>
							</fieldset>
						</td>
					</tr> 
					<tr>
						<td class="left_title">
							<?php echo JText::_( 'COM_JCHAT_CLOSED_TICKET' ); ?>:
						</td>
						<td class="right_details">
							<fieldset class="radio btn-group">
								<?php echo $this->lists['closed']; ?>
							</fieldset>
						</td>
					</tr> 
				</tbody>
				</table>
			</div>
		</div>
	</div>
	
	<div class="accordion-group">
		<div class="accordion-heading opened">
			<div class="accordion-toggle noaccordion">
				<h4><span class="icon-pencil"></span><?php echo JText::_( 'COM_JCHAT_TICKET_REPLY' ); ?></h4>
			</div>
		</div>
		<div id="details" class="accordion-body collapse in">
	      	<div class="accordion-inner">
	      		<table class="admintable response">
					<tr>
						<td width="20%" class="left_title"> 
							<?php echo JText::_( 'COM_JCHAT_EMAIL_SUBJECT' ); ?>: 
						</td>
						<td width="80%" class="right_details messageresponse">
					 		<input name="email_subject" data-validation="required" value="<?php echo JText::sprintf('COM_JCHAT_REPLYSUBJECT', $this->record->id, $this->record->sentdate); ?>" class="inputbox wide"/>
						</td>
					</tr>
					<tr class="nomessage">
						<td width="20%" class="left_title"> 
							<?php echo JText::_( 'COM_JCHAT_COMPOSE_RESPONSE' ); ?>: 
						</td>
						<td width="80%" class="right_details messageresponse"> 
					 		<?php echo $this->editor->display( 'response',  null , '100%', '350', '75', '20', false ) ; ?>
						</td>
					</tr>
					<tr class="nomessage">
						<td width="20%" class="left_title"></td>
						<td width="80%" class="right_details messageresponse">
							<div class="btn-wrapper" id="toolbar-upload">
								<button onclick="Joomla.submitbutton('lamessages.responsemessage')" class="btn btn-small">
									<span class="icon-upload"></span>
									<?php echo JText::_( 'COM_JCHAT_RESPONSE_LEAVED_MESSAGES' ); ?>
								</button>
							</div>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
			<input type="hidden" name="ticket_sender" value="<?php echo $this->nameOfUser;?>"/>
		</div>
	</div>
	
	<div class="accordion-group">
		<div class="accordion-heading opened">
			<div class="accordion-toggle noaccordion">
				<h4><span class="icon-list-view"></span><?php echo JText::_( 'COM_JCHAT_TICKET_RESPONSES' ); ?></h4>
			</div>
		</div>
		<div id="details" class="accordion-body collapse in">
	      	<div class="accordion-inner">
				<table class="admintable response">
					<tbody>
						<?php if(isset($this->record->responses) && is_array($this->record->responses)): 
							$this->record->responses = array_reverse($this->record->responses);
							?>
							<?php foreach($this->record->responses as $responseGiven): ?>
							<tr>
								<td width="20%" class="left_title"> 
									<p>
										<?php 
											// Transform the date string, get date time in UTC from DB
											$dateObject = JFactory::getDate($responseGiven[0]);
											// Set local time zone
											$dateObject->setTimezone(new DateTimeZone($this->joomlaConfig));
										?>
										<label class="label label-success"><?php echo $dateObject->format('Y-m-d H:i:s', true, false); ?></label>
									</p>
									<p>
										<label class="label label-warning"><?php echo $responseGiven[2]; ?></label>
									</p>
								</td>
								<td width="80%" class="right_details messageresponse">
									<div class="well well-large">
										<?php echo $responseGiven[1];?> 
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
		
	<input type="hidden" name="option" value="<?php echo $this->option;?>" /> 
	<input type="hidden" name="id" value="<?php echo $this->record->id;?>" /> 
	<input type="hidden" name="task" value="" /> 
</form>