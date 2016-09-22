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
	eblog.dashboard.lists.init( 'entries' );
});
</script>

<div id="dashboard-entries" class="prel stackSelectGroup">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
		<?php if( count($entries) > 0) : ?>
		<a class="buttons" href="javascript:void(0);" onclick="eblog.dashboard.drafts.discardAll();"><?php echo JText::_( 'COM_EASYBLOG_DISCARD_DRAFTS_BUTTON' );?></a>
		<?php endif; ?>
	</div>
	<div class="ui-optbox clearfix fsm">
		<div class="entries-select float-l">
			<ul class="ui-list-select-actions reset-ul float-li clearfix">
				<li>
					<input type="checkbox" class="stackSelectAll float-l" name="toggle" id="toggle"/>
					<label for="toggle" class="float-l mls mts"><?php echo JText::_( 'COM_EASYBLOG_SELECT_ALL' );?></label>
	            </li>
				<li id="select-actions">
					<select name="entries-action" id="entries-action">
						<option value="default"><?php echo JText::_('COM_EASYBLOG_WITH_SELECTED');?></option>
						<option value="discardDraft"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARD');?></option>
					</select>
					<input type="button" class="ui-button" value="<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>" onclick="eblog.dashboard.action( 'entries', 'index.php?option=com_easyblog&view=dashboard&layout=drafts' );"/>
				</li>
			</ul>
		</div>
	</div>
	<form name="entries-form" id="entries-form">

		<ul class="item_list reset-ul">
			<?php if ( $entries ) : ?>
			<?php $i = 0; ?>
			<?php foreach( $entries as $entry ): ?>
			<li id="eb-entry-<?php echo $entry->id; ?>">
				<?php if( !empty( $entry->source ) ){ ?>
					<b class="item-type item-type-<?php echo strtolower( $entry->source );?> float-r" title="<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?>"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_' . strtoupper( $entry->source ) ); ?></b>
				<?php } ?>

				<div class="listing">
					<div class="item_title mbs">
						<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=write&draft_id=' . $entry->id );?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>" class="fsl fwb ffa">
						<?php if( $entry->title == JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE' ) || empty( $entry->title) ){ ?>
							<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_NO_TITLE' ); ?>
						<?php } else { ?>
							<?php echo $entry->title; ?>
						<?php } ?>
						</a>
					</div>
					<?php
					$rejected	= $entry->getRejected();

					if( $rejected )
					{
					?>

					<div class="eblog-message error clearfix">
						<div>
							<?php echo JText::sprintf( 'COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED' , $rejected->author->getName() );?>
							<?php if( $rejected->message ){ ?>
							<a href="javascript:void(0);" onclick="eblog.dashboard.toggle(this);" class="float-r ui-toggle"><?php echo JText::_( 'COM_EASYBLOG_SHOW_MESSAGE' );?></a>
							<?php } ?>
						</div>
						<?php if( $rejected->message ){ ?>
						<div class="reject-message mtm" style="display: none;">
							<img class="float-l avatar mrs" src="<?php echo $rejected->author->getAvatar();?>" width="32" height="32" />
							<div class="reject-content">
								<?php echo $rejected->message; ?>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php
					}
					?>
					<div class="ui-item-content">
						<div>
						<?php if( strip_tags( $entry->content ) != '' ){ ?>
							<?php echo JString::substr( strip_tags( $entry->content ) , 0 , 300 ); ?>
						<?php } else { ?>
							<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_NO_CONTENT' );?>
						<?php } ?>
						</div>
						<span class="ui-inmsg mlm"></span>
						<ul class="ui-draft-meta clearfix reset-ul float-li mtm">
							<li class="ico-tags">
								<?php if (count($entry->tags)>0): ?>
									<a href="javascript: void(0);"><?php echo $this->getNouns( 'COM_EASYBLOG_TAGS_COUNT' , count( $entry->tags ) , true ); ?></a>
									<?php echo EasyBlogTooltipHelper::getTagsHTML( $entry->_tags, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'parent')) ); ?>
								<?php else: ?>
									<span><?php echo $this->getNouns( 'COM_EASYBLOG_TAGS_COUNT' , count( $entry->tags ) , true ); ?></span>
								<?php endif; ?>
							</li>
							<?php if($entry->category_id) { ?>
							<li class="ico-category"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $entry->category_id); ?>" class="fsm"><?php echo $entry->category; ?></a></li>
							<?php } ?>
		                </ul>
					</div>

					<span class="ui-list-select pabs"><input type="checkbox" class="stackSelect" value="<?php echo $entry->id; ?>" name="cid[]" id="cb<?php echo $i;?>" /></span>

					<ul id="eblog-comment-toolbar<?php echo $entry->id; ?>" class="ui-button-option clearfix reset-ul float-li mtm">
						<?php if( $this->acl->rules->add_entry ) : ?>
						<li><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&draft_id='.$entry->id);?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>" class="buttons sibling-l"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_CONTINUE_EDITING'); ?></a> </li>
						<?php endif; ?>
						<li><a href="javascript:eblog.dashboard.drafts.discard( '<?php echo $entry->id;?>' );" class="buttons sibling-r close"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARD'); ?></a></li>
						<li class="ui-clock float-r"><?php echo $this->formatDate( JText::_( 'DATE_FORMAT_LC1' ) ,  $entry->created); ?></li>
					</ul>
				</div>
			</li>
			<?php $i++; ?>
			<?php endforeach; ?>

		<?php else: ?>
            <li class="no_item">
                <div><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_EMPTY'); ?></div>
            </li>
		<?php endif; ?>
		</ul>
		<?php if ( !empty($pagination) ) : ?>
			<div class="pagination clearfix"><?php echo $pagination->getPagesLinks(); ?></div>
		<?php endif; ?>
	</form>
	<div class="clearfix"></div>
</div>
