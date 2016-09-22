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

$blog 		= $this->blog;
$draft		= $this->draft;
$editor		= $this->editor;
$acl		= $this->acl;
$author		= $this->author;

$blogId = JRequest::getInt('blogid', '');

$isPrivate = $this->isPrivate;
$allowComment = $this->allowComment;
$subscription = $this->subscription;
$frontpage = $this->frontpage;
$trackbacks = $this->trackbacks;

jimport( 'joomla.utilities.date' );
?>

<script type="text/javascript">

EasyBlog.require()
	.script(
		"legacy", "admin",
		"dashboard/editor",
		"dashboard/medialink"
	)
	.done(function($){

		$("#wysiwyg").implement(EasyBlog.Controller.Dashboard.Editor, {});
		$(".ui-medialink").implement(EasyBlog.Controller.Dashboard.MediaLink);


	$.Joomla("submitbutton", function(action){


	    if( action == 'rejectBlog')
	    {
			var draft_id    = $('#draft_id').val();
	        admin.blog.reject( draft_id );
			return false;
	    }
	    else
	    {
			eblog.editor.toggleSave();
			saveBlog();

			if( action == 'savePublishNew' )
			{
				$( '#savenew' ).val( '1' );
				action	= 'savePublish';
			}
		}

		$.Joomla("submitform", [action]);
	});

	window.insertMember = function( id , name )
	{
		$('#authorId').val(id);
		$('#authorName').val(name);
		$.Joomla("squeezebox").close();
	}

	window.insertCategory = function( id , name )
	{
		$('#category_id').val(id);
		$('#categoryTitle').val(name);
		$.Joomla("squeezebox").close();
	}

	$('#title').bind('change', function() {
		eblog.editor.permalink.generate();
	});

	// Editor initialization so we can use their methods.
	eblog.editor.getContent = function(){
		<?php echo 'return ' . JFactory::getEditor( $this->config->get( 'layout_editor' ) )->getContent( 'write_content' ); ?>
	}

	eblog.editor.setContent = function( value ){
		<?php echo 'return ' . JFactory::getEditor( $this->config->get( 'layout_editor' ) )->setContent( 'write_content' , 'value' ); ?>
	}

	eblog.editor.toggleSave = function(){
		<?php echo JFactory::getEditor( $this->config->get( 'layout_editor' ) )->save( 'write_content' ); ?>
	}

	// @task: Bind the reset hits button
	$( '#reset-hits' ).bind( 'click' , function(){

		if( confirm( '<?php echo JText::_('COM_EASYBLOG_CONFIRM_RESET_HITS', true);?>' ) )
		{
			EasyBlog.ajax( 'admin.views.blog.resethits' , {
				id: '<?php echo $this->blog->id;?>'
			}, function( state ){

				if( state )
				{
					$( '.hits-counter' ).html( 0 );
				}
			});
		}
	});

	window.saveBlog = function()
	{
		// Retrieve the main content.
		var editorContents 	= eblog.editor.getContent();

		// Try to break the parts with the read more.
		var val	= editorContents.split( '<hr id="system-readmore" />' );

		if( val.length > 1 )
		{
			// It has a read more tag
			var intro		= $.sanitizeHTML( val[ 0 ] );
			var fulltext	= $.sanitizeHTML( val[ 1 ] );
			var content 	= intro + '<hr id="system-readmore" />' + fulltext;
		}
		else
		{
			// Since there is no read more tag here, the first index is always the full content.
			var content 	= $.sanitizeHTML( editorContents );
		}

		if ($.browser.msie && (parseInt($.browser.version) < 9)) {

			function ieInnerHTML(obj, convertToLowerCase) {
			    var zz = obj.innerHTML ? String(obj.innerHTML) : obj
			       ,z  = zz.match(/(<.+[^>])/g);

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
			  return zz;
			}

			content = ieInnerHTML(content);
		}

		$( '#write_content_hidden' ).val( content );
	}


});

</script>

<div id="eblog-wrapper">
<form name="adminForm" id="adminForm" method="post" action="index.php">
<div id="blogForm">
<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="savenew" value="0" id="savenew" />
<input type="hidden" name="ispending" value="<?php echo $acl->rules->publish_entry ? '0' : '1'; ?>" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="blogs" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="metaid" value="<?php echo $this->meta->id; ?>" />
<input type="hidden" name="blogid" value="<?php echo $blog->id;?>" />
<input type="hidden" name="draft_id" id="draft_id" value="<?php echo $draft->id;?>" />
<input type="hidden" name="under_approval" value="<?php echo $this->pending_approval; ?>" />
</form>
</div>
