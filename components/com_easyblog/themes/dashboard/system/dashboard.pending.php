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
<script type="text/javascript">
EasyBlog.require().script('legacy').done(function($){
	eblog.dashboard.lists.init( 'pending' );
});
</script>

<div id="dashboard-pending" class="prel stackSelectGroup">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
	</div>

	<?php if( $isReview ) : ?>
	<div class="eblog-message warning">
		<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_REVIEW_NOTICE'); ?></span>
	</div>
	<?php endif; ?>

	<?php if( ( !empty($this->acl->rules->delete_entry) && !empty($this->acl->rules->manage_pending) ) && !$isReview  ) { ?>
	<div class="ui-optbox clearfix fsm">
		<div class="entries-select float-l">
			<ul class="ui-list-select-actions reset-ul float-li clearfix">
				<li>
					<input type="checkbox" class="stackSelectAll float-l" name="toggle" id="toggle"/>
					<label for="toggle" class="float-l mls mts"><?php echo JText::_( 'COM_EASYBLOG_SELECT_ALL' );?></label>
	            </li>
				<li id="select-actions">
					<select name="pending-action" id="pending-action">
						<option value="default"><?php echo JText::_('COM_EASYBLOG_WITH_SELECTED');?></option>
						<option value="rejectBlog"><?php echo JText::_('COM_EASYBLOG_REJECT');?></option>
					</select>
					<input type="button" class="ui-button" value="<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>" onclick="eblog.dashboard.action( 'pending', 'index.php?option=com_easyblog&view=dashboard&layout=pending' );"/>
				</li>
			</ul>
		</div>
	</div>
	<?php } ?>


	<form name="pending-form" id="pending-form">
		<ul class="item_list reset-ul">
		<?php if ( $entries ){ ?>
			<?php $i = 0; ?>
			<?php foreach( $entries as $entry ):
			$team   	= '';
			$teamBlog   = null;

			if( isset($entry->team_id) )
			{
			    if( !empty( $entry->team_id ) )
				{
					$team		= '&team=' . $entry->team_id;
					$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
					$teamBlog->load( $entry->team_id );
				}
 			}

			?>
			<li id="eb-entry-<?php echo $entry->id; ?>">
				<div class="listing">

				<?php if( $isReview ) { ?>
					<span class="ui-list-select pabs">&nbsp;</span>
				<?php } else { ?>
					<span class="ui-list-select pabs"><input type="checkbox" class="stackSelect" value="<?php echo $entry->id; ?>" name="cid[]" id="cb<?php echo $i;?>" /></span>
				<?php } ?>


				<div class="ui-avatar flaot-l">
    				<?php if( !empty($team) ) : ?>
                        <!-- teamblog avatar -->
    			        <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id); ?>" title="<?php echo $teamBlog->title; ?>"  class="isTeamBlog pabs">
    						<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" class="avatar" width="40" height="40" />
    					</a>
                    <?php endif; ?>
    					<!-- blogger avatar -->
    			        <a href="<?php echo $entry->author->getProfileLink(); ?>" title="<?php echo $entry->author->getName(); ?>"  class="isBlogger pabs">
    						<img src="<?php echo $entry->author->getAvatar(); ?>" alt="<?php echo $entry->author->getName(); ?>" width="40" height="40" />
    					</a>
    					<?php echo EasyBlogTooltipHelper::getBloggerHTML( $entry->author->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'prev')) ); ?>
				</div><!--end: .ui-avatar-->


				<div class="ui-content">
					<?php if( !empty( $entry->source ) ){ ?>
					<b class="item-type item-type-<?php echo strtolower( $entry->source );?> float-r" title="<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?>"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?></b>
					<?php } ?>

				<?php if( $isReview ) { ?>
					<div class="item_title mbs"><a href="javascript:void(0);" class="fsl fwb ffa"><?php echo $entry->title;?></a></div>

				<?php } else { ?>
					<div class="item_title mbs"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&draft_id='.$entry->id.'&approval=1');?>" class="fsl fwb ffa"><?php echo $entry->title;?></a></div>

				<?php } ?>

					<div class="ui-item-content">
						<ul class="ui-entry-meta clearfix reset-ul float-li">
						<li class="ico-tags"><?php echo $this->getNouns( 'COM_EASYBLOG_TAGS_COUNT' , count( $entry->_tags ) , true ); ?></li>
						<li class="ico-category"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $entry->category_id); ?>" class="fsm"><?php echo $entry->category; ?></a></li>
	                	</ul>
	                	<span class="ui-inmsg mlm"></span>
					</div><!--/ui-item-content-->

				<?php if( !$isReview ) { ?>
				<ul id="eblog-comment-toolbar<?php echo $entry->id; ?>" class="ui-button-option clearfix reset-ul float-li mtm">

					<?php if( $this->acl->rules->manage_pending && $this->acl->rules->add_entry ) : ?>
					<li class="ico-publish"><a class="buttons sibling-l" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&draft_id='.$entry->id.'&approval=1');?>"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_REVIEW_POST'); ?></a> </li>
					<?php endif; ?>
					<?php if( $this->acl->rules->manage_pending && $this->acl->rules->add_entry) : ?>
					<li><a  class="buttons sibling-m" href="javascript:eblog.blog.approve('index.php?option=com_easyblog&view=dashboard&layout=pending','<?php echo $entry->id;?>');"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_APPROVE_POST'); ?></a></li>
					<?php endif; ?>
					<?php if( $this->acl->rules->manage_pending ) : ?>
					<li><a  class="buttons sibling-r" href="javascript: eblog.editor.reject( '<?php echo $entry->id; ?>' );"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_REJECT_POST'); ?></a></li>
					<li class="ui-clock float-r"><?php echo $this->formatDate( JText::_( 'DATE_FORMAT_LC1' ) ,  $entry->created); ?></li>
					<?php endif; ?>

				</ul>
				<?php } ?>
				</div>
			</div>
			</li>
			<?php $i++; ?>
			<?php endforeach; ?>
		<?php } else { ?>
			<li class="no_item">
				<div><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_EMPTY'); ?></div>
			</li>
		<?php } ?>
		</ul>
		<?php if ( !empty($pagination) ) : ?>
			<div class="eblog-pagination"><?php echo $pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
	</form>
</div>
