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
<div class="row-fluid">
	<div class="span2">
		<div id="sidebar">
			<div class="sidebar-nav">

				<ul class="nav nav-list" id="submenu">
					<li class="active">
						<a href="index.php?option=com_easyblog&amp;view=blogs"><?php echo JText::_( 'COM_EASYBLOG_BLOG_ENTRIES' );?></a>
					</li>
					<li>
						<a href="index.php?option=com_easyblog&amp;view=categories"><?php echo JText::_( 'COM_EASYBLOG_BLOG_CATEGORIES' );?></a>
					</li>
					<li>
						<a href="index.php?option=com_easyblog&amp;view=tags"><?php echo JText::_( 'COM_EASYBLOG_BLOG_TAGS' );?></a>
					</li>
				</ul>

				<hr />

				<h4 class="page-header"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER' ); ?>:</h4>

				<div class="filter-select hidden-phone">
					<?php echo $this->getFilterBlogger( $this->filteredBlogger ); ?>
					<hr class="hr-condensed" />

					<?php echo $this->category; ?>
					<hr class="hr-condensed" />

					<?php echo $this->state; ?>
					<hr class="hr-condensed" />

					<select name="filter_source" class="inputbox" onchange="this.form.submit()">
						<option value="-1"<?php echo $this->source == '-1' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTERS_POST_TYPE');?></option>
						<option value=""<?php echo $this->source == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_NORMAL_POST' ); ?></option>
						<option value="link"<?php echo $this->source == 'link' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_LINK' ); ?></option>
						<option value="quote"<?php echo $this->source == 'quote' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_QUOTE' );?></option>
						<option value="photo"<?php echo $this->source == 'photo' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_PHOTO' );?></option>
						<option value="video"<?php echo $this->source == 'video' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_VIDEO' );?></option>
						<option value="twitter"<?php echo $this->source == 'twitter' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_TWITTER' );?></option>
						<option value="email"<?php echo $this->source == 'email' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_EMAIL' );?></option>
					</select>
					<hr class="hr-condensed" />

					<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
					<select name="filter_language" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->filterLanguage );?>
					</select>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="span10">

		<div class="filter-bar">
			<div class="filter-search input-append pull-left">
				<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
				<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();"
				placeholder="<?php echo JText::_( 'COM_EASYBLOG_SEARCH' , true ); ?>" />
				<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
				<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
			</div>
			<div class="blogger-listing-dropdown-limit">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>

		<table class="table table-striped" cellspacing="1">
		<thead>
			<tr>
				<th width="1%" class="nowrap hidden-phone center">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>

				<th width="1%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_STATUS' ); ?>
				</th>

				<th width="1%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_FEATURED' ); ?>
				</th>

				<th width="1%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_FRONTPAGE' ); ?>
				</th>

				<th>
					<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TITLE' ), 'a.title', $this->orderDirection, $this->order ); ?>
				</th>

				<?php if( !$this->browse ){ ?>
				<th width="10%" class="nowrap hidden-phone center"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_CONTRIBUTED_IN' ); ?></th>


				<th width="10%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTOPOSTING' ); ?>
				</th>
				<th width="1%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_NOTIFY' ); ?>
				</th>
				<?php } ?>

				<th width="10%" class="nowrap hidden-phone center">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTHOR' ); ?>
				</th>

				<?php if( !$this->browse ){ ?>
					<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
					<th width="5%" class="center nowrap hidden-phone"><?php echo JText::_( 'COM_EASYBLOG_LANGUAGE' );?></th>
					<?php } ?>
				<th width="10%" class="nowrap center hidden-phone"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_DATE', 'a.created', $this->orderDirection, $this->order ); ?></th>
				<th width="20" nowrap="nowrap center"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php if( $this->blogs ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $this->blogs as $row ){ ?>
				<tr>
					<td class="center hidden-iphone">
						<?php echo JHTML::_('grid.id', $i , $row->id); ?>
					</td>

					<td class="nowrap hidden-phone center">

						<?php if($row->published == 2) : ?>
						<img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/schedule.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_SCHEDULED');?>" />
						<?php elseif($row->published == 3) : ?>
						<img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/draft.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_DRAFT');?>" />
						<?php elseif($row->published == POST_ID_TRASHED ) : ?>
						<img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/trash.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_TRASHED');?>" />
						<?php else: ?>
						<?php echo JHTML::_( 'jgrid.published' , $row->published , $i ); ?>
						<?php endif; ?>
					</td>

					<td class="nowrap hidden-phone center">
						<a class="btn btn-micro jgrid" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( EasyBlogHelper::isFeatured( EBLOG_FEATURED_BLOG , $row->id ) ) ? 'unfeature' : 'feature';?>')">
						<?php if( EasyBlogHelper::isFeatured( EBLOG_FEATURED_BLOG , $row->id ) ){ ?>
							<i class="icon-star"></i>
						<?php } else { ?>
							<i class="icon-star-empty"></i>
						<?php } ?>
						</a>
					</td>

					<td class="nowrap hidden-phone center">
						<a onclick="return listItemTask('cb<?php echo $i;?>','toggleFrontpage')" href="javascript:void(0);" class="btn btn-micro active">
							<i class="icon-<?php echo !$row->frontpage ? 'unpublish' : 'publish';?>"></i>
						</a>
					</td>

					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if( $this->browse ){ ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $this->browseFunction; ?>('<?php echo $row->id;?>','<?php echo addslashes( $this->escape($row->title) );?>');"><?php echo $row->title;?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easyblog&c=blogs&task=edit&blogid=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
							<?php } ?>
							<div class="small">
								<?php echo JText::_( 'COM_EASYBLOG_CATEGORY' );?>:
								<?php if( $this->browse ){ ?>
									<?php echo $this->getCategoryName( $row->category_id);?>
								<?php } else { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=category&task=edit&catid=' . $row->category_id);?>"><?php echo $this->getCategoryName( $row->category_id);?></a>
								<?php } ?>
							</div>
						</div>

						<div class="pull-left">
							<div style="margin-left: 6px;display:none;" class="btn-group">
								<a class="dropdown-toggle btn btn-mini" data-toggle="dropdown" href="#">
									<span class="caret"></span>
								</a>

								<ul class="dropdown-menu">
									<li>
										<a href="index.php?option=com_easyblog&c=blogs&task=edit&blogid=<?php echo $row->id;?>"><?php echo JText::_( 'COM_EASYBLOG_EDIT' );?></a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="<?php echo EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $row->id, true, true);?>" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' );?></a>
									</li>
								</ul>
							</div>
						</div>
					</td>

					<?php if( !$this->browse ){ ?>
					<td class="nowrap hidden-phone center">
						<?php
							$extGroupName = '';
							if( !empty($row->external_group_id) && EasyBlogHelper::getHelper('Groups')->useGroups())
							{
							    $blog_contribute_source = EasyBlogHelper::getHelper( 'Groups' )->getGroupSourceType();
							    $extGroupName			= EasyBlogHelper::getHelper( 'Groups' )->getGroupContribution( $row->id, $blog_contribute_source, 'name' );
							    $extGroupName           = $extGroupName . ' (' . ucfirst($blog_contribute_source) . ')';
							}

							if( !empty($row->external_event_id) )
							{
							    $blog_contribute_source = EasyBlogHelper::getHelper( 'Event' )->getSourceType();
							    $extEventName			= EasyBlogHelper::getHelper( 'Event' )->getContribution( $row->id, $blog_contribute_source, 'name' );
							    $extEventName           = $extEventName . ' (' . ucfirst($blog_contribute_source) . ')';
							}

							$contributionDisplay    = '';
							if( $row->issitewide )
							{
							    $contributionDisplay    = JText::_('COM_EASYBLOG_BLOGS_WIDE');
							}
							else
							{
								if( !empty( $extGroupName ) )
								{
									$contributionDisplay    = $extGroupName;
								}
								else if( !empty( $extEventName ) )
								{
									$contributionDisplay    = $extEventName;
								}
								else
								{
									$contributionDisplay    = $row->teamname;
								}
							}
						?>
						<?php echo $contributionDisplay;  ?>
					</td>

					<td class="center hidden-phone small">
						<?php if( $row->published && $this->centralizedConfigured ){ ?>
							<?php foreach( $this->consumers as $consumer ){
							$shared 	= $consumer->isShared( $row->id ) ? '' : '_disabled';
							$title		= empty( $shared ) ? JText::sprintf( 'COM_EASYBLOG_AUTOPOST_SHARED' , $consumer->type ) : JText::sprintf( 'COM_EASYBLOG_AUTOPOST_NOT_SHARED_YET' , $consumer->type );
							?>
							<a href="javascript:void(0);" onclick="autopost('<?php echo $consumer->type;?>','<?php echo $row->id;?>');"><img id="oauth-<?php echo $consumer->type;?>" src="<?php echo JURI::root();?>/components/com_easyblog/assets/icons/socialshare/<?php echo $consumer->type;?><?php echo $shared;?>.png" title="<?php echo $this->escape( $title );?>" /></a>
							<?php } ?>
						<?php } else { ?>
							<div class="tip hasTooltip" data-original-title="<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_NOT_AVAILABLE_BECAUSE_UNPUBLISHED' , true ); ?>">
								<?php echo JText::_( 'COM_EASYBLOG_NOT_AVAILABLE' ); ?>
							</div>
						<?php } ?>
					</td>
					<td class="center hidden-phone small">
						<a class="btn btn-micro jgrid" onclick="return listItemTask('cb<?php echo $i;?>','toggleNotify')">
							<i class="icon-mail-2"></i>
						</a>
					</td>
					<?php } ?>

					<td class="center small">
						<span class="editlinktip">
							<?php if( !$this->browse ){ ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>">
							<?php } ?>
							<?php echo JFactory::getUser( $row->created_by )->name; ?>
							<?php if( !$this->browse ){ ?>
								</a>
							<?php } ?>
						</span>
					</td>

					<?php if( !$this->browse ){ ?>

					<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
						<td class="center hidden-phone">
							<?php if ($row->language=='*' || empty( $row->language) ){ ?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php } else { ?>
								<?php echo $this->escape( $this->getLanguageTitle( $row->language) ); ?>
							<?php } ?>
						</td>
					<?php } ?>

					<td class="center">
						<?php echo EasyBlogDateHelper::toFormat( EasyBlogDateHelper::getDate( $row->created ) , 'Y-m-d H:i:s'); ?>
					</td>

					<td class="center"><?php echo $row->id; ?></td>
					<?php } ?>
				</tr>
					<?php $i++; ?>
				<?php } ?>
			<?php } else { ?>
			<tr>
				<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
				<td colspan="15" align="center">
				<?php } else { ?>
				<td colspan="14" align="center">
				<?php } ?>
					<?php echo JText::_('COM_EASYBLOG_BLOGS_NO_ENTRIES');?>
				</td>
			</tr>
			<?php } ?>
		</tbody>

		<tfoot>
			<tr>
				<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
				<td colspan="15" align="center">
				<?php } else { ?>
				<td colspan="14" align="center">
				<?php } ?>
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>

	</div>
</div>
