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

	$( '.blog-comment-tabs' ).find( 'a' ).bind( 'click' , function(){
		$( this ).parents( 'ul' ).children( 'li' ).removeClass( 'active' );
		$( this ).parents( 'ul' ).children( 'li' ).addClass( 'inactive' );
		$( this ).parent().addClass( 'active' ).removeClass( 'inactive' );

		var activeId	= $( this ).parent().attr( 'id' );

		$( '.blog-comment-contents' ).children().hide();
		$( '.blog-comment-contents' ).find( '.blog-comment-' + activeId ).show();

		// We set timeout here by 1 is to ensure that the element is rendered by the browser before triggering resize event for the content to properly calculate it's WxH
		// We trigger resize here is so that responsive contents (eg, Komento) can properly calculate it's area WxH
		setTimeout(function() {
			$(window).resize();
		}, 1);

		if( activeId == 'system-disqus' )
		{
			$( '.blog-comment-contents' ).find( 'iframe' ).css( 'height' , 'auto' );
		}

	});
});
</script>
<!-- Comment anchor --> <a name="comments" id="comments"> </a>
<ul class="blog-comment-tabs reset-ul float-li clearfix">
	<?php $i = 0; ?>
	<?php foreach( $commentSystems as $key => $val ){ ?>
		<li class="<?php echo $i == 0 ? 'active' : 'inactive';?>" id="system-<?php echo strtolower( $key );?>">
			<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYBLOG_COMMENT_SYSTEM_' . strtoupper( $key ) ); ?></a>
		</li>
		<?php $i++;?>
	<?php } ?>
</ul>

<div class="blog-comment-contents">
	<?php $i = 0; ?>
	<?php foreach( $commentSystems as $key => $html ){ ?>
		<div class="blog-comment-system-<?php echo strtolower( $key );?>"<?php echo $i != 0 ? ' style="display:none;"' : '';?>>
			<?php echo $html;?>
		</div>
		<?php $i++;?>
	<?php } ?>
</div>
