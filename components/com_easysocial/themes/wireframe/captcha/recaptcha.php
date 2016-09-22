<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<script type="text/javascript">
EasySocial.require().script('http<?php echo $ssl ? 's' : '';?>://www.google.com/recaptcha/api/js/recaptcha_ajax.js').done(function($)
{
	Recaptcha.create( "<?php echo $key;?>" , 'recaptcha-image-<?php echo $uniqueId;?>' ,
	{
		lang		: "<?php echo $lang;?>",
		theme		: "<?php echo $theme;?>",
		tabindex	: 0,
		custom_theme_widget: "recaptcha_widget"
	});
});
</script>
<div id="recaptcha-image-<?php echo $uniqueId;?>"></div>
