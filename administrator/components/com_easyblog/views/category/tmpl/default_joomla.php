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
<script type="text/javascript">
EasyBlog.ready(function($){

	$( '#private' ).bind( 'change' , function(){

		console.log( $(this).val() );
		if( $(this).val() == '2' )
		{
			$( '#categoryaccess' ).show();
		}
		else
		{
			$( '#categoryaccess').hide();
		}
	});
});
</script>

<table width="100%">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_TITLE'); ?></legend>
				<table class="admintable">
					<tr>
					    <td class="key">
					    	<label for="parent_id" class="key"><?php echo JText::_('COM_EASYBLOG_PARENT'); ?></label>
					    </td>
						<td>
							<?php echo $this->parentList; ?>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_PARENT_TIPS' );?></div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="catname" class="key"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_NAME' ); ?></label>
						</td>
						<td>
							<input class="inputbox full-width" id="catname" name="title" size="55" maxlength="255" value="<?php echo $this->cat->title;?>" />
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_TITLE_TIPS' );?></div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="alias" class="key"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_ALIAS' ); ?></label>
						</td>
						<td>
							<input class="inputbox full-width" id="alias" name="alias" size="55" maxlength="255" value="<?php echo $this->cat->alias;?>" />
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_ALIAS_TIPS' );?></div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="catname" class="key"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DESCRIPTION' ); ?></label>
						</td>
						<td>
							<?php echo $this->editor->display('description', $this->cat->get( 'description') , '99%', '200', '10', '10', array('image', 'readmore', 'pagebreak'), array(), 'com_easyblog'); ?>
							<div style="clear:both;"></div>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_DESC_TIPS' );?></div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="published" class="key"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_PUBLISHED' ); ?></label>
						</td>
						<td>
							<?php echo $this->renderCheckbox( 'published' , $this->cat->published ); ?>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_PUBLISH_TIPS' );?></div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label class="key"><?php echo JText::_('COM_EASYBLOG_AUTHOR'); ?></label>
						</td>
						<td>
							<input type="hidden" name="created_by" id="created_by" value="<?php echo $this->cat->get( 'created_by' );?>" />
							<span id="author-name" class="bubble-item"<?php if( empty($this->cat->created_by)){ ?> style="display:none;"<?php } ?>>
								<?php
								if(!empty( $this->cat->created_by ) )
								{
									echo JFactory::getUser( $this->cat->get( 'created_by') )->name;
								}
								?>
							</span>

							<span>
								<a class="modal-button modal button" rel="{handler:'iframe',size:{x:650,y:375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1&browsefunction=insertUser"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a>
							</span>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_AUTHOR_TIPS' );?></div>
						</td>
					</tr>

					<?php if($this->config->get('layout_categoryavatar', true)) : ?>
					<tr>
			        	<td class="key">
			        		<label for="Filedata" class="key"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_AVATAR'); ?></label>
			        	</td>
						<td>
						    <?php if(! empty($this->cat->avatar)) { ?>
								<img style="border-style:solid; float:none;" src="<?php echo $this->cat->getAvatar(); ?>" width="60" height="60"/><br />
						    <?php }?>
							<?php if ($this->acl->rules->upload_cavatar) : ?>
								<input id="file-upload" type="file" name="Filedata" class="inputbox" size="33"/>
							<?php endif; ?>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_AVATAR_TIPS' );?></div>
						</td>
					</tr>
					<?php endif; ?>
					<tr style="display: none;">
						<td class="key"><label for="created"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_CREATED' ); ?></label></td>
						<td>
							<?php echo JHTML::_('calendar', $this->cat->created , "created", "created"); ?>
							<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_CREATED_TIPS' );?></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_ACCESS' ); ?></legend>
				<table class="admintable">
				<tr>
					<td class="key">
						<label for="private" class="key"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_PRIVACY'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions( 'category' ) , 'private' , 'size="1" class="inputbox"' , 'value' , 'text', $this->cat->private );?>
						<div class="small"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_PRIVACY_TIPS' );?></div>
					</td>
				</tr>
				</table>

				<table class="admintable" id="categoryaccess" style="<?php echo $this->cat->private != 2 ? 'display: none;' : '';?>">
				<?php foreach($this->categoryRules as $catRules) :
					$catRuleSet 	= $this->assignedACL[$catRules->id];
					$titleString	= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_TITLE';
					$descString		= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_DESC';
				?>
				    <tr>
				        <td class="key">
				        	<span><?php echo JText::_( $titleString ); ?></span>
				        </td>
				        <td>
				            <select multiple="multiple" name="category_acl_<?php echo $catRules->action; ?>[]">
							<?php foreach($catRuleSet as $ruleItem) : ?>
							    <option value="<?php echo $ruleItem->groupid; ?>" <?php echo ($ruleItem->status) ? 'selected="selected"' : ''; ?> ><?php echo $ruleItem->groupname; ?></option>
							<?php endforeach; ?>
							</select>
							<div style="clear:both;"></div>
							<div class="small"><?php echo JText::_( $descString ); ?></div>
						</td>
				    </tr>
				<?php endforeach; ?>
				</table>
			</fieldset>
		</td>
	</tr>
</table>