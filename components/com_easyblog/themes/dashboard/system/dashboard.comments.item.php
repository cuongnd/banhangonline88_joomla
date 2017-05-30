<?php
/**
 * @package     EasyBlog
 * @copyright   Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form name="comment-form" id="comment-form">
<ul class="item_list reset-ul">

<?php
if( $comments )
{
    $i  = 0;
?>

    <?php foreach( $comments as $comment ) { ?>
    <li id="eblog-comment-item<?php echo $comment->id; ?>">
        <?php if( $comment->created_by != 0 ) { ?>
        <?php $commenter    = EasyBlogHelper::getTable('Profile'); ?>
        <?php $commenter->load($comment->created_by); ?>
        <a href="<?php echo $commenter->getProfileLink(); ?>" class="ui-avatar float-l">
        <?php } ?>
        <span class="ui-avatar">
            <img class="avatar float-l" src="<?php echo $comment->author->getAvatar(); ?>" alt="<?php echo $comment->author->getName(); ?>" />
        </span>
        <?php if( $comment->created_by != 0 ) { ?></a><?php } ?>
        <?php echo EasyBlogTooltipHelper::getBloggerHTML( $comment->created_by, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'prev')) ); ?>

        <div class="ui-content">
            <div class="ui-comment-meta mbs">
                <?php
                    $blogLink       = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$comment->post_id);
                    $blogTitle      = $comment->blog_title;
                    $profileLink    = '#';
                    if( $comment->created_by != 0 )
                    {
                        $commentor      = EasyBlogHelper::getTable( 'Profile', 'Table');
                        $commentor->load( $comment->created_by );
                        $profileLink    = $commentor->getProfileLink();

                        $submitterName  = $commentor->getName();
                    }
                    else
                    {
                        $submitterName  = $comment->name;    
                    }
                    $submitterLink  = $profileLink;
                    
                    $dateSubmitted  = EasyBlogDateHelper::toFormat( EasyBlogDateHelper::dateWithOffSet($comment->created) , $system->config->get('layout_dateformat', '%A, %d %B %Y') );
                    echo JText::sprintf('COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENTED_ON_BLOG', '<b>' . $submitterName . '</b>', $blogLink, $blogTitle);
                ?>
            </div>
            <div class="ui-item-content" id="comment-content-<?php echo $comment->id; ?>">
                <?php if( !empty( $comment->title ) ) { ?>
                <b class="ui-comment-title">
                <?php
                    $commentLink    = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $comment->post_id . '#comment-' . $comment->id);
                    $commentTitle   = !empty( $comment->title ) ? $comment->title : JText::_('COM_EASYBLOG_COMMENT_NO_TITLE');
                    echo JText::sprintf('COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_TITLE', $commentLink, $commentTitle);
                ?>
                </b>
                <?php } ?>
                <div>
                <?php echo $comment->comment; ?>
                </div>
                <div class="ui-inmsg small"></div>
            </div>

            <?php if ( $comment->published == 2 ) : ?>
            <div class="ispending" id="moderate-publishing-<?php echo $comment->id;?>">
                <?php echo JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_UNDER_MODERATION'); ?>
                <span class="ico-publish">
                    &middot;
                    <a href="javascript:eblog.dashboard.comments.publishModerated( '<?php echo $comment->id;?>', 'publish' );" class="icon-published"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' );?></a>
                    &middot;
                    <a href="javascript:eblog.dashboard.comments.publishModerated( '<?php echo $comment->id;?>', 'unpublish' );" class="icon-unpublished"><?php echo JText::_( 'COM_EASYBLOG_UNPUBLISHED' );?></a>
                </span>
            </div>
            <?php endif; ?>
            
            <ul id="eblog-comment-toolbar<?php echo $comment->id; ?>" class="ui-button-option clearfix reset-ul float-li mtm">
                <?php if(!empty($this->acl->rules->manage_comment)) { ?>
                <li class="ico-publish" id="publishing-<?php echo $comment->id;?>">
                    <?php if($comment->published == 1) : ?>
                        <a href="javascript:eblog.dashboard.comments.publish( '<?php echo $comment->id;?>' );" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_PUBLISH' );?></a>
                    <?php elseif($comment->published != 2) : ?>
                        <a href="javascript:eblog.dashboard.comments.publish( '<?php echo $comment->id;?>' );" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_UNPUBLISH' );?></a>
                    <?php endif; ?>
                </li>
                <?php } ?>

                <?php if( !empty($this->acl->rules->edit_comment) ) : ?>
                <li><a href="javascript:eblog.dashboard.comments.edit( '<?php echo $comment->id;?>' );" class="buttons sibling-m"><?php echo JText::_('COM_EASYBLOG_EDIT'); ?></a></li>
                <?php endif; ?>

                <?php if( !empty($this->acl->rules->delete_comment) ) : ?>
                <li><a href="javascript:eblog.dashboard.comments.remove('index.php?option=com_easyblog&view=dashboard&layout=comments','<?php echo $comment->id;?>');" class="buttons sibling-m border-l"><?php echo JText::_('COM_EASYBLOG_DELETE'); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $blogLink.'#comment-'.$comment->id; ?>" class="buttons sibling-r"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_PERMALINK');?></a></li>

                <li class="ui-clock float-r">
                    <?php echo $dateSubmitted;?>
                </li>
            </ul>

        </div>
        <?php if( $showCheckbox ){ ?>
        <span class="ui-list-select pabs"><input type="checkbox" class="stackSelect" value="<?php echo $comment->id;?>" name="cid[]" id="cb<?php echo $i;?>" /></span>
        <?php } ?>
    </li>
    <?php
        $i++;
    }
    ?>

<?php
}
else
{
?>
    <li class="no_item">
    <div>
        <?php echo JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_NO_RECENT_COMMENTS'); ?>
    </div>
    </li>
<?php
    }
?>
</ul>
</form>
<?php if( $comments && isset( $pagination ) ) : ?>
    <?php if ( $pagination->getPagesLinks() ) : ?>
    <div class="eblog-pagination"><?php echo $pagination->getPagesLinks();?></div>
    <?php endif; ?>
<?php endif; ?>
