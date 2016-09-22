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

<div class="dashboard-tags">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
	</div>
	<?php if( $this->acl->rules->create_tag ){ ?>
    <div class="ui-modbox">
        <div class="ui-modhead">
        <div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW'); ?></div>
        </div>
        <div class="ui-modbody clearfix">
            <form id="frmNewTag" name="frmNewTag" action="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=addTag');?>" method="post">
            <ul class="list-form reset-ul">
                <li>
					<label for="tag" class="label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW'); ?> :</label>
                    <div>
    					<input type="text" id="tag" name="tag" class="input text width-250 float-l" />
                        <label class="float-l mls">
    					<input type="submit" name="create_new_button" class="buttons" value="<?php echo JText::_('COM_EASYBLOG_CREATE'); ?>" />
                        </label>
                    </div>
        		</li>
        	</ul>
            </form>
        </div>
    </div>
	<?php } ?>

	<form name="adminForm" id="adminForm" class="">

        <?php if( $tags ) { ?>
        <ul class="tag-list reset-ul float-li clearfix">
            <?php
				foreach( $tags as $tag )
				{
					$created	= EasyBlogDateHelper::dateWithOffSet($tag->created);
			?>

            <li id="td-<?php echo $tag->id; ?>">
                <a class="tag-delete" href="javascript:eblog.tag.remove('<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard');?>','<?php echo $tag->id;?>');">
                    <?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
                </a>
                <span class="tag-title" onclick="eblog.tag.edit('<?php echo $tag->id;?>');">
                    <?php echo $tag->title;?>
                </span>
                <b class="tag-posts">
                    <?php echo $tag->post_count; ?>
                </b>
            </li>

            <?php
				}
            ?>
        </ul>
        <?php } else { ?>
		<div class="eblog-message info"><strong><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE'); ?></strong></div>
        <?php } ?>
    </form>
</div>
