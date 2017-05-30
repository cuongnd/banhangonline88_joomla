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
	<fieldset id="acy_newsletter_form_menu">
		<div class="toolbar" id="acytoolbar" style="float: right;">
			<table><tr>

				<?php if(acymailing_isAllowed($this->config->get('acl_templates_view','all'))){ ?><td id="acybuttontemplate"><a class="modal"  rel="{handler: 'iframe', size: {x: 750, y: 550}}" href="<?php echo acymailing_completeLink("fronttemplate&task=theme",true ); ?>"><span class="icon-32-acytemplate" title="<?php echo JText::_('ACY_TEMPLATE'); ?>"></span><?php echo JText::_('ACY_TEMPLATE'); ?></a></td><?php } ?>

				<?php if(acymailing_isAllowed($this->config->get('acl_tags_view','all'))){ ?>
					<td id="acybuttontag"><a onclick="try{IeCursorFix();}catch(e){};" class="modal" rel="{handler: 'iframe', size: {x: 750, y: 550}}" href="<?php echo acymailing_completeLink("fronttag&task=tag&type=".$this->type,true ); ?>"><span class="icon-32-tag" title="<?php echo JText::_('TAGS'); ?>"></span><?php echo JText::_('TAGS'); ?></a></td>
					<td id="acybuttonreplace"><a onclick="javascript:submitbutton('replacetags'); return false;" href="#" ><span class="icon-32-replacetag" title="<?php echo JText::_('REPLACE_TAGS'); ?>"></span><?php echo JText::_('REPLACE_TAGS'); ?></a></td>
				<?php } ?>
				<td id="acybuttondivider"><span class="divider"></span></td>
				<td id="acybuttonpreview"><a onclick="javascript:submitbutton('savepreview'); return false;" href="#" ><span class="icon-32-acypreview" title="<?php echo JText::_('ACY_PREVIEW').' / '.JText::_('SEND'); ?>"></span><?php echo JText::_('ACY_PREVIEW').' / '.JText::_('SEND'); ?></a></td>
				<td id="acybuttonsave"><a onclick="javascript:submitbutton('save'); return false;" href="#" ><span class="icon-32-save" title="<?php echo JText::_('ACY_SAVE'); ?>"></span><?php echo JText::_('ACY_SAVE'); ?></a></td>
				<td id="acybuttonapply"><a onclick="javascript:submitbutton('apply'); return false;" href="#" ><span class="icon-32-apply" title="<?php echo JText::_('ACY_APPLY'); ?>"></span><?php echo JText::_('ACY_APPLY'); ?></a></td>
				<td id="acybuttoncancel"><a onclick="javascript:submitbutton('cancel'); return false;" href="#" ><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL'); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>

			</tr></table>
		</div>
		<div class="acyheader" style="float: left;"><h1><?php echo JText::_('NEWSLETTER').' : '.@$this->mail->subject; ?></h1></div>
	</fieldset>
	<div id="acymailing_edit">
		<form action="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl=frontnewsletter'); ?>" method="post" name="adminForm"  id="adminForm" autocomplete="off" enctype="multipart/form-data">

			<?php include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'info.form.php'); ?>
			<?php include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'param.form.php'); ?>
			<fieldset class="adminform" style="width:90%" id="htmlfieldset">
				<legend><?php echo JText::_( 'HTML_VERSION' ); ?></legend>
				<div style="clear:both"><?php echo $this->editor->display(); ?></div>
			</fieldset>
			<fieldset class="adminform" style="width:90%" id="textfieldset">
				<legend><?php echo JText::_( 'TEXT_VERSION' ); ?></legend>
				<textarea style="width:98%" rows="20" name="data[mail][altbody]" id="altbody" placeholder="<?php echo JText::_('AUTO_GENERATED_HTML'); ?>" ><?php echo @$this->mail->altbody; ?></textarea>
			</fieldset>


			<div class="clr"></div>
			<input type="hidden" name="cid[]" value="<?php echo @$this->mail->mailid; ?>" />
			<input type="hidden" id="tempid" name="data[mail][tempid]" value="<?php echo @$this->mail->tempid; ?>" />
			<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
			<input type="hidden" name="data[mail][type]" value="news" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="ctrl" value="frontnewsletter" />
			<input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid'); ?>"/>
			<?php if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
			echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>
