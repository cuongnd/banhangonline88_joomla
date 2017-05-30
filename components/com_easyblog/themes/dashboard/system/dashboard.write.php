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
EasyBlog.require()
.script('legacy')
.done(function($){

	window.emptyText = "<?php echo JText::_("COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE", true);?>";

	<?php if ( !$blog->title ) { ?>
		$( '#title' ).val(emptyText);
	<?php } ?>

	$("#title").bind("focus", function() {
			if ( $(this).val() == emptyText ) {
				$(this).val("").removeClass("default-text");
			}
	});

	$("#title").bind("blur", function() {
			if ( $(this).val() == "" ) {
				$(this).val(emptyText).addClass("default-text");
			}
	});

	// Editor initialization so we can use their methods.
	eblog.editor.getContent = function(){
		<?php echo 'return ' . JFactory::getEditor( $system->config->get( 'layout_editor' ) )->getContent( 'write_content' ); ?>
	}

	eblog.editor.setContent = function( value ){
		<?php echo 'return ' . JFactory::getEditor( $system->config->get( 'layout_editor' ) )->setContent( 'write_content', 'value'); ?>
	}

	eblog.drafts.getContent	= function(){
		<?php echo 'return ' . JFactory::getEditor( $system->config->get( 'layout_editor' ) )->getContent( 'write_content' ); ?>
	}

	eblog.editor.toggleSave = function(){
		<?php echo JFactory::getEditor( $system->config->get( 'layout_editor' ) )->save( 'write_content' ); ?>
	}

	$('#title').bind('change', function() {
		eblog.editor.permalink.generate();
	});

	$( '#permalink-data' ).bind( 'keyup' , function( e ){
		var code = (e.keyCode ? e.keyCode : e.which);
		if( code == 13 )
		{
			eblog.editor.permalink.save();
		}
	});

	$( '#search-content-write_content' ).bind( 'keyup' , function( e ){
		var code = (e.keyCode ? e.keyCode : e.which);
		if( code == 13 )
		{
			$( this ).next().click();
		}
	});

	$( '#description' ).bind( 'keyup' , function(){
		var length	= $(this).val().length;
		$( '#text-counter' ).val( length );
	});

	<?php
	if( $blog->id )
	{
	?>
	// If blog is edited, we do not want to automatically generate the permalink
	eblog.editor.permalink.edited	= true;
	<?php
	}
	?>
	eblog.checkbox.render();

	if( $.browser.msie && $.browser.version == '9.0' && typeof( tinymce ) == 'object' )
	{
		if( tinymce.majorVersion == '3' && tinymce.minorVersion == '2.6' )
		{
			$( '#easyblog-ie-errors' ).show();
		}
	}

	EasyBlog.ieInnerHTML = function(obj, convertToLowerCase) {
		console.log("In ieInnerHTML");
	    var zz = obj.innerHTML ? String(obj.innerHTML) : obj
	       ,z  = zz.match(/(<.+[^>])/g);

		console.log("z = "+z);
	    if (z) {
	     for ( var i=0;i<z.length;(i=i+1) ){
	      var y
	         ,zSaved = z[i]
	         ,attrRE = /\=[a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+[?\s+|?>]/g
	      ;

	      z[i] = z[i]
	              .replace(/([<|<\/].+?\w+).+[^>]/,
	                 function(a){return a;
	               });
	      y = z[i].match(attrRE);

	      if (y){
	        var j   = 0
	           ,len = y.length
	        while(j<len){
	          var replaceRE =
	               /(\=)([a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+)?([\s+|?>])/g
	             ,replacer  = function(){
	                  var args = Array.prototype.slice.call(arguments);
	                  return '="'+(convertToLowerCase
	                          ? args[2].toLowerCase()
	                          : args[2])+'"'+args[3];
	                };
	          z[i] = z[i].replace(y[j],y[j].replace(replaceRE,replacer));
	          j+=1;
	        }
	       }
	       zz = zz.replace(zSaved,z[i]);
	     }
	   }
	   console.log("zz = "+zz);
	  return zz;
	}

	EasyBlog.savePost = function() {
		var content = '';

		// Test if category is selected
		var catInput    = null;
		if( $( '#write_container input[name=category_id]' ).length > 0 )
		{
			catInput    = $( '#write_container input[name=category_id]' );
		}
		else
		{
			catInput    = $( '#write_container select[name=category_id]' );
		}

		if( $( catInput ).val() == 0 )
		{
			ejax.dialog( {
				title: '<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_CATEGORY_DIALOG_TITLE' , true );?>',
				content: '<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_CATEGORY_ERROR' , true );?>'
			});

			return false;
		}

		// Test if category is selected
		if( $( 'input[name=title]' ).val() == '' || $( 'input[name=title]').val() == emptyText )
		{
			ejax.dialog( {
				title: '<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_DIALOG_TITLE' , true );?>',
				content: '<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR' , true );?>'
			});

			return false;
		}

		eblog.editor.toggleSave();

		// Retrieve the main content.
		var editorContents 	= eblog.editor.getContent();

		// Try to break the parts with the read more.
		var val	= editorContents.split( '<hr id="system-readmore" />' );

		if( val.length > 1 )
		{
			// It has a read more tag
			var intro		= $.sanitizeHTML( val[0] );
			var fulltext	= $.sanitizeHTML( val[1] );
			var content 	= intro + '<hr id="system-readmore" />' + fulltext;
		}
		else
		{
			// Since there is no read more tag here, the first index is always the full content.
			var content 	= $.sanitizeHTML( editorContents );;
		}

		if ($.browser.msie && (parseInt($.browser.version) < 9)) {

			content = EasyBlog.ieInnerHTML(content);
		}
		$( '#write_content_hidden' ).val( content );

		$( '#save_post_button' ).addClass( 'ui-disabled' );
		$( '#save_post_button' ).attr( 'disabled' , 'disabled' );
		eblog.editor.checkPublishStatus();
	}

	// Bind save event
	$( '#save_post_button' ).bind( 'click' , function(){
		EasyBlog.savePost();
	});

	// Bind apply event
	$( '#apply_post_button' ).bind( 'click' , function(){
		$( 'input[name=apply]' ).val( 1 );

		EasyBlog.savePost();
	});


	<?php if( ( !$isEdit || ( $isEdit && !$draft->id ) || $isDraft ) && $system->config->get( 'main_autodraft' ) && !$isPending ){ ?>
	eblog.drafts.frequency	= <?php echo $system->config->get( 'main_autodraft_interval' ); ?> * 1000;
	setTimeout( 'eblog.drafts.check()', eblog.drafts.frequency );
	<?php } ?>




});


EasyBlog.ready(function($){

	window.changeAuthor	= function(id, name, avatar )
	{
		$( '#author-name' ).html( name );
		$( '#author-avatar' ).attr( 'src' , avatar );
		$( '#created_by' ).val( id );
		ejax.closedlg();
	}

	window.changeCategory = function(id, name)
	{
		$( '#category-item-name' ).html( name );
		$( '#category_id' ).val( id );
		ejax.closedlg();
	}

	window.addCategory =  function(name)
	{
		eblog.dashboard.categories.quicksave(name);
		return;
	}
})

EasyBlog.ready(function($) {

	var toolbar = $(".dashboard-option"),
		dashboardWidth;

	var dock = function() {

		if (toolbar.hasClass("docked")) return;

		// Enforce dashboard-head height
		$('.dashboard-head').height($('.dashboard-head').height());

		toolbar
			.css("top", "-100px")
			.addClass("docked");

		if (!toolbar.data("height")) {
			toolbar.data("height", toolbar.outerHeight());
		}

		if (!toolbar.data("horizontalPadding")) {
			toolbar.data("horizontalPadding", toolbar.outerWidth() - toolbar.width())
		}

		toolbar
			.width(dashboardWidth - toolbar.data("horizontalPadding"));

		if (!toolbar.hasClass("no-animation")) {

			toolbar
				.css("top", toolbar.data("height") * -1)
				.animate({top: "0px"}, {duration: 250});

		} else {

			toolbar.css("top", "0px");
			toolbar.removeClass("no-animation");
		}
	};

	var undock = function() {

		if (!toolbar.hasClass("docked")) return;

		if (!toolbar.hasClass("no-animation")) {

			toolbar
				.animate(
					{
						top: "-=100px"
					},
					{
						duration: 250,
						complete: function() {
							toolbar
								.removeClass("docked")
								.width("auto");
						}
					});

		} else {

			toolbar
				.removeClass("docked")
				.width("auto");
		}
	};

	var checkScroll, checkResize;

	$(window)
		.on("scroll.dashboardOption", function() {


			clearTimeout(checkScroll);

			checkScroll = setTimeout(function(){

				dockToolbar = $(document).scrollTop() > toolbar.data("offsetBottom");

				return (dockToolbar) ? dock() : undock();

			}, 250);

		})
		.on("resize.dashboardOption", (function() {

			clearTimeout(checkResize);

			checkResize = setTimeout(function(){

				toolbar.addClass("no-animation");

				undock();

				// Capture new width & offset bottom
				dashboardWidth = $("#ezblog-dashboard").width();

				toolbar.data("offsetBottom", toolbar.offset().top + toolbar.outerHeight());

				dockToolbar = $(document).scrollTop() > toolbar.data("offsetBottom");

				return (dockToolbar) ? dock() : undock();

			}, 500);

			return arguments.callee;

		})());

});

EasyBlog.require()
	.script(
		"dashboard/editor"
	)
	.done(function($){

		$("#write_container").implement(EasyBlog.Controller.Dashboard.Editor, {});
	});
</script>





<form name="adminForm" id="blogForm" method="post" action="">
<div class="dashboard-head dash-write clearfix">
	<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>

    <div class="dashboard-option small clearfix">
        <div class="float-r">

        	<?php
				if( $system->config->get( 'integrations_linkedin_centralized' ) || $system->config->get( 'integrations_facebook_centralized' ) || $system->config->get( 'integrations_twitter_centralized' )
				|| $system->config->get( 'integrations_linkedin_centralized_and_own') || $system->config->get( 'integrations_facebook_centralized_and_own') || $system->config->get( 'integrations_twitter_centralized_and_own' ) ) { ?>
            <div class="social-publish prel">
            	<a href="javascript:void(0);" class="buttons"><?php echo JText::_( 'COM_EASYBLOG_SHARE_TO' ); ?><i></i></a>
            	<div class="social-publish-options pabs">
            	<?php if( $system->config->get( 'integrations_linkedin_centralized' ) || $system->config->get( 'integrations_facebook_centralized' ) || $system->config->get( 'integrations_twitter_centralized') )
            	{
	            ?>
            		<div class="publish-centralized option">
            			<b><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS');?></b>
            			<div><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS_DESC' );?></div>
            			<div>
            				<span class="ui-highlighter publish-to in-block mrm">
            					<?php if( $system->config->get( 'integrations_facebook_centralized' ) ){ ?>
				                <span class="ui-span<?php echo ( $system->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="facebook" id="centralized-facebook"<?php echo ( $system->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>
				                <?php } ?>

				                <?php if( $system->config->get( 'integrations_twitter_centralized' ) ){ ?>
				                <span class="ui-span<?php echo ( $system->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="twitter" id="centralized-twitter"<?php echo ( $system->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>
				                <?php }?>

				                <?php if( $system->config->get( 'integrations_linkedin_centralized' ) ){ ?>
				                <span class="ui-span<?php echo ( $system->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="centralized[]" value="linkedin" id="centralized-linkedin"<?php echo ( $system->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="centralized-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
				                <?php } ?>
				            </span>

            				<div class="clear"></div>
            			</div>
            		</div>
        		<?php
        		}
        		?>
            		<div class="publish-personal option">
            			<b><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS');?></b>
            			<div><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS_DESC' );?></div>
            			<div>
            				<span class="ui-highlighter publish-to in-block mrm">
								<?php if(
										$this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ||
										$this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ||
										$this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' ) ){
								?>

								<?php if( $this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'FACEBOOK' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="facebook" id="socialshare-facebook"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'FACEBOOK' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
				            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
			            			</label>
				                </span>
				                <?php } ?>

				                <?php if( $this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'TWITTER' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="twitter" id="socialshare-twitter"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'TWITTER' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
				            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
			            			</label>
				                </span>
				                <?php } ?>

				                <?php if( $this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' )  ){?>
				                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'LINKEDIN' ) && empty($blog->id)) ? ' active' : '';?>">
				            		<input type="checkbox" name="socialshare[]" value="linkedin" id="socialshare-linkedin"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'LINKEDIN' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
				            		<label for="socialshare-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
				            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
			            			</label>
				                </span>
				                <?php } ?>

				                <?php } else { ?>
									<?php if(
												($this->acl->rules->update_facebook && $system->config->get( 'integrations_facebook_centralized_and_own' ) ) ||
												($this->acl->rules->update_twitter && $system->config->get( 'integrations_twitter_centralized_and_own' ) ) ||
												($this->acl->rules->update_linkedin && $system->config->get( 'integrations_linkedin_centralized_and_own' ) ) ){ ?>
				                		<div class="eblog-message"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile'); ?>#widget-profile-facebook" target="_blank"><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_SETUP_SOCIAL_INTEGRATION_LINK_TEXT') ?></a></div>
				                	<?php } ?>
                				<?php } ?>
				            </span>
            				<div class="clear"></div>
            			</div>
            		</div>
            	</div>
            </div>
            <?php } ?>


            <?php if ( !empty( $this->acl->rules->manage_pending ) && $isPending ) : ?>
            <button type="button" id="reject_post_button" onclick="eblog.editor.reject( '<?php echo $draft->id; ?>' );return false;" class="buttons sibling-l">
    			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_REJECT_POST'); ?>
    		</button><button type="button" id="save_post_button" onclick="eblog.editor.save();return false;" class="buttons butt-green sibling-r">
    			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_APPROVE_POST'); ?>
    		</button>
    		<?php else : ?>

    			<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' ); ?></a><?php if( !empty($this->acl->rules->publish_entry) ){ ?><button type="button" id="apply_post_button" onclick="eblog.editor.apply();return false;" class="buttons sibling-r"><?php echo JText::_( 'COM_EASYBLOG_APPLY_BUTTON');?></button><?php } ?>
    			<button type="button" id="save_post_button" class="buttons butt-green">
            	<?php if( $isEdit && !empty($this->acl->rules->publish_entry) ){ ?>
            		<?php echo JText::_( 'COM_EASYBLOG_UPDATE_POST_BUTTON' ); ?>
            	<?php } else { ?>
            		<?php if( empty($this->acl->rules->publish_entry) ){ ?>
                		<?php echo JText::_( 'COM_EASYBLOG_SUBMIT_FOR_REVIEW_BUTTON' ); ?>
                	<?php } else { ?>
                		<?php echo JText::_( 'COM_EASYBLOG_PUBLISH_NOW_BUTTON' ); ?>
                	<?php } ?>
                <?php } ?>
				</button>

    		<?php endif; ?>
        </div>

        <span class="has-tooltip">
			<a href="javascript:void(0)" onclick="eblog.dashboard.preview('<?php echo EasyBlogRouter::getItemId('entry');?>');return false;" class="buttons for-preview as-icon">
				<i><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_BUTTON'); ?></i>
			</a>
			<div class="tip-item">
				<i></i>
				<div>
					<b><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_BUTTON'); ?></b>
					<?php echo JText::_( 'COM_EASYBLOG_ENTRY_PREVIEW_BUTTON_TIPS' ); ?>
				</div>
			</div>
		</span>

        <?php if( empty($blog->id) ) : ?>
        <span class="has-tooltip">
			<a href="javascript:void(0);" onclick="eblog.drafts.save();return false;" class="buttons for-draft as-icon">
				<i><?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON'); ?></i>
			</a>
			<div class="tip-item">
				<i></i>
				<div>
					<b><?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON'); ?></b>
					<?php echo JText::_( 'COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON_TIPS' ); ?>
				</div>
			</div>
		</span>
        <?php endif; ?>

        <?php if( $useImageManager ){ ?>
        <span class="media_manager_button">
			<a href="javascript:void(0);" id="media_manager_button" class="buttons">
				<?php echo JText::_( 'COM_EASYBLOG_MEDIA_MANAGER' ); ?>
			</a>
		</span>
		<?php } ?>

        <span id="draft_status" class="small"><span></span></span>
    </div>
</div>

<div id="easyblog-ie-errors" class="eblog-message warning" style="display: none;"><?php echo JText::_( 'COM_EASYBLOG_EDITOR_INCOMPATIBLE');?> <a href="http://bit.ly/oKHHn9" target="_blank">http://bit.ly/oKHHn9</a></div>

<?php if ( empty($this->acl->rules->publish_entry) ){ ?>
	<div class="eblog-message warning">
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_ALL_NEW_ENTRY_WILL_BE_MODERATE_BY_ADMINS'); ?>
	</div>
<?php } ?>

<?php if( $draft->id && !$isDraft ) { ?>
	<div class="eblog-message warning clearfix">
		<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_DRAFT_EXISTS' ); ?>
		<a class="ui-button" href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=write&draft_id=' . $draft->id ); ?>"><?php echo JText::_( 'COM_EASYBLOG_LOAD_AUTOSAVED_BUTTON' ); ?></a>
	</div>
<?php } ?>

<div id="write_container">
	<!-- Multi tier category template -->
	<?php if($system->config->get('layout_dashboardcategoryselect') == 'multitier') { ?>
	<?php echo $this->fetch( 'dashboard.write.category.php' ); ?>
	<?php } ?>

	<a id="write-entry" style="height:1px;line-height:1px;font-size:0">&nbsp;</a>
	<div class="ui-modbox" id="widget-writepost">
		<div class="ui-modbody no-header clearfix">

			<?php if( $system->config->get( 'main_microblog' ) || $system->config->get( 'layout_dashboardcategoryselect') != 'multitier' ){ ?>
			<div class="write-postinfo clearfix">
				<?php if($system->config->get('layout_dashboardcategoryselect') != 'multitier') { ?>
			    <div class="float-l write-selectcategory width-half" style="line-height:25px">
			    	<div>
					<?php if( !empty( $nestedCategories ) ){ ?>
						<?php echo $nestedCategories; ?>
					<?php } else { ?>
						<a href="javascript:void(0);" onclick="eblog.dashboard.changeCategory('<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SELECT_CATEGORY'); ?>', '<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=listCategories&tmpl=component&browse=1');?>');" class="category-switcher buttons" style="display:block">
						    <span id="category-item-name"><?php echo ( empty( $defaultCategoryName ) ) ? JText::_('COM_EASYBLOG_DASHBOARD_SELECT_CATEGORY') : $defaultCategoryName ; ?></span>
						</a>
						<input type="hidden" name="category_id" id="category_id" value="<?php echo $defaultCategory; ?>" />
					<?php } ?>
					</div>
				</div>
				<?php } ?>

				<?php if( $system->config->get( 'main_microblog' ) ){ ?>
				<div class="float-l width-half">
					<div>
						<select name="source" class="inputbox">
							<option value=""<?php echo $blog->source == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_STANDARD_POST' );?></option>
							<option value="photo"<?php echo $blog->source == 'photo' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO' );?></option>
							<option value="video"<?php echo $blog->source == 'video' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO' );?></option>
							<option value="quote"<?php echo $blog->source == 'quote' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_QUOTE' );?></option>
							<option value="link"<?php echo $blog->source == 'link' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_LINK' );?></option>
						</select>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="write-posteditor mtm">

				<div id="editor-write_title" class="clearfix">

					<?php echo $this->fetch( 'dashboard.write.blogimage.php' ); ?>

					<div class="blogTitle">
						<input type="text" name="title" id="title" value="<?php echo $blog->title; ?>" class="ui-post_title input width-full" />
						<div class="permalink-editor mts fft">
	                  		<strong><?php echo JText::_( 'COM_EASYBLOG_PERMALINK' );?></strong> :
	                  		<span id="permalink-value"<?php echo $blog->id ? ' style="display: inline;"' : '';?>>
	    						<span id="permalink-url"><?php echo $blog->permalink;?></span>
	    						<a href="javascript:void(0);" onclick="eblog.editor.permalink.edit();" id="edit-permalink" class="ui-button" style="display: inline;"><?php echo JText::_('COM_EASYBLOG_EDIT'); ?></a>
	    					</span>
	    					<span id="permalink-edit" style="display: none;">
	    						<span><input type="text" name="permalink" id="permalink-data" value="<?php echo $blog->permalink;?>" class="ui-post_slug input text width-200" /></span>
	    						<!-- show link below after edit button clicked -->
	                        	<a href="javascript:eblog.editor.permalink.save();" id="save-slug" class="ui-button"><?php echo JText::_('COM_EASYBLOG_SAVE'); ?></a>
	                        	<a href="javascript:eblog.editor.permalink.edit();" id="cancel-slug"><?php echo JText::_('COM_EASYBLOG_CANCEL'); ?></a>
	                      	</span>
						</div>

						<div class="write-selectreaders" style="padding-top:15px;margin-top:15px;border-top:1px dotted #DDD">
					    	<div>
							<?php if( !$external && $system->config->get('main_blogprivacy_override') && $this->acl->rules->enable_privacy ){ ?>
							<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions( '', $blog->created_by ) , 'private' , 'size="1" class="input select"' , 'value' , 'text' , $isPrivate );?>
							<?php } ?>

							<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' && $system->config->get( 'main_multi_language' ) ){ ?>
								<select name="eb_language" class="inputbox">
									<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
									<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' , $blog->language );?>
								</select>
							<?php } ?>

							</div>
						</div>
					</div>
          		</div>

          		<div id="editor-write_body">
					<?php echo $this->fetch( 'dashboard.write.insert.php' ); ?>
					<div id="wysiwyg" class="clearfix">
						<?php echo $editor->display('write_content', $this->escape( $content ), '100%', '350', '10', '10', array('image', 'pagebreak','ninjazemanta'), null, 'com_easyblog'); ?>
						<input id="write_content_hidden" value="" type="hidden" name="write_content_hidden"/>
					</div>
				</div>
			</div>

			<?php echo $this->fetch( 'dashboard.write.tags.php' ); ?>
			<?php echo $this->fetch( 'dashboard.write.options.php' ); ?>
		</div><!-- end: .ui-modbody -->
	</div>

	<?php if( $system->config->get( 'layout_dashboardseo' ) ){ ?>
	<?php echo $this->fetch( 'dashboard.write.seo.php' ); ?>
	<?php } ?>

	<?php if( $system->config->get( 'layout_dashboardtrackback' ) && $system->config->get( 'main_trackbacks' ) ){ ?>
	<?php echo $this->fetch( 'dashboard.write.trackback.php' ); ?>
	<?php } ?>
</div>

<?php if( $isPending ) : ?>
<input type="hidden" name="under_approval" value="1" />
<?php endif; ?>
<input type="hidden" name="draft_id" id="draft_id" value="<?php echo $draft->id;?>" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="controller" value="dashboard" />
<input type="hidden" name="task" id="form-task" value="save" />
<input type="hidden" name="id" value="<?php echo $blog->id; ?>" />
<input type="hidden" name="metaid" value="<?php echo $meta->id; ?>" />
<input type="hidden" name="apply" value="0" />
</form>
<div class="clearfix"></div>
