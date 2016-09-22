<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<form name="frmEditCategory" id="frmEditCategory" enctype="multipart/form-data" action="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=saveCategory');?>" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
	<tr>
		<td class="key" width="30%">
			<label for="title"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY' ); ?></label>
		</td>
		<td>
			<input name="title" id="title" size="55" maxlength="255" value="<?php echo $this->escape( $category->title );?>" />
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="alias"><?php echo JText::_( 'COM_EASYBLOG_CATEGORY_ALIAS' ); ?></label>
		</td>
		<td>
			<input name="alias" id="alias" size="55" maxlength="255" value="<?php echo $this->escape( $category->alias );?>" />
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="parent_id" class="label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PARENT'); ?></label>
		</td>
		<td>
			<?php echo $parentList; ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="private" class="label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PRIVACY'); ?></label>
		</td>
		<td>
			<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions( 'category' ) , 'private' , 'size="1" class="input text"' , 'value' , 'text', $category->private );?>
		</td>
	</tr>
	<?php if($system->config->get('layout_categoryavatar', true)) : ?>
	<tr>
		<td class="key">
			<label for="Filedata" class="label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_AVATAR'); ?></label>
		</td>
		<td>
		    <?php if(! empty($category->avatar)) { ?>
			<img style="border-style:solid;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/><br />
			<?php } ?>
			
	    	<?php if($this->acl->rules->upload_cavatar){ ?>
			<input id="file-upload" type="file" name="Filedata" class="input file" size="33"/>
			<?php } ?>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="key">
			<label for="published"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHING_STATUS' ); ?></label>
		</td>
		<td>
    		<select id="published" name="published" class="ui-select">
    			<option value="1"<?php echo $category->published ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_PUBLISHED');?></option>
    			<option value="0"<?php echo !$category->published ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED');?></option>
    		</select>
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="created"><?php echo JText::_('COM_EASYBLOG_CREATED'); ?></label>
		</td>
		<td>
			<?php
			    $createdDate    = EasyBlogDateHelper::getDate( $category->created );
			?>
			<input type="text" name="created" id="created" value="<?php echo $createdDate->toFormat( $system->config->get( 'layout_systemdateformat' ) ); ?>" class="calendar"/>
		</td>
	</tr>
</table>

<div class="ui-modbox">
    <div class="ui-modhead">
    	<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACCESS'); ?></div>
    </div>
    <div class="ui-modbody clearfix">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
			<?php foreach($categoryRules as $catRules) :
  					$catRuleSet 	= $assignedACL[$catRules->id];
				$titleString	= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_TITLE';
				$descString		= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_DESC';
				//var_dump($catRuleSet);exit;
			?>
			    <tr>
			        <td width="65%">
						<label for="Filedata" class="label">
							<?php echo JText::_( $titleString ); ?> <br />
							(<?php echo JText::_( $descString ); ?>)
						</label>

					</td>
			        <td>
			            <select multiple="multiple" name="category_acl_<?php echo $catRules->action; ?>[]">
						<?php foreach($catRuleSet as $ruleItem) : ?>
						    <option value="<?php echo $ruleItem->groupid; ?>" <?php echo ($ruleItem->status) ? 'selected="selected"' : ''; ?> ><?php echo $ruleItem->groupname; ?></option>
						<?php endforeach; ?>
						</select>

					</td>
			    </tr>
			<?php endforeach; ?>
      	</table>
	</div>
</div>
<div> * <?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACL_NOTES'); ?></div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="id" id="id" value="<?php echo $category->id; ?>"/>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
</div>
</form>