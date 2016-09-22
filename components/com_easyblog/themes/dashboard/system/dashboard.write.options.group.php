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
<?php
$groups	= EasyBlogHelper::getHelper( 'Groups' )->getFormHTML( $external, $extGroupId , $blogSource, $isPending );

if( $groups )
{
?>
	<strong style="display:block;padding:5px 0;margin-top:10px;border-top:1px solid #ddd"><?php echo JText::_( 'COM_EASYBLOG_INTEGRATION_GROUPS' );?></strong>
	<input type="hidden" name="groups_type" value="" />
	<input type="hidden" name="return" value="<?php echo JRequest::getVar( 'return' ); ?>" />
<?php
	echo $groups;
}
