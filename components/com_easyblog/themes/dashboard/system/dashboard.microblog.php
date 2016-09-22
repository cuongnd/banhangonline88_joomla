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
EasyBlog.ready(function($){
	$( '.ui-quickpost-tab a' ).each( function(){
		$( this ).bind( 'click' , function(){

			var item 	= $( this ).children( 'span' ).attr( 'class' );

			$( this ).parents( 'ul' ).children().removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
			$( '#quick-post-wrap' ).children().hide();
			$( '#quick-post-wrap #' + item ).show();

		})
	});

	// Find the active tab and activate the click event for it.
	$( '#write_container ul.ui-quickpost-tab li.type-<?php echo $activeType;?> a' ).click();

	// Bind the autoposting checkboxes
	$( '#write_container .autopost-microblog' ).each(function(){

		// Bind event clicks on the label
		$( this ).find( 'label.socialshare-label' ).bind( 'click' , function(){
			$(this).prev('input').click();
		});

		// When the input is changed, add the appropriate classes.
		$( this ).find( 'input[name=socialshare\\[\\]]' ).bind( 'change' , function(){

			if( $(this).is(':checked') )
			{
				$(this).parent().addClass( 'active' );
			}
			else
			{
				$( this ).parent().removeClass( 'active' );
			}
		});
	});

	$( '#quick-post-wrap > div' ).find( '.show-advanced-parameters' ).bind( 'click' , function(){

		$( this ).parents( 'form[name=quick-post]' ).find( '.advanced-parameters' ).each( function(){
			if( $(this).css('display') == 'none' )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	});

	// TODO: Don't pollute global namespace
	window.getTags = function( formType ) {

		var tags 	= new Array;

		$( '#microblog-' + formType + ' input[name=tags\\[\\]]' ).each(function(){
			tags.push( $(this).val() );
		});

		return tags;
	}
});
</script>
<div class="dashboard-head clearfix">
	<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
</div>
<div id="write_container">
	<!-- System Messages -->
	<div id="eblog-message" class="eblog-message"><div></div></div>

	<?php if( $this->acl->rules->add_entry ) { ?>
		<div class="ui-modbox" id="widget-quickpost">
			<div class="ui-modhead">
				<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_POST_A_SHORT_MESSAGE'); ?></div>
				<a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
			</div>
			<div class="ui-modbody clearfix">
				<div class="ui-quickpost-tab">
					<ul class="ui-quickpost-tab reset-ul float-li clearfix">
						<li class="type-text">
							<a href="javascript:void(0);"><span class="quick-text"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_TEXT' );?></span></a>
						</li>
						<li class="type-photo">
							<a href="javascript:void(0);"><span class="quick-photo"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO' );?></span></a>
						</li>
						<li class="type-video">
							<a href="javascript:void(0);"><span class="quick-video"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO' );?></span></a>
						</li>
						<li class="type-quote">
							<a href="javascript:void(0);"><span class="quick-quote"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_QUOTE' );?></span></a>
						</li>
						<li class="type-link">
							<a href="javascript:void(0);"><span class="quick-link"><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_LINK' );?></span></a>
						</li>
					</ul>
				</div>
				<div id="quick-post-wrap">
					<div id="quick-photo">
						<?php echo $this->fetch( 'dashboard.microblog.photo.php' );?>
					</div>

					<div id="quick-video">
						<?php echo $this->fetch( 'dashboard.microblog.video.php' );?>
					</div>

					<div id="quick-quote">
						<?php echo $this->fetch( 'dashboard.microblog.quote.php' );?>
					</div>

					<div id="quick-link">
						<?php echo $this->fetch( 'dashboard.microblog.link.php' );?>
					</div>

					<div id="quick-text">
						<?php echo $this->fetch( 'dashboard.microblog.text.php' );?>
					</div>
				</div>
        	</div>
        </div>
	<?php } ?>
</div>
