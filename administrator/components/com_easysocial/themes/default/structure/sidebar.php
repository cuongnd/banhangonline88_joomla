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
<div data-sidebar>
	<ul id="sinav" class="accordion list-unstyled">

		<?php foreach( $menus as $menu ){ ?>

			<?php if( (isset( $menu->access ) && FD::user()->authorise( $menu->access , 'com_easysocial' ) ) || !isset( $menu->access) ){ ?>
			<li class="menu-<?php echo $menu->class;?> menuItem<?php echo !empty( $menu->childs ) ? ' accordion-group' : '';?><?php echo $menu->view == $view ? ' active' : '';?>">
				<a href="<?php echo $menu->link == 'null' ? 'javascript:void(0);' : $menu->link;?>"<?php echo $menu->link == 'null' ? ' data-sidebar-menu-toggle' : '';?>>
					<i class="<?php echo $menu->class;?>"></i>
					<span><?php echo JText::_( $menu->title ); ?></span>

					<span class="badge"><?php echo $menu->count > 0 ? $menu->count : ''; ?></span>
				</a>
				<b></b>

				<?php if( isset( $menu->childs ) && $menu->childs ){ ?>
				<ul class="list-unstyled accordion-body collapse<?php echo $menu->view == $view ? ' in' : '';?>" id="menu-<?php echo $menu->uid;?>">
					<?php foreach( $menu->childs as $child ){ ?>
						<?php $active = JRequest::getVar( (string) $menu->active , '' ); ?>

						<li class="menu-<?php echo $child->class;?> childItem<?php echo $active == $child->url->{$menu->active} && $view == $child->url->view ? ' active' : '';?>">
							<a href="<?php echo $child->link;?>">
								<?php if( $child->class ){ ?>
								<i class="<?php echo $child->class;?> ies-small mr-5"></i>
								<?php } ?>

								<span><?php echo JText::_( $child->title ); ?></span>
								<i class="icon-caret-right"></i>
							</a>
							<span class="badge"><?php echo $child->count > 0 ? $child->count : ''; ?></span>
						</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>

		<?php } ?>

	</ul>

	<div class="side-widget-rounded version-widget">
		<div class="side-wbody fd-small" style="display: none;" data-easysocial-version></div>
	</div>

</div>
