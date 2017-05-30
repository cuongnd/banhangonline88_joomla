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
EasyBlog.ready( function($){

	$( '#subuser dt span' ).bind( 'click' , function(){
		var id			= $( this ).attr( 'id' ),
			className	= 'user-' + id;

		$( '#subuser dt' ).removeClass( 'open' ).addClass( 'closed' );
		$( this ).parent().addClass( 'open' );
		
		$( '.tab-details' ).hide();
		$( '.' + className ).show();
	});
});
</script>

<form name="adminForm" id="adminForm" action="index.php?option=com_easyblog&c=user" method="post" enctype="multipart/form-data">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="user" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->user->id;?>" />
</form>
