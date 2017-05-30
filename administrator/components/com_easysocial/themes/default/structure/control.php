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
<div class="row">

	<div class="col-md-7">
		<div class="media">
			<?php if( !empty( $page->iconUrl ) ){ ?>
			<div class="media-object pull-left<?php echo $page->iconRounded ? ' es-avatar es-avatar-rounded' : '';?>">
				<img src="<?php echo $page->iconUrl;?>" width="32" />
			</div>
			<?php } ?>

			<?php if( !empty( $page->icon ) ){ ?>
			<div class="media-object pull-left">
				<i class="<?php echo $page->icon;?> pull-left"></i>
			</div>
			<?php } ?>
			<div class="media-body">
				<h2><?php echo $page->heading; ?></h2>
				<p><?php echo $page->description; ?></p>
			</div>
		</div>


		<div class="clearfix">

		</div>
	</div>

	<div class="col-md-5">

		<div class="navbar pull-right">

			<ul class="nav pull-right">

				<?php if( isset( $page->actions ) ){ ?>
				<li class="dropdown_">
					<a data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-es" role="button" id="drop2" href="#">
						<i class="icon-cog"></i> Sub Actions <b class="caret"></b>
					</a>
					<ul aria-labelledby="drop2" role="menu" class="dropdown-menu">
						<li><a href="#" tabindex="-1">Action</a></li>
						<li><a href="#" tabindex="-1">Another action</a></li>
						<li><a href="#" tabindex="-1">Something else here</a></li>
						<li class="divider"></li>
						<li><a href="#" tabindex="-1">Separated link</a></li>
					</ul>
				</li>
				<?php } ?>

				<?php if( isset( $page->help ) && $page->help ){ ?>
				<li class="helpWrap">
					<a class="btn btn-es dropdown-toggle_ helpButton" href="#" data-bs-toggle="dropdown">
						<i class="ies-support ies-small mr-5"></i>
						<?php echo JText::_( 'COM_EASYSOCIAL_BUTTON_HELP' );?>
						<b class="caret"></b>
					</a>
					<!-- <ul class="dropdown-menu">
						<li> -->
							<div class="dropdown-menu dropdown-menu-modal es-help-dropmenu">
								<div class="modal-header">
									<h5>
										<?php echo JText::_( 'COM_EASYSOCIAL_HELP_TITLE' ); ?>
									</h5>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">

										</div>
									</div>
								</div>
								<div class="modal-footer">
									<div class="pull-right">
										<a href="javascript:void(0);" class="btn btn-danger">Get Help &raquo;</a>
									</div>
								</div>
							</div>
						<!-- </li>
					</ul> -->
				</li>
				<?php } ?>
			</ul>

		</div>

	</div>

</div>
