<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" valign="top">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_ACL_RULE_SET' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="150" class="key">
						<label for="cid"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></label>
					</td>
					<td>
						<div id="aclid"><?php echo !empty($this->rulesets->id)? $this->rulesets->id : ''; ?></div>
					</td>
				</tr>
				<tr>
					<td width="150" class="key">
						<label for="name"><?php echo JText::_( 'COM_EASYBLOG_ACL_NAME' ); ?></label>
					</td>
					<td>
						<input type="text" readonly="readonly" class="inputbox" id="aclname" value="<?php echo !empty($this->rulesets->name)?  $this->escape( $this->rulesets->name ) : ''; ?>">
						<?php if ( $this->type == 'assigned' ) : ?>
						[ <a class="modal" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a> ]
						<?php endif; ?>
					</td>

				</tr>
			<?php
			foreach($this->rulesets->rules as $key=>$data)
			{
			?>
				<tr>
					<td width="150" class="key">
						<label for="name">
							<?php echo JText::_( 'COM_EASYBLOG_ACL_OPTION_' . $key ); ?>
						</label>
					</td>
					<td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( $this->getDescription( $key ) ); ?></div>
							<?php echo $this->renderCheckbox( $key , $data ); ?>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
			</table>
		</fieldset>
		</td>
		<td width="50%" valign="top">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_ACL_TEXT_FILTERS' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_EASYBLOG_ACL_TEXT_FILTERS_INFO' );?></p>
			<table class="adminTable" cellspacing="1">
				<tr>
					<td width="150" class="key" style="text-align:right;">
						<label for="disallow-tags"><?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_TAGS' ); ?></label>
					</td>
					<td>
						<textarea id="disallow-tags" name="disallow_tags" class="inputbox full-width textarea"><?php echo $this->filter->disallow_tags;?></textarea>
					</td>
				</tr>
				<tr>
					<td width="150" class="key">
						<label for="disallow-attributes"><?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES' ); ?></label>
					</td>
					<td>
						<textarea id="disallow-attributes" name="disallow_attributes" class="inputbox full-width textarea"><?php echo $this->filter->disallow_attributes;?></textarea>
					</td>

				</tr>
			</table>
		</fieldset>
		</td>
	</tr>
</table>