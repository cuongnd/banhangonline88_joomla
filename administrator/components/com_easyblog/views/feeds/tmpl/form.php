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
<script type="text/javascript">
EasyBlog.ready(function($){

window.insertMember = function( id , name )
{
	$( '#item_creator' ).val( id );
	$( '#author_name' ).html( name );
	$.Joomla("squeezebox").close();
}

window.insertCategory = function( id , name )
{
	$( '#item_category' ).val( id );
	$( '#category_name' ).html( name );
	$.Joomla("squeezebox").close();
}

});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="feeds" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="cid" value="<?php echo $this->feed->id;?>" />
</form>
