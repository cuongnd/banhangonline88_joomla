<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>	 <div>
	<?php echo $this->tabs->startPane( 'mail_tab');?>
 	<?php echo $this->tabs->startPanel(JText::_( 'ATTACHMENTS' ), 'mail_attachments');?>
	<br style="font-size:1px"/>
		<?php if(!empty($this->mail->attach)){?>
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'ATTACHED_FILES' ); ?></legend>
			<?php
					foreach($this->mail->attach as $idAttach => $oneAttach){
						$idDiv = 'attach_'.$idAttach;
						echo '<div id="'.$idDiv.'">'.$oneAttach->filename.' ('.(round($oneAttach->size/1000,1)).' Ko)';
						echo $this->toggleClass->delete($idDiv,$this->mail->mailid.'_'.$idAttach,'mail');
				echo '</div>';
					}
		?>
		</fieldset>
		<?php } ?>
		<div id="loadfile">
			<input type="file" style="width:auto;" name="attachments[]" />
		</div>
		<a href="javascript:void(0);" onclick='addFileLoader()'><?php echo JText::_('ADD_ATTACHMENT'); ?></a>
			<?php echo JText::sprintf('MAX_UPLOAD',$this->values->maxupload);?>
		<?php echo $this->tabs->endPanel(); echo $this->tabs->startPanel(JText::_( 'SENDER_INFORMATIONS' ), 'mail_sender');?>
		<br style="font-size:1px"/>
		<table width="100%" class="paramlist admintable">
			<tr>
					<td class="paramlist_key">
						<?php echo JText::_( 'FROM_NAME' ); ?>
					</td>
					<td class="paramlist_value">
						<input class="inputbox" id="fromname" type="text" name="data[mail][fromname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromname); ?>" />
					</td>
				</tr>
			<tr>
					<td class="paramlist_key">
						<?php echo JText::_( 'FROM_ADDRESS' ); ?>
					</td>
					<td class="paramlist_value">
						<input class="inputbox" id="fromemail" type="text" name="data[mail][fromemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromemail); ?>" />
					</td>
				</tr>
				<tr>
				<td class="paramlist_key">
					<?php echo JText::_( 'REPLYTO_NAME' ); ?>
					</td>
					<td class="paramlist_value">
						<input class="inputbox" id="replyname" type="text" name="data[mail][replyname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyname); ?>" />
					</td>
				</tr>
				<tr>
				<td class="paramlist_key">
					<?php echo JText::_( 'REPLYTO_ADDRESS' ); ?>
					</td>
					<td class="paramlist_value">
						<input class="inputbox" id="replyemail" type="text" name="data[mail][replyemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyemail); ?>" />
					</td>
			</tr>
		</table>

<?php echo $this->tabs->endPanel();
		echo $this->tabs->startPanel(JText::_( 'META_DATA' ), 'mail_metadata');?>
		<br style="font-size:1px"/>
		<table width="100%" class="paramlist admintable" id="metadatatable">
			<tr>
					<td class="paramlist_key">
						<label for="metakey"><?php echo JText::_( 'META_KEYWORDS' ); ?></label>
					</td>
					<td class="paramlist_value">
						<textarea id="metakey" name="data[mail][metakey]" rows="5" cols="30" ><?php echo @$this->mail->metakey; ?></textarea>
					</td>
				</tr>
			<tr>
					<td class="paramlist_key">
						<label for="metadesc"><?php echo JText::_( 'META_DESC' ); ?></label>
					</td>
					<td class="paramlist_value">
						<textarea id="metadesc" name="data[mail][metadesc]" rows="5" cols="30" ><?php echo @$this->mail->metadesc; ?></textarea>
					</td>
				</tr>
		</table>
<?php
	echo $this->tabs->endPanel();
	echo $this->tabs->endPane(); ?>
	</div>
