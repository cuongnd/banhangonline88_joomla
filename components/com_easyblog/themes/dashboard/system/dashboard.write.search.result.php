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
	.script("<?php echo JURI::root();?>components/com_easyblog/assets/vendors/clipboard/ZeroClipboard.js")
	.done(function($){

		// Load the .swf files for ZeroClipboard to work.
		ZeroClipboard.setMoviePath( '<?php echo JURI::root();?>components/com_easyblog/assets/vendors/clipboard/ZeroClipboard.swf' );

		$( '.blog-search-items' ).children().each(function(){
			var buttonId 		= $( this ).find( '.copy-link' ).attr( 'id' ),
				containerId		= $( this ).find( '.copy-container' ).attr( 'id' ),
				url 			= $( this ).find( '.search-url' ).val();

			var clip 	= new ZeroClipboard.Client();

			// Reset the clip
			clip.setText( '' );

			clip.addEventListener( 'mouseDown' , function(){
				clip.setText( url );
			});

			// Now, let's glue it back to the button
			clip.glue( buttonId , containerId );
		});
	});
</script>

<span style="display:none;" id="clip-item"></span>
<ul class="blog-search-items reset-ul">
	<?php
	$i 	= 1;
	foreach( $result as $entry )
	{
		$postLink		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id );
		$externalLink	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id , false , true );
	?>
	<li>
		<input type="button" onclick="eblog.editor.search.insert('<?php echo $externalLink; ?>', '<?php echo addslashes($this->escape( $entry->title )); ?>', 'write_content');return false;" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK'); ?>" class="ui-button float-r mts" />
		<span id="container-<?php echo $i;?>" style="position:relative;" class="float-r copy-container">
			<input type="button" id="button-<?php echo $i;?>" value="<?php echo JText::_('COM_EASYBLOG_COPY_TO_CLIPBOARD'); ?>" class="ui-button mts copy-link" />
		</span>
		<input type="hidden" class="search-url" value="<?php echo $externalLink;?>" />
		<div>
            <a href="<?php echo $postLink; ?>" target="_blank"><?php echo $entry->title; ?></a> - <span><?php echo $this->formatDate( '%b %d, %y' , $entry->created ); ?> </span>
		</div>
	</li>
	<?php
		$i++;
	}
	?>
</ul>
