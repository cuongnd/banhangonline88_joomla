<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$data = $displayData;

JFactory::getDocument()->addStyleDeclaration(
'#'.$data->id.'{display:none;}'.
'#'.$data->id.' + .ark_preloader { display:block !important;}'.
'#'.$data->id.' + .ark_preloader + #editor-xtd-buttons {visibility:hidden;}');
?>
<div class="ark_preloader" style="display:none;">
	<div  class="inner_container">
		 <i class="animate icon-cog cog1-pos"></i>
		 <i class="animate icon-cog cog2-pos"></i>
		 <i class="animate icon-cog cog3-pos"></i>
	</div>
</div>