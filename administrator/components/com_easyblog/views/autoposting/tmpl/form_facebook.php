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

	var left = (screen.width/2)-( 300 /2);
	var top = (screen.height/2)-( 300 /2);

	$( '#facebook-login' ).bind( 'click' , function(){
		var url = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&c=autoposting&task=request&type=<?php echo EBLOG_OAUTH_FACEBOOK;?>&return=from&call=doneLogin';
		window.open(url, "", 'scrollbars=no,resizable=no, width=300,height=300,left=' + left + ',top=' + top );
	});
});

window.doneLogin = function(){
	window.location.href = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&view=autoposting&layout=form&type=facebook';
}
</script>
<?php
$expires 	= $this->oauth->getAccessTokenValue( 'expires' );
$created 	= strtotime( $this->oauth->created );

$expire 	= EasyBlogHelper::getDate( $created + $expires )->toFormat( '%A, %d %B %Y' );
$this->expire = $expire;
?>
<form name="adminForm" action="index.php" method="post" class="adminForm" id="adminForm">

<?php echo $this->loadTemplate( 'facebook_' . $this->getTheme() ); ?>
<input type="hidden" name="task" value="save" />
<input type="hidden" name="type" value="facebook" />
<input type="hidden" name="c" value="autoposting" />
<input type="hidden" name="option" value="com_easyblog" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
