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

<div class="row-fluid">
	<div class="span12">
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#theme-parameter" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_TAB_MAIN' ); ?></a>
				</li>
				<li>
					<a href="#theme-advanceTheme" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME' ); ?></a>
				</li>
			</ul>
		</div>

		<div class="tab-content">

			<div class="tab-pane active" id="theme-parameter">
				<table width="100%" class="table table-striped">
				<tbody>
					<?php echo $this->loadTemplate('main_bootstrap');?>
				</tbody>
				</table>
			</div>

			<div class="tab-pane" id="theme-advanceTheme">
				<table width="100%" class="table table-striped">
					<tbody>
						<?php echo $this->loadTemplate('advance_theme_bootstrap');?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

