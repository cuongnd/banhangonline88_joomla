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
$actionUrl  = '';

if($category->id)
{
    $actionUrl  = EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=saveCategory');
}
else
{
	$actionUrl  = EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=addCategory');
}
?>
<script type="text/javascript">
EasyBlog.ready(function($) {

	$('#private').bind('change', function() {

		if ($(this).val()=='2') {

			$('.assigned-acl').show();

		} else {

			$('.assigned-acl').hide();
		}
	});
});
</script>
<div id="dashboard-categories" class="stackSelectGroup mtl">
	<?php if( $this->acl->rules->create_category ){ ?>
	<div class="ui-modbox" id="widget-create-category">
	    <div class="ui-modhead">
	    	<div class="ui-modtitle">
	    	    <?php if($category->id): ?>
                    <?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_EDITING', $this->escape($category->title) ); ?>
				<?php else : ?>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE_NEW'); ?>
				<?php endif; ?>
			</div>
	    </div>
	    <div class="ui-modbody clearfix">
	        <div id="add_category">
	        	<form id="frmNewCat" name="frmNewCat" enctype="multipart/form-data" action="<?php echo $actionUrl; ?>" method="post">
	        	<ul class="list-form reset-ul">
	        		<li>
	                	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME'); ?> :</label>
	        			<div>
	                        <input type="text" id="title" name="title" class="input text width-350" value="<?php echo $this->escape( $category->title );?>"/>
	        			</div>
	        		</li>
	        		<li>
	                	<label><?php echo JText::_('COM_EASYBLOG_CATEGORY_ALIAS'); ?> :</label>
	        			<div>
	                        <input name="alias" id="alias" class="input text width-350" maxlength="255" value="<?php echo $this->escape( $category->alias );?>" />
	        			</div>
	        		</li>

	        		<li>
	                	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_DESCRIPTION'); ?> :</label>
        				<div>
        					<?php echo $editor->display('description', $category->get('description') , '90%', '200', '10', '10', false, array(), 'com_easyblog'); ?>
        				</div>
	        		</li>
	        		<li>
	                	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PARENT'); ?> :</label>
	        			<div>
	        				<?php echo $parentList; ?>
	        			</div>
	        		</li>
	        		<li>
	        			<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PRIVACY'); ?> :</label>
	        			<div>
	        				<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions( 'category' ) , 'private' , 'size="1" class="input text"' , 'value' , 'text', $category->private );?>
	        			</div>
	        		</li>
                    <?php
                        foreach($categoryRules as $catRules) :
                            $catRuleSet 	= $assignedACL[$catRules->id];
                            $titleString	= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_TITLE';
                            $descString		= 'COM_EASYBLOG_CATEGORIES_ACL_'.$catRules->action.'_DESC';
					?>
                    <li class="assigned-acl clearfix"<?php echo $category->private == '2' ? ' style="display:block;"' : '';?>>
                        <label>
                            <?php echo JText::_( $titleString ); ?> :
                        </label>
                        <div>
                            <select multiple="multiple" name="category_acl_<?php echo $catRules->action; ?>[]" class="float-l">
							<?php foreach($catRuleSet as $ruleItem) : ?>
							    <option value="<?php echo $ruleItem->groupid; ?>" <?php echo ($ruleItem->status) ? 'selected="selected"' : ''; ?> ><?php echo $ruleItem->groupname; ?></option>
							<?php endforeach; ?>
							</select>
                            <label class="mlm"><?php echo JText::_( $descString ); ?></label>
                        </div>
                    </li>
                    <?php endforeach; ?>

					<?php if($system->config->get('layout_categoryavatar', true)) : ?>
	        		<li>
	                	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_AVATAR'); ?> :</label>
	        			<div>
						    <?php if(! empty($category->avatar)) { ?>
							<img style="border-style:solid;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/><br />
							<?php } ?>

							<?php if($this->acl->rules->upload_cavatar){ ?>
	        				<input id="file-upload" type="file" name="Filedata" size="33"/>
	        				<?php } ?>
	        			</div>
	        		</li>
	        		<?php endif; ?>
                </ul>

				<?php echo JHTML::_( 'form.token' ); ?>
				<input type="hidden" name="id" id="id" value="<?php echo $category->id; ?>"/>
	            <div class="ui-modfoot clearfix">
	                <span class="float-l"><a class="buttons" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=categories'); ?>"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?></a></span>
					<span class="float-r"><input type="submit" value="<?php echo JText::_('COM_EASYBLOG_SAVE_BUTTON'); ?>" class="buttons"></span>
					<span class="float-r mrs"><input type="reset" value="<?php echo JText::_('COM_EASYBLOG_RESET_BUTTON'); ?>" class="buttons" name="reset-form"></span>
	            </div>
	        	</form>
	        </div>
	    </div>
	</div>
	<?php } ?>
</div>
