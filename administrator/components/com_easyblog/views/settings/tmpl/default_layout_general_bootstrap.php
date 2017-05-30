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
	<div class="span12">

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_COMMENT_IN_BLOG_LISTING' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_COMMENT_IN_BLOG_LISTING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_showcomment' , $this->config->get( 'layout_showcomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_NUMBER_OF_COMMENT_DISPLAY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_NUMBER_OF_COMMENT_DISPLAY_DESC' ); ?></div>
							<input type="text" name="layout_showcommentcount" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('layout_showcommentcount' , '3' );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_blogger_breadcrumb' , $this->config->get( 'layout_blogger_breadcrumb' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_zero_as_plural' , $this->config->get( 'layout_zero_as_plural' ) );?>
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_COPYRIGHT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_COPYRIGHT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_copyrights' , $this->config->get( 'layout_copyrights' ) );?>
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_SHOW_NAVIGATIONS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_SHOW_NAVIGATIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_navigation' , $this->config->get( 'layout_navigation' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_responsive' , $this->config->get( 'layout_responsive' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_ORDERING' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_DESC' ); ?></div>
								<?php
									$listLength = array();
									$listLength[] = JHTML::_( 'select.option' , 'modified' , JText::_( 'COM_EASYBLOG_LAST_MODIFIED' ) );
									$listLength[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST' ) );
									$listLength[] = JHTML::_('select.option', 'alphabet', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET' ) );
									$listLength[] = JHTML::_('select.option', 'popular', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_HITS' ) );
									$listLength[] = JHTML::_('select.option', 'published', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED' ) );
									echo JHTML::_('select.genericlist', $listLength, 'layout_postorder', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_postorder' , 'latest' ) );
								?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_DESC' ); ?></div>
								<?php
									$listLength = array();
									$listLength[] = JHTML::_('select.option', 'desc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_DESCENDING' ) );
									$listLength[] = JHTML::_('select.option', 'asc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_ASCENDING' ) );
									echo JHTML::_('select.genericlist', $listLength, 'layout_postsort', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_postsort' , 'desc' ) );
								?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_USER_LAYOUT' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_AUTHOR_INFO' ); ?>
						</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_AUTHOR_INFO_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_showauthorinfo' , $this->config->get( 'main_showauthorinfo' , true ) );?>
							</div>
						</td>
					</tr>

					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_AUTHOR_POSTS' ); ?>
						</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_AUTHOR_POSTS_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_showauthorposts' , $this->config->get( 'main_showauthorposts' , true ) );?>
							</div>
							<div>
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_AUTHOR_POSTSCOUNT' ); ?>
								</span>
								<div>
									<input type="text" id="main_showauthorpostscount" name="main_showauthorpostscount" value="<?php echo $this->config->get( 'main_showauthorpostscount' , '3' );?>" ?>
								</div>
							</div>
						</td>
					</tr>

					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT_DESC' ); ?></div>
								<?php
									$nameFormat = array();
									$nameFormat[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYBLOG_REAL_NAME_OPTION' ) );
									$nameFormat[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYBLOG_USERNAME_OPTION' ) );
									$nameFormat[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYBLOG_NICKNAME_OPTION' ) );
									$showdet = JHTML::_('select.genericlist', $nameFormat, 'layout_nameformat', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_nameformat' , 'name' ) );
									echo $showdet;
								?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_DATE_FORMAT' ); ?></legend>
			<table class="admintable" cellpadding="1">
			<tbody>
				<tr>
					<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SHORT_DATE_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SHORT_DATE_FORMAT_DESC' ); ?></div>
							<input type="text" name="layout_shortdateformat" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_shortdateformat' , '%b %d' );?>" />
							<a href="http://php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DATE_FORMAT'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_GENERAL_DATE_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_GENERAL_DATE_FORMAT_DESC' ); ?></div>
							<input type="text" name="layout_dateformat" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_dateformat' , '%b %d, %Y' );?>" />
							<a href="http://php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DATE_FORMAT'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TIME_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TIME_FORMAT_DESC' ); ?></div>
							<input type="text" name="layout_timeformat" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_timeformat' , '%I:%M:%S %p' );?>" />
							<a href="http://php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TIME_FORMAT'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_STREAM_TIME_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_STREAM_TIME_FORMAT_DESC' ); ?></div>
							<input type="text" name="layout_streamtimeformat" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_streamtimeformat' , '%I:%M %p' );?>" />
							<a href="http://php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TIME_FORMAT'); ?></a>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_FRONTPAGE_TRUNCATION' ); ?></legend>
			<table class="admintable" cellpadding="1">
			<tbody>
			<tr>
				<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_CONTENT_AS_INTROTEXT' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_CONTENT_AS_INTROTEXT_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'layout_blogasintrotext' , $this->config->get( 'layout_blogasintrotext' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE' ); ?>
					</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE_DESC' ); ?></div>
						<select name="main_truncate_type" class="inputbox" id="truncateType">
							<option value="chars"<?php echo $this->config->get( 'main_truncate_type' ) == 'chars' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_CHARACTERS' ); ?></option>
							<option value="words"<?php echo $this->config->get( 'main_truncate_type' ) == 'words' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_WORDS' ); ?></option>
							<option value="paragraph"<?php echo $this->config->get( 'main_truncate_type' ) == 'paragraph' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_PARAGRAPH' ); ?></option>
							<option value="break"<?php echo $this->config->get( 'main_truncate_type' ) == 'break' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_BREAK' );?></option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_ADD_ELLIPSES' ); ?>
					</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_ADD_ELLIPSES_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_truncate_ellipses' , $this->config->get( 'main_truncate_ellipses' ) ); ?>
					</div>
				</td>
			</tr>
			<tr id="maxchars" style="<?php echo ($this->config->get( 'main_truncate_type' ) == 'chars' || $this->config->get( 'main_truncate_type' ) == 'words') ? '' : 'display:none;';?>">
				<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT_DESC' ); ?></div>
						<input type="text" name="layout_maxlengthasintrotext" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('layout_maxlengthasintrotext' , '150' );?>" />
					</div>
				</td>
			</tr>
			<tr id="maxtag" style="<?php echo ($this->config->get( 'main_truncate_type' ) == 'break' || $this->config->get( 'main_truncate_type' ) == 'paragraph') ? '' : 'display:none;';?>">
				<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS_DESC' ); ?></div>
						<input type="text" name="main_truncate_maxtag" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_truncate_maxtag');?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SHOW_READMORE' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SHOW_READMORE_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'layout_respect_readmore' , $this->config->get( 'layout_respect_readmore' ) );?>
					</div>
				</td>
			</tr>

			<?php
				$mediaType = array('image', 'video', 'media');

				foreach( $mediaType as $media )
				{
			?>

			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper( $media ) . '_POSITIONS' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper( $media ) . '_POSITIONS_DESC' ); ?></div>
						<select name="main_truncate_<?php echo $media; ?>_position">
							<option value="top"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'top' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_TOP_OPTION' ); ?></option>
							<option value="bottom"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'bottom' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_BOTTOM_OPTION' );?></option>
							<option value="hidden"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'hidden' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_DO_NOT_SHOW_OPTION' );?></option>
						</select>
					</div>
				</td>
			</tr>

			<?php
				}//end foreach
			?>

			</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_PAGINATION_LIST_LIMIT' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_LATEST_POSTS' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_LATEST_POSTS_DESC' ); ?></div>
								<?php echo $this->getPaginationSettings( 'layout_listlength' , $this->config->get('layout_listlength' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_CATEGORIES_PAGE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_CATEGORIES_PAGE_DESC' ); ?></div>
								<?php echo $this->getPaginationSettings( 'layout_pagination_categories' , $this->config->get( 'layout_pagination_categories' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_BLOGGER_PAGE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_BLOGGER_PAGE_DESC' ); ?></div>
								<?php echo $this->getPaginationSettings( 'layout_pagination_bloggers' , $this->config->get( 'layout_pagination_bloggers' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_CATEGORIES_IN_CATEGORIES_LIST_PAGE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_CATEGORIES_IN_CATEGORIES_LIST_PAGE_DESC' ); ?></div>
								<?php echo $this->getPaginationSettings( 'layout_pagination_categories_per_page' , $this->config->get( 'layout_pagination_categories_per_page' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_ARCHIVE_LIST_PAGE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_ARCHIVE_LIST_PAGE_DESC' ); ?></div>
								<?php echo $this->getPaginationSettings( 'layout_pagination_archive' , $this->config->get( 'layout_pagination_archive' ) );?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_TITLE' );?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_featured' , $this->config->get( 'layout_featured' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_AUTO_ROTATE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_AUTO_ROTATE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_featured_autorotate' , $this->config->get( 'layout_featured_autorotate' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_SHOW_ALL_PAGES' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_SHOW_ALL_PAGES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_featured_allpages' , $this->config->get( 'layout_featured_allpages' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_INTROTEXT_LIMIT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_INTROTEXT_LIMIT_DESC' ); ?></div>
							<input type="text" name="layout_featured_intro_limit" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('layout_featured_intro_limit' );?>" />
							<?php echo JText::_( 'COM_EASYBLOG_WORDS' );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_AUTO_ROTATE_INTERVAL' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_LISTINGS_AUTO_ROTATE_INTERVAL_DESC' ); ?></div>
							<input type="text" name="layout_featured_autorotate_interval" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('layout_featured_autorotate_interval' );?>" />
							<?php echo JText::_( 'COM_EASYBLOG_SECONDS' );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_SHOW_IN_FRONTPAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_SHOW_IN_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_featured_frontpage' , $this->config->get( 'layout_featured_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_PIN_TO_TOP' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_FEATURED_PIN_TO_TOP_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_featured_pin' , $this->config->get( 'layout_featured_pin' ) );?>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>


			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_RELATED_POST' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RELATED_POSTS' ); ?>
							</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RELATED_POSTS_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_relatedpost' , $this->config->get( 'main_relatedpost' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAXIMUM_RELATED_POSTS' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAXIMUM_RELATED_POSTS_DESC' ); ?></div>
								<input type="text" name="main_max_relatedpost" class="inputbox" style="width: 50px;" maxlength="2" value="<?php echo $this->config->get('main_max_relatedpost', '5' );?>" />
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

	</div>
</div>
