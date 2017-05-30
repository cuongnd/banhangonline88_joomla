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

<?php for ($i=0,$n=count($folders); $i<$n; $i++) :

	if (! isset($folders[$i]))
		continue;

	$item   =& $folders[$i];
	$item->_tmp_folder = $folders[$i];
?>

<div class="item">
	<a href="index.php?option=com_easyblog&amp;view=images&amp;tmpl=component&amp;folder=<?php echo $item->_tmp_folder->path_relative; ?>">
		<img src="<?php echo JURI::base() ?>components/com_media/images/folder.gif" width="60" height="60" alt="<?php echo $item->_tmp_folder->name; ?>" />
		<span><?php echo $item->_tmp_folder->name; ?></span></a>
</div>
	
<?php endfor; ?>
