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
<table width="100%" class="paramlist admintable paramstable admintable">
<tr>
    <td valign="top" class="key"><span for="adsense_published"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL'); ?></span></td>
    <td class="paramlist_value">
    	<input type="text" class="inputbox full-width" name="google_profile_url" id="google_profile_url" value="<?php echo $this->escape( $this->bloggerParams->get( 'google_profile_url' ) );?>" />
	</td>
</tr>
<tr>
	<td valign="top" class="key">
		<span><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL_SHOW' ); ?></span>
	</td>
	<td>
		<?php echo $this->renderCheckbox( 'show_google_profile_url' , $this->bloggerParams->get( 'show_google_profile_url' ) );?>
	</td>
</tr>
</table>	