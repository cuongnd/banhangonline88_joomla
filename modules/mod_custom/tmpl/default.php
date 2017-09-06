<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$params = $module->params;
$current_module_image = $params->get('module_image', '');
?>
<div id="mod_custom_<?php echo $module->id ?>">
	<?php if ($module->showtitle): ?><h3 class="module-title"> <?php if ($current_module_image != ''): ?><img
		class="icon"
		src="<?php echo JUri::root() . $current_module_image ?>"><?php endif; ?><?php echo $module->title ?>
		</h3> <?php endif; ?>

	<div class="custom<?php echo $moduleclass_sfx ?>" <?php if ($params->get('backgroundimage')) : ?> style="background-image:url(<?php echo $params->get('backgroundimage');?>)"<?php endif;?> >
		<?php echo $module->content;?>
	</div>
</div>
