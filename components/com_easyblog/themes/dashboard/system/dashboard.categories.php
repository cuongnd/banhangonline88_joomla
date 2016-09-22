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
<div id="dashboard-categories" class="stackSelectGroup">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
		<?php if( $this->acl->rules->create_category ){ ?>
		<a class="buttons" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=category'); ?>"><?php echo JText::_( 'COM_EASYBLOG_ADD_NEW_CATEGORY_BUTTON' );?></a>
		<?php } ?>
	</div>
	<form name="easyblog-categories">
	<div class="ui-optbox clearfix fsm">
	<ul class="ui-entries-filter reset-ul float-li float-r">
		<li class="<?php echo $order == 'count' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories&order=count' );?>"><?php echo JText::_('COM_EASYBLOG_SORT_BY_TOTAL_POSTS'); ?></a></li>
		<li class="<?php echo $order == 'latest' ? 'active' : ''; ?>"><a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories&order=latest' );?>"><?php echo JText::_('COM_EASYBLOG_SORT_BY_DATE_CREATED'); ?></a></li>
	</ul>
	</div>
<?php
if( $categories )
{
?>
<ul class="item_list no-cbox reset-ul">
	<?php
		foreach( $categories as $category )
		{
			$created	= EasyBlogDateHelper::dateWithOffSet($category->created);
	?>
		<li id="td-<?php echo $category->id; ?>" class="prel">
			<?php if($system->config->get('layout_categoryavatar' ) ){ ?>
            <div class="ui-avatar float-l">
                <a class="avatar float-l" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id);?>">
					<img src="<?php echo $category->getAvatar();?>" class="avatar" />
				</a>
            </div>
            <?php } ?>
            <div class="ui-content">
    			<div class="item_title">
    				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id);?>" class="fsl ffa fwb">
    					<?php echo $category->title;?>
    				</a>
    			</div>
                <div class="item_content mbs">
					<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATED_ON' , $this->formatDate( JText::_( 'DATE_FORMAT_LC1' ) , $category->created ) );?>
				</div>
				<div class="mbs">
					<span><?php echo $this->getNouns( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_POST_COUNT' , $category->getPostCount() , true ); ?></span> 
					&middot;
					<span><?php echo JText::sprintf( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_CHILD_COUNT' , $category->getChildCount() ); ?></span>
				</div>
                <ul class="ui-button-option clearfix reset-ul float-li mtm">
                    <li>
        				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=category&id=' . $category->id); ?>" class="buttons<?php if($category->getPostCount() <= 0 && $category->getChildCount() <= 0 && $this->acl->rules->delete_category ) { echo ' sibling-l'; } ?>">
        					<?php echo JText::_('COM_EASYBLOG_EDIT'); ?>
        				</a>
                    </li>
    				<?php if($category->getPostCount() <= 0 && $category->getChildCount() <= 0 && $this->acl->rules->delete_category ) { ?>
                    <li>
        				<a href="javascript:eblog.dashboard.categories.remove('index.php?option=com_easyblog&view=dashboard&layout=categories','<?php echo $category->id;?>');" class="buttons sibling-r" style="border-left:0">
        					<?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
        				</a>
                    </li>
    				<?php } ?>
                </ul>
            </div>
		</li>
	<?php
		}
	?>
</ul>
<?php
}
else
{
?>
<div class="eblog-message info">
	<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NO_CATEGORY_AVAILABLE'); ?>
</div>
<?php 
}
?>
</form>
<?php if ( $categories ) : ?>
	<?php if ( $pagination->getPagesLinks() ) { ?>
	<div class="eblog-pagination"><?php echo $pagination->getPagesLinks(); ?></div>
	<?php } ?>
<?php endif; ?>
</div>