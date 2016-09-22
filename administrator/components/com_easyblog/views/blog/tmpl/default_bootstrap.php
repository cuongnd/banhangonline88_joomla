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
		
		<div class="span8">
			<ul class="list-form reset-ul" style="margin-left:10px">
				<li>
	    			<div class="clearfix"><label for="title" class="fsl ffa fwb"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_TITLE'); ?></label></div>
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TITLE_DESC' );?></div>
						<input type="text" name="title" id="title" value="<?php echo $this->blog->title; ?>" class="inputbox write-title width-full ffa fsl" />
					</div>
				</li>
				<li>
	    			<div class="clearfix"><label for="slug" class="fsl ffa fwb"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_PERMALINK'); ?></label></div>
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PERMALINK_DESC' );?></div>
						<input type="text" name="permalink" id="permalink" value="<?php echo $this->blog->permalink;?>" class="inputbox write-slug width-full" />
					</div>
			  	</li>
				<li style="margin-top:15px;padding-top:15px;border-top:1px dotted #ddd">
					<div id="editor-write_body" class="clearfix">

						<div id="editor-content" class="clearfix mbs">

							<div class="ui-medialink">

								<div class="ui-togmenugroup clearfix pas">

									<a href="javascript:void(0);" class="ico-dglobe float-l prel mrs ui-togmenu olderPosts" togbox="olderPosts">
										<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT'); ?></b>
										<span class="ui-toolnote">
											<i></i>
											<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT'); ?></b>
											<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT_TIPS'); ?></span>
										</span>
									</a>

									<i></i>
							        <?php
							            $this->editorName = 'write_content';
										echo $this->loadTemplate( 'images' );
									?>

									<i></i>
									<a class="float-l ico-dvideo prel" href="javascript: void(0);" onclick="eblog.dashboard.videos.showForm('write_content');" title="<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?>">
										<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?></b>
										<span class="ui-toolnote">
											<i></i>
											<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?></b>
											<span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_TIPS' ); ?></span>
										</span>
									</a>
								</div>


								<div class="ui-togbox olderPosts">
									<div class="pas search-field" style="background:#f5f5f5;">
							            <div class="pas mrl">
											<input type="text" id="search-content" class="input width-half" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>';}" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>') {this.value = '';}" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>" />
											<input type="button" onclick="eblog.editor.search.load('write_content');return false;" value="<?php echo JText::_('COM_EASYBLOG_SEARCH'); ?>" class="buttons" style="height:26px!important" />
										</div>
									</div>
									<div class="search-results-content"></div>
								</div>

								<?php echo $this->loadTemplate( 'media' ); ?>
								<div class="ui-togbox miniManager"></div>

							</div>

						</div>

						<div id="wysiwyg" class="clearfix">
		    				<?php echo $this->editor->display( 'write_content', EasyBlogHelper::getHelper('String')->escape( $this->content ), '100%', '550', '10', '10' , array('pagebreak','ninjazemanta','image') ); ?>
		    				<input id="write_content_hidden" value="" type="hidden" name="write_content_hidden"/>
		    			</div>

					</div>
				</li>
			</ul>	
		</div>

		<div class="span4">

			<div class="accordion" id="options">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#publishing"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_OPTIONS' ); ?></a>
					</div>
					<div id="publishing" class="accordion-body collapse in">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'publishing' ); ?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#blogformat"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_FORMAT' );?></a>
					</div>
					
					<div id="blogformat" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'blog_format' );?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#blogimage"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_IMAGE' );?></a>
					</div>
					
					<div id="blogimage" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'blog_image' );?>
						</div>
					</div>
				</div>

				<?php
					// @rule: Only show autoposting panel when necessary
					if(
					$this->acl->rules->update_facebook && $this->config->get( 'integrations_facebook' )  ||
					$this->acl->rules->update_twitter && $this->config->get( 'integrations_twitter' )  ||
					$this->acl->rules->update_linkedin && $this->config->get( 'integrations_linkedin' ) )
					{
				?>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#autoposting"><?php echo JText::_( 'COM_EASYBLOG_BLOG_AUTOPOSTING' );?></a>
					</div>
					
					<div id="autoposting" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'autoposting' );?>
						</div>
					</div>
				</div>
				<?php
					}
				?>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#tags"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TAGS' );?></a>
					</div>
					
					<div id="tags" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'tagging' );?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#location"><?php echo JText::_( 'COM_EASYBLOG_BLOG_LOCATION' );?></a>
					</div>
					
					<div id="location" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'location' );?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#metadata"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_METADATA' );?></a>
					</div>
					
					<div id="metadata" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'metadata' );?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#trackbacks"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TRACKBACKS' );?></a>
					</div>
					
					<div id="trackbacks" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'trackbacks' );?>
						</div>
					</div>
				</div>

				<?php if( $this->config->get( 'layout_dashboard_zemanta' ) && $this->config->get( 'layout_dashboard_zemanta_api') != '' ) { ?>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#zemanta"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_ZEMANTA' );?></a>
					</div>
					
					<div id="zemanta" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'zemanta' );?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

		</div>

	</div>

</div>

