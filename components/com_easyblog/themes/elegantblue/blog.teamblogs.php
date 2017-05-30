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
<div id="ezblog-body">
	<div id="ezblog-section">
		<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_PAGE_HEADING'); ?></span>
	</div>
	<div id="ezblog-profile" class="forTeamBlog">

    	<div id="ezblog-detail" class="team-blog_<?php echo $team->id; ?> profile-item clearfix prel">
            <div class="profile-head clearfix">
                <div class="blog-avatar float-l prel">
                    <div class="avatar-wrap-a">
                        <div class="author-avatar clearfix">
                            <div class="float-l prel mls">
                            <img src="<?php echo $team->avatar; ?>" alt="<?php echo $team->title; ?>" width="60" height="60" class="avatar" />
                            </div>
                        </div>
                    </div>
                    <div class="avatar-wrap-b"></div>
                </div>

                <div class="profile-basic">
                    <h3 class="profile-title rip">
                        <a href="<?php echo  EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$team->id);?>"><?php echo $team->title;?></a>
                        <?php if ($isFeatured) : ?><sup class="tag-featured"><?php echo JText::_( 'COM_EASYBLOG_FEATURED_TEAMBLOG_FEATURED' );?></sup><?php endif; ?>
                    </h3>
                    <div class="profile-bio mtm">
                        <?php if(! empty( $team->description )) : ?>
                        <?php   echo nl2br($team->description); ?>
                        <?php endif; ?>
                    </div>

                    <?php if( $this->getParam( 'show_teamblogavatar') ){ ?>
    					<?php if($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EasyBlogHelper::isSiteAdmin() ){ ?>
                        <ul class="team-members clearfix reset-ul float-li mtm mbm">
                    		<?php
                    		    if(! empty($teamMembers))
                    		    {
                            ?>
                            <?php
                    		        foreach($teamMembers as $member)
                    		        {
                    		 ?>
                            <li>
                				<?php if ( $config->get('layout_avatar') ) { ?>
                                    <a href="<?php echo $member->getProfileLink(); ?>" title="<?php echo $member->displayName; ?>" class="avatar float-l mrs">
                						<img class="avatar" src="<?php echo $member->getAvatar(); ?>" alt="<?php echo $member->displayName; ?>" width="32" height="32" />
                                    </a>
                                    <?php echo EasyBlogTooltipHelper::getBloggerHTML( $member->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'parent')) ); ?>
                				<?php } else { ?>
                                <?php
                                    $blogger    = EasyBlogHelper::getTable( 'Profile', 'Table');
                                    $blogger->load( $member->id );
                                ?>
                                    <a href="<?php echo $blogger->getProfileLink(); ?>"><?php echo $member->displayName;?></a>
                                <?php } ?>
                    		</li>
                    		 <?php
                    		        }
                    		    }
                    		?>
                    	</ul>
                        <?php } ?>
                    <?php } ?>

                    <div class="profile-connect">
                        <ul class="connect-links reset-ul float-li clearfix">
                		<?php if($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EasyBlogHelper::isSiteAdmin() && ($system->config->get( 'main_teamsubscription' )) ){ ?>
                            <li>
                    	        <a class="link-subscribe" href="javascript:eblog.subscription.show('<?php echo EBLOG_SUBSCRIPTION_TEAMBLOG; ?>','<?php echo $team->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM'); ?>">
                                    <span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM'); ?></span>
                                </a>
                            </li>
                		<?php } ?>

                		<?php if( ($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EasyBlogHelper::isSiteAdmin() ) && ($system->config->get( 'main_rss' )) ){ ?>
                            <li>
                    			<a class="link-rss" href="<?php echo  EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=teamblog&id=' . $team->id );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
                                    <span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
                                </a>
                            </li>
                		<?php } ?>
						<?php if( !$team->isMember && $system->my->id > 0 ) { ?>
                            <li>
    							<a class="link-jointeam" href="javascript:eblog.teamblog.join('<?php echo $team->id;?>');">
                                    <span><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOG_JOIN_TEAM' );?></span>
                                </a>
                            </li>
						<?php } ?>

						<?php if( $siteadmin ) : ?>
                            <li>
                            <?php if ($isFeatured) { ?>
                                <a class="feature-del" href="javascript:eblog.featured.remove('teamblog','<?php echo $team->id;?>');" title="<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?>">
                                    <span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?></span>
                                </a>
                            <?php } else { ?>
    							<a class="feature-add" href="javascript:eblog.featured.add('teamblog','<?php echo $team->id;?>');" title="<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?>">
                                    <span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?></span>
                                </a>
                            <?php } ?>
                            </li>
                        <?php endif; ?>
                        </ul>
                    </div>
                </div><!--end: .profile-info-->
            </div><!--end: .profile-head-->
    	</div>
    	<!-- Teamblog Block -->
        </div>


		<?php if(isset($statType)) : ?>
		<div>
		    <h2>
		    <?php echo ($statType == 'tag') ? JText::sprintf('COM_EASYBLOG_TEAMBLOG_STAT_TAG', $team->title, $statObject->title) : JText::sprintf('COM_EASYBLOG_TEAMBLOG_STAT_CATEGORY', $team->title, $statObject->title); ?>
		    </h2>
		</div>
		<?php endif ?>

    <?php if($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && empty($team->isMember) && !EasyBlogHelper::isSiteAdmin() ){ ?>
        <div class="eblog-message warning mtm">
            <?php echo JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY'); ?>
            <?php echo ($system->my->id != 0) ? JText::sprintf('COM_EASYBLOG_TEAMBLOG_CLICK_TO_JOIN', 'eblog.teamblog.join('.$team->id.')') : '' ; ?>
        </div>
    <?php } else if( $data ) { ?>
        <div id="ezblog-posts" class="forTeamBlog">
            <?php foreach ( $data as $row ) { ?>
                <?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
            <?php } ?>
        </div>

        <?php if(count( $data ) <= 0) { ?>
        <div><?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY'); ?></div>
        <?php } ?>

        <?php if( $pagination ){ ?>
        <div class="eblog-pagination clearfix">
            <?php echo $pagination; ?>
        </div>
        <?php } ?>
    <?php } ?>
</div>
