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
<div class="widget-box">
	<h3><?php echo JText::_( 'COM_EASYSOCIAL_WIDGET_TITLE_STATISTICS' );?></h3>

	<div id="stat" class="accordion-body in">
		<div class="wbody wbody-padding">
			<div class="es-stat">
				<div class="row es-stat-items">
					<div class="col-md-3 es-stat-item">
						<a href="javascript:void(0);" class="pull-left">
							<span><i class="ies-user-add"></i></span>
						</a>
						<ul class="list-unstyled">
							<li class="es-stat-no"><?php echo $totalUsers; ?></li>
							<li class="es-stat-title"><?php echo JText::_( 'COM_EAYSOCIAL_USERS' );?></li>
						</ul>
					</div>
					<div class="col-md-3 es-stat-item">
						<a href="javascript:void(0);" class="pull-left">
							<span><i class="ies-users"></i></span>
						</a>
						<ul class="list-unstyled">
							<li class="es-stat-no"><?php echo $totalGroups;?></li>
							<li class="es-stat-title"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS' );?></li>
						</ul>
					</div>
					<div class="col-md-3 es-stat-item">
						<a href="javascript:void(0);" class="pull-left">
							<span><i class="ies-user"></i></span>
						</a>
						<ul class="list-unstyled">
							<li class="es-stat-no"><?php echo $totalOnline;?></li>
							<li class="es-stat-title"><?php echo JText::_( 'COM_EASYSOCIAL_ONLINE' );?></li>
						</ul>
					</div>
					<div class="col-md-3 es-stat-item">
						<a href="javascript:void(0);" class="pull-left">
							<span><i class="ies-pictures"></i></span>
						</a>
						<ul class="list-unstyled">
							<li class="es-stat-no"><?php echo $totalAlbums;?></li>
							<li class="es-stat-title"><?php echo JText::_( 'COM_EASYSOCIAL_WIDGETS_STATS_TOTAL_ALBUMS' );?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
