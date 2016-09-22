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

// Add the Calendar includes to the document <head> section
JHTML::_('behavior.calendar');

// Add tooltip behavior.
JHTML::_('behavior.tooltip');

//Load pane behavior
jimport('joomla.html.pane');

?>
<div id="eblog-wrapper">

<table class="noshow" cellpadding="5" cellspacing="5">
	<tr>
		<td valign="top" width="60%" style="padding-right:8px">
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
		</td>
		<td valign="top" width="38%">
		<?php
			$pane	= JPane::getInstance('sliders', array('allowAllClose' => true));

			echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_OPTIONS' ) , "detail-page" );
			echo $this->loadTemplate( 'publishing' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_FORMAT' ), "blog-format" );
			echo $this->loadTemplate( 'blog_format' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_IMAGE' ), "blog-image" );
			echo $this->loadTemplate( 'blog_image' );
			echo $pane->endPanel();
			// @rule: Only show autoposting panel when necessary
			if(
			$this->acl->rules->update_facebook && $this->config->get( 'integrations_facebook' )  ||
			$this->acl->rules->update_twitter && $this->config->get( 'integrations_twitter' )  ||
			$this->acl->rules->update_linkedin && $this->config->get( 'integrations_linkedin' ) )
			{
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOG_AUTOPOSTING' ) , "autoposting-page" );
				echo $this->loadTemplate( 'autoposting' );
				echo $pane->endPanel();

			}
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TAGS' ), "metadata-page" );
			echo $this->loadTemplate( 'tagging' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOG_LOCATION' ) , "location-page" );
			echo $this->loadTemplate( 'location' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_METADATA' ), "params-page" );
			echo $this->loadTemplate( 'metadata' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TRACKBACKS' ), "metadata-page" );
			echo $this->loadTemplate( 'trackbacks' );
			echo $pane->endPanel();

			if( $this->config->get( 'layout_dashboard_zemanta' ) && $this->config->get( 'layout_dashboard_zemanta_api') != '' )
			{
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_ZEMANTA' ), "zemanta-page" );
				echo $this->loadTemplate( 'zemanta' );
				echo $pane->endPanel();
			}

			echo $pane->endPane();
		?>
		</td>
	</tr>
</table>

</div>