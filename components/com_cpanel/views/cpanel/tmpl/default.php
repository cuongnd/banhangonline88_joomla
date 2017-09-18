<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
$user = JFactory::getUser();
$input=JFactory::getApplication()->input;
require_once JPATH_ROOT.DS.'components/com_cpanel/iconhelper.php';
$html = JHtml::_('links.linksgroups', ModQuickIconHelper::getButtonsControl());
?>
<div class="row">
	<div class="col-md-12">
		<div class="icon">
			<img src="">
			<h3><a href="index.php?option=com_languages&view=languages">Quản lý ngôn ngữ</a></h3>
		</div>
	</div>
</div>
